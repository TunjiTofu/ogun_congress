<?php

namespace App\Filament\Resources\CamperResource\RelationManagers;

use App\Enums\ContactType;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
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
                ->label('Contact Type')
                ->options([
                    'parent_guardian'   => 'Parent / Guardian',
                    'emergency_contact' => 'Emergency Contact',
                ])
                ->required()
                ->native(false),

            Forms\Components\TextInput::make('full_name')
                ->label('Full Name')
                ->required()
                ->maxLength(255),

            Forms\Components\TextInput::make('relationship')
                ->label('Relationship')
                ->placeholder('e.g. Mother, Father, Uncle')
                ->maxLength(100),

            Forms\Components\TextInput::make('phone')
                ->label('Phone Number')
                ->tel()
                ->maxLength(20),

            Forms\Components\TextInput::make('email')
                ->label('Email')
                ->email()
                ->maxLength(255),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\BadgeColumn::make('type')
                    ->formatStateUsing(fn ($state) => match (is_string($state) ? $state : $state?->value) {
                        'parent_guardian'   => '👨‍👩‍👧 Parent / Guardian',
                        'emergency_contact' => '🆘 Emergency Contact',
                        default             => $state,
                    })
                    ->colors([
                        'primary' => 'parent_guardian',
                        'danger'  => 'emergency_contact',
                    ]),

                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')->weight('bold')->searchable(),

                Tables\Columns\TextColumn::make('relationship')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('phone')
                    ->placeholder('—')->copyable(),

                Tables\Columns\TextColumn::make('email')
                    ->placeholder('—')->toggleable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Contact')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'secretariat'])),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'secretariat'])),

                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
            ]);
    }
}
