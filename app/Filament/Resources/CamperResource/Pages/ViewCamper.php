<?php

namespace App\Filament\Resources\CamperResource\Pages;

use App\Filament\Resources\CamperResource;
use App\Models\Camper;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;

class ViewCamper extends ViewRecord
{
    protected static string $resource = CamperResource::class;

    protected function resolveRecord(int|string $key): Camper
    {
        return Camper::with([
            'church.district', 'contacts', 'health',
            'media', 'registrationCode', 'campRole',
        ])->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'secretariat'])),

            // ── Assign / change camp role ─────────────────────────────────
            Actions\Action::make('assign_role')
                ->label(fn () => $this->record->is_official ? 'Change Camp Role' : 'Assign Camp Role')
                ->icon('heroicon-o-shield-check')
                ->color('warning')
                ->visible(fn () => auth()->user()->hasRole('super_admin'))
                ->form([
                    \Filament\Forms\Components\Select::make('camp_role_id')
                        ->label('Official Role')
                        ->options(
                            \App\Models\CampRole::where('is_active', true)
                                ->orderBy('sort_order')
                                ->pluck('name', 'id')
                        )
                        ->default(fn () => $this->record->camp_role_id)
                        ->required()
                        ->searchable(),
                ])
                ->fillForm(fn () => ['camp_role_id' => $this->record->camp_role_id])
                ->action(function (array $data) {
                    $this->record->update([
                        'is_official'  => true,
                        'camp_role_id' => $data['camp_role_id'],
                    ]);
                    \App\Jobs\GenerateCamperDocumentsJob::dispatch($this->record->id);
                    Notification::make()
                        ->title('Camp role assigned. ID card will regenerate.')
                        ->success()->send();
                }),

            // ── Remove official status ────────────────────────────────────
            Actions\Action::make('remove_role')
                ->label('Remove Camp Role')
                ->icon('heroicon-o-shield-exclamation')
                ->color('danger')
                ->visible(fn () => auth()->user()->hasRole('super_admin') && $this->record->is_official)
                ->requiresConfirmation()
                ->modalDescription('This will remove the official role and restore the regular department ID card.')
                ->action(function () {
                    $this->record->update([
                        'is_official'  => false,
                        'camp_role_id' => null,
                    ]);
                    \App\Jobs\GenerateCamperDocumentsJob::dispatch($this->record->id);
                    Notification::make()
                        ->title('Camp role removed. ID card will regenerate.')
                        ->success()->send();
                }),

            Actions\Action::make('regenerate')
                ->label('Regenerate Docs')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->visible(fn () => auth()->user()->hasRole('super_admin'))
                ->requiresConfirmation()
                ->action(function () {
                    \App\Jobs\GenerateCamperDocumentsJob::dispatch($this->record->id);
                    Notification::make()->title('Queued for regeneration.')->success()->send();
                }),
        ];
    }
}
