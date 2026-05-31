<?php

namespace App\Filament\Resources\CampMediaResource\Pages;

use App\Filament\Resources\CampMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCampMedia extends ListRecords
{
    protected static string $resource = CampMediaResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()->label('Add Media')];
    }
}
