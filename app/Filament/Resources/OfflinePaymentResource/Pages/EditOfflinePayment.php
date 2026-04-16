<?php

namespace App\Filament\Resources\OfflinePaymentResource\Pages;

use App\Filament\Resources\OfflinePaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
        return $this->record->isPending();
    }
}
