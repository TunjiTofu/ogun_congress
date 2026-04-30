<?php

namespace App\Jobs;

use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRegistrationConfirmationSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly string $phone,
        private readonly string $name,
        private readonly string $camperNumber,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(SmsService $smsService): void
    {
        $url      = config('app.url') . '/registration/success/' . $this->camperNumber;
        $campDate = setting('camp_start_date', 'TBA');
        $venue    = setting('camp_venue', 'TBA');

        $message = "Registration complete! Welcome, {$this->name}. "
            . "Your ID card is ready at {$url}. "
            . "Camp begins {$campDate} at {$venue}.";

        $smsService->send($this->phone, $message, 'registration_complete');
    }
}
