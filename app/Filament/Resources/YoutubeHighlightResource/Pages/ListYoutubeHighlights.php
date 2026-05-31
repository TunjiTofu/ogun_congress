<?php

namespace App\Filament\Resources\YoutubeHighlightResource\Pages;

use App\Filament\Resources\YoutubeHighlightResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListYoutubeHighlights extends ListRecords
{
    protected static string $resource = YoutubeHighlightResource::class;
    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
