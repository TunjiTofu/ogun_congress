<?php

namespace App\Filament\Resources\CampSettingResource\Pages;

use App\Filament\Resources\CampSettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCampSetting extends EditRecord
{
    protected static string $resource = CampSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
