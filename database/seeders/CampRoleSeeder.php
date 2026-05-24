<?php

namespace Database\Seeders;

use App\Models\CampRole;
use Illuminate\Database\Seeder;

class CampRoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Security',      'color' => '#722F37', 'sort_order' => 1],
            ['name' => 'Welfare',       'color' => '#722F37', 'sort_order' => 2],
            ['name' => 'Platform',      'color' => '#722F37', 'sort_order' => 3],
            ['name' => 'Secretariat',   'color' => '#722F37', 'sort_order' => 4],
            ['name' => 'Protocol',      'color' => '#722F37', 'sort_order' => 5],
            ['name' => 'Health',        'color' => '#722F37', 'sort_order' => 6],
            ['name' => 'Media',         'color' => '#722F37', 'sort_order' => 7],
            ['name' => 'Usher',         'color' => '#722F37', 'sort_order' => 8],
        ];

        foreach ($roles as $role) {
            CampRole::firstOrCreate(['name' => $role['name']], $role);
        }

        $this->command->info('Camp roles seeded: ' . count($roles) . ' roles.');
    }
}
