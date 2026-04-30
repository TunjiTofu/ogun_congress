<?php

namespace App\Filament\Resources\CamperResource\RelationManagers;

use App\Enums\CheckinEventType;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Form;

class CheckinEventsRelationManager extends RelationManager
{
    protected static string $relationship = 'checkinEvents';
    protected static ?string $title       = 'Check-In History';

    public function form(Form $form): Form
    {
        return $form->schema([]); // Read-only — no create/edit
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('event_type')
                    ->label('Event')
                    ->formatStateUsing(fn ($state) => $state instanceof CheckinEventType
                        ? $state->label()
                        : $state)
                    ->colors([
                        'success' => CheckinEventType::CHECK_IN->value,
                        'warning' => CheckinEventType::CHECK_OUT->value,
                        'info'    => CheckinEventType::PROGRAMME_ATTENDANCE->value,
                    ]),

                Tables\Columns\TextColumn::make('session.name')
                    ->label('Session')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('scanned_at')
                    ->label('Time')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('scannedBy.name')
                    ->label('Scanned By')
                    ->placeholder('PWA Device'),

                Tables\Columns\TextColumn::make('device_id')
                    ->label('Device')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('consent_collected')
                    ->label('Consent')
                    ->boolean()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('notes')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('scanned_at', 'desc')
            ->headerActions([]) // Immutable log — no create action
            ->actions([])       // No edit or delete
            ->bulkActions([]);
    }
}
