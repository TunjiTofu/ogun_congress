<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendPaymentRejectedSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly string $phone,
        private readonly string $name,
        private readonly string $reason,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(SmsService $smsService): void
    {
        $contactPhone = setting('secretariat_phone', 'the secretariat');

        $message = "Hello {$this->name}, we could not confirm your Ogun Youth Camp payment. "
            . "Reason: {$this->reason}. "
            . "Please contact {$contactPhone} for assistance.";

        $smsService->send($this->phone, $message, 'payment_rejected');
    }
}
