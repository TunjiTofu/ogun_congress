<?php

namespace App\Filament\Widgets;

use App\Enums\CamperCategory;
use App\Enums\CheckinEventType;
use App\Enums\CodeStatus;
use App\Enums\OfflinePaymentStatus;
use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\OfflinePayment;
use App\Models\RegistrationCode;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    // Poll every 30 seconds for near-real-time updates
    protected static ?string $pollingInterval = '30s';

    protected function getStats(): array
    {
        $totalRegistered = Camper::count();

        $checkedInToday = CheckinEvent::where('event_type', CheckinEventType::CHECK_IN)
            ->whereDate('scanned_at', today())
            ->distinct('camper_id')
            ->count();

        $pendingOffline = OfflinePayment::where('status', OfflinePaymentStatus::PENDING)->count();

        $confirmedPayments = OfflinePayment::where('status', OfflinePaymentStatus::CONFIRMED)->count()
            + RegistrationCode::where('payment_type', 'online')
                ->whereIn('status', [CodeStatus::ACTIVE, CodeStatus::CLAIMED])
                ->count();

        $activeUnclaimedCodes = RegistrationCode::where('status', CodeStatus::ACTIVE)->count();

        $consentOutstanding = Camper::consentOutstanding()->count();

        return [
            Stat::make('Total Registered', number_format($totalRegistered))
                ->description('Campers fully registered')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            Stat::make('Checked In Today', number_format($checkedInToday))
                ->description('Arrivals today')
                ->descriptionIcon('heroicon-m-arrow-right-circle')
                ->color('info'),

            Stat::make('Pending Payments', number_format($pendingOffline))
                ->description('Offline payments awaiting review')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOffline > 0 ? 'warning' : 'gray'),

            Stat::make('Confirmed Payments', number_format($confirmedPayments))
                ->description('Online + offline combined')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make('Active Codes', number_format($activeUnclaimedCodes))
                ->description('Paid but not yet registered')
                ->descriptionIcon('heroicon-m-key')
                ->color($activeUnclaimedCodes > 0 ? 'warning' : 'gray'),

            Stat::make('Consent Outstanding', number_format($consentOutstanding))
                ->description('Under-18 without collected form')
                ->descriptionIcon('heroicon-m-document-text')
                ->color($consentOutstanding > 0 ? 'danger' : 'success'),
        ];
    }
}
