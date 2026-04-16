<?php

namespace App\Filament\Resources\OfflinePaymentResource\Pages;

use App\Enums\OfflinePaymentStatus;
use App\Filament\Resources\OfflinePaymentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOfflinePayment extends CreateRecord
{
    protected static string $resource = OfflinePaymentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = OfflinePaymentStatus::PENDING->value;
        return $data;
    }
}
