<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserResource extends Resource
{
    protected static ?string $model           = User::class;
    protected static ?string $navigationIcon  = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?string $navigationLabel = 'Admin Users';
    protected static ?int    $navigationSort  = 22;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(191),

            Forms\Components\TextInput::make('email')
                ->email()
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(191),

            Forms\Components\TextInput::make('phone')
                ->tel()
                ->maxLength(20),

            Forms\Components\TextInput::make('password')
                ->password()
                ->revealable()
                ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                ->dehydrated(fn ($state) => filled($state))
                ->required(fn ($record) => $record === null)
                ->helperText(fn ($record) => $record ? 'Leave blank to keep current password.' : null),

            Forms\Components\Select::make('roles')
                ->multiple()
                ->relationship('roles', 'name')
                ->preload()
                ->options(
                    Role::orderBy('name')
                        ->pluck('name', 'id')
                        ->mapWithKeys(fn ($name, $id) => [$id => match($name) {
                            'super_admin'       => 'Super Admin',
                            'accountant'        => 'Accountant',
                            'secretariat'       => 'Secretariat',
                            'security'          => 'Security',
                            'church_coordinator'=> 'Church Coordinator',
                            default             => ucwords(str_replace('_', ' ', $name)),
                        }])
                        ->toArray()
                )
                ->required(),

            Forms\Components\Toggle::make('is_active')
                ->label('Account Active')
                ->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('email')->searchable(),

                Tables\Columns\TextColumn::make('phone')->placeholder('—'),

                Tables\Columns\TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'super_admin'        => 'Super Admin',
                        'accountant'         => 'Accountant',
                        'secretariat'        => 'Secretariat',
                        'security'           => 'Security',
                        'church_coordinator' => 'Church Coordinator',
                        default              => ucwords(str_replace('_', ' ', $state)),
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('Never')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (User $r) => $r->is_active && ! $r->hasRole('super_admin'))
                    ->action(fn (User $record) => $record->update(['is_active' => false])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
