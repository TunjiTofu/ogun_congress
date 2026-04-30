<?php

namespace App\Filament\Resources\CamperResource\RelationManagers;

use App\Enums\ContactType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'contacts';
    protected static ?string $title       = 'Contacts';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Select::make('type')
                ->options(collect(ContactType::cases())
                    ->mapWithKeys(fn ($e) => [$e->value => $e->label()])
                    ->toArray())
                ->required(),

            Forms\Components\TextInput::make('full_name')
                ->required()
                ->maxLength(191),

            Forms\Components\TextInput::make('relationship')
                ->required()
                ->maxLength(50)
                ->placeholder('e.g. Mother, Pastor'),

            Forms\Components\TextInput::make('phone')
                ->required()
                ->tel()
                ->maxLength(20),

            Forms\Components\TextInput::make('email')
                ->email()
                ->maxLength(191),

            Forms\Components\Toggle::make('is_primary')
                ->label('Primary Contact'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->formatStateUsing(fn ($state) => $state instanceof ContactType
                        ? $state->label()
                        : $state)
                    ->colors([
                        'info'    => ContactType::PARENT_GUARDIAN->value,
                        'warning' => ContactType::EMERGENCY_CONTACT->value,
                    ]),

                Tables\Columns\TextColumn::make('full_name')->label('Name'),
                Tables\Columns\TextColumn::make('relationship'),
                Tables\Columns\TextColumn::make('phone')->copyable(),
                Tables\Columns\TextColumn::make('email')->placeholder('—'),
                Tables\Columns\IconColumn::make('is_primary')->boolean(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
