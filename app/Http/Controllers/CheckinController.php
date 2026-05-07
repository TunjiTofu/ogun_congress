<?php

namespace App\Http\Controllers;

use App\Enums\CheckinEventType;
use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class CheckinController extends Controller
{
    public function auth(Request $request): JsonResponse
    {
        Log::channel('stack')->info('checkin.auth.attempt', [
            'email'     => $request->email,
            'device_id' => $request->device_id,
            'ip'        => $request->ip(),
        ]);

        $request->validate([
            'email'     => ['required', 'email'],
            'pin'       => ['required', 'string'],
            'device_id' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->pin, $user->password)) {
            Log::warning('checkin.auth.failed', ['email' => $request->email]);
            return response()->json(['success' => false, 'message' => 'Invalid credentials.'], 401);
        }

        if (! $user->hasAnyRole(['secretariat', 'security', 'super_admin'])) {
            Log::warning('checkin.auth.unauthorized', [
                'email' => $request->email,
                'roles' => $user->getRoleNames(),
            ]);
            return response()->json(['success' => false, 'message' => 'Your account does not have check-in access.'], 403);
        }

        $user->tokens()->where('name', 'like', 'checkin-%')->delete();

        $token = $user->createToken('checkin-' . ($request->device_id ?? 'device'))->plainTextToken;

        Log::info('checkin.auth.success', [
            'user_id'   => $user->id,
            'email'     => $user->email,
            'device_id' => $request->device_id,
        ]);

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => ['name' => $user->name, 'roles' => $user->getRoleNames()],
        ]);
    }

    public function sync(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 500), 500);
        $page    = (int) $request->input('page', 1);

        Log::info('checkin.sync.request', [
            'user_id' => auth()->id(),
            'page'    => $page,
            'per_page'=> $perPage,
        ]);

        try {
            $campers = Camper::with(['media', 'church.district'])
                ->orderBy('full_name')
                ->paginate($perPage, ['*'], 'page', $page);

            $data = collect($campers->items())->map(fn (Camper $c) => $this->formatCamper($c));

            Log::info('checkin.sync.success', [
                'user_id' => auth()->id(),
                'page'    => $page,
                'count'   => $data->count(),
                'total'   => $campers->total(),
            ]);

            return response()->json([
                'data'         => $data,
                'total'        => $campers->total(),
                'current_page' => $campers->currentPage(),
                'last_page'    => $campers->lastPage(),
            ]);
        } catch (\Throwable $e) {
            Log::error('checkin.sync.error', [
                'user_id' => auth()->id(),
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Sync failed: ' . $e->getMessage()], 500);
        }
    }

    public function lookup(string $identifier): JsonResponse
    {
        $identifier = strtoupper(trim($identifier));

        Log::info('checkin.lookup', [
            'user_id'    => auth()->id(),
            'identifier' => $identifier,
        ]);

        try {
            $camper = Camper::with(['media', 'church.district'])
                ->where('camper_number', $identifier)
                ->first();

            if (! $camper) {
                $camper = Camper::with(['media', 'church.district'])
                    ->where('full_name', 'like', '%' . $identifier . '%')
                    ->first();
            }

            if (! $camper) {
                Log::info('checkin.lookup.not_found', ['identifier' => $identifier]);
                return response()->json(['success' => false, 'message' => 'Camper not found.'], 404);
            }

            Log::info('checkin.lookup.found', [
                'camper_number' => $camper->camper_number,
                'identifier'    => $identifier,
            ]);

            return response()->json(['success' => true, 'camper' => $this->formatCamper($camper)]);
        } catch (\Throwable $e) {
            Log::error('checkin.lookup.error', ['error' => $e->getMessage(), 'identifier' => $identifier]);
            return response()->json(['message' => 'Lookup failed: ' . $e->getMessage()], 500);
        }
    }

    public function storeEvents(Request $request): JsonResponse
    {
        Log::info('checkin.events.incoming', [
            'user_id' => auth()->id(),
            'count'   => count($request->input('events', [])),
            'ip'      => $request->ip(),
        ]);

        try {
            $request->validate([
                'events'                         => ['required', 'array', 'min:1'],
                'events.*.uuid'                  => ['required', 'string', 'max:100'],
                'events.*.camper_number'          => ['required', 'string'],
                'events.*.event_type'             => ['required', 'string', 'in:check_in,check_out,programme_attendance'],
                'events.*.occurred_at'            => ['required', 'date'],
                'events.*.programme_session_id'   => ['nullable', 'integer'],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::warning('checkin.events.validation_failed', [
                'errors' => $e->errors(),
                'input'  => $request->input('events'),
            ]);
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 422);
        }

        $saved   = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($request->input('events') as $event) {
            try {
                if (CheckinEvent::where('uuid', $event['uuid'])->exists()) {
                    $skipped++;
                    Log::debug('checkin.events.duplicate', ['uuid' => $event['uuid']]);
                    continue;
                }

                $camper = Camper::where('camper_number', $event['camper_number'])->first();

                if (! $camper) {
                    $errors[] = 'Camper not found: ' . $event['camper_number'];
                    Log::warning('checkin.events.camper_not_found', ['camper_number' => $event['camper_number']]);
                    continue;
                }

                CheckinEvent::create([
                    'uuid'                 => $event['uuid'],
                    'camper_id'            => $camper->id,
                    'event_type'           => CheckinEventType::from($event['event_type']),
                    'programme_session_id' => $event['programme_session_id'] ?? null,
                    'occurred_at'          => $event['occurred_at'],
                    'device_id'            => $event['device_id'] ?? null,
                    'recorded_by'          => auth()->id(),
                ]);

                // Mark consent collected on camper record
                if (! empty($event['consent_collected'])) {
                    $camper->update(['consent_collected' => true]);
                }

                $saved++;

            } catch (\Throwable $e) {
                $errors[] = $e->getMessage();
                Log::error('checkin.events.item_error', [
                    'uuid'  => $event['uuid'] ?? 'unknown',
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('checkin.events.complete', [
            'user_id' => auth()->id(),
            'saved'   => $saved,
            'skipped' => $skipped,
            'errors'  => count($errors),
        ]);

        return response()->json([
            'saved'   => $saved,
            'skipped' => $skipped,
            'total'   => count($request->input('events')),
            'errors'  => $errors,
        ]);
    }

    public function sessions(): JsonResponse
    {
        $sessions = \App\Models\ProgrammeSession::where('is_active', true)
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->get(['id', 'title', 'date', 'start_time', 'end_time', 'venue']);

        Log::info('checkin.sessions.fetched', [
            'user_id' => auth()->id(),
            'count'   => $sessions->count(),
            'date'    => today()->toDateString(),
        ]);

        return response()->json($sessions);
    }

    private function formatCamper(Camper $c): array
    {
        $lastEventType = CheckinEvent::where('camper_id', $c->id)
            ->whereIn('event_type', ['check_in', 'check_out'])
            ->latest('occurred_at')
            ->value('event_type');

        $isCheckedIn = $lastEventType === 'check_in'
            || (is_object($lastEventType) && $lastEventType === CheckinEventType::CHECK_IN);

        $lastEvent = CheckinEvent::with('recordedBy')
            ->where('camper_id', $c->id)
            ->whereIn('event_type', ['check_in', 'check_out'])
            ->latest('occurred_at')
            ->first();

        return [
            'camper_number'          => $c->camper_number,
            'full_name'              => $c->full_name,
            'gender'                 => $c->gender?->value,
            'category'               => $c->category?->label() ?? $c->category?->value,
            'club_rank'              => $c->club_rank,
            'church'                 => $c->church?->name,
            'district'               => $c->church?->district?->name,
            'photo_url'              => $c->getFirstMedia('photo')
                ? route('camper.photo', $c->id)
                : null,
            'is_checked_in'          => $isCheckedIn,
            'requires_consent'       => $c->requiresConsentForm(),
            'consent_required'       => $c->requiresConsentForm(),
            'consent_collected'      => (bool) $c->consent_collected,
            'last_event_type'        => $lastEvent ? (is_string($lastEvent->event_type) ? $lastEvent->event_type : $lastEvent->event_type?->value) : null,
            'last_event_at'          => $lastEvent?->occurred_at?->format('g:i A, d M Y'),
            'last_event_by'          => $lastEvent?->recordedBy?->name,
        ];
    }
}
