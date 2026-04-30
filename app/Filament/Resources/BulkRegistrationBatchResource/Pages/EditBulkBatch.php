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
            $data['church_display'], $data['district_display'], $data['duplicate_warning']);
        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->isDraft()) {
            $this->record->recalculateTotal();
        }
    }

    /**
     * Server-side duplicate check before Livewire persists entries.
     * Stops the save and shows an error if the same name+phone+category
     * appears more than once in a batch.
     */
    protected function beforeValidate(): void
    {
        $entries = $this->data['entries'] ?? [];
        $seen    = [];

        foreach ($entries as $entry) {
            $key = strtolower(trim($entry['full_name'] ?? ''))
                . '|' . trim($entry['phone'] ?? '')
                . '|' . ($entry['category'] ?? '');

            if (empty(trim($entry['full_name'] ?? ''))) continue;

            if (isset($seen[$key])) {
                $this->halt();
                \Filament\Notifications\Notification::make()
                    ->title('Duplicate camper entry')
                    ->body("\"" . trim($entry['full_name']) . "\" appears more than once with the same phone and category. Each camper must be a unique entry.")
                    ->danger()
                    ->persistent()
                    ->send();
                return;
            }
            $seen[$key] = true;
        }
    }
}
