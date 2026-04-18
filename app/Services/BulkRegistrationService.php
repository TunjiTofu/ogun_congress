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
use Illuminate\Support\Facades\Log;

class BulkRegistrationService
{
    public function __construct(
        private readonly CodeGenerationService $codeGenerationService,
    ) {}

    /**
     * Calculate the fee for a given category from camp settings.
     */
    public function feeForCategory(CamperCategory $category): float
    {
        return (float) setting("fee_{$category->value}", match($category) {
            CamperCategory::ADVENTURER   => 5000,
            CamperCategory::PATHFINDER   => 5000,
            CamperCategory::SENIOR_YOUTH => 7000,
        });
    }

    /**
     * Add or update entries in a draft batch, then recalculate total.
     */
    public function syncEntries(BulkRegistrationBatch $batch, array $entries): void
    {
        if (! $batch->isDraft()) {
            throw new \LogicException("Batch [{$batch->id}] is not in draft status.");
        }

        DB::transaction(function () use ($batch, $entries) {
            // Remove old entries and rebuild
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
     * Submit a batch for payment — moves status to pending_payment.
     */
    public function submitForPayment(BulkRegistrationBatch $batch): void
    {
        if (! $batch->isDraft()) {
            throw new \LogicException("Only draft batches can be submitted.");
        }

        if ($batch->entries()->count() === 0) {
            throw new \LogicException("Cannot submit an empty batch.");
        }

        $batch->update(['status' => 'pending_payment']);

        Log::info('bulk.submitted_for_payment', [
            'batch_id'       => $batch->id,
            'church_id'      => $batch->church_id,
            'camper_count'   => $batch->entries()->count(),
            'expected_total' => $batch->expected_total,
        ]);
    }

    /**
     * Confirm a batch payment and generate one ACTIVE code per camper.
     *
     * The amount_paid must match the expected_total within a tolerance of ₦1.
     * Dispatches SMS to each camper with their individual code.
     */
    public function confirmBatch(
        BulkRegistrationBatch $batch,
        float                 $amountPaid,
        int                   $confirmedByUserId,
    ): void {
        if (! $batch->isPendingPayment()) {
            throw new \LogicException("Batch [{$batch->id}] is not pending payment.");
        }

        $expectedTotal = (float) $batch->expected_total;

        if (abs($amountPaid - $expectedTotal) > 1.00) {
            throw new \InvalidArgumentException(
                "Amount paid (₦" . number_format($amountPaid, 2) . ") "
                . "does not match expected total (₦" . number_format($expectedTotal, 2) . "). "
                . "Difference: ₦" . number_format(abs($amountPaid - $expectedTotal), 2) . "."
            );
        }

        DB::transaction(function () use ($batch, $amountPaid, $confirmedByUserId) {

            // Confirm the batch
            $batch->update([
                'status'       => 'confirmed',
                'amount_paid'  => $amountPaid,
                'confirmed_by' => $confirmedByUserId,
                'confirmed_at' => now(),
            ]);

            // Generate one code per entry
            foreach ($batch->entries as $entry) {
                $code = $this->codeGenerationService->generate();

                $registrationCode = RegistrationCode::create([
                    'code'          => $code,
                    'payment_type'  => PaymentType::OFFLINE,
                    'status'        => CodeStatus::ACTIVE,
                    'prefill_name'  => $entry->full_name,
                    'prefill_phone' => $entry->phone,
                    'amount_paid'   => $entry->fee,
                    'created_by'    => $confirmedByUserId,
                    'activated_at'  => now(),
                    'expires_at'    => now()->addDays((int) config('camp.code_expiry_days', 14)),
                ]);

                $entry->update([
                    'registration_code_id' => $registrationCode->id,
                    'status'               => 'code_issued',
                ]);

                // SMS each camper
                SendRegistrationCodeSmsJob::dispatch(
                    phone: $entry->phone,
                    code:  $code,
                    name:  $entry->full_name,
                );
            }

            Log::info('bulk.batch_confirmed', [
                'batch_id'    => $batch->id,
                'codes_issued'=> $batch->entries()->count(),
                'total_paid'  => $amountPaid,
                'confirmed_by'=> $confirmedByUserId,
            ]);
        });
    }

    /**
     * Reject a batch and notify the coordinator.
     */
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
