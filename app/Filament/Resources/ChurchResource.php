<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChurchResource\Pages;
use App\Models\Church;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChurchResource extends Resource
{
    protected static ?string $model           = Church::class;
    protected static ?string $navigationIcon  = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?string $navigationLabel = 'Churches';
    protected static ?int    $navigationSort  = 11;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('district_id')
                ->label('District')
                ->options(District::orderBy('name')->pluck('name', 'id'))
                ->required()
                ->searchable(),

            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(191),

            Forms\Components\Textarea::make('address')
                ->rows(2)
                ->maxLength(500),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district.zone')
                    ->label('Zone')
                    ->placeholder('—')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('campers_count')
                    ->label('Campers')
                    ->counts('campers'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('district')
                    ->relationship('district', 'name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (Church $record, Tables\Actions\DeleteAction $action) {
                        if ($record->campers()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete — church has registered campers.')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListChurches::route('/'),
            'create' => Pages\CreateChurch::route('/create'),
            'edit'   => Pages\EditChurch::route('/{record}/edit'),
        ];
    }
}
