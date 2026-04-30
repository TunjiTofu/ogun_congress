<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

/**
 * Override Filament's default Dashboard.
 * Coordinators → CoordinatorDashboard; Accountants → AccountantDashboard.
 */
class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int    $navigationSort  = -10;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'secretariat', 'security']);
    }
}
