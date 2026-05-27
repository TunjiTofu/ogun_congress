<?php

namespace App\Filament\Widgets;

use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\OfflinePayment;
use App\Models\RegistrationCode;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $total        = Camper::count();
        $adventurers  = Camper::where('category', 'adventurer')->count();
        $pathfinders  = Camper::where('category', 'pathfinder')->count();
        $seniorYouth  = Camper::where('category', 'senior_youth')->count();

        $checkedIn = CheckinEvent::selectRaw('camper_id')
            ->whereIn('id', fn ($sub) =>
            $sub->selectRaw('MAX(id)')->from('checkin_events')
                ->whereIn('event_type', ['check_in', 'check_out'])
                ->groupBy('camper_id'))
            ->where('event_type', 'check_in')->count();

        $pendingOffline   = OfflinePayment::where('status', 'pending')->count();
        $activeCodes      = RegistrationCode::where('status', 'ACTIVE')->count();
        $consentPending   = Camper::whereIn('category', ['adventurer', 'pathfinder'])
            ->where('consent_collected', false)->count();
        $photosPending    = Camper::where('photo_status', 'pending')
            ->whereHas('media', fn ($q) => $q->where('collection_name', 'photo'))->count();
        $photosRejected   = Camper::where('photo_status', 'rejected')->count();
        $officials        = Camper::where('is_official', true)->count();

        // Trend: registrations last 7 days
        $last7 = Camper::where('created_at', '>=', now()->subDays(7))->count();

        return [
            Stat::make('Total Registered', number_format($total))
                ->description("{$last7} in last 7 days")
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('primary')
                ->chart(
                    Camper::selectRaw('COUNT(*) as count')
                        ->where('created_at', '>=', now()->subDays(14))
                        ->groupByRaw('DATE(created_at)')
                        ->orderByRaw('DATE(created_at)')
                        ->pluck('count')->toArray()
                ),

            Stat::make('Currently In Camp', number_format($checkedIn))
                ->description($total > 0 ? round($checkedIn / $total * 100) . '% of registered' : 'No registrations')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('success'),

            Stat::make('Pending Offline Payments', number_format($pendingOffline))
                ->description('Awaiting accountant confirmation')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color($pendingOffline > 0 ? 'warning' : 'gray'),

            Stat::make('Active Codes (Unclaimed)', number_format($activeCodes))
                ->description('Payment confirmed, not yet registered')
                ->descriptionIcon('heroicon-m-key')
                ->color($activeCodes > 0 ? 'info' : 'gray'),

            Stat::make('Consent Forms Pending', number_format($consentPending))
                ->description('Under-18 without collected consent')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($consentPending > 0 ? 'warning' : 'success'),

            Stat::make('Photos Pending Review', number_format($photosPending))
                ->description($photosRejected > 0 ? "{$photosRejected} rejected" : 'No rejections')
                ->descriptionIcon('heroicon-m-camera')
                ->color($photosPending > 0 ? 'warning' : 'success'),
        ];
    }
}
