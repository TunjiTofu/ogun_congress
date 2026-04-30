<?php

namespace App\Filament\Resources;

use App\Enums\CamperCategory;
use App\Filament\Resources\BadgeColorResource\Pages;
use App\Models\BadgeColorConfig;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BadgeColorResource extends Resource
{
    protected static ?string $model           = BadgeColorConfig::class;
    protected static ?string $navigationIcon  = 'heroicon-o-swatch';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?string $navigationLabel = 'Badge Colors';
    protected static ?int    $navigationSort  = 21;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('category')
                ->options(collect(CamperCategory::cases())
                    ->mapWithKeys(fn ($e) => [$e->value => $e->label()])
                    ->toArray())
                ->required()
                ->disabled(fn ($record) => $record !== null), // can't change category on edit

            Forms\Components\ColorPicker::make('color_hex')
                ->label('Badge Colour')
                ->required(),

            Forms\Components\TextInput::make('label')
                ->label('Display Label')
                ->placeholder('e.g. Blue — Adventurers')
                ->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->formatStateUsing(fn ($state) => $state instanceof CamperCategory ? $state->label() : $state)
                    ->sortable(),

                Tables\Columns\ColorColumn::make('color_hex')->label('Colour'),

                Tables\Columns\TextColumn::make('label'),
            ])
            ->actions([Tables\Actions\EditAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBadgeColors::route('/'),
            'create' => Pages\CreateBadgeColor::route('/create'),
            'edit'   => Pages\EditBadgeColor::route('/{record}/edit'),
        ];
    }
}
