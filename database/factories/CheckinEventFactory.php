<?php

namespace Database\Factories;

use App\Enums\CheckinEventType;
use App\Models\Camper;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CheckinEventFactory extends Factory
{
    public function definition(): array
    {
        return [
            'uuid'              => (string) Str::uuid(),
            'camper_id'         => Camper::factory(),
            'event_type'        => CheckinEventType::CHECK_IN,
            'session_id'        => null,
            'scanned_by'        => null,
            'device_id'         => 'test-device-' . fake()->numerify('###'),
            'scanned_at'        => now(),
            'synced_at'         => null,
            'consent_collected' => false,
            'notes'             => null,
        ];
    }

    public function checkIn(): static
    {
        return $this->state(fn () => ['event_type' => CheckinEventType::CHECK_IN]);
    }

    public function checkOut(): static
    {
        return $this->state(fn () => ['event_type' => CheckinEventType::CHECK_OUT]);
    }

    public function offline(): static
    {
        return $this->state(fn () => ['synced_at' => now()]);
    }
}
