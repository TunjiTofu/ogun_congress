<?php

namespace App\Filament\Resources\CamperResource\Pages;

use App\Enums\CamperCategory;
use App\Filament\Resources\CamperResource;
use App\Models\Camper;
use App\Models\Church;
use App\Models\District;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCamper extends EditRecord
{
    protected static string $resource = CamperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),

            // ── Regenerate this camper's documents ────────────────────────
            Actions\Action::make('regenerate_this')
                ->label('Regenerate Documents')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Regenerate ID Card & Consent Form')
                ->modalDescription(fn () => 'Regenerate documents for ' . $this->record->full_name . '.')
                ->action(function () {
                    \App\Jobs\GenerateCamperDocumentsJob::dispatch($this->record->id);
                    Notification::make()
                        ->title('Documents queued for ' . $this->record->full_name)
                        ->success()->send();
                }),

            // ── Bulk regenerate with filters ──────────────────────────────
            Actions\Action::make('regenerate_bulk')
                ->label('Regenerate in Bulk')
                ->icon('heroicon-o-queue-list')
                ->color('gray')
                ->form([
                    Select::make('district_id')
                        ->label('District (optional)')
                        ->options(District::orderBy('name')->pluck('name', 'id'))
                        ->live()
                        ->afterStateUpdated(fn ($set) => $set('church_id', null))
                        ->placeholder('All districts')->nullable(),
                    Select::make('church_id')
                        ->label('Church (optional)')
                        ->options(fn ($get) => $get('district_id')
                            ? Church::where('district_id', $get('district_id'))->orderBy('name')->pluck('name', 'id')
                            : Church::orderBy('name')->pluck('name', 'id'))
                        ->searchable()->placeholder('All churches')->nullable(),
                    Select::make('category')
                        ->label('Department (optional)')
                        ->options(collect(CamperCategory::cases())
                            ->mapWithKeys(fn ($c) => [$c->value => $c->label()]))
                        ->placeholder('All departments')->nullable(),
                ])
                ->modalHeading('Regenerate ID Cards & Consent Forms')
                ->modalSubmitActionLabel('Queue Regeneration')
                ->action(function (array $data) {
                    $query = Camper::query();
                    if (! empty($data['district_id'])) {
                        $query->whereIn('church_id', Church::where('district_id', $data['district_id'])->pluck('id'));
                    }
                    if (! empty($data['church_id']))  $query->where('church_id', $data['church_id']);
                    if (! empty($data['category']))   $query->where('category', $data['category']);
                    $ids = $query->pluck('id');
                    if ($ids->isEmpty()) {
                        Notification::make()->title('No campers matched.')->warning()->send();
                        return;
                    }
                    foreach ($ids as $id) {
                        \App\Jobs\GenerateCamperDocumentsJob::dispatch($id);
                    }
                    Notification::make()
                        ->title("{$ids->count()} camper(s) queued for regeneration.")
                        ->success()->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }

    // ── Load health data into flat form fields ────────────────────────────────
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $health = $this->record->health;
        if ($health) {
            $data['health_medical_conditions'] = $health->medical_conditions;
            $data['health_medications']        = $health->medications;
            $data['health_allergies']          = $health->allergies;
            $data['health_doctor_name']        = $health->doctor_name;
            $data['health_doctor_phone']       = $health->doctor_phone;
        }
        return $data;
    }

    // ── Save health data back to camper_health, exclude from campers table ────
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (auth()->user()->hasRole('super_admin')) {
            $this->record->health()->updateOrCreate(
                ['camper_id' => $this->record->id],
                [
                    'medical_conditions' => $data['health_medical_conditions'] ?? null,
                    'medications'        => $data['health_medications'] ?? null,
                    'allergies'          => $data['health_allergies'] ?? null,
                    'doctor_name'        => $data['health_doctor_name'] ?? null,
                    'doctor_phone'       => $data['health_doctor_phone'] ?? null,
                ]
            );
        }

        // Remove health fields so they don't get written to the campers table
        unset(
            $data['health_medical_conditions'],
            $data['health_medications'],
            $data['health_allergies'],
            $data['health_doctor_name'],
            $data['health_doctor_phone']
        );

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
