<?php

namespace App\Filament\Pages;

use App\Models\BulkRegistrationBatch;
use App\Models\OfflinePayment;
use App\Models\RegistrationCode;
use Filament\Pages\Page;

class AccountantDashboard extends Page
{
    protected static ?string $navigationIcon  = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'My Dashboard';
    protected static ?int    $navigationSort  = -10;
    protected static string  $view            = 'filament.pages.accountant-dashboard';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('accountant');
    }

    public function getViewData(): array
    {
        $pendingOffline = OfflinePayment::where('status', 'pending')->count();
        $confirmedOffline = OfflinePayment::where('status', 'confirmed')->count();
        $totalOfflineCollected = OfflinePayment::where('status', 'confirmed')->sum('amount');

        $pendingBatches = BulkRegistrationBatch::where('status', 'pending_payment')->count();
        $confirmedBatches = BulkRegistrationBatch::where('status', 'confirmed')->count();
        $totalBatchCollected = BulkRegistrationBatch::where('status', 'confirmed')->sum('amount_paid');

        $recentPendingPayments = OfflinePayment::where('status', 'pending')
            ->with('registrationCode')->latest()->limit(5)->get();

        $recentPendingBatches = BulkRegistrationBatch::where('status', 'pending_payment')
            ->with('church.district', 'createdBy')->latest()->limit(5)->get();

        $totalRevenue = $totalOfflineCollected + $totalBatchCollected;

        return compact(
            'pendingOffline', 'confirmedOffline', 'totalOfflineCollected',
            'pendingBatches', 'confirmedBatches', 'totalBatchCollected',
            'recentPendingPayments', 'recentPendingBatches', 'totalRevenue'
        );
    }
}
