<?php

namespace App\Filament\Resources\BadgeColorResource\Pages;

use App\Filament\Resources\BadgeColorResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBadgeColor extends EditRecord
{
    protected static string $resource = BadgeColorResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}
