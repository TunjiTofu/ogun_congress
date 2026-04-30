<?php

use App\Enums\CheckinEventType;
use App\Enums\CodeStatus;
use App\Models\Camper;
use App\Models\CheckinEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

// ── Helpers ───────────────────────────────────────────────────────────────────

function makeCheckinUser(): User
{
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'secretariat', 'guard_name' => 'web']);
    $user = User::factory()->create(['is_active' => true]);
    $user->assignRole('secretariat');
    return $user;
}

function checkinToken(User $user, string $deviceId = 'device-001'): string
{
    return $user->createToken('checkin:' . $deviceId, ['checkin'])->plainTextToken;
}

// ════════════════════════════════════════════════════════════════════════════
// PWA AUTH
// ════════════════════════════════════════════════════════════════════════════

describe('PWA authentication', function () {

    it('issues a Sanctum token scoped to checkin ability', function () {
        $user = makeCheckinUser();

        $response = $this->postJson('/api/checkin/auth', [
            'device_id' => 'my-device-001',
            'email'     => $user->email,
            'pin'       => 'password', // default factory password
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'token', 'user'])
            ->assertJsonPath('success', true);
    });

    it('rejects invalid credentials', function () {
        $user = makeCheckinUser();

        $response = $this->postJson('/api/checkin/auth', [
            'device_id' => 'device-001',
            'email'     => $user->email,
            'pin'       => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['pin']);
    });

    it('rejects inactive user', function () {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'secretariat', 'guard_name' => 'web']);
        $user = User::factory()->create(['is_active' => false]);
        $user->assignRole('secretariat');

        $response = $this->postJson('/api/checkin/auth', [
            'device_id' => 'device-001',
            'email'     => $user->email,
            'pin'       => 'password',
        ]);

        $response->assertStatus(422);
    });

});

// ════════════════════════════════════════════════════════════════════════════
// SYNC ENDPOINT
// ════════════════════════════════════════════════════════════════════════════

describe('Sync endpoint', function () {

    it('returns only CLAIMED campers', function () {
        $user = makeCheckinUser();

        // Create a claimed camper
        $claimedCode = \App\Models\RegistrationCode::factory()->create([
            'status' => CodeStatus::CLAIMED,
        ]);
        Camper::factory()->create(['registration_code_id' => $claimedCode->id]);

        // Create an active (not yet registered) code — should NOT appear
        \App\Models\RegistrationCode::factory()->create(['status' => CodeStatus::ACTIVE]);

        $response = $this->withToken(checkinToken($user))
            ->getJson('/api/checkin/sync');

        $response->assertStatus(200);

        $data = $response->json('data');
        expect(count($data))->toBe(1);
    });

    it('rejects unauthenticated requests', function () {
        $response = $this->getJson('/api/checkin/sync');
        $response->assertStatus(401);
    });

});

// ════════════════════════════════════════════════════════════════════════════
// EVENT STORE — IDEMPOTENCY
// ════════════════════════════════════════════════════════════════════════════

describe('Check-in event storage', function () {

    it('inserts new events and skips duplicate UUIDs', function () {
        $user   = makeCheckinUser();
        $camper = Camper::factory()->create();
        $uuid   = (string) Str::uuid();

        $events = [
            [
                'uuid'               => $uuid,
                'camper_number'      => $camper->camper_number,
                'event_type'         => CheckinEventType::CHECK_IN->value,
                'scanned_at'         => now()->toISOString(),
                'device_id'          => 'device-001',
                'consent_collected'  => false,
            ],
        ];

        // First submission
        $response1 = $this->withToken(checkinToken($user))
            ->postJson('/api/checkin/events', ['events' => $events]);

        $response1->assertStatus(200)
            ->assertJsonPath('inserted', 1)
            ->assertJsonPath('skipped', 0);

        // Second submission — same UUID — should be deduplicated
        $response2 = $this->withToken(checkinToken($user))
            ->postJson('/api/checkin/events', ['events' => $events]);

        $response2->assertStatus(200)
            ->assertJsonPath('inserted', 0)
            ->assertJsonPath('skipped', 1);

        // Only one record in DB
        expect(CheckinEvent::where('uuid', $uuid)->count())->toBe(1);
    });

    it('handles batch of mixed new and duplicate UUIDs', function () {
        $user   = makeCheckinUser();
        $camper = Camper::factory()->create();

        $existingUuid = (string) Str::uuid();
        $newUuid      = (string) Str::uuid();

        // Pre-insert the first event
        CheckinEvent::factory()->create([
            'uuid'       => $existingUuid,
            'camper_id'  => $camper->id,
            'event_type' => CheckinEventType::CHECK_IN,
            'scanned_at' => now(),
        ]);

        $events = [
            [
                'uuid'          => $existingUuid,  // duplicate
                'camper_number' => $camper->camper_number,
                'event_type'    => CheckinEventType::CHECK_IN->value,
                'scanned_at'    => now()->toISOString(),
                'device_id'     => 'device-001',
            ],
            [
                'uuid'          => $newUuid,        // new
                'camper_number' => $camper->camper_number,
                'event_type'    => CheckinEventType::CHECK_OUT->value,
                'scanned_at'    => now()->addMinutes(5)->toISOString(),
                'device_id'     => 'device-001',
            ],
        ];

        $response = $this->withToken(checkinToken($user))
            ->postJson('/api/checkin/events', ['events' => $events]);

        $response->assertStatus(200)
            ->assertJsonPath('inserted', 1)
            ->assertJsonPath('skipped', 1);
    });

    it('silently skips events for unknown camper numbers', function () {
        $user = makeCheckinUser();

        $events = [
            [
                'uuid'          => (string) Str::uuid(),
                'camper_number' => 'OGN-2026-DOESNOTEXIST',
                'event_type'    => CheckinEventType::CHECK_IN->value,
                'scanned_at'    => now()->toISOString(),
                'device_id'     => 'device-001',
            ],
        ];

        $response = $this->withToken(checkinToken($user))
            ->postJson('/api/checkin/events', ['events' => $events]);

        $response->assertStatus(200)
            ->assertJsonPath('inserted', 0);
    });

});

// ════════════════════════════════════════════════════════════════════════════
// REAL-TIME CAMPER LOOKUP
// ════════════════════════════════════════════════════════════════════════════

describe('Camper lookup endpoint', function () {

    it('returns camper card data for a valid camper number', function () {
        $user   = makeCheckinUser();
        $camper = Camper::factory()
            ->for(\App\Models\Church::factory()->for(\App\Models\District::factory()))
            ->create();

        $response = $this->withToken(checkinToken($user))
            ->getJson("/api/checkin/camper/{$camper->camper_number}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['camper' => [
                'camper_number', 'full_name', 'category',
                'church', 'district', 'consent_collected',
                'consent_required', 'latest_event',
            ]]);
    });

    it('returns 404 for unknown camper number', function () {
        $user = makeCheckinUser();

        $response = $this->withToken(checkinToken($user))
            ->getJson('/api/checkin/camper/OGN-2026-GHOST');

        $response->assertStatus(404);
    });

});
