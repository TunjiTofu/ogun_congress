<?php

namespace App\Filament\Resources;

use App\Enums\OfflinePaymentStatus;
use App\Filament\Resources\OfflinePaymentResource\Pages;
use App\Models\OfflinePayment;
use App\Services\PaymentService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OfflinePaymentResource extends Resource
{
    protected static ?string $model = OfflinePayment::class;
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?string $navigationLabel = 'Offline Payments';
    protected static ?int    $navigationSort  = 1;

    public static function getNavigationBadge(): ?string
    {
        return (string) OfflinePayment::where('status', OfflinePaymentStatus::PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string
    {
        return 'warning';
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['accountant', 'super_admin']);
    }

    // ── Form ──────────────────────────────────────────────────────────────────

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Camper Details')
                ->schema([
                    Forms\Components\TextInput::make('submitted_name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(191),

                    Forms\Components\TextInput::make('submitted_phone')
                        ->label('Phone Number')
                        ->required()
                        ->tel()
                        ->maxLength(20),

                    Forms\Components\TextInput::make('amount')
                        ->label('Amount (₦)')
                        ->required()
                        ->numeric()
                        ->prefix('₦'),
                ])->columns(3),

            Forms\Components\Section::make('Bank Transfer Details')
                ->schema([
                    Forms\Components\TextInput::make('bank_name')
                        ->label('Bank Name')
                        ->maxLength(100),

                    Forms\Components\DatePicker::make('deposit_date')
                        ->label('Date of Deposit')
                        ->maxDate(now()),

                    Forms\Components\FileUpload::make('proof_image_path')
                        ->label('Proof of Payment')
                        ->image()
                        ->disk('private')
                        ->directory('payment-proofs')
                        ->visibility('private')
                        ->imagePreviewHeight('200')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf']),
                ])->columns(3),

            Forms\Components\Textarea::make('notes')
                ->label('Internal Notes')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    // ── Table ─────────────────────────────────────────────────────────────────

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('submitted_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('submitted_phone')
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->money('NGN')
                    ->sortable(),

                Tables\Columns\TextColumn::make('bank_name')
                    ->label('Bank')
                    ->placeholder('—'),

                Tables\Columns\TextColumn::make('deposit_date')
                    ->label('Deposit Date')
                    ->date()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => OfflinePaymentStatus::PENDING->value,
                        'success' => OfflinePaymentStatus::CONFIRMED->value,
                        'danger'  => OfflinePaymentStatus::REJECTED->value,
                    ])
                    ->formatStateUsing(fn ($state) => $state instanceof OfflinePaymentStatus
                        ? $state->label()
                        : $state),

                Tables\Columns\TextColumn::make('registrationCode.code')
                    ->label('Code Issued')
                    ->placeholder('Pending')
                    ->copyable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(collect(OfflinePaymentStatus::cases())
                        ->mapWithKeys(fn ($e) => [$e->value => $e->label()])
                        ->toArray()),
            ])
            ->actions([
                // View proof image in a modal
                Tables\Actions\Action::make('view_proof')
                    ->label('View Proof')
                    ->icon('heroicon-o-photo')
                    ->color('gray')
                    ->modalHeading('Payment Proof')
                    ->modalContent(fn (OfflinePayment $record) => view(
                        'filament.modals.payment-proof',
                        ['record' => $record]
                    ))
                    ->visible(fn (OfflinePayment $record) => filled($record->proof_image_path)),

                // Confirm payment
                Tables\Actions\Action::make('confirm')
                    ->label('Confirm Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Confirm this payment?')
                    ->modalDescription(fn (OfflinePayment $r) =>
                        "Confirm ₦" . number_format($r->amount, 2) . " payment from {$r->submitted_name}? "
                        . "A registration code will be generated and sent via SMS."
                    )
                    ->visible(fn (OfflinePayment $r) => $r->isPending())
                    ->action(function (OfflinePayment $record, PaymentService $paymentService) {
                        try {
                            $paymentService->confirmOfflinePayment($record, auth()->id());

                            Notification::make()
                                ->title('Payment confirmed. Registration code sent via SMS.')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Failed to confirm payment: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                // Reject payment
                Tables\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->rows(3),
                    ])
                    ->visible(fn (OfflinePayment $r) => $r->isPending())
                    ->action(function (OfflinePayment $record, array $data, PaymentService $paymentService) {
                        $paymentService->rejectOfflinePayment(
                            $record,
                            auth()->id(),
                            $data['rejection_reason'],
                        );

                        Notification::make()
                            ->title('Payment rejected. Camper notified via SMS.')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListOfflinePayments::route('/'),
            'create' => Pages\CreateOfflinePayment::route('/create'),
            'edit'   => Pages\EditOfflinePayment::route('/{record}/edit'),
        ];
    }
}
