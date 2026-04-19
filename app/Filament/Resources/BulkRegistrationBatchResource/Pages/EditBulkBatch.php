<?php

namespace App\Filament\Resources\BulkRegistrationBatchResource\Pages;

use App\Filament\Resources\BulkRegistrationBatchResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBulkBatch extends EditRecord
{
    protected static string $resource = BulkRegistrationBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(fn () => $this->record->isDraft()),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Coordinator: always enforce their church
        if (auth()->user()->hasRole('church_coordinator')) {
            $data['church_id'] = auth()->user()->church_id;
        }
        unset($data['district_id'], $data['district_id_for_church'], $data['entries'],
            $data['church_display'], $data['district_display']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->isDraft()) {
            $this->record->recalculateTotal();
        }
    }
}
