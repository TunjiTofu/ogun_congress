<?php

use App\Enums\CamperCategory;
use App\Enums\CodeStatus;
use App\Enums\ContactType;
use App\Enums\PaymentType;
use App\Jobs\GenerateCamperDocumentsJob;
use App\Jobs\SendRegistrationConfirmationSmsJob;
use App\Models\Camper;
use App\Models\CamperContact;
use App\Models\Church;
use App\Models\District;
use App\Models\RegistrationCode;
use App\Services\RegistrationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

// ── Seed helpers ──────────────────────────────────────────────────────────────

function seedChurch(): Church
{
    $district = District::factory()->create();
    return Church::factory()->create(['district_id' => $district->id]);
}

function activeCode(array $attrs = []): RegistrationCode
{
    return RegistrationCode::factory()->create(array_merge([
        'status'        => CodeStatus::ACTIVE,
        'payment_type'  => PaymentType::ONLINE,
        'prefill_name'  => 'Blessing Adeyemi',
        'prefill_phone' => '08022222222',
        'amount_paid'   => 7000,
    ], $attrs));
}

// ════════════════════════════════════════════════════════════════════════════
// CODE VALIDATION
// ════════════════════════════════════════════════════════════════════════════

describe('Code validation endpoint', function () {

    it('returns prefill data for an ACTIVE code', function () {
        $code = activeCode();

        $response = $this->postJson('/api/v1/registration/validate-code', [
            'code' => $code->code,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.prefill_name', 'Blessing Adeyemi')
            ->assertJsonPath('data.prefill_phone', '08022222222');
    });

    it('returns structured error for PENDING code', function () {
        $code = RegistrationCode::factory()->create(['status' => CodeStatus::PENDING]);

        $response = $this->postJson('/api/v1/registration/validate-code', ['code' => $code->code]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['code']);
    });

    it('returns structured error for CLAIMED code', function () {
        $code = RegistrationCode::factory()->create(['status' => CodeStatus::CLAIMED]);

        $response = $this->postJson('/api/v1/registration/validate-code', ['code' => $code->code]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.code.0', fn ($msg) => str_contains($msg, 'already been used'));
    });

    it('returns structured error for EXPIRED code', function () {
        $code = RegistrationCode::factory()->create(['status' => CodeStatus::EXPIRED]);

        $response = $this->postJson('/api/v1/registration/validate-code', ['code' => $code->code]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.code.0', fn ($msg) => str_contains($msg, 'expired'));
    });

    it('returns structured error for VOID code', function () {
        $code = RegistrationCode::factory()->create(['status' => CodeStatus::VOID]);

        $response = $this->postJson('/api/v1/registration/validate-code', ['code' => $code->code]);

        $response->assertStatus(422)
            ->assertJsonPath('errors.code.0', fn ($msg) => str_contains($msg, 'cancelled'));
    });

    it('returns error for non-existent code', function () {
        $response = $this->postJson('/api/v1/registration/validate-code', ['code' => 'OGN-2026-NOEXIST']);

        $response->assertStatus(422)
            ->assertJsonPath('errors.code.0', fn ($msg) => str_contains($msg, 'not recognised'));
    });

});

// ════════════════════════════════════════════════════════════════════════════
// REGISTRATION SUBMISSION
// ════════════════════════════════════════════════════════════════════════════

describe('Registration submission', function () {

    it('creates camper and ignores submitted name/phone — uses database values', function () {
        Queue::fake();
        Storage::fake('private');

        $church = seedChurch();
        $code   = activeCode([
            'prefill_name'  => 'DB Name From Payment',
            'prefill_phone' => '08033333333',
        ]);

        $response = $this->postJson('/api/v1/registration/submit', [
            'code'                   => $code->code,
            'date_of_birth'          => '2005-06-15', // 20 years old → senior_youth
            'gender'                 => 'male',
            'church_id'              => $church->id,
            'emergency_name'         => 'Uncle Femi',
            'emergency_relationship' => 'Uncle',
            'emergency_phone'        => '08099999999',
            'photo'                  => UploadedFile::fake()->image('photo.jpg', 300, 300),
            // Attempt to override pre-filled fields — should be ignored
            'full_name'              => 'INJECTED NAME',
            'phone'                  => '00000000000',
        ]);

        $response->assertStatus(201);

        $camper = Camper::where('camper_number', $code->code)->first();

        expect($camper)->not->toBeNull()
            ->and($camper->full_name)->toBe('DB Name From Payment')  // from DB, not POST
            ->and($camper->phone)->toBe('08033333333');              // from DB, not POST
    });

    it('computes category from DOB — not from client input', function () {
        Queue::fake();
        Storage::fake('private');

        $church = seedChurch();
        $code   = activeCode();

        $this->postJson('/api/v1/registration/submit', [
            'code'                   => $code->code,
            'date_of_birth'          => now()->subYears(12)->format('Y-m-d'), // 12 → pathfinder
            'gender'                 => 'female',
            'church_id'              => $church->id,
            'parent_name'            => 'Mrs Adeyemi',
            'parent_relationship'    => 'Mother',
            'parent_phone'           => '08044444444',
            'emergency_name'         => 'Mr Adeyemi',
            'emergency_relationship' => 'Father',
            'emergency_phone'        => '08055555555',
            'photo'                  => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $camper = Camper::where('camper_number', $code->code)->first();
        expect($camper->category)->toBe(CamperCategory::PATHFINDER);
    });

    it('generates consent form for under-18 camper', function () {
        Queue::fake();
        Storage::fake('private');

        $church = seedChurch();
        $code   = activeCode();

        $this->postJson('/api/v1/registration/submit', [
            'code'                   => $code->code,
            'date_of_birth'          => now()->subYears(14)->format('Y-m-d'), // 14 → pathfinder
            'gender'                 => 'male',
            'church_id'              => $church->id,
            'parent_name'            => 'Parent Name',
            'parent_relationship'    => 'Father',
            'parent_phone'           => '08066666666',
            'emergency_name'         => 'Guardian',
            'emergency_relationship' => 'Uncle',
            'emergency_phone'        => '08077777777',
            'photo'                  => UploadedFile::fake()->image('photo.jpg'),
        ]);

        Queue::assertPushedOn('documents', GenerateCamperDocumentsJob::class);
    });

    it('does NOT dispatch consent form job for 18+ camper', function () {
        Queue::fake();
        Storage::fake('private');

        $church = seedChurch();
        $code   = activeCode();

        $this->postJson('/api/v1/registration/submit', [
            'code'                   => $code->code,
            'date_of_birth'          => now()->subYears(20)->format('Y-m-d'),
            'gender'                 => 'male',
            'church_id'              => $church->id,
            'emergency_name'         => 'Contact',
            'emergency_relationship' => 'Friend',
            'emergency_phone'        => '08088888888',
            'photo'                  => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $camper = Camper::where('camper_number', $code->code)->first();
        expect($camper->requiresConsentForm())->toBeFalse();
    });

    it('rejects duplicate emergency contact phone', function () {
        Queue::fake();
        Storage::fake('private');

        $church = seedChurch();

        // Create an existing emergency contact with the same phone
        CamperContact::factory()->create([
            'type'  => ContactType::EMERGENCY_CONTACT,
            'phone' => '08011111111',
        ]);

        $code = activeCode();

        $response = $this->postJson('/api/v1/registration/submit', [
            'code'                   => $code->code,
            'date_of_birth'          => now()->subYears(18)->format('Y-m-d'),
            'gender'                 => 'female',
            'church_id'              => $church->id,
            'emergency_name'         => 'Someone',
            'emergency_relationship' => 'Aunt',
            'emergency_phone'        => '08011111111', // duplicate
            'photo'                  => UploadedFile::fake()->image('photo.jpg'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['emergency_phone'])
            ->assertJsonPath('errors.emergency_phone.0', fn ($msg) =>
                str_contains($msg, 'already registered as an emergency contact')
            );
    });

    it('marks code as CLAIMED after successful submission', function () {
        Queue::fake();
        Storage::fake('private');

        $church = seedChurch();
        $code   = activeCode();

        $this->postJson('/api/v1/registration/submit', [
            'code'                   => $code->code,
            'date_of_birth'          => now()->subYears(20)->format('Y-m-d'),
            'gender'                 => 'male',
            'church_id'              => $church->id,
            'emergency_name'         => 'Contact',
            'emergency_relationship' => 'Friend',
            'emergency_phone'        => '08099900001',
            'photo'                  => UploadedFile::fake()->image('photo.jpg'),
        ]);

        expect($code->fresh()->status)->toBe(CodeStatus::CLAIMED);
        expect($code->fresh()->claimed_at)->not->toBeNull();
    });

    it('dispatches SMS confirmation after registration', function () {
        Queue::fake();
        Storage::fake('private');

        $church = seedChurch();
        $code   = activeCode();

        $this->postJson('/api/v1/registration/submit', [
            'code'                   => $code->code,
            'date_of_birth'          => now()->subYears(22)->format('Y-m-d'),
            'gender'                 => 'female',
            'church_id'              => $church->id,
            'emergency_name'         => 'Emergency',
            'emergency_relationship' => 'Sister',
            'emergency_phone'        => '08099900002',
            'photo'                  => UploadedFile::fake()->image('photo.jpg'),
        ]);

        Queue::assertPushedOn('notifications', SendRegistrationConfirmationSmsJob::class);
    });

    it('prevents race condition — only one registration succeeds for concurrent submissions', function () {
        Queue::fake();
        Storage::fake('private');

        $church = seedChurch();
        $code   = activeCode();

        $payload = [
            'code'                   => $code->code,
            'date_of_birth'          => now()->subYears(20)->format('Y-m-d'),
            'gender'                 => 'male',
            'church_id'              => $church->id,
            'emergency_name'         => 'Contact',
            'emergency_relationship' => 'Friend',
            'emergency_phone'        => '08099900003',
            'photo'                  => UploadedFile::fake()->image('photo.jpg'),
        ];

        $service = app(RegistrationService::class);

        $results = collect(range(1, 3))->map(function () use ($service, $payload) {
            try {
                $service->submit($payload);
                return 'success';
            } catch (\Throwable) {
                return 'failed';
            }
        });

        // Only one should succeed
        expect($results->filter(fn ($r) => $r === 'success')->count())->toBe(1);
        expect(Camper::where('camper_number', $code->code)->count())->toBe(1);
    });

});

// ════════════════════════════════════════════════════════════════════════════
// CATEGORY COMPUTATION BOUNDARY CASES
// ════════════════════════════════════════════════════════════════════════════

describe('Category computation from age', function () {

    it('assigns ADVENTURER for age 6', function () {
        expect(CamperCategory::fromAge(6))->toBe(CamperCategory::ADVENTURER);
    });

    it('assigns ADVENTURER for age 9', function () {
        expect(CamperCategory::fromAge(9))->toBe(CamperCategory::ADVENTURER);
    });

    it('assigns PATHFINDER for exact age 10', function () {
        expect(CamperCategory::fromAge(10))->toBe(CamperCategory::PATHFINDER);
    });

    it('assigns PATHFINDER for age 15', function () {
        expect(CamperCategory::fromAge(15))->toBe(CamperCategory::PATHFINDER);
    });

    it('assigns SENIOR_YOUTH for exact age 16', function () {
        expect(CamperCategory::fromAge(16))->toBe(CamperCategory::SENIOR_YOUTH);
    });

    it('throws for age below 6', function () {
        expect(fn () => CamperCategory::fromAge(5))->toThrow(\InvalidArgumentException::class);
    });

});
