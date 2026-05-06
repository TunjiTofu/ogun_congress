<?php

namespace App\Filament\Resources\ProgrammeSessionResource\Pages;

use App\Filament\Resources\ProgrammeSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateProgrammeSession extends CreateRecord
{
    protected static string $resource = ProgrammeSessionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
}

