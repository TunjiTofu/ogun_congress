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
                        ->label('Payment Proof / Teller Upload')
                        ->image()
                        ->disk('public')
                        ->directory('bulk-payment-proofs')
                        ->visibility('public')
                        ->imagePreviewHeight('120')
                        ->helperText('Upload a photo of the bank teller or transfer receipt.'),
                ])
                ->columns(2)
                ->collapsed(fn ($record) => $record?->isDraft()),

            // Camper entries (repeater)
            Forms\Components\Section::make('Campers in This Batch')
                ->schema([
                    Forms\Components\Placeholder::make('total_display')
                        ->label('Expected Total')
                        ->content(function ($record, $get) {
                            // Try from saved record first
                            if ($record && $record->expected_total > 0) {
                                $count = $record->entries()->count();
                                return '₦' . number_format($record->expected_total, 2) . " ({$count} camper" . ($count !== 1 ? 's' : '') . ')';
                            }
                            // Try to calculate from live form data
                            $entries = $get('entries') ?? [];
                            $total = collect($entries)->sum(fn ($e) => (float)($e['fee'] ?? 0));
                            $count = count($entries);
                            if ($count > 0) {
                                return '₦' . number_format($total, 2) . " ({$count} camper" . ($count !== 1 ? 's' : '') . ')';
                            }
                            return 'Add campers below to see the total.';
                        })->extraAttributes(['class' => 'text-lg font-bold text-primary-600']),

                    Forms\Components\Repeater::make('entries')
                        ->relationship('entries')
                        ->schema([
                            Forms\Components\TextInput::make('full_name')
                                ->label('Full Name')
                                ->required()
                                ->maxLength(191)
                                ->rules([
                                    function () {
                                        return function (string $attribute, $value, \Closure $fail) {
                                            // Uniqueness checked at form-level via afterValidation
                                        };
                                    },
                                ]),

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

                            // Fix: eager-load registrationCode to avoid lazy loading violation
                            Forms\Components\Placeholder::make('code_issued')
                                ->label('Code')
                                ->content(function ($record) {
                                    if (! $record?->id) return '—';
                                    // Reload with eager-loaded relationship to avoid lazy loading
                                    $entry = \App\Models\BulkRegistrationEntry::with('registrationCode')
                                        ->find($record->id);
                                    return $entry?->registrationCode?->code ?? '—';
                                })
                                ->visibleOn('edit'),
                        ])
                        ->columns(4)
                        ->addActionLabel('Add Camper')
                        ->reorderable(false)
                        ->live()
                        ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                            // Validate uniqueness within the batch: same name+phone+category is not allowed
                            if (! is_array($state)) return;

                            $seen  = [];
                            $dupes = [];

                            foreach ($state as $entry) {
                                $name     = strtolower(trim($entry['full_name'] ?? ''));
                                $phone    = trim($entry['phone'] ?? '');
                                $category = $entry['category'] ?? '';

                                if (! $name && ! $phone) continue;

                                $key = $name . '|' . $phone . '|' . $category;

                                if (isset($seen[$key])) {
                                    $dupes[] = trim($entry['full_name'] ?? '');
                                } else {
                                    $seen[$key] = true;
                                }
                            }

                            // Store duplicate names in a hidden field for display
                            if (! empty($dupes)) {
                                $set('duplicate_warning', 'Duplicate entries detected: ' . implode(', ', array_unique($dupes)) . '. Each camper must have a unique name, phone, and category combination.');
                            } else {
                                $set('duplicate_warning', null);
                            }
                        })
                        ->disabled(fn ($record) => $record && ! $record->isDraft())
                        ->minItems(1),

                    // Duplicate warning banner
                    Forms\Components\Placeholder::make('duplicate_warning')
                        ->label('')
                        ->content(fn ($state) => $state ?? '')
                        ->hidden(fn ($get) => ! $get('duplicate_warning'))
                        ->extraAttributes(['class' => 'text-danger-600 bg-danger-50 rounded-lg p-3 text-sm font-medium']),
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

                Tables\Columns\ImageColumn::make('proof_image_path')
                    ->label('Proof')
                    ->disk('public')
                    ->height(40)
                    ->width(60)
                    ->placeholder('—'),

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

                // Pay online via Paystack
                Tables\Actions\Action::make('pay_paystack')
                    ->label('Pay with Paystack')
                    ->icon('heroicon-o-credit-card')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Pay with Paystack?')
                    ->modalDescription(fn (BulkRegistrationBatch $r) =>
                        "You will be redirected to Paystack to pay ₦" . number_format($r->expected_total, 2) .
                        " for " . $r->entries()->count() . " campers. This will lock the camper list."
                    )
                    ->visible(fn (BulkRegistrationBatch $r) => $r->isDraft() &&
                        auth()->user()->hasAnyRole(['church_coordinator', 'super_admin']) &&
                        config('services.paystack.secret_key'))
                    ->action(function (BulkRegistrationBatch $record, BulkRegistrationService $service) {
                        try {
                            $result = $service->initiatePaystackPayment($record);
                            Notification::make()
                                ->title('Redirecting to Paystack...')
                                ->body('You will be redirected to complete payment.')
                                ->success()->send();
                            // Redirect via Filament JS redirect
                            redirect()->away($result['authorization_url'])->send();
                            exit;
                        } catch (\Throwable $e) {
                            Notification::make()->title($e->getMessage())->danger()->send();
                        }
                    }),

                // Submit for offline payment
                Tables\Actions\Action::make('submit_offline')
                    ->label('Submit for Offline Payment')
                    ->icon('heroicon-o-building-library')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Submit for Bank Transfer Payment?')
                    ->modalDescription(fn (BulkRegistrationBatch $r) =>
                        "This locks the camper list. Expected total: ₦" . number_format($r->expected_total, 2) .
                        " for " . $r->entries()->count() . " campers. Upload your bank transfer proof below."
                    )
                    ->visible(fn (BulkRegistrationBatch $r) => $r->isDraft() &&
                        auth()->user()->hasAnyRole(['church_coordinator', 'super_admin']))
                    ->action(function (BulkRegistrationBatch $record, BulkRegistrationService $service) {
                        try {
                            $service->submitForOfflinePayment($record);
                            Notification::make()
                                ->title('Submitted for offline payment. Upload your bank transfer proof on this page.')
                                ->warning()->send();
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
                        Forms\Components\Placeholder::make('expected_total_display')
                            ->label('Expected Total')
                            ->content(fn (BulkRegistrationBatch $r) =>
                                '₦' . number_format($r->entries()->sum('fee'), 2) .
                                ' for ' . $r->entries()->count() . ' camper(s)'
                            ),
                        Forms\Components\TextInput::make('amount_paid')
                            ->label('Amount Received (₦)')
                            ->numeric()
                            ->prefix('₦')
                            ->required()
                            ->minValue(1)
                            ->helperText(fn (BulkRegistrationBatch $r) =>
                                "Must be ₦" . number_format($r->entries()->sum('fee'), 2) . " (within ₦1 tolerance)"
                            )
                            ->default(fn (BulkRegistrationBatch $r) =>
                            (string) $r->entries()->sum('fee')
                            ),
                    ])
                    ->visible(fn (BulkRegistrationBatch $r) => $r->isPendingPayment() &&
                        auth()->user()->hasAnyRole(['accountant', 'super_admin']))
                    ->action(function (BulkRegistrationBatch $record, array $data, BulkRegistrationService $service) {
                        try {
                            // Refresh expected total from actual entries before confirming
                            $record->recalculateTotal();
                            $record->refresh();
                            $service->confirmBatch($record, (float) $data['amount_paid'], auth()->id());
                            Notification::make()
                                ->title('Confirmed! ' . $record->entries()->count() . ' codes generated and sent via SMS.')
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
