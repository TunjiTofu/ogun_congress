<?php

namespace App\Filament\Resources\CamperResource\Pages;

use App\Filament\Resources\CamperResource;
use App\Models\Camper;
use App\Models\Church;
use App\Models\District;
use App\Enums\CamperCategory;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\HtmlString;

class ListCampers extends ListRecords
{
    protected static string $resource = CamperResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Custom view — shows card grid on mobile, table on desktop.
     * Filament renders this via getView().
     */
//    public function getView(): string
//    {
//        return 'filament.pages.campers-list';
//    }

    public function getViewData(): array
    {
        $user         = auth()->user();
        $isSuperAdmin = $user->hasRole('super_admin');

        // Apply simple filters from query string
        $query = Camper::with(['church.district', 'media', 'campRole'])
            ->orderBy('created_at', 'desc');

        if (request('filter_category'))   $query->where('category', request('filter_category'));
        if (request('filter_church'))     $query->where('church_id', request('filter_church'));
        if (request('filter_district'))   $query->whereHas('church', fn ($q) => $q->where('district_id', request('filter_district')));
        if (request('filter_photo'))      $query->where('photo_status', request('filter_photo'));
        if (request('q')) {
            $q = request('q');
            $query->where(fn ($b) => $b->where('full_name', 'like', "%{$q}%")
                ->orWhere('camper_number', 'like', "%{$q}%")
                ->orWhere('phone', 'like', "%{$q}%"));
        }

        $perPage = in_array((int) request('per_page', 24), [12, 24, 48, 100])
            ? (int) request('per_page', 24) : 24;
        $campers = $query->paginate($perPage);
        $districts = District::orderBy('name')->get();
        $churches  = Church::orderBy('name')->get();

        $baseUrl   = route('filament.admin.resources.campers.index');
        return compact('campers', 'districts', 'churches', 'isSuperAdmin', 'baseUrl');
    }
}
