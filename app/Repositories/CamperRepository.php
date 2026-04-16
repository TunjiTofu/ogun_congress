<?php

namespace App\Repositories;

use App\Models\Camper;
use App\Repositories\Interfaces\CamperRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class CamperRepository extends BaseRepository implements CamperRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Camper());
    }

    public function findByCamperNumber(string $camperNumber): ?Camper
    {
        return Camper::where('camper_number', $camperNumber)->first();
    }

    public function findByCamperNumberOrFail(string $camperNumber): Camper
    {
        return Camper::where('camper_number', $camperNumber)->firstOrFail();
    }

    /**
     * Returns all claimed campers for the PWA offline sync.
     * Only the fields the PWA needs — keeps payload small.
     */
    public function getClaimedForSync(int $page = 1, int $perPage = 500): LengthAwarePaginator
    {
        return Camper::select([
                'id',
                'camper_number',
                'full_name',
                'category',
                'consent_collected',
                'photo_path',
                'badge_color',
            ])
            ->with(['registrationCode:id,status'])
            ->whereHas('registrationCode', fn ($q) => $q->claimed())
            ->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Campers with any declared health condition.
     * Restricted to super_admin in the Filament policy — not enforced here.
     */
    public function getWithHealthAlerts(): Collection
    {
        return Camper::with(['health', 'church.district', 'contacts'])
            ->whereHas('health', fn ($q) => $q->where('has_alert', true))
            ->orderBy('full_name')
            ->get();
    }

    /**
     * Full-text style search across name, camper number, and church name.
     */
    public function search(string $term): Collection
    {
        $like = "%{$term}%";

        return Camper::with(['church.district'])
            ->where(function ($q) use ($like) {
                $q->where('full_name', 'like', $like)
                  ->orWhere('camper_number', 'like', $like)
                  ->orWhereHas('church', fn ($cq) => $cq->where('name', 'like', $like));
            })
            ->limit(50)
            ->get();
    }
}
