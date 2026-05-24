<?php

use App\Models\CampSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add registration control to camp_settings if not exists
        $settings = [
            ['key' => 'registration_open',       'value' => '1',   'group' => 'registration', 'label' => 'Registration Open'],
            ['key' => 'registration_closes_at',  'value' => '',    'group' => 'registration', 'label' => 'Registration Closing Date'],
        ];

        foreach ($settings as $setting) {
            CampSetting::firstOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
};
