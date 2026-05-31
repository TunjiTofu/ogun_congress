<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin',
            'accountant',
            'secretariat',
            'security',
            'church_coordinator',
            'district_coordinator',
            'camp_director',
            'admin',
            'media',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->command->info('Roles seeded: ' . implode(', ', $roles));
    }
}
