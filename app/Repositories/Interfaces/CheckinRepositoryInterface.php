<?php

namespace App\Repositories\Interfaces;

use App\Models\CheckinEvent;
use Illuminate\Database\Eloquent\Collection;

interface CheckinRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Bulk insert offline sync events.
     * Deduplicates by UUID — events that already exist are silently skipped.
     */
    public function bulkInsertDeduped(array $events): int;

    /**
     * Returns the latest check-in event for a camper.
     */
    public function getLatestForCamper(int $camperId): ?CheckinEvent;

    /**
     * Returns all events for a given programme session.
     */
    public function getForSession(int $sessionId): Collection;

    /**
     * Check whether a UUID has already been recorded (idempotency guard).
     */
    public function existsByUuid(string $uuid): bool;
}
