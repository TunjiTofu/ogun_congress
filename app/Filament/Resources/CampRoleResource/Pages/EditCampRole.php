<?php

namespace App\Filament\Resources\CampRoleResource\Pages;

use App\Filament\Resources\CampRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampRole extends EditRecord
{
    protected static string $resource = CampRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
