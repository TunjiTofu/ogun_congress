<?php

namespace Database\Seeders;

use App\Models\ClubRank;
use Illuminate\Database\Seeder;

class ClubRankSeeder extends Seeder
{
    public function run(): void
    {
        $ranks = [
            // Adventurers (ages 6–9)
            ['ministry' => 'adventurer', 'rank_name' => 'Little Lamb',   'sort_order' => 1],
            ['ministry' => 'adventurer', 'rank_name' => 'Early Bird',    'sort_order' => 2],
            ['ministry' => 'adventurer', 'rank_name' => 'Busy Bee',      'sort_order' => 3],
            ['ministry' => 'adventurer', 'rank_name' => 'Sunbeam',       'sort_order' => 4],
            ['ministry' => 'adventurer', 'rank_name' => 'Builder',       'sort_order' => 5],
            ['ministry' => 'adventurer', 'rank_name' => 'Helping Hand',  'sort_order' => 6],

            // Pathfinders (ages 10–15)
            ['ministry' => 'pathfinder', 'rank_name' => 'Friend',        'sort_order' => 1],
            ['ministry' => 'pathfinder', 'rank_name' => 'Companion',     'sort_order' => 2],
            ['ministry' => 'pathfinder', 'rank_name' => 'Explorer',      'sort_order' => 3],
            ['ministry' => 'pathfinder', 'rank_name' => 'Ranger',        'sort_order' => 4],
            ['ministry' => 'pathfinder', 'rank_name' => 'Voyager',       'sort_order' => 5],
            ['ministry' => 'pathfinder', 'rank_name' => 'Guide',         'sort_order' => 6],

            // Senior Youth (ages 16+)
            ['ministry' => 'senior_youth', 'rank_name' => 'Ambassador',    'sort_order' => 1],
            ['ministry' => 'senior_youth', 'rank_name' => 'Young Adults',  'sort_order' => 2],
        ];

        ClubRank::truncate();

        foreach ($ranks as $rank) {
            ClubRank::create($rank);
        }

        $this->command->info('Club ranks seeded: ' . count($ranks) . ' entries.');
    }
}
