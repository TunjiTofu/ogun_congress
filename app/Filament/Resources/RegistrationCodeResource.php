<?php

namespace App\Filament\Resources;

use App\Enums\CodeStatus;
use App\Enums\PaymentType;
use App\Filament\Resources\RegistrationCodeResource\Pages;
use App\Jobs\SendRegistrationCodeSmsJob;
use App\Models\RegistrationCode;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RegistrationCodeResource extends Resource
{
    protected static ?string $model           = RegistrationCode::class;
    protected static ?string $navigationIcon  = 'heroicon-o-key';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?string $navigationLabel = 'Registration Codes';
    protected static ?int    $navigationSort  = 2;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['accountant', 'super_admin']);
    }

    public static function canCreate(): bool
    {
        return false; // Codes are created via payment flows only
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]); // Read-only resource — no create/edit form
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
                Tables\Actions\Action::make('resend_sms')
                    ->label('Resend Code SMS')
                    ->icon('heroicon-o-device-phone-mobile')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalDescription(fn (RegistrationCode $r) =>
                        "Resend the registration code to {$r->prefill_phone}?"
                    )
                    ->visible(fn (RegistrationCode $r) => $r->status === CodeStatus::ACTIVE)
                    ->action(function (RegistrationCode $record) {
                        SendRegistrationCodeSmsJob::dispatch(
                            phone: $record->prefill_phone,
                            code:  $record->code,
                            name:  $record->prefill_name,
                        );

                        Notification::make()
                            ->title('SMS queued successfully.')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('void')
                    ->label('Void Code')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (RegistrationCode $r) => $r->status === CodeStatus::ACTIVE)
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
