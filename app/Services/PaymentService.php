<?php

namespace App\Services;

use App\Enums\CodeStatus;
use App\Enums\OfflinePaymentStatus;
use App\Enums\PaymentType;
use App\Jobs\SendRegistrationCodeSmsJob;
use App\Models\OfflinePayment;
use App\Models\RegistrationCode;
use App\Repositories\Interfaces\OfflinePaymentRepositoryInterface;
use App\Repositories\Interfaces\RegistrationCodeRepositoryInterface;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function __construct(
        private readonly RegistrationCodeRepositoryInterface $codeRepository,
        private readonly OfflinePaymentRepositoryInterface   $offlinePaymentRepository,
        private readonly CodeGenerationService               $codeGenerationService,
    ) {}

    // ── Online (Paystack) ─────────────────────────────────────────────────────

    /**
     * Step 1 of the online flow.
     *
     * - Generates a unique registration code (status: PENDING)
     * - Calls Paystack Initialize Transaction API
     * - Returns the code and Paystack authorization_url
     *
     * @return array{ code: string, authorization_url: string }
     * @throws \RuntimeException|\Illuminate\Http\Client\RequestException
     */
    public function initiatePaystackPayment(
        string $name,
        string $phone,
        int    $amountNaira,
    ): array {
        $registrationCode = DB::transaction(function () use ($name, $phone) {
            $code = $this->codeGenerationService->generate();

            return $this->codeRepository->create([
                'code'          => $code,
                'payment_type'  => PaymentType::ONLINE,
                'status'        => CodeStatus::PENDING,
                'prefill_name'  => $name,
                'prefill_phone' => $phone,
            ]);
        });

        Log::info('payment.initiate', [
            'code'   => $registrationCode->code,
            'phone'  => $phone,
            'amount' => $amountNaira,
        ]);

        try {
            $paystackData = $this->callPaystackInitialize(
                reference:  $registrationCode->code,
                amountKobo: $amountNaira * 100,
                name:       $name,
                phone:      $phone,
            );
        } catch (\Throwable $e) {
            Log::error('payment.paystack_init_failed', [
                'code'  => $registrationCode->code,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }

        $registrationCode->update([
            'paystack_reference' => $paystackData['reference'],
        ]);

        return [
            'code'              => $registrationCode->code,
            'authorization_url' => $paystackData['authorization_url'],
        ];
    }

    /**
     * Called by PaystackWebhookJob when charge.success arrives.
     *
     * Idempotent — safe to call multiple times with the same reference.
     */
    public function handlePaystackSuccess(string $reference, int $amountKobo): void
    {
        $registrationCode = $this->codeRepository->findByPaystackReference($reference)
            ?? $this->codeRepository->findByCode($reference); // fallback if ref = code

        if (! $registrationCode) {
            Log::warning("Paystack webhook: no code found for reference [{$reference}]");
            return;
        }

        // Already processed — idempotency guard
        if ($registrationCode->status !== CodeStatus::PENDING) {
            Log::info("Paystack webhook: code [{$registrationCode->code}] already in status [{$registrationCode->status->value}]. Skipping.");
            return;
        }

        $amountNaira = $amountKobo / 100;

        $this->codeRepository->markAsActive($registrationCode, $amountNaira);

        Log::info('payment.activated', [
            'code'   => $registrationCode->code,
            'amount' => $amountNaira,
            'phone'  => $registrationCode->prefill_phone,
        ]);

        SendRegistrationCodeSmsJob::dispatch(
            phone: $registrationCode->prefill_phone,
            code:  $registrationCode->code,
            name:  $registrationCode->prefill_name,
        );
    }

    /**
     * Verify a Paystack HMAC-SHA512 webhook signature.
     */
    public function verifyPaystackWebhookSignature(string $payload, string $signature): bool
    {
        $secret   = config('services.paystack.webhook_secret');
        $expected = hash_hmac('sha512', $payload, $secret);

        return hash_equals($expected, $signature);
    }

    // ── Offline (Bank Transfer) ───────────────────────────────────────────────

    /**
     * Called by the Filament Confirm action on an OfflinePayment.
     *
     * Generates an ACTIVE code and dispatches the confirmation SMS.
     * Wrapped in a transaction — if code generation fails, the payment
     * status is NOT changed.
     */
    public function confirmOfflinePayment(OfflinePayment $payment, int $confirmedByUserId): RegistrationCode
    {
        if (! $payment->isPending()) {
            throw new \LogicException(
                "Offline payment [{$payment->id}] is already in status [{$payment->status->value}]."
            );
        }

        return DB::transaction(function () use ($payment, $confirmedByUserId) {
            // Confirm the payment record
            $confirmedPayment = $this->offlinePaymentRepository->confirm(
                $payment,
                $confirmedByUserId,
            );

            // Generate and activate the code in one step
            $code = $this->codeGenerationService->generate();

            $registrationCode = $this->codeRepository->create([
                'code'               => $code,
                'payment_type'       => PaymentType::OFFLINE,
                'status'             => CodeStatus::ACTIVE,
                'prefill_name'       => $confirmedPayment->submitted_name,
                'prefill_phone'      => $confirmedPayment->submitted_phone,
                'amount_paid'        => $confirmedPayment->amount,
                'offline_payment_id' => $confirmedPayment->id,
                'created_by'         => $confirmedByUserId,
                'activated_at'       => now(),
                'expires_at'         => now()->addDays(
                    (int) config('camp.code_expiry_days', 14)
                ),
            ]);

            Log::info('payment.offline_confirmed', [
                'code'       => $registrationCode->code,
                'payment_id' => $payment->id,
                'amount'     => $confirmedPayment->amount,
                'confirmed_by' => $confirmedByUserId,
            ]);

            SendRegistrationCodeSmsJob::dispatch(
                phone: $confirmedPayment->submitted_phone,
                code:  $registrationCode->code,
                name:  $confirmedPayment->submitted_name,
            );

            return $registrationCode;
        });
    }

    /**
     * Reject an offline payment and notify the camper.
     */
    public function rejectOfflinePayment(
        OfflinePayment $payment,
        int            $rejectedByUserId,
        string         $reason,
    ): OfflinePayment {
        if (! $payment->isPending()) {
            throw new \LogicException(
                "Offline payment [{$payment->id}] cannot be rejected — current status: [{$payment->status->value}]."
            );
        }

        $rejected = $this->offlinePaymentRepository->reject($payment, $rejectedByUserId, $reason);

        Log::info('payment.offline_rejected', [
            'payment_id'  => $payment->id,
            'rejected_by' => $rejectedByUserId,
            'reason'      => $reason,
        ]);

        \App\Jobs\SendPaymentRejectedSmsJob::dispatch(
            phone:  $rejected->submitted_phone,
            name:   $rejected->submitted_name,
            reason: $reason,
        );

        return $rejected;
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    /**
     * @return array{ reference: string, authorization_url: string }
     * @throws RequestException
     */
    private function callPaystackInitialize(
        string $reference,
        int    $amountKobo,
        string $name,
        string $phone,
    ): array {
        $callbackUrl = route('registration.callback') . '?reference=' . $reference;

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->acceptJson()
            ->post(config('services.paystack.payment_url') . '/transaction/initialize', [
                'reference'    => $reference,
                'amount'       => $amountKobo,
                'email'        => config('services.paystack.merchant_email'),
                'callback_url' => $callbackUrl,
                'metadata'     => [
                    'custom_fields' => [
                        ['display_name' => 'Name',  'variable_name' => 'name',  'value' => $name],
                        ['display_name' => 'Phone', 'variable_name' => 'phone', 'value' => $phone],
                    ],
                ],
            ]);

        $response->throw(); // Throws RequestException on 4xx/5xx

        $data = $response->json('data');

        return [
            'reference'         => $data['reference'],
            'authorization_url' => $data['authorization_url'],
        ];
    }
}
