<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Pages\ViewRecord;

class ViewContactMessage extends ViewRecord
{
    protected static string $resource = ContactMessageResource::class;

    protected function mutateRecordDataBeforeFill(array $data): array
    {
        // Auto-mark as read when opened
        if (! $this->record->is_read) {
            $this->record->markAsRead();
        }
        return $data;
    }
}
