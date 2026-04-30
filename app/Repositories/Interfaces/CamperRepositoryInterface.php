<?php

namespace App\Repositories\Interfaces;

use App\Models\Camper;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface CamperRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCamperNumber(string $camperNumber): ?Camper;

    public function findByCamperNumberOrFail(string $camperNumber): Camper;

    /**
     * Returns campers for the PWA offline sync endpoint.
     * Only CLAIMED registrations, paginated.
     */
    public function getClaimedForSync(int $page = 1, int $perPage = 500): LengthAwarePaginator;

    /**
     * Returns campers with medical alerts for the health dashboard.
     */
    public function getWithHealthAlerts(): Collection;

    /**
     * Full search across name, camper number, and church.
     */
    public function search(string $term): Collection;
}
