<?php

namespace App\Filament\Resources\SkillRegistrationResource\Pages;

use App\Filament\Resources\SkillRegistrationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSkillRegistration extends EditRecord
{
    protected static string $resource = SkillRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
