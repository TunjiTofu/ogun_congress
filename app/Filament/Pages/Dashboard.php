<?php

namespace App\Filament\Pages;

use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\Church;
use App\Models\ContactMessage;
use App\Models\District;
use App\Models\OfflinePayment;
use App\Models\ProgrammeSession;
use App\Models\RegistrationCode;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int    $navigationSort  = -10;
    protected static string  $view            = 'filament.pages.super-admin-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'secretariat', 'security', 'admin']);
    }

    protected function getHeaderActions(): array
    {
        if (! auth()->user()->hasAnyRole(['super_admin', 'admin'])) {
            return [];
        }

        return [
            Action::make('export_management_report')
                ->label('Export Management Report')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->url(route('exports.management-report'))
                ->openUrlInNewTab(false),

            Action::make('export_health')
                ->label('Health Report PDF')
                ->icon('heroicon-o-heart')
                ->color('gray')
                ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin']))
                ->url(route('exports.health-report'))
                ->openUrlInNewTab(),


        ];
    }

    public function getViewData(): array
    {
        $isSuperAdmin = auth()->user()->hasRole('super_admin');

        $total       = Camper::count();
        $adventurers = Camper::where('category', 'adventurer')->count();
        $pathfinders = Camper::where('category', 'pathfinder')->count();
        $seniorYouth = Camper::where('category', 'senior_youth')->count();
        $officials   = Camper::where('is_official', true)->count();

        // Live check-in count
        $currentlyInIds = CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', fn ($sub) =>
            $sub->selectRaw('MAX(id)')->from('checkin_events')
                ->whereIn('event_type', ['check_in', 'check_out'])
                ->groupBy('camper_id'))
            ->where('event_type', 'check_in')
            ->pluck('camper_id');

        $checkedIn  = $currentlyInIds->count();
        $checkedOut = CheckinEvent::where('event_type', 'check_in')
                ->distinct('camper_id')->count() - $checkedIn;

        $pendingOffline  = OfflinePayment::where('status', 'pending')->count();
        $activeCodes     = RegistrationCode::where('status', 'ACTIVE')->count();
        $consentPending  = Camper::whereIn('category', ['adventurer', 'pathfinder'])
            ->where('consent_collected', false)->count();
        $photosPending   = Camper::where('photo_status', 'pending')
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'photo'))->count();
        $photosRejected  = Camper::where('photo_status', 'rejected')->count();
        $unreadMessages  = ContactMessage::where('is_read', false)->count();

        // Registrations per day last 14 days
        $regsByDay = Camper::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays(13))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $chartDays = collect(range(13, 0))->map(fn ($d) => now()->subDays($d)->format('Y-m-d'));
        $chartData = $chartDays->map(fn ($d) => $regsByDay[$d]->count ?? 0)->values()->toArray();
        $chartLabels = $chartDays->map(fn ($d) => Carbon::parse($d)->format('d M'))->toArray();

        // District summary
        $districtStats = $isSuperAdmin
            ? District::with('churches')->get()->map(fn ($d) => [
                'name'      => $d->name,
                'total'     => Camper::whereIn('church_id', $d->churches->pluck('id'))->count(),
                'checked_in'=> Camper::whereIn('church_id', $d->churches->pluck('id'))
                    ->whereIn('id', $currentlyInIds->toArray())->count(),
            ])->sortByDesc('total')->values()
            : collect();

        // Today's sessions
        $todaySessions = ProgrammeSession::where('is_active', true)
            ->whereDate('date', today())->orderBy('start_time')->get()
            ->map(fn ($s) => [
                'title'      => $s->title,
                'start_time' => $s->start_time ? Carbon::parse($s->start_time)->format('g:i A') : '—',
                'venue'      => $s->venue ?? 'Main Hall',
                'attendance' => CheckinEvent::where('event_type', 'programme_attendance')
                    ->where('programme_session_id', $s->id)->distinct('camper_id')->count(),
            ]);

        // Recent 8 registrations
        $recentRegistrations = Camper::with('church')
            ->latest()->limit(8)->get();

        // Recent check-in activity
        $recentCheckins = CheckinEvent::with(['camper.church', 'recordedBy'])
            ->whereIn('event_type', ['check_in', 'check_out'])
            ->latest('occurred_at')->limit(8)->get();

        // Registration status
        $regOpen    = setting('registration_open', '1') === '1';
        $closesAt   = setting('registration_closes_at');
        $regEffOpen = $regOpen && (! $closesAt || now()->lt(Carbon::parse($closesAt)));

        return compact(
            'total', 'adventurers', 'pathfinders', 'seniorYouth', 'officials',
            'checkedIn', 'checkedOut', 'pendingOffline', 'activeCodes',
            'consentPending', 'photosPending', 'photosRejected', 'unreadMessages',
            'chartData', 'chartLabels', 'districtStats',
            'todaySessions', 'recentRegistrations', 'recentCheckins',
            'regEffOpen', 'closesAt', 'isSuperAdmin'
        );
    }
}
