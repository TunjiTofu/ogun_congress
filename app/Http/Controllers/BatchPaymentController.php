<?php

namespace App\Http\Controllers;

use App\Models\BulkRegistrationBatch;
use App\Services\BulkRegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BatchPaymentController extends Controller
{
    public function __construct(
        private readonly BulkRegistrationService $bulkService,
    ) {}

    /**
     * GET /batch-payment/callback/{batch}?reference=BATCH-X-XXXXX
     * Called by Paystack after batch payment is completed.
     */
    public function callback(Request $request, BulkRegistrationBatch $batch)
    {
        $reference = $request->query('reference');

        if (! $reference || $batch->paystack_reference !== $reference) {
            return redirect()->route('admin.resources.bulk-registration-batches.edit', $batch)
                ->with('error', 'Payment reference mismatch. Please contact the admin.');
        }

        if ($batch->isConfirmed()) {
            // Already processed — idempotent redirect
            return redirect('/admin/bulk-registration-batches/' . $batch->id . '/edit')
                ->with('success', 'Payment already confirmed. Codes have been generated.');
        }

        // Verify with Paystack
        try {
            $verifyResponse = Http::withToken(config('services.paystack.secret_key'))
                ->get(config('services.paystack.payment_url') . '/transaction/verify/' . $reference);

            if ($verifyResponse->failed() || $verifyResponse->json('data.status') !== 'success') {
                Log::warning('bulk.paystack_callback_verification_failed', [
                    'batch_id'  => $batch->id,
                    'reference' => $reference,
                    'response'  => $verifyResponse->json(),
                ]);

                return redirect('/admin/bulk-registration-batches/' . $batch->id . '/edit')
                    ->with('error', 'Payment could not be verified. Please contact support.');
            }

            $amountKobo = $verifyResponse->json('data.amount');
            $amountNaira = $amountKobo / 100;

            $this->bulkService->confirmBatch($batch, $amountNaira, auth()->id());

            Log::info('bulk.paystack_callback_confirmed', [
                'batch_id' => $batch->id,
                'amount'   => $amountNaira,
            ]);

        } catch (\Throwable $e) {
            Log::error('bulk.paystack_callback_error', [
                'batch_id' => $batch->id,
                'error'    => $e->getMessage(),
            ]);

            return redirect('/admin/bulk-registration-batches/' . $batch->id . '/edit')
                ->with('error', 'Payment processing error: ' . $e->getMessage());
        }

        $count = $batch->entries()->count();

        return redirect('/admin/bulk-registration-batches/' . $batch->id . '/edit')
            ->with('success',
                "Payment confirmed! {$count} registration codes have been generated and sent via SMS."
            );
    }
}
