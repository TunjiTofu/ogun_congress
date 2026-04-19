<?php

namespace App\Filament\Resources\OfflinePaymentResource\Pages;

use App\Enums\OfflinePaymentStatus;
use App\Filament\Resources\OfflinePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOfflinePayments extends ListRecords
{
    protected static string $resource = OfflinePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Record Payment'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn ($q) => $q->where('status', OfflinePaymentStatus::PENDING))
                ->badge(fn () => \App\Models\OfflinePayment::where('status', OfflinePaymentStatus::PENDING)->count())
                ->badgeColor('warning'),

            'confirmed' => Tab::make('Confirmed')
                ->modifyQueryUsing(fn ($q) => $q->where('status', OfflinePaymentStatus::CONFIRMED)),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn ($q) => $q->where('status', OfflinePaymentStatus::REJECTED)),
        ];
    }
}
