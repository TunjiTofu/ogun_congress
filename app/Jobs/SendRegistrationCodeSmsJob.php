<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRegistrationCodeSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly string $phone,
        private readonly string $code,
        private readonly string $name,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(SmsService $smsService): void
    {
        $url     = config('app.url') . '/register';
        $expiry  = config('camp.code_expiry_days', 14);

        $message = "Hello {$this->name}, your Ogun Youth Camp registration code is: {$this->code}. "
            . "Visit {$url} to complete your registration. Valid for {$expiry} days.";

        $smsService->send($this->phone, $message, 'registration_code_issued');
    }
}
