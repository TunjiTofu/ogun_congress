<?php

namespace Database\Seeders;

use App\Enums\CamperCategory;
use App\Models\BadgeColorConfig;
use Illuminate\Database\Seeder;

class BadgeColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            CamperCategory::ADVENTURER->value   => ['color_hex' => '#1B6BB5', 'label' => 'Blue — Adventurers'],
            CamperCategory::PATHFINDER->value   => ['color_hex' => '#1A6B3A', 'label' => 'Green — Pathfinders'],
            CamperCategory::SENIOR_YOUTH->value => ['color_hex' => '#C9A94D', 'label' => 'Gold — Senior Youth'],
        ];

        foreach ($colors as $category => $config) {
            BadgeColorConfig::firstOrCreate(
                ['category' => $category],
                $config,
            );
        }

        $this->command->info('Badge colors seeded.');
    }
}
