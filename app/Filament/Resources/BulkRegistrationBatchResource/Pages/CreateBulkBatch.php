<?php

namespace App\Filament\Resources\BulkRegistrationBatchResource\Pages;

use App\Filament\Resources\BulkRegistrationBatchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBulkBatch extends CreateRecord
{
    protected static string $resource = BulkRegistrationBatchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['status']     = 'draft';
        // Remove the UI-only district cascade field — not a real column
        unset($data['district_id'], $data['district_id_for_church']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
