<?php

namespace Database\Seeders;

use App\Models\CampSetting;
use Illuminate\Database\Seeder;

class CampSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General
            ['key' => 'camp_name',             'value' => 'Ogun Conference Youth Camp', 'label' => 'Camp Name',                  'group' => 'general'],
            ['key' => 'camp_year',             'value' => (string) now()->year,          'label' => 'Camp Year',                  'group' => 'general'],
            ['key' => 'camp_theme',            'value' => '',                            'label' => 'Camp Theme',                 'group' => 'general'],
            ['key' => 'camp_dates',            'value' => 'TBA',                         'label' => 'Camp Dates (Display Text)',  'group' => 'general'],
            ['key' => 'camp_start_date',       'value' => '',                            'label' => 'Camp Start Date',            'group' => 'general'],
            ['key' => 'camp_end_date',         'value' => '',                            'label' => 'Camp End Date',              'group' => 'general'],
            ['key' => 'camp_venue',            'value' => 'TBA',                         'label' => 'Camp Venue',                 'group' => 'general'],
            ['key' => 'registration_deadline', 'value' => '',                            'label' => 'Registration Deadline',      'group' => 'general'],

            // Bank details
            ['key' => 'bank_name',           'value' => '',  'label' => 'Bank Name',        'group' => 'payment'],
            ['key' => 'bank_account_number', 'value' => '',  'label' => 'Account Number',   'group' => 'payment'],
            ['key' => 'bank_account_name',   'value' => '',  'label' => 'Account Name',     'group' => 'payment'],
            ['key' => 'paystack_enabled',    'value' => '1', 'label' => 'Paystack Enabled', 'group' => 'payment'],

            // Fees in Naira
            ['key' => 'fee_adventurer',    'value' => '5000', 'label' => 'Adventurer Fee (₦)',   'group' => 'fees'],
            ['key' => 'fee_pathfinder',    'value' => '5000', 'label' => 'Pathfinder Fee (₦)',   'group' => 'fees'],
            ['key' => 'fee_senior_youth',  'value' => '7000', 'label' => 'Senior Youth Fee (₦)', 'group' => 'fees'],

            // Code settings
            ['key' => 'code_expiry_days', 'value' => '14', 'label' => 'Code Expiry (Days)', 'group' => 'registration'],

            // Contact
            ['key' => 'secretariat_phone', 'value' => '', 'label' => 'Secretariat Phone Number', 'group' => 'contact'],
            ['key' => 'whatsapp_number',   'value' => '', 'label' => 'WhatsApp Payment Number',  'group' => 'contact'],

            // Reminders
            ['key' => 'reminder_7day_enabled', 'value' => '1', 'label' => '7-Day Reminder Enabled', 'group' => 'notifications'],
            ['key' => 'reminder_1day_enabled', 'value' => '1', 'label' => '1-Day Reminder Enabled', 'group' => 'notifications'],
        ];

        foreach ($settings as $setting) {
            CampSetting::firstOrCreate(['key' => $setting['key']], $setting);
        }

        $this->command->info('Camp settings seeded: ' . count($settings) . ' entries.');
    }
}
