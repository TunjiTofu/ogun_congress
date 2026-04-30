<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DistrictResource\Pages;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DistrictResource extends Resource
{
    protected static ?string $model           = District::class;
    protected static ?string $navigationIcon  = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?string $navigationLabel = 'Districts';
    protected static ?int    $navigationSort  = 10;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(191)
                ->unique(ignoreRecord: true),

            Forms\Components\TextInput::make('zone')
                ->maxLength(100)
                ->placeholder('e.g. Abeokuta Zone'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('zone')->placeholder('—'),
                Tables\Columns\TextColumn::make('churches_count')
                    ->label('Churches')
                    ->counts('churches')
                    ->sortable(),
                Tables\Columns\TextColumn::make('campers_count')
                    ->label('Campers')
                    ->getStateUsing(fn ($record) => $record->churches()
                        ->withCount('campers')
                        ->get()
                        ->sum('campers_count')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(function (District $record, Tables\Actions\DeleteAction $action) {
                        if ($record->churches()->exists()) {
                            \Filament\Notifications\Notification::make()
                                ->title('Cannot delete — district has churches.')
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
            'index'  => Pages\ListDistricts::route('/'),
            'create' => Pages\CreateDistrict::route('/create'),
            'edit'   => Pages\EditDistrict::route('/{record}/edit'),
        ];
    }
}
