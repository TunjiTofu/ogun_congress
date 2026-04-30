<?php

namespace Database\Factories;

use App\Enums\CodeStatus;
use App\Enums\PaymentType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RegistrationCodeFactory extends Factory
{
    public function definition(): array
    {
        $year    = now()->year;
        $charset = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        $segment = '';
        for ($i = 0; $i < 6; $i++) {
            $segment .= $charset[random_int(0, strlen($charset) - 1)];
        }

        return [
            'code'               => "OGN-{$year}-{$segment}",
            'payment_type'       => PaymentType::ONLINE,
            'status'             => CodeStatus::ACTIVE,
            'prefill_name'       => fake()->name(),
            'prefill_phone'      => '080' . fake()->numerify('########'),
            'amount_paid'        => 7000,
            'paystack_reference' => null,
            'offline_payment_id' => null,
            'activated_at'       => now(),
            'expires_at'         => now()->addDays(14),
            'claimed_at'         => null,
            'created_by'         => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => [
            'status'      => CodeStatus::PENDING,
            'amount_paid' => null,
            'activated_at'=> null,
            'expires_at'  => null,
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => ['status' => CodeStatus::ACTIVE]);
    }

    public function claimed(): static
    {
        return $this->state(fn () => [
            'status'     => CodeStatus::CLAIMED,
            'claimed_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status'     => CodeStatus::EXPIRED,
            'expires_at' => now()->subDay(),
        ]);
    }

    public function voided(): static
    {
        return $this->state(fn () => ['status' => CodeStatus::VOID]);
    }

    public function online(): static
    {
        return $this->state(fn () => ['payment_type' => PaymentType::ONLINE]);
    }

    public function offline(): static
    {
        return $this->state(fn () => ['payment_type' => PaymentType::OFFLINE]);
    }
}
