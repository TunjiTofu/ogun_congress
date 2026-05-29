<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Mail\AdminWelcomeMail;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
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

            Forms\Components\Section::make('Role Assignment')
                ->description('Assign district or church based on the user\'s role.')
                ->schema([

                    // ── District Coordinator: only district_id saved ────────────────
                    Forms\Components\Select::make('district_id')
                        ->label('District')
                        ->options(\App\Models\District::orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->nullable()
                        ->live()
                        ->afterStateUpdated(fn (\Filament\Forms\Set $set) => $set('church_id', null))
                        ->helperText('Required for District Coordinator. Also used to filter churches below.')
                        ->afterStateHydrated(function ($state, $record, \Filament\Forms\Set $set) {
                            // Pre-populate district from church if not set directly
                            if (! $record?->district_id && $record?->church_id) {
                                $church = \App\Models\Church::find($record->church_id);
                                $set('district_id', $church?->district_id);
                            }
                        }),

                    // ── Church Coordinator: district_id + church_id both saved ──────
                    Forms\Components\Select::make('church_id')
                        ->label('Church')
                        ->options(fn (\Filament\Forms\Get $get) =>
                        $get('district_id')
                            ? \App\Models\Church::where('district_id', $get('district_id'))
                            ->orderBy('name')->pluck('name', 'id')
                            : \App\Models\Church::orderBy('name')->pluck('name', 'id')
                        )
                        ->searchable()
                        ->nullable()
                        ->helperText('Required for Church Coordinator. Select district first to filter.'),

                ])
                ->collapsible()
                ->columns(2),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),

                Tables\Columns\TextColumn::make('email')->searchable(),

                Tables\Columns\TextColumn::make('phone')->placeholder('—'),

                Tables\Columns\TextColumn::make('temp_password')
                    ->label('Temp Password')
                    ->placeholder('—')
                    ->copyable()
                    ->copyMessage('Password copied')
                    ->fontFamily('mono')
                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                    ->tooltip('Temporary password — blank once changed by user'),

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

                Tables\Actions\Action::make('reset_password')
                    ->label('Reset Password')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn () => auth()->user()->hasRole('super_admin'))
                    ->requiresConfirmation()
                    ->modalDescription('A new temporary password will be generated and emailed to the user. They must change it on next login.')
                    ->action(function (User $record) {
                        $newPwd = 'Tmp@' . strtoupper(Str::random(5)) . rand(10, 99) . '!';
                        $record->update([
                            'password'             => Hash::make($newPwd),
                            'temp_password'        => $newPwd,
                            'must_change_password' => true,
                        ]);
                        $role = $record->getRoleNames()->first() ?? 'admin';
                        try {
                            Mail::to($record->email)->send(
                                new AdminWelcomeMail($record, $newPwd, ucfirst(str_replace('_', ' ', $role)))
                            );
                            Notification::make()
                                ->title('Password reset. Credentials emailed to ' . $record->email)
                                ->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Password reset, but email failed: ' . $e->getMessage())
                                ->warning()->send();
                        }
                    }),

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
