<?php

namespace App\Filament\Resources;

use App\Enums\CodeStatus;
use App\Enums\PaymentType;
use App\Filament\Resources\RegistrationCodeResource\Pages;
use App\Models\RegistrationCode;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RegistrationCodeResource extends Resource
{
    protected static ?string $model           = RegistrationCode::class;
    protected static ?string $navigationIcon  = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?string $navigationLabel = 'Registration Codes';
    protected static ?int    $navigationSort  = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['accountant', 'super_admin', 'church_coordinator', 'admin']);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    // ── Eagerly load relationships once per page, not per row ─────────────────
    // Without this, Filament triggers N+1 queries for church/district on every
    // rendered row, each creating new Eloquent model instances in memory.
    // A single eager-loaded JOIN is far cheaper than 25–100 separate queries.
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with(['church', 'church.district'])
            ->select([
                'registration_codes.id',
                'registration_codes.code',
                'registration_codes.status',
                'registration_codes.payment_type',
                'registration_codes.prefill_name',
                'registration_codes.prefill_phone',
                'registration_codes.prefill_category',
                'registration_codes.prefill_church_id',
                'registration_codes.amount_paid',
                'registration_codes.activated_at',
                'registration_codes.claimed_at',
                'registration_codes.expires_at',
                'registration_codes.created_at',
                'registration_codes.updated_at',
            ]);

        // Coordinators only see their own church's codes
        if (auth()->user()?->hasRole('church_coordinator')) {
            $query->where('prefill_church_id', auth()->user()->church_id);
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => CodeStatus::PENDING->value,
                        'success' => CodeStatus::ACTIVE->value,
                        'info'    => CodeStatus::CLAIMED->value,
                        'gray'    => CodeStatus::EXPIRED->value,
                        'danger'  => CodeStatus::VOID->value,
                    ])
                    ->formatStateUsing(fn ($state) => $state instanceof CodeStatus
                        ? $state->label()
                        : $state),

                Tables\Columns\BadgeColumn::make('payment_type')
                    ->label('Type')
                    ->colors([
                        'success' => PaymentType::ONLINE->value,
                        'info'    => PaymentType::OFFLINE->value,
                    ])
                    ->formatStateUsing(fn ($state) => $state instanceof PaymentType
                        ? $state->label()
                        : $state),

                Tables\Columns\TextColumn::make('prefill_name')
                    ->label('Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('prefill_phone')
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('prefill_category')
                    ->label('Department')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'adventurer'   => 'Adventurer',
                        'pathfinder'   => 'Pathfinder',
                        'senior_youth' => 'Senior Youth',
                        default        => $state ?? '—',
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'adventurer'   => 'info',
                        'pathfinder'   => 'success',
                        'senior_youth' => 'warning',
                        default        => 'gray',
                    })
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin'])),

                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')
                    ->searchable()
                    ->placeholder('—')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin'])),

                Tables\Columns\TextColumn::make('church.district.name')
                    ->label('District')
                    ->placeholder('—')
                    ->visible(fn () => auth()->user()->hasAnyRole(['super_admin', 'admin'])),

                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Amount')
                    ->money('NGN')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('activated_at')
                    ->label('Activated')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('claimed_at')
                    ->label('Claimed')
                    ->dateTime('d M Y, H:i')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            // Smaller default page size reduces peak memory per request
            ->defaultPaginationPageOption(25)
            ->paginationPageOptions([10, 25, 50, 100])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(CodeStatus::cases())
                        ->mapWithKeys(fn ($e) => [$e->value => $e->label()])
                        ->toArray()),

                Tables\Filters\SelectFilter::make('payment_type')
                    ->label('Payment Type')
                    ->options(collect(PaymentType::cases())
                        ->mapWithKeys(fn ($e) => [$e->value => $e->label()])
                        ->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('void')
                    ->label('Void Code')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (RegistrationCode $r) =>
                        $r->status === CodeStatus::ACTIVE
                        && ! auth()->user()->hasRole('church_coordinator')
                    )
                    ->action(fn (RegistrationCode $record) => $record->update([
                        'status' => CodeStatus::VOID,
                    ])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRegistrationCodes::route('/'),
        ];
    }
}
