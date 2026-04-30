<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class RecentActivityWidget extends BaseWidget
{
    protected static ?string $heading       = 'Recent Activity';
    protected static ?int    $sort          = 4;
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->placeholder('System')
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Action'),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => class_basename($state ?? '')),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
            ])
            ->paginated(false);
    }
}
