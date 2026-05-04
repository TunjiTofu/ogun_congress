<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProgrammeSessionResource\Pages;
use App\Models\ProgrammeSession;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgrammeSessionResource extends Resource
{
    protected static ?string $model = ProgrammeSession::class;
    protected static ?string $navigationIcon  = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Programme Sessions';
    protected static ?string $navigationGroup = 'Camp Operations';
    protected static ?int    $navigationSort  = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['super_admin', 'secretariat']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Session Details')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(200)
                        ->placeholder('e.g. Morning Devotion, Bible Study, Evening Programme'),

                    Forms\Components\TextInput::make('venue')
                        ->default('Main Hall')
                        ->maxLength(100),

                    Forms\Components\DatePicker::make('date')
                        ->required()
                        ->displayFormat('d M Y')
                        ->native(false),

                    Forms\Components\TimePicker::make('start_time')
                        ->required()
                        ->seconds(false),

                    Forms\Components\TimePicker::make('end_time')
                        ->nullable()
                        ->seconds(false)
                        ->after('start_time'),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Active (visible on check-in PWA)')
                        ->default(true),

                    Forms\Components\Textarea::make('description')
                        ->nullable()
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->date('D, d M Y')
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->wrap(),

                Tables\Columns\TextColumn::make('start_time')
                    ->time('H:i')
                    ->label('Start'),

                Tables\Columns\TextColumn::make('end_time')
                    ->time('H:i')
                    ->label('End')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('venue')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('attendees_count')
                    ->label('Attendees')
                    ->counts('attendees')
                    ->badge()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        Forms\Components\DatePicker::make('date')->native(false),
                    ])
                    ->query(fn ($query, array $data) => $data['date']
                        ? $query->whereDate('date', $data['date'])
                        : $query),

                Tables\Filters\TernaryFilter::make('is_active')->label('Active only'),
            ])
            ->actions([
                Tables\Actions\Action::make('attendance')
                    ->label('View Attendance')
                    ->icon('heroicon-o-user-group')
                    ->color('info')
                    ->url(fn (ProgrammeSession $r) => static::getUrl('attendance', ['record' => $r])),

                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
            ])
            ->headerActions([
                Tables\Actions\Action::make('export_all')
                    ->label('Export Attendance')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(route('attendance.export.all'))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'      => Pages\ListProgrammeSessions::route('/'),
            'create'     => Pages\CreateProgrammeSession::route('/create'),
            'edit'       => Pages\EditProgrammeSession::route('/{record}/edit'),
            'attendance' => Pages\SessionAttendance::route('/{record}/attendance'),
        ];
    }
}
