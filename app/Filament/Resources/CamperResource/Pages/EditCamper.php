<?php

namespace App\Filament\Resources\CamperResource\Pages;

use App\Filament\Resources\CamperResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCamper extends EditRecord
{
    protected static string $resource = CamperResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
