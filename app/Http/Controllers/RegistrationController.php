<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitRegistrationRequest;
use App\Models\Camper;
use App\Models\ClubRank;
use App\Models\District;
use App\Models\RegistrationCode;
use App\Services\DocumentGenerationService;
use App\Services\RegistrationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RegistrationController extends Controller
{
    public function __construct(
        protected readonly RegistrationService $registrationService,
        protected readonly DocumentGenerationService $documentService,
    ) {}

    /**
     * POST /api/v1/registration/validate-code
     *
     * Returns pre-fill data for an ACTIVE code, or a structured error.
     * The client uses this to populate the read-only fields on the form.
     */
    public function validateCode(Request $request): JsonResponse
    {
        $request->validate(['code' => ['required', 'string']]);

        // validateCode() throws ValidationException on any non-ACTIVE status,
        // which Laravel automatically converts to a 422 JSON response.
        $prefill = $this->registrationService->validateCode($request->input('code'));

        return response()->json([
            'success' => true,
            'data'    => $prefill,
        ]);
    }

    /**
     * POST /api/v1/registration/submit
     *
     * Submits the completed registration form.
     * Multipart/form-data (includes photo upload).
     */
    public function submit(SubmitRegistrationRequest $request): JsonResponse
    {
        $camper = $this->registrationService->submit($request->validated());

        return response()->json([
            'success'        => true,
            'camper_number'  => $camper->camper_number,
            'redirect'       => route('registration.success', ['code' => $camper->camper_number]),
        ], 201);
    }

    /**
     * GET /api/v1/registration/downloads/{code}
     *
     * Returns signed temporary URLs for the camper's documents.
     * Code must be CLAIMED.
     */
    public function downloads(string $code): JsonResponse
    {
        $registrationCode = RegistrationCode::where('code', $code)
            ->where('status', \App\Enums\CodeStatus::CLAIMED)
            ->firstOrFail();

        $camper = $registrationCode->camper;

        if (! $camper) {
            return response()->json(['success' => false, 'message' => 'Camper record not found.'], 404);
        }

        $urls = [];

        if ($camper->id_card_path) {
            $urls['id_card'] = Storage::temporaryUrl(
                $camper->id_card_path,
                now()->addHours(24),
            );
        }

        if ($camper->consent_form_path) {
            $urls['consent_form'] = Storage::temporaryUrl(
                $camper->consent_form_path,
                now()->addHours(24),
            );
        }

        // If documents are still generating, return a pending state
        if (empty($urls)) {
            return response()->json([
                'success' => true,
                'status'  => 'generating',
                'message' => 'Your documents are being prepared. Please check back in a moment.',
            ]);
        }

        return response()->json([
            'success'      => true,
            'status'       => 'ready',
            'camper_name'  => $camper->full_name,
            'camper_number'=> $camper->camper_number,
            'urls'         => $urls,
        ]);
    }

    /**
     * GET /registration/success/{code}
     *
     * Public-facing download page. Accessible any time with a CLAIMED code.
     */
    public function success(string $code)
    {
        $registrationCode = RegistrationCode::where('code', $code)
            ->where('status', \App\Enums\CodeStatus::CLAIMED)
            ->firstOrFail();

        $camper = $registrationCode->camper()->with(['church.district', 'contacts'])->firstOrFail();

        return view('registration.success', compact('camper', 'registrationCode'));
    }

    /**
     * POST /registration/validate (web form)
     *
     * Validates the code and redirects to the registration wizard.
     */
    public function validateCodeWeb(\Illuminate\Http\Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        try {
            $this->registrationService->validateCode($request->input('code'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()
                ->withInput()
                ->with('error', $e->validator->errors()->first('code'));
        }

        return redirect()->route('registration.form', ['code' => $request->input('code')]);
    }

    /**
     * GET /registration/form/{code}
     *
     * Shows the multi-step registration wizard.
     */
    public function form(string $code)
    {
        try {
            $prefill = $this->registrationService->validateCode($code);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->route('registration.index')
                ->with('error', $e->validator->errors()->first('code'));
        }

        $districts = District::orderBy('name')->get();

        // Group ranks by ministry for the Alpine.js CLUB_RANKS constant
        $clubRanks = ClubRank::orderBy('sort_order')
            ->get(['ministry', 'rank_name'])
            ->groupBy('ministry')
            ->map(fn ($ranks) => $ranks->pluck('rank_name')->values())
            ->toArray();

        return view('registration.form', compact('code', 'prefill', 'districts', 'clubRanks'));
    }

    /**
     * POST /registration/submit (web form)
     *
     * Handles the full multi-step form submission from the browser.
     */
    public function submitWeb(\App\Http\Requests\SubmitRegistrationRequest $request)
    {
        $camper = $this->registrationService->submit($request->validated());

        return redirect()->route('registration.success', ['code' => $camper->camper_number]);
    }
}
