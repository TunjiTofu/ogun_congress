<?php

namespace App\Filament\Resources\CamperResource\Pages;

use App\Filament\Resources\CamperResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCamper extends ViewRecord
{
    protected static string $resource = CamperResource::class;

    /**
     * Eagerly load all relations needed by the infolist.
     * Filament resolves the record via getEloquentQuery()->findOrFail($key),
     * but we override here to be explicit.
     */
    protected function resolveRecord(int|string $key): \App\Models\Camper
    {
        return \App\Models\Camper::with([
            'church.district',
            'contacts',
            'health',
            'media',
            'registrationCode',
        ])->findOrFail($key);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'secretariat'])),
        ];
    }

    // No infolist() override — uses CamperResource::infolist() which has all sections
}
