<?php

use App\Models\CampSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add camp_over setting
        CampSetting::firstOrCreate(
            ['key' => 'camp_over'],
            ['value' => '0', 'label' => 'Camp Is Over', 'group' => 'registration']
        );
    }

    public function down(): void
    {
        CampSetting::where('key', 'camp_over')->delete();
    }
};
