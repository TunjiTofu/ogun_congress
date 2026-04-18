<?php

namespace App\Filament\Resources\CampSettingResource\Pages;

use App\Filament\Resources\CampSettingResource;
use Filament\Resources\Pages\ListRecords;

class ListCampSettings extends ListRecords
{
    protected static string $resource = CampSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
