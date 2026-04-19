<?php

namespace App\Services;

use App\Enums\CamperCategory;
use App\Enums\CodeStatus;
use App\Enums\PaymentType;
use App\Jobs\SendRegistrationCodeSmsJob;
use App\Models\BulkRegistrationBatch;
use App\Models\BulkRegistrationEntry;
use App\Models\RegistrationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BulkRegistrationService
{
    public function __construct(
        private readonly CodeGenerationService $codeGenerationService,
    ) {}

    public function feeForCategory(CamperCategory $category): float
    {
        return (float) setting("fee_{$category->value}", match($category) {
            CamperCategory::ADVENTURER   => 5000,
            CamperCategory::PATHFINDER   => 5000,
            CamperCategory::SENIOR_YOUTH => 7000,
        });
    }

    public function syncEntries(BulkRegistrationBatch $batch, array $entries): void
    {
        if (! $batch->isDraft()) {
            throw new \LogicException("Batch [{$batch->id}] is not in draft status.");
        }
        DB::transaction(function () use ($batch, $entries) {
            $batch->entries()->delete();
            foreach ($entries as $entry) {
                $category = CamperCategory::from($entry['category']);
                BulkRegistrationEntry::create([
                    'batch_id'  => $batch->id,
                    'full_name' => $entry['full_name'],
                    'phone'     => $entry['phone'],
                    'category'  => $category,
                    'fee'       => $this->feeForCategory($category),
                    'status'    => 'pending',
                ]);
            }
            $batch->recalculateTotal();
        });
    }

    /**
     * Initiate Paystack payment for the entire batch.
     * Returns the authorization_url to redirect the coordinator.
     */
    public function initiatePaystackPayment(BulkRegistrationBatch $batch): array
    {
        if (! $batch->isDraft()) {
            throw new \LogicException("Only draft batches can initiate payment.");
        }
        if ($batch->entries()->count() === 0) {
            throw new \LogicException("Cannot pay for an empty batch.");
        }

        $amountKobo = (int)($batch->expected_total * 100);
        $reference  = 'BATCH-' . $batch->id . '-' . now()->timestamp;

        $callbackUrl = route('batch.payment.callback', ['batch' => $batch->id])
            . '?reference=' . $reference;

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->acceptJson()
            ->post(config('services.paystack.payment_url') . '/transaction/initialize', [
                'reference'    => $reference,
                'amount'       => $amountKobo,
                'email'        => config('services.paystack.merchant_email'),
                'callback_url' => $callbackUrl,
                'metadata'     => [
                    'batch_id'     => $batch->id,
                    'church'       => $batch->church?->name,
                    'camper_count' => $batch->entries()->count(),
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Paystack initialization failed: ' . $response->body());
        }

        $batch->update([
            'status'             => 'pending_payment',
            'payment_type'       => 'online',
            'paystack_reference' => $reference,
        ]);

        Log::info('bulk.paystack_initiated', [
            'batch_id'   => $batch->id,
            'reference'  => $reference,
            'amount'     => $batch->expected_total,
        ]);

        return [
            'reference'         => $reference,
            'authorization_url' => $response->json('data.authorization_url'),
        ];
    }

    /**
     * Handle successful Paystack webhook for a batch payment.
     */
    public function handleBatchPaystackSuccess(string $reference, int $amountKobo): void
    {
        $batch = BulkRegistrationBatch::where('paystack_reference', $reference)->first();

        if (! $batch) {
            Log::warning('bulk.paystack_webhook_unmatched', ['reference' => $reference]);
            return;
        }

        if ($batch->isConfirmed()) {
            Log::info('bulk.paystack_webhook_duplicate', ['batch_id' => $batch->id]);
            return;
        }

        $this->confirmBatch($batch, $amountKobo / 100, null);
    }

    /**
     * Submit a draft batch for offline payment.
     */
    public function submitForOfflinePayment(BulkRegistrationBatch $batch): void
    {
        if (! $batch->isDraft()) {
            throw new \LogicException("Only draft batches can be submitted.");
        }
        if ($batch->entries()->count() === 0) {
            throw new \LogicException("Cannot submit an empty batch.");
        }
        $batch->update([
            'status'       => 'pending_payment',
            'payment_type' => 'offline',
        ]);
        Log::info('bulk.submitted_for_offline_payment', [
            'batch_id'       => $batch->id,
            'expected_total' => $batch->expected_total,
        ]);
    }

    /**
     * Confirm a batch and generate one ACTIVE code per camper.
     * Works for both Paystack (auto) and offline (manual accountant confirm).
     *
     * @param int|null $confirmedByUserId null for Paystack webhook
     */
    public function confirmBatch(
        BulkRegistrationBatch $batch,
        float                 $amountPaid,
        ?int                  $confirmedByUserId,
    ): void {
        if ($batch->isConfirmed()) return; // idempotent

        $expectedTotal = (float) $batch->expected_total;

        if (abs($amountPaid - $expectedTotal) > 1.00) {
            throw new \InvalidArgumentException(
                "Amount paid (\u{20A6}" . number_format($amountPaid, 2) . ") " .
                "does not match expected total (\u{20A6}" . number_format($expectedTotal, 2) . "). " .
                "Difference: \u{20A6}" . number_format(abs($amountPaid - $expectedTotal), 2) . "."
            );
        }

        DB::transaction(function () use ($batch, $amountPaid, $confirmedByUserId) {
            $batch->update([
                'status'       => 'confirmed',
                'amount_paid'  => $amountPaid,
                'confirmed_by' => $confirmedByUserId,
                'confirmed_at' => now(),
            ]);

            foreach ($batch->entries()->get() as $entry) {
                $code = $this->codeGenerationService->generate();

                $registrationCode = RegistrationCode::create([
                    'code'             => $code,
                    'payment_type'     => $batch->isOnlinePayment() ? PaymentType::ONLINE : PaymentType::OFFLINE,
                    'status'           => CodeStatus::ACTIVE,
                    'prefill_name'     => $entry->full_name,
                    'prefill_phone'    => $entry->phone,
                    'prefill_category' => $entry->category->value,
                    'amount_paid'      => $entry->fee,
                    'bulk_batch_id'    => $batch->id,
                    'created_by'       => $confirmedByUserId,
                    'activated_at'     => now(),
                    'expires_at'       => now()->addDays((int) config('camp.code_expiry_days', 14)),
                ]);

                $entry->update([
                    'registration_code_id' => $registrationCode->id,
                    'status'               => 'code_issued',
                ]);

                SendRegistrationCodeSmsJob::dispatch(
                    phone: $entry->phone,
                    code:  $code,
                    name:  $entry->full_name,
                );
            }

            Log::info('bulk.batch_confirmed', [
                'batch_id'     => $batch->id,
                'codes_issued' => $batch->entries()->count(),
                'total_paid'   => $amountPaid,
                'by'           => $confirmedByUserId ?? 'paystack_webhook',
            ]);
        });
    }

    public function rejectBatch(
        BulkRegistrationBatch $batch,
        int                   $rejectedByUserId,
        string                $reason,
    ): void {
        $batch->update([
            'status'           => 'rejected',
            'confirmed_by'     => $rejectedByUserId,
            'confirmed_at'     => now(),
            'rejection_reason' => $reason,
        ]);
        Log::info('bulk.batch_rejected', [
            'batch_id'    => $batch->id,
            'rejected_by' => $rejectedByUserId,
            'reason'      => $reason,
        ]);
    }
}
