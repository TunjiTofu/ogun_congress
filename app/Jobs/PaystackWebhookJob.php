<?php

namespace App\Jobs;

use App\Services\PaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PaystackWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries   = 5;
    public int $backoff = 10; // seconds between retries

    public function __construct(
        private readonly string $event,
        private readonly array  $data,
    ) {
        $this->onQueue('critical');
    }

    public function handle(PaymentService $paymentService): void
    {
        if ($this->event !== 'charge.success') {
            Log::info("PaystackWebhookJob: unhandled event [{$this->event}]. Skipping.");
            return;
        }

        $reference  = $this->data['reference'];
        $amountKobo = (int) $this->data['amount'];

        Log::info("PaystackWebhookJob: processing [{$reference}] amount [{$amountKobo} kobo]");

        $paymentService->handlePaystackSuccess($reference, $amountKobo);
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("PaystackWebhookJob failed for reference [{$this->data['reference']}]: " . $exception->getMessage());
    }
}
