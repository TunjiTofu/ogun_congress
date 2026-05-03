<?php

namespace App\Filament\Resources\BulkRegistrationBatchResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';
    protected static ?string $title       = 'Camper Entries';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('full_name')
            ->defaultPaginationPageOption(10)
            ->paginated([10, 25, 50])
            ->columns([
                Tables\Columns\TextColumn::make('index')
                    ->label('#')
                    ->rowIndex()
                    ->width(40),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->weight('bold')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state?->label() ?? $state)
                    ->sortable(),

                Tables\Columns\TextColumn::make('fee')
                    ->label('Fee')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('registrationCode.code')
                    ->label('Code Issued')
                    ->badge()
                    ->color('success')
                    ->placeholder('Not issued')
                    ->copyable()
                    ->searchable(),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
