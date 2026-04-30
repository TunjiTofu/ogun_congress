<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampSettingResource\Pages;
use App\Models\CampSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CampSettingResource extends Resource
{
    protected static ?string $model           = CampSetting::class;
    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = 'Reports & Settings';
    protected static ?string $navigationLabel = 'Camp Settings';
    protected static ?int    $navigationSort  = 20;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('super_admin');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('label')
                ->label('Setting Name')
                ->disabled(),

            Forms\Components\Textarea::make('value')
                ->label('Value')
                ->rows(3)
                ->helperText('Leave blank to clear this setting.'),

            Forms\Components\TextInput::make('group')
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Setting')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('value')
                    ->label('Current Value')
                    ->placeholder('(not set)')
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\BadgeColumn::make('group')
                    ->label('Group')
                    ->colors([
                        'primary' => 'general',
                        'success' => 'payment',
                        'warning' => 'fees',
                        'info'    => 'contact',
                        'gray'    => 'notifications',
                    ]),
            ])
            ->defaultSort('group')
            ->filters([
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'general'       => 'General',
                        'payment'       => 'Payment',
                        'fees'          => 'Fees',
                        'registration'  => 'Registration',
                        'contact'       => 'Contact',
                        'notifications' => 'Notifications',
                        'announcements' => 'Announcements',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Update'),
                Tables\Actions\Action::make('add_announcement')
                    ->label('New Announcement')
                    ->icon('heroicon-o-megaphone')
                    ->color('success')
                    ->visible(fn () => false) // shown from header only
                    ->url(fn () => static::getUrl('create')),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add Announcement')
                    ->icon('heroicon-o-megaphone')
                    ->mutateFormDataUsing(fn (array $data) => array_merge($data, ['group' => 'announcements'])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCampSettings::route('/'),
            'create' => Pages\CreateCampSetting::route('/create'),
            'edit'   => Pages\EditCampSetting::route('/{record}/edit'),
        ];
    }
}
