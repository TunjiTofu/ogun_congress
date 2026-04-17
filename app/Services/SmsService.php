<?php

namespace App\Services;

use App\Models\NotificationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private string $provider;

    public function __construct()
    {
        $this->provider = config('services.sms.provider', 'termii');
    }

    /**
     * Send an SMS message and log the attempt.
     */
    public function send(string $phone, string $message, string $trigger = 'general'): bool
    {
        $phone = $this->normalizePhone($phone);

        try {
            $response = match ($this->provider) {
                'termii'          => $this->sendViaTermii($phone, $message),
                'africas_talking' => $this->sendViaAfricasTalking($phone, $message),
                default           => throw new \InvalidArgumentException("Unknown SMS provider: {$this->provider}"),
            };

            $this->log($phone, $message, $trigger, 'sent', $response);

            Log::info('sms.sent', ['trigger' => $trigger, 'phone' => $phone]);

            return true;
        } catch (\Throwable $e) {
            Log::error('sms.failed', [
                'trigger' => $trigger,
                'phone'   => $phone,
                'error'   => $e->getMessage(),
            ]);
            $this->log($phone, $message, $trigger, 'failed', $e->getMessage());

            return false;
        }
    }

    // ── Providers ─────────────────────────────────────────────────────────────

    private function sendViaTermii(string $phone, string $message): string
    {
        $response = Http::post(config('services.termii.base_url') . '/sms/send', [
            'to'       => $phone,
            'from'     => config('services.sms.sender_id'),
            'sms'      => $message,
            'type'     => 'plain',
            'channel'  => 'generic',
            'api_key'  => config('services.sms.api_key'),
        ]);

        $response->throw();

        return $response->body();
    }

    private function sendViaAfricasTalking(string $phone, string $message): string
    {
        $response = Http::withHeaders([
            'apiKey' => config('services.africas_talking.api_key'),
            'Accept' => 'application/json',
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => config('services.africas_talking.username'),
            'to'       => $phone,
            'message'  => $message,
            'from'     => config('services.sms.sender_id'),
        ]);

        $response->throw();

        return $response->body();
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Normalize phone to E.164 for Nigerian numbers.
     * Accepts: 08012345678 / 8012345678 / +2348012345678
     */
    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '234' . substr($phone, 1);
        } elseif (! str_starts_with($phone, '234')) {
            $phone = '234' . $phone;
        }

        return '+' . $phone;
    }

    private function log(
        string $phone,
        string $message,
        string $trigger,
        string $status,
        string $providerResponse = '',
    ): void {
        NotificationLog::create([
            'recipient_phone'   => $phone,
            'channel'           => 'sms',
            'message'           => $message,
            'trigger'           => $trigger,
            'status'            => $status,
            'provider_response' => $providerResponse,
            'sent_at'           => $status === 'sent' ? now() : null,
        ]);
    }
}
