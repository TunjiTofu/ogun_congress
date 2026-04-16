<?php

namespace Database\Seeders;

use App\Enums\CamperCategory;
use App\Models\CampSetting;
use App\Models\Church;
use App\Models\District;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

// ── Roles ─────────────────────────────────────────────────────────────────────

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super_admin', 'accountant', 'secretariat', 'security'];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }

        $this->command->info('Roles seeded: ' . implode(', ', $roles));
    }
}

// ── Camp Settings ─────────────────────────────────────────────────────────────

class CampSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'camp_name',            'value' => 'Ogun Conference Youth Camp',   'label' => 'Camp Name',                   'group' => 'general'],
            ['key' => 'camp_year',            'value' => (string) now()->year,             'label' => 'Camp Year',                   'group' => 'general'],
            ['key' => 'camp_theme',           'value' => '',                               'label' => 'Camp Theme',                  'group' => 'general'],
            ['key' => 'camp_dates',           'value' => 'TBA',                            'label' => 'Camp Dates (Display Text)',   'group' => 'general'],
            ['key' => 'camp_start_date',      'value' => '',                               'label' => 'Camp Start Date',             'group' => 'general'],
            ['key' => 'camp_end_date',        'value' => '',                               'label' => 'Camp End Date',               'group' => 'general'],
            ['key' => 'camp_venue',           'value' => 'TBA',                            'label' => 'Camp Venue',                  'group' => 'general'],
            ['key' => 'registration_deadline','value' => '',                               'label' => 'Registration Deadline',       'group' => 'general'],

            // Bank details
            ['key' => 'bank_name',            'value' => '',  'label' => 'Bank Name',          'group' => 'payment'],
            ['key' => 'bank_account_number',  'value' => '',  'label' => 'Account Number',     'group' => 'payment'],
            ['key' => 'bank_account_name',    'value' => '',  'label' => 'Account Name',       'group' => 'payment'],
            ['key' => 'paystack_enabled',     'value' => '1', 'label' => 'Paystack Enabled',   'group' => 'payment'],

            // Fees (in Naira)
            ['key' => 'fee_adventurer',       'value' => '5000', 'label' => 'Adventurer Fee (₦)',   'group' => 'fees'],
            ['key' => 'fee_pathfinder',       'value' => '5000', 'label' => 'Pathfinder Fee (₦)',   'group' => 'fees'],
            ['key' => 'fee_senior_youth',     'value' => '7000', 'label' => 'Senior Youth Fee (₦)', 'group' => 'fees'],

            // Code settings
            ['key' => 'code_expiry_days',     'value' => '14', 'label' => 'Code Expiry (Days)',      'group' => 'registration'],

            // Contact
            ['key' => 'secretariat_phone',    'value' => '', 'label' => 'Secretariat Phone Number',  'group' => 'contact'],
            ['key' => 'whatsapp_number',      'value' => '', 'label' => 'WhatsApp Payment Number',   'group' => 'contact'],

            // Reminders
            ['key' => 'reminder_7day_enabled','value' => '1', 'label' => '7-Day Reminder Enabled', 'group' => 'notifications'],
            ['key' => 'reminder_1day_enabled','value' => '1', 'label' => '1-Day Reminder Enabled', 'group' => 'notifications'],
        ];

        foreach ($settings as $setting) {
            CampSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting,
            );
        }

        $this->command->info('Camp settings seeded: ' . count($settings) . ' entries.');
    }
}

// ── Districts & Churches ──────────────────────────────────────────────────────

class DistrictAndChurchSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'Abeokuta District' => [
                'zone'     => 'Abeokuta Zone',
                'churches' => [
                    'Abeokuta Central SDA Church',
                    'Ake SDA Church',
                    'Iyana Mortuary SDA Church',
                    'Lafenwa SDA Church',
                ],
            ],
            'Sagamu District' => [
                'zone'     => 'Sagamu Zone',
                'churches' => [
                    'Sagamu Central SDA Church',
                    'Ikenne SDA Church',
                    'Ilishan SDA Church',
                ],
            ],
            'Ijebu-Ode District' => [
                'zone'     => 'Ijebu Zone',
                'churches' => [
                    'Ijebu-Ode Central SDA Church',
                    'Ijebu-Igbo SDA Church',
                    'Odogbolu SDA Church',
                ],
            ],
            'Ota District' => [
                'zone'     => 'Ota Zone',
                'churches' => [
                    'Ota Central SDA Church',
                    'Sango SDA Church',
                    'Owode SDA Church',
                ],
            ],
        ];

        foreach ($data as $districtName => $info) {
            $district = District::firstOrCreate(
                ['name' => $districtName],
                ['zone' => $info['zone']],
            );

            foreach ($info['churches'] as $churchName) {
                Church::firstOrCreate(
                    ['name' => $churchName, 'district_id' => $district->id],
                );
            }
        }

        $this->command->info('Districts and churches seeded.');
    }
}

// ── Badge Colors ──────────────────────────────────────────────────────────────

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
            \App\Models\BadgeColorConfig::firstOrCreate(
                ['category' => $category],
                $config,
            );
        }

        $this->command->info('Badge colors seeded.');
    }
}

// ── Admin User ────────────────────────────────────────────────────────────────

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@ogunconference.org'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('password'), // CHANGE IN PRODUCTION
                'phone'     => null,
                'is_active' => true,
            ],
        );

        $admin->assignRole('super_admin');

        // Sample accountant
        $accountant = User::firstOrCreate(
            ['email' => 'accountant@ogunconference.org'],
            [
                'name'      => 'Camp Accountant',
                'password'  => Hash::make('password'),
                'is_active' => true,
            ],
        );
        $accountant->assignRole('accountant');

        $this->command->warn('Admin user seeded. CHANGE THE PASSWORD before deploying to production!');
        $this->command->line('  Email:    admin@ogunconference.org');
        $this->command->line('  Password: password');
    }
}
