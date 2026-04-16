<?php

namespace App\Filament\Resources;

use App\Enums\CamperCategory;
use App\Enums\Gender;
use App\Filament\Resources\CamperResource\Pages;
use App\Models\Camper;
use App\Models\Church;
use App\Models\District;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CamperResource extends Resource
{
    protected static ?string $model           = Camper::class;
    protected static ?string $navigationIcon  = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Campers';
    protected static ?string $navigationLabel = 'All Campers';
    protected static ?int    $navigationSort  = 1;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['secretariat', 'super_admin']);
    }

    // ── Form ──────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Identity')
                ->schema([
                    Forms\Components\TextInput::make('full_name')
                        ->disabled()
                        ->helperText('Copied from payment record. Cannot be changed here.'),

                    Forms\Components\TextInput::make('phone')
                        ->disabled()
                        ->helperText('Copied from payment record.'),

                    Forms\Components\TextInput::make('camper_number')
                        ->disabled(),

                    Forms\Components\Select::make('category')
                        ->options(collect(CamperCategory::cases())
                            ->mapWithKeys(fn ($e) => [$e->value => $e->label()])
                            ->toArray())
                        ->disabled(),
                ])->columns(2),

            Forms\Components\Section::make('Personal Details')
                ->schema([
                    Forms\Components\DatePicker::make('date_of_birth')
                        ->required(),

                    Forms\Components\Select::make('gender')
                        ->options(collect(Gender::cases())
                            ->mapWithKeys(fn ($e) => [$e->value => $e->label()])
                            ->toArray())
                        ->required(),

                    Forms\Components\Textarea::make('home_address')
                        ->rows(2)
                        ->columnSpanFull(),
                ])->columns(2),

            Forms\Components\Section::make('Church & Ministry')
                ->schema([
                    // District cascades to church
                    Forms\Components\Select::make('district_id')
                        ->label('District')
                        ->options(District::orderBy('name')->pluck('name', 'id'))
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('church_id', null))
                        ->required()
                        ->dehydrated(false), // not a real column — just for cascade

                    Forms\Components\Select::make('church_id')
                        ->label('Church')
                        ->options(fn (Get $get) => Church::where('district_id', $get('district_id'))
                            ->orderBy('name')
                            ->pluck('name', 'id'))
                        ->required()
                        ->searchable(),

                    Forms\Components\TextInput::make('ministry')
                        ->maxLength(100),

                    Forms\Components\TextInput::make('club_rank')
                        ->label('Club Rank')
                        ->maxLength(100),
                ])->columns(2),

            Forms\Components\Section::make('Check-In Status')
                ->schema([
                    Forms\Components\Toggle::make('consent_collected')
                        ->label('Consent Form Collected')
                        ->helperText('Mark this when the signed physical form has been received at check-in.')
                        ->onColor('success'),
                ])->visibleOn('edit'),
        ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('photo')
                    ->circular()
                    ->defaultImageUrl(asset('images/placeholder-avatar.png'))
                    ->getStateUsing(fn (Camper $r) => $r->getFirstMediaUrl('photo', 'thumb')),

                Tables\Columns\TextColumn::make('full_name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('camper_number')
                    ->searchable()
                    ->copyable()
                    ->label('Code'),

                Tables\Columns\BadgeColumn::make('category')
                    ->colors([
                        'info'    => CamperCategory::ADVENTURER->value,
                        'success' => CamperCategory::PATHFINDER->value,
                        'warning' => CamperCategory::SENIOR_YOUTH->value,
                    ])
                    ->formatStateUsing(fn ($state) => $state instanceof CamperCategory
                        ? $state->label()
                        : $state),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')
                    ->searchable(),

                Tables\Columns\TextColumn::make('church.district.name')
                    ->label('District')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('registrationCode.payment_type')
                    ->label('Payment')
                    ->colors([
                        'success' => 'online',
                        'info'    => 'offline',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state?->value ?? $state) {
                        'online'  => 'Online',
                        'offline' => 'Bank Transfer',
                        default   => '—',
                    }),

                Tables\Columns\IconColumn::make('consent_collected')
                    ->label('Consent')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(collect(CamperCategory::cases())
                        ->mapWithKeys(fn ($e) => [$e->value => $e->label()])
                        ->toArray()),

                Tables\Filters\SelectFilter::make('district')
                    ->relationship('church.district', 'name')
                    ->label('District'),

                Tables\Filters\SelectFilter::make('church')
                    ->relationship('church', 'name')
                    ->searchable(),

                Tables\Filters\TernaryFilter::make('consent_collected')
                    ->label('Consent Form')
                    ->trueLabel('Collected')
                    ->falseLabel('Outstanding')
                    ->nullable(),

                Tables\Filters\Filter::make('has_health_alert')
                    ->label('Health Alert')
                    ->query(fn ($query) => $query->whereHas('health', fn ($q) => $q->where('has_alert', true)))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('mark_consent')
                    ->label('Mark Consent Collected')
                    ->icon('heroicon-o-clipboard-document-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Camper $r) => ! $r->consent_collected && $r->requiresConsentForm())
                    ->action(fn (Camper $record) => $record->update(['consent_collected' => true])),

                Tables\Actions\Action::make('regenerate_docs')
                    ->label('Regenerate Documents')
                    ->icon('heroicon-o-arrow-path')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(function (Camper $record) {
                        \App\Jobs\GenerateCamperDocumentsJob::dispatch($record->id);
                        \Filament\Notifications\Notification::make()
                            ->title('Document generation queued.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelationManagers(): array
    {
        return [
            \App\Filament\Resources\CamperResource\RelationManagers\ContactsRelationManager::class,
            \App\Filament\Resources\CamperResource\RelationManagers\CheckinEventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCampers::route('/'),
            'create' => Pages\CreateCamper::route('/create'),
            'edit'   => Pages\EditCamper::route('/{record}/edit'),
            'view'   => Pages\ViewCamper::route('/{record}'),
        ];
    }
}
