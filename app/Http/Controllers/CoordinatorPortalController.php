<?php

namespace App\Http\Controllers;

use App\Enums\CamperCategory;
use App\Enums\CodeStatus;
use App\Enums\ContactType;
use App\Jobs\GenerateCamperDocumentsJob;
use App\Jobs\SendRegistrationConfirmationSmsJob;
use App\Models\BulkRegistrationBatch;
use App\Models\BulkRegistrationEntry;
use App\Models\Camper;
use App\Models\CamperContact;
use App\Models\CamperHealth;
use App\Models\District;
use App\Models\RegistrationCode;
use App\Repositories\Interfaces\RegistrationCodeRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoordinatorPortalController extends Controller
{
    /**
     * GET /coordinator-portal
     * Login page — coordinator enters their admin email and password,
     * or we use a simple batch code for access.
     */
    public function index()
    {
        return view('coordinator-portal.index');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! auth()->attempt($request->only('email', 'password'))) {
            return back()->withInput()->with('error', 'Invalid credentials.');
        }

        if (! auth()->user()->hasRole('church_coordinator')) {
            auth()->logout();
            return back()->with('error', 'This portal is for Church Coordinators only.');
        }

        session(['coordinator_logged_in' => true]);

        return redirect()->route('coordinator.portal.dashboard');
    }

    public function logout()
    {
        auth()->logout();
        session()->forget('coordinator_logged_in');
        return redirect()->route('coordinator.portal.index');
    }

    /**
     * GET /coordinator-portal/dashboard
     * Shows all confirmed batches and their camper entries with form status.
     */
    public function dashboard()
    {
        if (! auth()->check() || ! auth()->user()->hasRole('church_coordinator')) {
            return redirect()->route('coordinator.portal.index');
        }

        $user    = auth()->user();
        $church  = $user->church()->with('district')->first();

        $batches = BulkRegistrationBatch::where('created_by', $user->id)
            ->where('status', 'confirmed')
            ->with(['entries.registrationCode.camper'])
            ->latest()
            ->get();

        return view('coordinator-portal.dashboard', compact('user', 'church', 'batches'));
    }

    /**
     * GET /coordinator-portal/batch/{batch}/camper/{entry}
     * Show the registration form for a specific batch camper entry.
     */
    public function form(BulkRegistrationBatch $batch, BulkRegistrationEntry $entry)
    {
        if (! auth()->check() || ! auth()->user()->hasRole('church_coordinator')) {
            return redirect()->route('coordinator.portal.index');
        }

        if ($batch->created_by !== auth()->id()) {
            abort(403, 'You do not have access to this batch.');
        }

        if ($entry->batch_id !== $batch->id) {
            abort(404);
        }

        // Already registered — show their docs
        if ($entry->status === 'registered' && $entry->registrationCode?->camper) {
            return redirect()->route('coordinator.portal.dashboard')
                ->with('info', "{$entry->full_name} is already registered.");
        }

        if (! $entry->registrationCode) {
            return redirect()->route('coordinator.portal.dashboard')
                ->with('error', 'No registration code found for this camper. Contact admin.');
        }

        $districts = District::orderBy('name')->get();

        // Club ranks for the JS
        $clubRanks = \App\Models\ClubRank::orderBy('sort_order')
            ->get(['ministry', 'rank_name'])
            ->groupBy('ministry')
            ->map(fn ($ranks) => $ranks->pluck('rank_name')->values())
            ->toArray();

        $prefill = [
            'prefill_name'     => $entry->full_name,
            'prefill_phone'    => $entry->phone,
            'prefill_category' => $entry->category->value,
            'amount_paid'      => $entry->fee,
            'payment_type'     => 'Bulk Batch',
        ];

        $code = $entry->registrationCode->code;

        return view('coordinator-portal.camper-form', compact(
            'batch', 'entry', 'prefill', 'code', 'districts', 'clubRanks'
        ));
    }

    /**
     * POST /coordinator-portal/batch/{batch}/camper/{entry}
     * Process the registration form for a batch camper.
     */
    public function submitForm(
        Request $request,
        BulkRegistrationBatch $batch,
        BulkRegistrationEntry $entry,
        RegistrationCodeRepositoryInterface $codeRepo,
    ) {
        if (! auth()->check() || ! auth()->user()->hasRole('church_coordinator')) {
            return redirect()->route('coordinator.portal.index');
        }

        if ($batch->created_by !== auth()->id() || $entry->batch_id !== $batch->id) {
            abort(403);
        }

        $validated = $request->validate([
            'gender'               => ['required', 'in:male,female'],
            'photo'                => ['nullable', 'image', 'max:2048'],
            'home_address'         => ['nullable', 'string'],
            'church_id'            => ['required', 'exists:churches,id'],
            'ministry'             => ['nullable', 'string'],
            'club_rank'            => ['nullable', 'string'],
            'parent_name'          => ['nullable', 'string'],
            'parent_relationship'  => ['nullable', 'string'],
            'parent_phone'         => ['nullable', 'string'],
            'parent_email'         => ['nullable', 'email'],
            'medical_conditions'   => ['nullable', 'string'],
            'medications'          => ['nullable', 'string'],
            'allergies'            => ['nullable', 'string'],
        ]);

        DB::transaction(function () use ($entry, $validated, $request, $codeRepo) {
            $registrationCode = $entry->registrationCode()->lockForUpdate()->first();
            $category         = $entry->category; // locked from batch entry — never from form

            // Create camper record
            $camper = Camper::create([
                'registration_code_id' => $registrationCode->id,
                'camper_number'        => $registrationCode->code,
                'full_name'            => $entry->full_name,
                'phone'                => $entry->phone,
                'gender'               => $validated['gender'],
                'category'             => $category,
                'home_address'         => $validated['home_address'] ?? null,
                'church_id'            => $validated['church_id'],
                'ministry'             => $validated['ministry'] ?? null,
                'club_rank'            => $validated['club_rank'] ?? null,
            ]);

            // Photo via Spatie
            if ($request->hasFile('photo')) {
                $camper->addMediaFromRequest('photo')
                    ->toMediaCollection('photo');
            }

            // Health record
            CamperHealth::create([
                'camper_id'          => $camper->id,
                'medical_conditions' => $validated['medical_conditions'] ?? null,
                'medications'        => $validated['medications'] ?? null,
                'allergies'          => $validated['allergies'] ?? null,
            ]);

            // Parent/guardian contact
            if ($category->requiresParentalConsent() && ! empty($validated['parent_name'])) {
                CamperContact::create([
                    'camper_id'    => $camper->id,
                    'type'         => ContactType::PARENT_GUARDIAN,
                    'full_name'    => $validated['parent_name'],
                    'relationship' => $validated['parent_relationship'] ?? null,
                    'phone'        => $validated['parent_phone'] ?? null,
                    'email'        => $validated['parent_email'] ?? null,
                    'is_primary'   => true,
                ]);
            }

            // Mark code as CLAIMED
            $codeRepo->markAsClaimed($registrationCode);

            // Mark entry as registered
            $entry->update(['status' => 'registered']);

            // Generate documents
            GenerateCamperDocumentsJob::dispatch($camper->id);

            SendRegistrationConfirmationSmsJob::dispatch(
                phone:        $entry->phone,
                name:         $entry->full_name,
                camperNumber: $registrationCode->code,
            );

            Log::info('coordinator.camper_registered', [
                'batch_id'      => $entry->batch_id,
                'entry_id'      => $entry->id,
                'camper_number' => $registrationCode->code,
            ]);
        });

        return redirect()->route('coordinator.portal.dashboard')
            ->with('success', "{$entry->full_name} has been registered successfully. Documents are being generated.");
    }
}
