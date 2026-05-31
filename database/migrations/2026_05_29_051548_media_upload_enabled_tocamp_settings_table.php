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
        CampSetting::firstOrCreate(
            ['key' => 'media_upload_enabled'],
            ['value' => '0', 'label' => 'Media / Album Enabled', 'group' => 'features']
        );
        CampSetting::firstOrCreate(
            ['key' => 'youtube_channel_url'],
            ['value' => 'https://www.youtube.com/@AYMOgunConference', 'label' => 'YouTube Channel URL', 'group' => 'features']
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
