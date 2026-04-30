<?php

namespace App\Repositories;

use App\Models\CheckinEvent;
use App\Repositories\Interfaces\CheckinRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CheckinRepository extends BaseRepository implements CheckinRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new CheckinEvent());
    }

    /**
     * Bulk-insert offline sync events.
     *
     * Strategy: collect UUIDs that are NOT already in the table,
     * then insert only the new ones. This is safe and performant
     * even if the PWA retries the same batch multiple times.
     */
    public function bulkInsertDeduped(array $events): int
    {
        if (empty($events)) {
            return 0;
        }

        $incomingUuids = array_column($events, 'uuid');

        // Find which UUIDs already exist
        $existingUuids = CheckinEvent::whereIn('uuid', $incomingUuids)
            ->pluck('uuid')
            ->toArray();

        $newEvents = array_filter(
            $events,
            fn ($e) => ! in_array($e['uuid'], $existingUuids)
        );

        if (empty($newEvents)) {
            return 0;
        }

        $rows = array_map(function ($e) {
            return [
                'uuid'              => $e['uuid'],
                'camper_id'         => $e['camper_id'],
                'event_type'        => $e['event_type'],
                'session_id'        => $e['session_id'] ?? null,
                'scanned_by'        => null,               // offline — no authenticated user
                'device_id'         => $e['device_id'],
                'scanned_at'        => $e['scanned_at'],
                'synced_at'         => now(),
                'consent_collected' => $e['consent_collected'] ?? false,
                'notes'             => $e['notes'] ?? null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ];
        }, array_values($newEvents));

        CheckinEvent::insert($rows);

        return count($rows);
    }

    public function getLatestForCamper(int $camperId): ?CheckinEvent
    {
        return CheckinEvent::where('camper_id', $camperId)
            ->orderByDesc('scanned_at')
            ->first();
    }

    public function getForSession(int $sessionId): Collection
    {
        return CheckinEvent::with(['camper:id,camper_number,full_name,category'])
            ->where('session_id', $sessionId)
            ->orderBy('scanned_at')
            ->get();
    }

    public function existsByUuid(string $uuid): bool
    {
        return CheckinEvent::where('uuid', $uuid)->exists();
    }
}
