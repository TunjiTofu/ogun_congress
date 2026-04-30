<?php

use App\Enums\CodeStatus;
use App\Enums\OfflinePaymentStatus;
use App\Enums\PaymentType;
use App\Jobs\PaystackWebhookJob;
use App\Jobs\SendRegistrationCodeSmsJob;
use App\Models\OfflinePayment;
use App\Models\RegistrationCode;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

// ── Helpers ───────────────────────────────────────────────────────────────────

function makeAccountant(): User
{
    $user = User::factory()->create([
        'email'    => 'accountant@test.com',
        'password' => Hash::make('password'),
        'is_active'=> true,
    ]);
    $user->assignRole('accountant');
    return $user;
}

function paystackSignature(string $payload): string
{
    return hash_hmac('sha512', $payload, config('services.paystack.webhook_secret'));
}

// ════════════════════════════════════════════════════════════════════════════
// PAYSTACK ONLINE PAYMENT FLOW
// ════════════════════════════════════════════════════════════════════════════

describe('Paystack payment initiation', function () {

    it('creates a PENDING code and returns authorization_url', function () {
        Queue::fake();

        Http::fake([
            '*/transaction/initialize' => Http::response([
                'status' => true,
                'data'   => [
                    'reference'       => 'OGN-2026-AAAAAA',
                    'authorization_url' => 'https://checkout.paystack.com/test123',
                ],
            ]),
        ]);

        $response = $this->postJson('/api/v1/payment/initiate', [
            'name'     => 'Ade Okonkwo',
            'phone'    => '08012345678',
            'category' => 'senior_youth',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'code', 'authorization_url', 'amount']);

        $code = $response->json('code');

        $this->assertDatabaseHas('registration_codes', [
            'code'         => $code,
            'payment_type' => PaymentType::ONLINE->value,
            'status'       => CodeStatus::PENDING->value,
            'prefill_name' => 'Ade Okonkwo',
            'prefill_phone'=> '08012345678',
        ]);
    });

    it('validates name and phone are required', function () {
        $response = $this->postJson('/api/v1/payment/initiate', []);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'phone', 'category']);
    });

    it('rejects invalid phone number format', function () {
        Http::fake(['*/transaction/initialize' => Http::response(['status' => true, 'data' => ['reference' => 'x', 'authorization_url' => 'https://x.com']])]);

        $response = $this->postJson('/api/v1/payment/initiate', [
            'name'     => 'Test User',
            'phone'    => '12345',           // invalid
            'category' => 'senior_youth',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['phone']);
    });

});

// ════════════════════════════════════════════════════════════════════════════
// PAYSTACK WEBHOOK
// ════════════════════════════════════════════════════════════════════════════

describe('Paystack webhook', function () {

    it('accepts valid webhook and dispatches job', function () {
        Queue::fake();

        $code = RegistrationCode::factory()->create([
            'status'              => CodeStatus::PENDING,
            'payment_type'        => PaymentType::ONLINE,
            'paystack_reference'  => 'OGN-2026-TEST01',
        ]);

        $payload = json_encode([
            'event' => 'charge.success',
            'data'  => [
                'reference' => 'OGN-2026-TEST01',
                'amount'    => 700000, // 7000 Naira in kobo
            ],
        ]);

        $response = $this->withHeaders([
            'X-Paystack-Signature' => paystackSignature($payload),
        ])->postJson('/api/webhooks/paystack', json_decode($payload, true));

        $response->assertStatus(200);
        Queue::assertPushedOn('critical', PaystackWebhookJob::class);
    });

    it('rejects webhook with invalid signature', function () {
        Queue::fake();

        $payload = json_encode([
            'event' => 'charge.success',
            'data'  => ['reference' => 'OGN-2026-FAKE'],
        ]);

        $response = $this->withHeaders([
            'X-Paystack-Signature' => 'invalid_signature',
        ])->postJson('/api/webhooks/paystack', json_decode($payload, true));

        $response->assertStatus(401);
        Queue::assertNothingPushed();
    });

    it('activates PENDING code when webhook job processes', function () {
        Queue::fake();

        $code = RegistrationCode::factory()->create([
            'status'             => CodeStatus::PENDING,
            'payment_type'       => PaymentType::ONLINE,
            'paystack_reference' => 'OGN-2026-WEBHOOKTEST',
            'prefill_phone'      => '08011111111',
            'prefill_name'       => 'Test Camper',
        ]);

        $service = app(PaymentService::class);
        $service->handlePaystackSuccess('OGN-2026-WEBHOOKTEST', 700000);

        expect($code->fresh()->status)->toBe(CodeStatus::ACTIVE);
        expect($code->fresh()->amount_paid)->toBe(7000.0);

        Queue::assertPushedOn('notifications', SendRegistrationCodeSmsJob::class);
    });

    it('is idempotent — duplicate webhook does not re-process', function () {
        Queue::fake();

        $code = RegistrationCode::factory()->create([
            'status'             => CodeStatus::ACTIVE, // already processed
            'payment_type'       => PaymentType::ONLINE,
            'paystack_reference' => 'OGN-2026-DUPE',
        ]);

        $service = app(PaymentService::class);
        $service->handlePaystackSuccess('OGN-2026-DUPE', 700000);

        // Status should remain ACTIVE, not change to anything else
        expect($code->fresh()->status)->toBe(CodeStatus::ACTIVE);
        Queue::assertNotPushed(SendRegistrationCodeSmsJob::class);
    });

});

// ════════════════════════════════════════════════════════════════════════════
// OFFLINE PAYMENT FLOW
// ════════════════════════════════════════════════════════════════════════════

describe('Offline payment confirmation', function () {

    beforeEach(function () {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
    });

    it('creates ACTIVE code and dispatches SMS when accountant confirms', function () {
        Queue::fake();

        $accountant = makeAccountant();
        $payment    = OfflinePayment::factory()->create([
            'status' => OfflinePaymentStatus::PENDING,
            'amount' => 5000,
        ]);

        $service = app(PaymentService::class);
        $code    = $service->confirmOfflinePayment($payment, $accountant->id);

        expect($code->status)->toBe(CodeStatus::ACTIVE);
        expect($code->payment_type)->toBe(PaymentType::OFFLINE);
        expect($payment->fresh()->status)->toBe(OfflinePaymentStatus::CONFIRMED);

        Queue::assertPushedOn('notifications', SendRegistrationCodeSmsJob::class);
    });

    it('cannot confirm the same payment twice', function () {
        $accountant = makeAccountant();
        $payment    = OfflinePayment::factory()->create([
            'status' => OfflinePaymentStatus::CONFIRMED, // already confirmed
        ]);

        $service = app(PaymentService::class);

        expect(fn () => $service->confirmOfflinePayment($payment, $accountant->id))
            ->toThrow(\LogicException::class);
    });

});
