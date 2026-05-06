<?php

namespace App\Filament\Resources\ProgrammeSessionResource\Pages;

use App\Filament\Resources\ProgrammeSessionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProgrammeSessions extends ListRecords
{
    protected static string $resource = ProgrammeSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(fn () => auth()->user()->hasRole('super_admin')),
        ];
    }
}

