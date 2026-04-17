<?php

namespace App\Http\Controllers;

use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\User;
use App\Enums\CheckinEventType;
use App\Repositories\Interfaces\CamperRepositoryInterface;
use App\Repositories\Interfaces\CheckinRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CheckinController extends Controller
{
    public function __construct(
        private readonly CamperRepositoryInterface  $camperRepository,
        private readonly CheckinRepositoryInterface $checkinRepository,
    ) {}

    /**
     * POST /api/checkin/auth
     *
     * Authenticates a PWA device with a staff PIN.
     * Returns a Sanctum token scoped to checkin abilities.
     */
    public function auth(Request $request): JsonResponse
    {
        $request->validate([
            'device_id' => ['required', 'string', 'max:100'],
            'email'     => ['required', 'email'],
            'pin'       => ['required', 'string'],
        ]);

        $user = User::where('email', $request->email)
            ->where('is_active', true)
            ->first();

        if (! $user || ! Hash::check($request->pin, $user->password)) {
            Log::warning('checkin.auth_failed', ['email' => $request->email]);
            throw ValidationException::withMessages([
                'pin' => 'Invalid credentials.',
            ]);
        }

        // Revoke any existing checkin tokens for this device to prevent accumulation
        $user->tokens()
            ->where('name', 'checkin:' . $request->device_id)
            ->delete();

        $token = $user->createToken(
            name:       'checkin:' . $request->device_id,
            abilities:  ['checkin'],
            expiresAt:  now()->addDays(7),
        );

        Log::info('checkin.auth_success', [
            'user_id'   => $user->id,
            'device_id' => $request->device_id,
        ]);

        return response()->json([
            'success' => true,
            'token'   => $token->plainTextToken,
            'user'    => [
                'name'  => $user->name,
                'roles' => $user->getRoleNames(),
            ],
        ]);
    }

    /**
     * GET /api/checkin/sync
     *
     * Returns paginated CLAIMED campers for offline cache.
     * Requires valid Sanctum token with [checkin] ability.
     */
    public function sync(Request $request): JsonResponse
    {
        $page    = (int) $request->query('page', 1);
        $results = $this->camperRepository->getClaimedForSync($page, 500);

        return response()->json([
            'success'      => true,
            'data'         => $results->items(),
            'current_page' => $results->currentPage(),
            'last_page'    => $results->lastPage(),
            'total'        => $results->total(),
        ]);
    }

    /**
     * POST /api/checkin/events
     *
     * Accepts an array of offline check-in events.
     * Fully idempotent — duplicate UUIDs are silently ignored.
     * Requires valid Sanctum token with [checkin] ability.
     */
    public function storeEvents(Request $request): JsonResponse
    {
        $request->validate([
            'events'                    => ['required', 'array', 'min:1', 'max:50'],
            'events.*.uuid'             => ['required', 'uuid'],
            'events.*.camper_number'    => ['required', 'string'],
            'events.*.event_type'       => ['required', 'string', 'in:check_in,check_out,programme_attendance'],
            'events.*.scanned_at'       => ['required', 'date'],
            'events.*.device_id'        => ['required', 'string'],
            'events.*.consent_collected'=> ['nullable', 'boolean'],
            'events.*.session_id'       => ['nullable', 'integer'],
            'events.*.notes'            => ['nullable', 'string'],
        ]);

        // Resolve camper_number → camper_id for each event
        $camperNumbers = array_column($request->events, 'camper_number');
        $campers       = Camper::whereIn('camper_number', $camperNumbers)
            ->pluck('id', 'camper_number');

        $enriched = array_filter(
            array_map(function ($event) use ($campers) {
                if (! isset($campers[$event['camper_number']])) {
                    return null; // Unknown camper — skip
                }

                return array_merge($event, [
                    'camper_id' => $campers[$event['camper_number']],
                ]);
            }, $request->events)
        );

        $inserted = $this->checkinRepository->bulkInsertDeduped(array_values($enriched));
        $skipped  = count($request->events) - $inserted;

        if ($inserted > 0) {
            Log::info('checkin.events_synced', [
                'inserted'  => $inserted,
                'skipped'   => $skipped,
                'device_id' => $request->events[0]['device_id'] ?? 'unknown',
            ]);
        }

        return response()->json([
            'success'  => true,
            'inserted' => $inserted,
            'skipped'  => $skipped,
        ]);
    }

    /**
     * GET /api/checkin/camper/{code}
     *
     * Real-time camper lookup for online check-in.
     * Returns full card data including photo URL.
     */
    public function camper(string $code): JsonResponse
    {
        $camper = $this->camperRepository->findByCamperNumber($code);

        if (! $camper) {
            return response()->json([
                'success' => false,
                'message' => 'Camper not found.',
            ], 404);
        }

        $latestEvent = $this->checkinRepository->getLatestForCamper($camper->id);

        return response()->json([
            'success' => true,
            'camper'  => [
                'camper_number'     => $camper->camper_number,
                'full_name'         => $camper->full_name,
                'category'          => $camper->category->label(),
                'badge_color'       => $camper->badge_color,
                'church'            => $camper->church->name ?? null,
                'district'          => $camper->church->district->name ?? null,
                'consent_collected' => $camper->consent_collected,
                'consent_required'  => $camper->requiresConsentForm(),
                'photo_url'         => $camper->getFirstMediaUrl('photo', 'thumb'),
                'latest_event'      => $latestEvent ? [
                    'type'       => $latestEvent->event_type->label(),
                    'scanned_at' => $latestEvent->scanned_at->toISOString(),
                ] : null,
            ],
        ]);
    }
}
