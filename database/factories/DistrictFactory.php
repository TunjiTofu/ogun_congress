<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class DistrictFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->city() . ' District',
            'zone' => fake()->randomElement(['Abeokuta Zone', 'Sagamu Zone', 'Ijebu Zone', 'Ota Zone']),
        ];
    }
}
