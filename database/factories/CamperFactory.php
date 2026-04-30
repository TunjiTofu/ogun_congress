<?php

namespace Database\Factories;

use App\Enums\CamperCategory;
use App\Enums\CodeStatus;
use App\Enums\Gender;
use App\Models\Church;
use App\Models\RegistrationCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CamperFactory extends Factory
{
    public function definition(): array
    {
        $gender   = fake()->randomElement(Gender::cases());
        $category = fake()->randomElement(CamperCategory::cases());
        $dob      = match ($category) {
            CamperCategory::ADVENTURER   => fake()->dateTimeBetween('-9 years', '-6 years'),
            CamperCategory::PATHFINDER   => fake()->dateTimeBetween('-15 years', '-10 years'),
            CamperCategory::SENIOR_YOUTH => fake()->dateTimeBetween('-35 years', '-16 years'),
        };

        // We need a claimed registration code for each camper
        $code = RegistrationCode::factory()->claimed()->create();

        return [
            'registration_code_id' => $code->id,
            'camper_number'        => $code->code,
            'full_name'            => $code->prefill_name,
            'phone'                => $code->prefill_phone,
            'date_of_birth'        => $dob,
            'gender'               => $gender,
            'category'             => $category,
            'home_address'         => fake()->address(),
            'church_id'            => Church::factory(),
            'ministry'             => $category !== CamperCategory::SENIOR_YOUTH
                ? fake()->randomElement(['Adventurers', 'Pathfinders'])
                : null,
            'club_rank'            => null,
            'volunteer_role'       => $category === CamperCategory::SENIOR_YOUTH
                ? fake()->randomElement([null, 'Worship Leader', 'Security Volunteer', 'Kitchen Helper'])
                : null,
            'photo_path'           => null,
            'badge_color'          => config("camp.badge_colors.{$category->value}"),
            'id_card_path'         => null,
            'consent_form_path'    => null,
            'consent_collected'    => false,
        ];
    }

    public function adventurer(): static
    {
        return $this->state(function () {
            $dob  = fake()->dateTimeBetween('-9 years', '-6 years');
            $code = RegistrationCode::factory()->claimed()->create();
            return [
                'registration_code_id' => $code->id,
                'camper_number'        => $code->code,
                'full_name'            => $code->prefill_name,
                'phone'                => $code->prefill_phone,
                'date_of_birth'        => $dob,
                'category'             => CamperCategory::ADVENTURER,
                'ministry'             => 'Adventurers',
                'badge_color'          => config('camp.badge_colors.adventurer'),
            ];
        });
    }

    public function pathfinder(): static
    {
        return $this->state(function () {
            $dob  = fake()->dateTimeBetween('-15 years', '-10 years');
            $code = RegistrationCode::factory()->claimed()->create();
            return [
                'registration_code_id' => $code->id,
                'camper_number'        => $code->code,
                'full_name'            => $code->prefill_name,
                'phone'                => $code->prefill_phone,
                'date_of_birth'        => $dob,
                'category'             => CamperCategory::PATHFINDER,
                'ministry'             => 'Pathfinders',
                'badge_color'          => config('camp.badge_colors.pathfinder'),
            ];
        });
    }

    public function seniorYouth(): static
    {
        return $this->state(function () {
            $dob  = fake()->dateTimeBetween('-35 years', '-16 years');
            $code = RegistrationCode::factory()->claimed()->create();
            return [
                'registration_code_id' => $code->id,
                'camper_number'        => $code->code,
                'full_name'            => $code->prefill_name,
                'phone'                => $code->prefill_phone,
                'date_of_birth'        => $dob,
                'category'             => CamperCategory::SENIOR_YOUTH,
                'ministry'             => null,
                'badge_color'          => config('camp.badge_colors.senior_youth'),
            ];
        });
    }

    public function withConsentCollected(): static
    {
        return $this->state(fn () => ['consent_collected' => true]);
    }
}
