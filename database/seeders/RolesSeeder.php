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
            'church_coordinator', // Bulk registration for local churches
            'camp_director',       // NEW: read-only across entire system
            'district_coordinator', // NEW: read-only scoped to their district
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->command->info('Roles seeded: ' . implode(', ', $roles));
    }
}
