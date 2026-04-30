<?php

namespace Database\Factories;

use App\Enums\ContactType;
use App\Models\Camper;
use Illuminate\Database\Eloquent\Factories\Factory;

class CamperContactFactory extends Factory
{
    public function definition(): array
    {
        return [
            'camper_id'    => Camper::factory(),
            'type'         => ContactType::EMERGENCY_CONTACT,
            'full_name'    => fake()->name(),
            'relationship' => fake()->randomElement(['Mother', 'Father', 'Uncle', 'Aunt', 'Guardian', 'Pastor']),
            'phone'        => '080' . fake()->unique()->numerify('########'),
            'email'        => fake()->optional()->safeEmail(),
            'is_primary'   => true,
        ];
    }

    public function parentGuardian(): static
    {
        return $this->state(fn () => ['type' => ContactType::PARENT_GUARDIAN]);
    }

    public function emergency(): static
    {
        return $this->state(fn () => ['type' => ContactType::EMERGENCY_CONTACT]);
    }
}
