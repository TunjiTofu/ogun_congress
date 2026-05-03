<?php

namespace App\Filament\Resources\BulkRegistrationBatchResource\Pages;

use App\Filament\Resources\BulkRegistrationBatchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBulkBatch extends CreateRecord
{
    protected static string $resource = BulkRegistrationBatchResource::class;

    /**
     * Pre-fill church_id from the coordinator's assigned church.
     * This runs before the form is displayed.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = auth()->user();
        if ($user->hasRole('church_coordinator') && $user->church_id) {
            $data['church_id'] = $user->church_id;
            // Also set the district cascade field for display
            $church = \App\Models\Church::find($user->church_id);
            if ($church) {
                $data['district_id'] = $church->district_id;
            }
        }
        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = auth()->user();
        $data['created_by'] = $user->id;
        $data['status']     = 'draft';

        // Coordinators always get their church — no conditional
        if ($user->hasRole('church_coordinator')) {
            $data['church_id'] = $user->church_id;
        }

        // Strip all UI-only and relationship fields
        unset($data['district_id'], $data['district_id_for_church'], $data['entries'],
            $data['church_display'], $data['district_display'], $data['duplicate_warning']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}

