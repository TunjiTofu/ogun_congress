<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ogunconference.org'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ],
        );
        $admin->assignRole('super_admin');

        $accountant = User::firstOrCreate(
            ['email' => 'accountant@ogunconference.org'],
            [
                'name'      => 'Camp Accountant',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ],
        );
        $accountant->assignRole('accountant');

        $this->command->warn('⚠  CHANGE THESE PASSWORDS before going to production!');
        $this->command->line('  Admin →      admin@ogunconference.org / password');
        $this->command->line('  Accountant → accountant@ogunconference.org / password');
    }
}
