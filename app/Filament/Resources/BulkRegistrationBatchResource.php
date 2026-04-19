<?php

namespace App\Filament\Resources;

use App\Enums\CamperCategory;
use App\Filament\Resources\BulkRegistrationBatchResource\Pages;
use App\Models\BulkRegistrationBatch;
use App\Models\Church;
use App\Models\District;
use App\Services\BulkRegistrationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BulkRegistrationBatchResource extends Resource
{
    protected static ?string $model           = BulkRegistrationBatch::class;
    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Payments';
    protected static ?string $navigationLabel = 'Bulk Registration';
    protected static ?int    $navigationSort  = 3;

    public static function canAccess(): bool
    {
        return auth()->user()->hasAnyRole(['church_coordinator', 'accountant', 'super_admin']);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([

            Forms\Components\Section::make('Batch Details')
                ->schema([
                    // Coordinator: church is auto-set from their profile (read-only)
                    // Super admin / accountant: can select
                    Forms\Components\Select::make('district_id')
                        ->label('District')
                        ->options(District::orderBy('name')->pluck('name', 'id'))
                        ->live()
                        ->afterStateUpdated(fn (Set $set) => $set('church_id', null))
                        ->required()
                        ->dehydrated(false)
                        ->hidden(fn () => auth()->user()->hasRole('church_coordinator'))
                        ->disabled(fn ($record) => $record && ! $record->isDraft()),

                    Forms\Components\Select::make('church_id')
                        ->label('Church')
                        ->options(fn (Get $get) => Church::where('district_id', $get('district_id'))
                            ->orderBy('name')->pluck('name', 'id'))
                        ->required()
                        ->searchable()
                        ->hidden(fn () => auth()->user()->hasRole('church_coordinator'))
                        ->disabled(fn ($record) => $record && ! $record->isDraft()),

                    // Read-only church display for coordinators
                    Forms\Components\Placeholder::make('church_display')
                        ->label('Your Church')
                        ->content(fn () => auth()->user()->church?->name ?? '—')
                        ->visible(fn () => auth()->user()->hasRole('church_coordinator')),

                    Forms\Components\Placeholder::make('district_display')
                        ->label('District')
                        ->content(fn () => auth()->user()->church?->district?->name ?? '—')
                        ->visible(fn () => auth()->user()->hasRole('church_coordinator')),

                    Forms\Components\Textarea::make('notes')
                        ->rows(2)
                        ->columnSpanFull()
                        ->disabled(fn ($record) => $record && ! $record->isDraft()),
                ])->columns(2),

            // Bank transfer details (filled when submitting for payment)
            Forms\Components\Section::make('Payment Details')
                ->schema([
                    Forms\Components\TextInput::make('bank_name')
                        ->maxLength(100),

                    Forms\Components\DatePicker::make('deposit_date')
                        ->maxDate(now()),

                    Forms\Components\TextInput::make('amount_paid')
                        ->label('Amount Paid (₦)')
                        ->numeric()
                        ->prefix('₦')
                        ->helperText('Must match the expected total exactly.'),

                    Forms\Components\FileUpload::make('proof_image_path')
                        ->label('Payment Proof')
                        ->image()
                        ->disk('private')
                        ->directory('bulk-payment-proofs')
                        ->visibility('private'),
                ])
                ->columns(2)
                ->collapsed(fn ($record) => $record?->isDraft()),

            // Camper entries (repeater)
            Forms\Components\Section::make('Campers in This Batch')
                ->schema([
                    Forms\Components\Placeholder::make('total_display')
                        ->label('Expected Total')
                        ->content(fn ($record) => $record
                            ? '₦' . number_format($record->expected_total, 2) .
                            ' for ' . $record->entries()->count() . ' campers'
                            : 'Will be calculated when you save.'),

                    Forms\Components\Repeater::make('entries')
                        ->relationship('entries')
                        ->schema([
                            Forms\Components\TextInput::make('full_name')
                                ->label('Full Name')
                                ->required()
                                ->maxLength(191),

                            Forms\Components\TextInput::make('phone')
                                ->label('Phone Number')
                                ->required()
                                ->tel()
                                ->maxLength(20),

                            Forms\Components\Select::make('category')
                                ->label('Category')
                                ->options(collect(CamperCategory::cases())
                                    ->mapWithKeys(fn ($e) => [$e->value => $e->label() . ' (Ages ' . $e->ageRange() . ')'])
                                    ->toArray())
                                ->required()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    if ($state) {
                                        $cat = CamperCategory::from($state);
                                        $fee = (float) setting("fee_{$cat->value}", 5000);
                                        $set('fee', $fee);
                                    }
                                }),

                            Forms\Components\TextInput::make('fee')
                                ->label('Fee (₦)')
                                ->numeric()
                                ->prefix('₦')
                                ->disabled()
                                ->dehydrated(),

                            Forms\Components\Placeholder::make('code_issued')
                                ->label('Code')
                                ->content(fn ($record) => $record?->registrationCode?->code ?? '—')
                                ->visibleOn('edit'),
                        ])
                        ->columns(4)
                        ->addActionLabel('Add Camper')
                        ->reorderable(false)
                        ->disabled(fn ($record) => $record && ! $record->isDraft())
                        ->minItems(1),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function ($query) {
                // Church coordinators only see their own church's batches
                if (auth()->user()->hasRole('church_coordinator')) {
                    // Find the church linked to this coordinator via their user record
                    // Coordinators are linked to churches via the church_coordinator_users pivot (if exists)
                    // For now, filter by batches they created
                    $query->where('created_by', auth()->id());
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('church.name')
                    ->label('Church')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('church.district.name')
                    ->label('District')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('entries_count')
                    ->label('Campers')
                    ->counts('entries'),

                Tables\Columns\TextColumn::make('expected_total')
                    ->label('Expected Total')
                    ->money('NGN'),

                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Amount Paid')
                    ->money('NGN')
                    ->placeholder('—'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray'    => 'draft',
                        'warning' => 'pending_payment',
                        'success' => 'confirmed',
                        'danger'  => 'rejected',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'draft'           => 'Draft',
                        'pending_payment' => 'Pending Payment',
                        'confirmed'       => 'Confirmed',
                        'rejected'        => 'Rejected',
                        default           => $state,
                    }),

                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft'           => 'Draft',
                        'pending_payment' => 'Pending Payment',
                        'confirmed'       => 'Confirmed',
                        'rejected'        => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                // Submit for payment
                Tables\Actions\Action::make('submit_payment')
                    ->label('Submit for Payment')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Submit Batch for Payment?')
                    ->modalDescription(fn (BulkRegistrationBatch $r) =>
                        "This will lock the camper list. Total: ₦" . number_format($r->expected_total, 2) .
                        " for " . $r->entries()->count() . " campers."
                    )
                    ->visible(fn (BulkRegistrationBatch $r) => $r->isDraft() &&
                        auth()->user()->hasAnyRole(['church_coordinator', 'super_admin']))
                    ->action(function (BulkRegistrationBatch $record, BulkRegistrationService $service) {
                        try {
                            $service->submitForPayment($record);
                            Notification::make()->title('Batch submitted. Please proceed with bank transfer.')->warning()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                // Confirm payment (accountant/super_admin)
                Tables\Actions\Action::make('confirm')
                    ->label('Confirm Payment')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Amount Received (₦)')
                            ->numeric()
                            ->prefix('₦')
                            ->required()
                            ->helperText(fn (BulkRegistrationBatch $r) =>
                                "Expected: ₦" . number_format($r->expected_total, 2)
                            ),
                    ])
                    ->visible(fn (BulkRegistrationBatch $r) => $r->isPendingPayment() &&
                        auth()->user()->hasAnyRole(['accountant', 'super_admin']))
                    ->action(function (BulkRegistrationBatch $record, array $data, BulkRegistrationService $service) {
                        try {
                            $service->confirmBatch($record, (float) $data['amount_paid'], auth()->id());
                            Notification::make()
                                ->title('Batch confirmed! ' . $record->entries()->count() . ' codes generated and SMS sent.')
                                ->success()->send();
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                // Reject (accountant/super_admin)
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
                    ->visible(fn (BulkRegistrationBatch $r) => $r->isPendingPayment() &&
                        auth()->user()->hasAnyRole(['accountant', 'super_admin']))
                    ->action(function (BulkRegistrationBatch $record, array $data, BulkRegistrationService $service) {
                        $service->rejectBatch($record, auth()->id(), $data['rejection_reason']);
                        Notification::make()->title('Batch rejected.')->warning()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListBulkBatches::route('/'),
            'create' => Pages\CreateBulkBatch::route('/create'),
            'edit'   => Pages\EditBulkBatch::route('/{record}/edit'),
        ];
    }
}
