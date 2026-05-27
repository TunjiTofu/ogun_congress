<?php

namespace App\Filament\Pages;

use App\Models\BulkRegistrationBatch;
use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\Church;
use App\Models\District;
use Filament\Pages\Page;

class DistrictCoordinatorDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'My District';
    protected static ?int    $navigationSort  = -10;
    protected static string  $view            = 'filament.pages.district-coordinator-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('district_coordinator');
    }

    public function getViewData(): array
    {
        $user     = auth()->user();
        $district = District::with('churches')->find($user->district_id);

        if (! $district) {
            return [
                'district'        => null,
                'churches'        => collect(),
                'churchStats'     => collect(),
                'totalRegistered' => 0,
                'totalCheckedIn'  => 0,
                'consentPending'  => 0,
                'categoryBreakdown' => [],
            ];
        }

        $churchIds = $district->churches->pluck('id');

        // All campers in this district
        $campers = Camper::whereIn('church_id', $churchIds)
            ->with('church')
            ->get();

        // Currently checked-in IDs
        $checkedInIds = CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')
                    ->from('checkin_events')
                    ->whereIn('event_type', ['check_in', 'check_out'])
                    ->groupBy('camper_id');
            })
            ->where('event_type', 'check_in')
            ->pluck('camper_id');

        // Per-church breakdown
        $churchStats = $district->churches->map(function (Church $church) use ($campers, $checkedInIds) {
            $churchCampers   = $campers->where('church_id', $church->id);
            $checkedIn       = $churchCampers->whereIn('id', $checkedInIds->toArray())->count();
            $consentPending  = $churchCampers->filter(fn ($c) => $c->requiresConsentForm() && ! $c->consent_collected)->count();

            return [
                'church'          => $church,
                'total'           => $churchCampers->count(),
                'checked_in'      => $checkedIn,
                'consent_pending' => $consentPending,
                'adventurers'     => $churchCampers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'adventurer')->count(),
                'pathfinders'     => $churchCampers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'pathfinder')->count(),
                'senior_youth'    => $churchCampers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'senior_youth')->count(),
            ];
        })->sortByDesc('total')->values();

        // District totals
        $categoryBreakdown = [
            'adventurers'  => $campers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'adventurer')->count(),
            'pathfinders'  => $campers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'pathfinder')->count(),
            'senior_youth' => $campers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'senior_youth')->count(),
        ];

        return [
            'district'          => $district,
            'churches'          => $district->churches,
            'churchStats'       => $churchStats,
            'totalRegistered'   => $campers->count(),
            'totalCheckedIn'    => $checkedInIds->count(),
            'consentPending'    => $campers->filter(fn ($c) => $c->requiresConsentForm() && ! $c->consent_collected)->count(),
            'categoryBreakdown' => $categoryBreakdown,
        ];
    }
}
