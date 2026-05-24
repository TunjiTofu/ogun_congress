<?php

namespace App\Filament\Resources\CampRoleResource\Pages;

use App\Filament\Resources\CampRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampRoles extends ListRecords
{
    protected static string $resource = CampRoleResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
