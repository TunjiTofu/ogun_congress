<?php

namespace App\Filament\Pages;

use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\Church;
use App\Models\District;
use App\Models\ProgrammeSession;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class CampDirectorDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-presentation-chart-bar';
    protected static ?string $navigationLabel = 'Director Overview';
    protected static ?int    $navigationSort  = -10;
    protected static string  $view            = 'filament.pages.camp-director-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('camp_director');
    }

    public function getViewData(): array
    {
        $totalCampers = Camper::count();

        // Currently in camp (last event = check_in)
        $currentlyInIds = CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', function ($sub) {
                $sub->selectRaw('MAX(id)')->from('checkin_events')
                    ->whereIn('event_type', ['check_in', 'check_out'])
                    ->groupBy('camper_id');
            })
            ->where('event_type', 'check_in')
            ->pluck('camper_id');

        $totalCheckedIn  = $currentlyInIds->count();
        $totalCheckedOut = CheckinEvent::where('event_type', 'check_in')
                ->distinct('camper_id')->count() - $totalCheckedIn;

        $consentPending = Camper::whereIn('category', ['adventurer', 'pathfinder'])
            ->where('consent_collected', false)->count();

        $photosPending  = Camper::where('photo_status', 'pending')
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'photo'))->count();
        $photosRejected = Camper::where('photo_status', 'rejected')->count();

        // Category breakdown
        $categoryBreakdown = [
            'adventurers'  => Camper::where('category', 'adventurer')->count(),
            'pathfinders'  => Camper::where('category', 'pathfinder')->count(),
            'senior_youth' => Camper::where('category', 'senior_youth')->count(),
        ];

        // District breakdown
        $districtStats = District::with('churches')->get()->map(function (District $district) use ($currentlyInIds) {
            $churchIds  = $district->churches->pluck('id');
            $campers    = Camper::whereIn('church_id', $churchIds)->get();
            $checkedIn  = $campers->whereIn('id', $currentlyInIds->toArray())->count();
            $consent    = $campers->filter(fn ($c) => $c->requiresConsentForm() && ! $c->consent_collected)->count();

            return [
                'district'        => $district,
                'churches'        => $district->churches->count(),
                'total'           => $campers->count(),
                'checked_in'      => $checkedIn,
                'consent_pending' => $consent,
                'adventurers'     => $campers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'adventurer')->count(),
                'pathfinders'     => $campers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'pathfinder')->count(),
                'senior_youth'    => $campers->filter(fn ($c) => ($c->category?->value ?? $c->category) === 'senior_youth')->count(),
            ];
        })->sortByDesc('total')->values();

        // Today's active sessions
        $todaySessions = ProgrammeSession::where('is_active', true)
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->get()
            ->map(fn ($s) => [
                'title'      => $s->title,
                'start_time' => $s->start_time ? Carbon::parse($s->start_time)->format('g:i A') : '—',
                'end_time'   => $s->end_time   ? Carbon::parse($s->end_time)->format('g:i A')   : '—',
                'venue'      => $s->venue ?? 'Main Hall',
                'attendance' => CheckinEvent::where('event_type', 'programme_attendance')
                    ->where('programme_session_id', $s->id)->distinct('camper_id')->count(),
            ]);

        // Recent check-in activity (last 10)
        $recentActivity = CheckinEvent::with(['camper.church', 'recordedBy'])
            ->whereIn('event_type', ['check_in', 'check_out'])
            ->latest('occurred_at')
            ->limit(10)
            ->get();

        return compact(
            'totalCampers', 'totalCheckedIn', 'totalCheckedOut',
            'consentPending', 'photosPending', 'photosRejected',
            'categoryBreakdown', 'districtStats',
            'todaySessions', 'recentActivity'
        );
    }
}
