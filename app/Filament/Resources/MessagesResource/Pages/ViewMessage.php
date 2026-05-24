<?php

namespace App\Filament\Resources\MessagesResource\Pages;

use App\Filament\Resources\MessagesResource;
use Filament\Resources\Pages\ViewRecord;

class ViewMessage extends ViewRecord
{
    protected static string $resource = MessagesResource::class;

    protected function getHeaderActions(): array { return []; }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Auto-mark as read when opened
        if (! $this->record->is_read) {
            $this->record->update(['is_read' => true, 'read_at' => now()]);
        }
        return $data;
    }
}
