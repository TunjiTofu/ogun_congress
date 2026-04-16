<?php

namespace Database\Factories;

use App\Models\District;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChurchFactory extends Factory
{
    public function definition(): array
    {
        return [
            'district_id' => District::factory(),
            'name'        => fake()->randomElement([
                'Central SDA Church',
                'Bethel SDA Church',
                'Calvary SDA Church',
                'Grace SDA Church',
                'Emmanuel SDA Church',
                'Faith SDA Church',
                'Hope SDA Church',
                'Victory SDA Church',
            ]) . ' (' . fake()->city() . ')',
            'address' => fake()->address(),
        ];
    }
}
