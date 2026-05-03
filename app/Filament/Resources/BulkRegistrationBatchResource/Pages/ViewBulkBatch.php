<?php

namespace App\Filament\Resources\BulkRegistrationBatchResource\Pages;

use App\Filament\Resources\BulkRegistrationBatchResource;
use App\Services\BulkRegistrationService;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Illuminate\Support\HtmlString;

class ViewBulkBatch extends ViewRecord
{
    protected static string $resource = BulkRegistrationBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Edit — coordinator/super_admin only, non-confirmed
            Action::make('edit')
                ->label('Edit Batch')
                ->icon('heroicon-o-pencil')
                ->color('gray')
                ->url(fn () => BulkRegistrationBatchResource::getUrl('edit', ['record' => $this->record]))
                ->visible(fn () => auth()->user()->hasAnyRole(['church_coordinator', 'super_admin'])
                    && in_array($this->record->status, ['draft', 'pending_payment', 'rejected'])),

            // Confirm Payment — accountant/super_admin, pending_payment only
            Action::make('confirm_payment')
                ->label('Confirm Payment')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirm this payment?')
                ->modalDescription(function () {
                    $b = $this->record;
                    return 'Confirm ₦' . number_format($b->amount_paid ?: $b->expected_total)
                        . ' from ' . ($b->church?->name ?? '—')
                        . ' for ' . $b->entries()->count() . ' camper(s). Codes will be issued immediately.';
                })
                ->visible(fn () => auth()->user()->hasAnyRole(['accountant', 'super_admin'])
                    && $this->record->status === 'pending_payment')
                ->action(function () {
                    try {
                        app(BulkRegistrationService::class)->confirmBatch(
                            $this->record,
                            $this->record->amount_paid ?? $this->record->expected_total,
                            auth()->id()
                        );
                        Notification::make()->title('Payment confirmed. Codes issued.')->success()->send();
                        // Redirect to refresh the page
                        redirect(BulkRegistrationBatchResource::getUrl('view', ['record' => $this->record]));
                    } catch (\Throwable $e) {
                        Notification::make()->title('Error: ' . $e->getMessage())->danger()->send();
                    }
                }),

            // Reject — accountant/super_admin, pending_payment only
            Action::make('reject')
                ->label('Reject')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Reason for rejection')
                        ->placeholder('e.g. Amount does not match expected total, teller image is unreadable...')
                        ->required()
                        ->rows(3),
                ])
                ->visible(fn () => auth()->user()->hasAnyRole(['accountant', 'super_admin'])
                    && $this->record->status === 'pending_payment')
                ->action(function (array $data) {
                    $this->record->update([
                        'status'           => 'rejected',
                        'rejection_reason' => $data['rejection_reason'],
                        'confirmed_by'     => auth()->id(),
                    ]);
                    Notification::make()->title('Batch rejected. Coordinator will be notified.')->warning()->send();
                    redirect(BulkRegistrationBatchResource::getUrl('view', ['record' => $this->record]));
                }),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        $batch = $this->record->load(['church.district', 'createdBy', 'entries.registrationCode']);

        return $infolist->schema([

            // ── Status + rejection banner ─────────────────────────────────
            Components\Section::make()
                ->schema([
                    Components\TextEntry::make('status')
                        ->label('Batch Status')
                        ->badge()
                        ->formatStateUsing(fn ($state) => match($state) {
                            'draft'           => '📝 Draft',
                            'pending_payment' => '⏳ Pending Payment Review',
                            'confirmed'       => '✅ Confirmed',
                            'rejected'        => '❌ Rejected',
                            default           => ucwords(str_replace('_', ' ', $state)),
                        })
                        ->color(fn ($state) => match($state) {
                            'confirmed'       => 'success',
                            'pending_payment' => 'warning',
                            'rejected'        => 'danger',
                            default           => 'gray',
                        }),

                    Components\TextEntry::make('rejection_reason')
                        ->label('❗ Rejection Reason — Action Required')
                        ->visible(fn () => $this->record->status === 'rejected' && $this->record->rejection_reason)
                        ->color('danger')
                        ->weight('bold')
                        ->columnSpan(3)
                        ->formatStateUsing(fn ($state): HtmlString => new HtmlString(
                            '<div style="background:#FEF2F2;border:1.5px solid #FCA5A5;border-radius:8px;padding:10px 14px;color:#991B1B;font-size:0.88rem;line-height:1.6">'
                            . '<strong style="display:block;margin-bottom:4px">Payment Rejected — Please fix and resubmit:</strong>'
                            . e($state)
                            . '</div>'
                        ))
                        ->html(),
                ])->columns(4),

            // ── Church & Coordinator ──────────────────────────────────────
            Components\Section::make('Church & Coordinator')
                ->schema([
                    Components\TextEntry::make('church.name')
                        ->label('Local Church')->weight('bold'),
                    Components\TextEntry::make('church.district.name')
                        ->label('District')->weight('bold'),
                    Components\TextEntry::make('createdBy.name')
                        ->label('Submitted By')->weight('bold'),
                    Components\TextEntry::make('created_at')
                        ->label('Submitted On')
                        ->dateTime('d M Y, H:i')->weight('bold'),
                ])->columns(4),

            // ── Payment details ───────────────────────────────────────────
            Components\Section::make('Payment Details')
                ->schema([
                    Components\TextEntry::make('payment_type')
                        ->label('Payment Method')
                        ->formatStateUsing(fn ($state) => $state === 'online' ? 'Online (Paystack)' : 'Bank Transfer')
                        ->badge()
                        ->color(fn ($state) => $state === 'online' ? 'success' : 'info'),

                    Components\TextEntry::make('bank_name')
                        ->label('Bank')->placeholder('—')->weight('bold'),

                    Components\TextEntry::make('deposit_date')
                        ->label('Deposit Date')->date('d M Y')
                        ->placeholder('—')->weight('bold'),

                    Components\TextEntry::make('expected_total')
                        ->label('Expected Total')->money('NGN')->weight('bold'),

                    Components\TextEntry::make('amount_paid')
                        ->label('Amount Paid')->money('NGN')
                        ->placeholder('Not confirmed yet')->weight('bold')
                        ->color(fn ($record) => $record->amount_paid == $record->expected_total
                            ? 'success' : 'warning'),

                    Components\TextEntry::make('notes')
                        ->label('Notes')->placeholder('—')->weight('bold'),

                    // Proof image — full row
                    Components\TextEntry::make('proof_image_path')
                        ->label('Payment Proof / Teller')
                        ->columnSpanFull()
                        ->formatStateUsing(function ($state): HtmlString {
                            if (! $state) {
                                return new HtmlString(
                                    '<span style="color:#94A3B8;font-style:italic">No proof uploaded.</span>'
                                );
                            }
                            $url = route('proof.image', ['path' => base64_encode($state)]);
                            return new HtmlString(
                                '<div>'
                                . '<a href="' . e($url) . '" target="_blank" rel="noopener">'
                                . '<img src="' . e($url) . '" '
                                . 'style="max-width:360px;max-height:280px;border-radius:10px;'
                                . 'border:1px solid #374151;display:block;cursor:zoom-in;" '
                                . 'alt="Payment proof"/>'
                                . '</a>'
                                . '<p style="font-size:0.72rem;color:#94A3B8;margin-top:6px">Click to open full size</p>'
                                . '</div>'
                            );
                        })
                        ->html(),
                ])->columns(3),

        ]);
    }
}
