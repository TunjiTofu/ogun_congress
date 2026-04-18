<?php

namespace App\Filament\Resources\BadgeColorResource\Pages;

use App\Filament\Resources\BadgeColorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBadgeColors extends ListRecords
{
    protected static string $resource = BadgeColorResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Add Category Colour')];
    }
}
