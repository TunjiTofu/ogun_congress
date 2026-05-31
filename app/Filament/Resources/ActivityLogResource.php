<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages\ListActivityLogs;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityLogResource extends Resource
{
    protected static ?string $model           = Activity::class;
    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Activity Log';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?int    $navigationSort  = 99;
    protected static bool    $canCreate       = false;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('causer.name')
                    ->label('User')
                    ->default('System')
                    ->searchable()
                    ->description(fn ($record) => $record->causer?->email),

                Tables\Columns\BadgeColumn::make('log_name')
                    ->label('Category')
                    ->colors([
                        'primary' => 'default',
                        'warning' => fn ($state) => in_array($state, ['offline_payments', 'registration_codes']),
                        'success' => 'bulk_batches',
                        'danger'  => fn ($state) => str_contains($state ?? '', 'delete'),
                    ]),

                Tables\Columns\TextColumn::make('description')
                    ->label('Action')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Model')
                    ->formatStateUsing(fn ($state) => $state ? class_basename($state) : '—'),

                Tables\Columns\TextColumn::make('subject_id')
                    ->label('Record ID')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('properties')
                    ->label('Details')
                    ->formatStateUsing(function ($state) {
                        // $state may be a JSON string, array, or Illuminate Collection
                        if (empty($state)) {
                            return '—';
                        }

                        // Normalise to array
                        if (is_string($state)) {
                            $decoded = json_decode($state, true);
                            $data    = is_array($decoded) ? $decoded : [];
                        } elseif ($state instanceof \Illuminate\Support\Collection) {
                            $data = $state->toArray();
                        } elseif (is_array($state)) {
                            $data = $state;
                        } else {
                            return '—';
                        }

                        // Prefer changes > attributes > whole payload
                        $changes = $data['changes'] ?? $data['attributes'] ?? $data;

                        if (! is_array($changes) || empty($changes)) {
                            return '—';
                        }

                        return collect($changes)
                            ->map(fn ($v, $k) => "{$k}: " . (is_array($v) ? json_encode($v) : $v))
                            ->take(4)
                            ->implode(' · ');
                    })
                    ->wrap()
                    ->limit(120),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('log_name')
                    ->label('Category')
                    ->options([
                        'default'            => 'General',
                        'offline_payments'   => 'Offline Payments',
                        'registration_codes' => 'Registration Codes',
                        'bulk_batches'       => 'Bulk Batches',
                        'camp_media'         => 'Camp Media',
                        'youtube_highlights' => 'YouTube Highlights',
                        'user_management'    => 'User Management',
                    ]),

                Tables\Filters\SelectFilter::make('causer_id')
                    ->label('User')
                    ->options(
                        \App\Models\User::orderBy('name')
                            ->pluck('name', 'id')
                            ->toArray()
                    ),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s')
            ->actions([]) // read-only
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivityLogs::route('/'),
        ];
    }
}
