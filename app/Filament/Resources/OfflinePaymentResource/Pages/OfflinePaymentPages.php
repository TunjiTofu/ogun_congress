<?php

namespace App\Filament\Resources\OfflinePaymentResource\Pages;

use App\Filament\Resources\OfflinePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use App\Enums\OfflinePaymentStatus;
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
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', OfflinePaymentStatus::PENDING))
                ->badge(fn () => \App\Models\OfflinePayment::where('status', OfflinePaymentStatus::PENDING)->count())
                ->badgeColor('warning'),
            'confirmed' => Tab::make('Confirmed')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', OfflinePaymentStatus::CONFIRMED)),
            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('status', OfflinePaymentStatus::REJECTED)),
        ];
    }
}

class CreateOfflinePayment extends CreateRecord
{
    protected static string $resource = OfflinePaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Status always starts as pending when accountant creates record
        $data['status'] = OfflinePaymentStatus::PENDING->value;
        return $data;
    }
}

class EditOfflinePayment extends EditRecord
{
    protected static string $resource = OfflinePaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function canEdit(): bool
    {
        // Only allow editing pending payments
        return $this->record->isPending();
    }
}
