<?php

namespace App\Filament\Resources\CamperResource\Pages;

use App\Enums\CamperCategory;
use App\Filament\Resources\CamperResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListCampers extends ListRecords
{
    protected static string $resource = CamperResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Campers are created via registration only
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'adventurers' => Tab::make('Adventurers')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('category', CamperCategory::ADVENTURER))
                ->badge(fn () => \App\Models\Camper::where('category', CamperCategory::ADVENTURER)->count()),

            'pathfinders' => Tab::make('Pathfinders')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('category', CamperCategory::PATHFINDER))
                ->badge(fn () => \App\Models\Camper::where('category', CamperCategory::PATHFINDER)->count()),

            'senior_youth' => Tab::make('Senior Youth')
                ->modifyQueryUsing(fn (Builder $q) => $q->where('category', CamperCategory::SENIOR_YOUTH))
                ->badge(fn () => \App\Models\Camper::where('category', CamperCategory::SENIOR_YOUTH)->count()),
        ];
    }
}
