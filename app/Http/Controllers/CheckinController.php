<?php

namespace App\Http\Controllers;

use App\Enums\CheckinEventType;
use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CheckinController extends Controller
{
    /**
     * POST /api/checkin/auth
     * Authenticate staff and return a Sanctum token scoped to check-in.
     */
    public function auth(Request $request): JsonResponse
    {
        $request->validate([
            'email'     => ['required', 'email'],
            'pin'       => ['required', 'string'],
            'device_id' => ['nullable', 'string'],
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->pin, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        // Only allow secretariat and security roles (plus super_admin)
        if (! $user->hasAnyRole(['secretariat', 'security', 'super_admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Your account does not have check-in access.',
            ], 403);
        }

        // Revoke previous checkin tokens for this device, issue a fresh one
        $user->tokens()->where('name', 'like', 'checkin-%')->delete();

        $token = $user->createToken(
            'checkin-' . ($request->device_id ?? 'device')
        )->plainTextToken;

        return response()->json([
            'success' => true,
            'token'   => $token,
            'user'    => [
                'name'  => $user->name,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    /**
     * GET /api/checkin/sync
     * Returns all registered campers for offline caching (paginated).
     */
    public function sync(Request $request): JsonResponse
    {
        $perPage = min((int) $request->input('per_page', 500), 500);
        $page    = (int) $request->input('page', 1);

        $campers = Camper::with(['media', 'church.district'])
            ->orderBy('full_name')
            ->paginate($perPage, ['*'], 'page', $page);

        $data = collect($campers->items())->map(fn (Camper $c) => $this->formatCamper($c));

        return response()->json([
            'data'         => $data,
            'total'        => $campers->total(),
            'current_page' => $campers->currentPage(),
            'last_page'    => $campers->lastPage(),
        ]);
    }

    /**
     * GET /api/checkin/camper/{identifier}
     * Real-time lookup by camper_number or name.
     */
    public function lookup(string $identifier): JsonResponse
    {
        $identifier = strtoupper(trim($identifier));

        $camper = Camper::with(['media', 'church.district'])
            ->where('camper_number', $identifier)
            ->first();

        if (! $camper) {
            $camper = Camper::with(['media', 'church.district'])
                ->where('full_name', 'like', '%' . $identifier . '%')
                ->first();
        }

        if (! $camper) {
            return response()->json(['success' => false, 'message' => 'Camper not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'camper'  => $this->formatCamper($camper),
        ]);
    }

    /**
     * POST /api/checkin/events
     * Accept a batch of check-in events. Idempotent by UUID.
     */
    public function storeEvents(Request $request): JsonResponse
    {
        $request->validate([
            'events'                       => ['required', 'array', 'min:1'],
            'events.*.uuid'                => ['required', 'string', 'max:100'],
            'events.*.camper_number'       => ['required', 'string'],
            'events.*.event_type'          => ['required', 'string', 'in:check_in,check_out,programme_attendance'],
            'events.*.occurred_at'         => ['required', 'date'],
            'events.*.programme_session_id'=> ['nullable', 'integer', 'exists:programme_sessions,id'],
        ]);

        $saved = 0;

        foreach ($request->input('events') as $event) {
            // Skip duplicates
            if (CheckinEvent::where('uuid', $event['uuid'])->exists()) {
                continue;
            }

            $camper = Camper::where('camper_number', $event['camper_number'])->first();
            if (! $camper) continue;

            CheckinEvent::create([
                'uuid'                 => $event['uuid'],
                'camper_id'            => $camper->id,
                'event_type'           => CheckinEventType::from($event['event_type']),
                'programme_session_id' => $event['programme_session_id'] ?? null,
                'occurred_at'          => $event['occurred_at'],
                'device_id'            => $event['device_id'] ?? null,
                'recorded_by'          => auth()->id(),
            ]);

            $saved++;
        }

        return response()->json([
            'saved' => $saved,
            'total' => count($request->input('events')),
        ]);
    }

    /**
     * GET /api/checkin/sessions
     * Returns today's active programme sessions for the PWA attendance picker.
     */
    public function sessions(): JsonResponse
    {
        $sessions = \App\Models\ProgrammeSession::where('is_active', true)
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->get(['id', 'title', 'date', 'start_time', 'end_time', 'venue']);

        return response()->json($sessions);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function formatCamper(Camper $c): array
    {
        // Determine check-in status from last event
        $lastEvent = CheckinEvent::where('camper_id', $c->id)
            ->latest('occurred_at')
            ->value('event_type');

        $isCheckedIn = $lastEvent instanceof CheckinEventType
            ? $lastEvent === CheckinEventType::CHECK_IN
            : $lastEvent === 'check_in';

        return [
            'camper_number'     => $c->camper_number,
            'full_name'         => $c->full_name,
            'gender'            => $c->gender?->value,
            // 'category' as label string — the PWA displays this directly
            'category'          => $c->category?->label() ?? $c->category?->value,
            'club_rank'         => $c->club_rank,
            // 'church' and 'district' match the PWA field names
            'church'            => $c->church?->name,
            'district'          => $c->church?->district?->name,
            'photo_url'         => $c->getFirstMedia('photo')
                ? route('camper.photo', $c->id)
                : null,
            'is_checked_in'     => $isCheckedIn,
            'requires_consent'  => $c->requiresConsentForm(),
            'consent_required'  => $c->requiresConsentForm(),  // alias used by PWA
            'consent_collected' => (bool) $c->consent_collected,
        ];
    }
}
