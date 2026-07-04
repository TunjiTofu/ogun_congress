<?php

namespace App\Filament\Resources\BulkRegistrationBatchResource\Pages;

use App\Filament\Resources\BulkRegistrationBatchResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateBulkBatch extends CreateRecord
{
    protected static string $resource = BulkRegistrationBatchResource::class;

    public function mount(): void
    {
        // Block coordinators from creating new batches when registration is closed.
        // Admins and super_admins can always create batches regardless of this setting.
        if (auth()->user()->hasRole('church_coordinator')) {
            $closed = setting('registration_open', '1') !== '1'
                || (
                    setting('registration_closes_at')
                    && now()->gt(\Illuminate\Support\Carbon::parse(setting('registration_closes_at')))
                );

            if ($closed) {
                Notification::make()
                    ->title('Registration is currently closed.')
                    ->body('New bulk registrations cannot be created at this time. Contact the conference office for assistance.')
                    ->danger()
                    ->persistent()
                    ->send();

                $this->redirect(static::getResource()::getUrl('index'));
                return;
            }
        }

        parent::mount();
    }

    /**
     * Pre-fill church_id from the coordinator's assigned church.
     * This runs before the form is displayed.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $user = auth()->user();
        if ($user->hasRole('church_coordinator') && $user->church_id) {
            $data['church_id'] = $user->church_id;
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

        if ($user->hasRole('church_coordinator')) {
            $data['church_id'] = $user->church_id;
        }

        unset($data['district_id'], $data['district_id_for_church'], $data['entries'],
            $data['church_display'], $data['district_display'], $data['duplicate_warning']);
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        // Coordinators go to the list so they see the draft banner
        // and know they still need to submit for payment.
        if (auth()->user()->hasRole('church_coordinator')) {
            return $this->getResource()::getUrl('index');
        }

        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        if (auth()->user()->hasRole('church_coordinator')) {
            return 'Batch created! Please review it and submit for payment when ready.';
        }
        return 'Batch created successfully.';
    }
}

