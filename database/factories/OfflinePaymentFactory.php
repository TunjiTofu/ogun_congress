<?php

namespace Database\Factories;

use App\Enums\OfflinePaymentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfflinePaymentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'submitted_name'  => fake()->name(),
            'submitted_phone' => '080' . fake()->numerify('########'),
            'amount'          => fake()->randomElement([5000, 7000]),
            'bank_name'       => fake()->randomElement(['Access Bank', 'GTBank', 'First Bank', 'UBA', 'Zenith Bank']),
            'deposit_date'    => fake()->dateTimeBetween('-7 days', 'now'),
            'proof_image_path'=> null,
            'notes'           => null,
            'status'          => OfflinePaymentStatus::PENDING,
            'confirmed_by'    => null,
            'confirmed_at'    => null,
            'rejection_reason'=> null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn () => ['status' => OfflinePaymentStatus::PENDING]);
    }

    public function confirmed(): static
    {
        return $this->state(fn () => [
            'status'       => OfflinePaymentStatus::CONFIRMED,
            'confirmed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'           => OfflinePaymentStatus::REJECTED,
            'confirmed_at'     => now(),
            'rejection_reason' => 'Payment not found in bank records.',
        ]);
    }
}
