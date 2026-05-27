<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampRoleResource\Pages;
use App\Models\CampRole;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampRoleResource extends Resource
{
    protected static ?string $model           = CampRole::class;
    protected static ?string $navigationIcon  = 'heroicon-o-shield-check';
    protected static ?string $navigationLabel = 'Camp Roles';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?int    $navigationSort  = 10;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('name')
                    ->required()->maxLength(50)
                    ->placeholder('e.g. Security, Welfare, Platform, Secretariat')
                    ->helperText('This label will appear boldly on the official\'s ID card.'),

                Forms\Components\ColorPicker::make('color')
                    ->label('ID Card Colour')
                    ->default('#722F37')
                    ->helperText('Defaults to wine (#722F37). Each role can have its own colour.'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active (available when marking officials)')
                    ->default(true),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()->default(0)->label('Sort Order'),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->weight('bold')->sortable()->searchable(),

                Tables\Columns\ColorColumn::make('color')->label('ID Card Colour'),

                Tables\Columns\TextColumn::make('campers_count')
                    ->label('Assigned Officials')
                    ->counts('campers')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),

                Tables\Columns\TextColumn::make('sort_order')->label('Order')->sortable(),
            ])
            ->reorderable('sort_order')
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (CampRole $record) {
                        // Unset this role from all campers before deleting
                        $record->campers()->update(['is_official' => false, 'camp_role_id' => null]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCampRoles::route('/'),
            'create' => Pages\CreateCampRole::route('/create'),
            'edit'   => Pages\EditCampRole::route('/{record}/edit'),
        ];
    }
}
