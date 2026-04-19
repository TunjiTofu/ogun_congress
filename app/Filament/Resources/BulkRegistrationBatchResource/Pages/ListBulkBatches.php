<?php

namespace App\Filament\Resources\BulkRegistrationBatchResource\Pages;

use App\Filament\Resources\BulkRegistrationBatchResource;
use App\Models\BulkRegistrationBatch;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBulkBatches extends ListRecords
{
    protected static string $resource = BulkRegistrationBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('New Bulk Registration'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all'             => Tab::make('All'),
            'draft'           => Tab::make('Draft')->modifyQueryUsing(fn ($q) => $q->where('status', 'draft')),
            'pending_payment' => Tab::make('Pending Payment')
                ->modifyQueryUsing(fn ($q) => $q->where('status', 'pending_payment'))
                ->badge(fn () => \App\Models\BulkRegistrationBatch::where('status', 'pending_payment')->count())
                ->badgeColor('warning'),
            'confirmed'       => Tab::make('Confirmed')->modifyQueryUsing(fn ($q) => $q->where('status', 'confirmed')),
        ];
    }
}
