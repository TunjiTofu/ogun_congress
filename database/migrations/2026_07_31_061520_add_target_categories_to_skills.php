<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            // Replaces the single 'category' column with a multi-category JSON array.
            // e.g. ["pathfinder","senior_youth"] or ["senior_youth"]
            // NULL = available to all (kept for backwards compatibility)
            $table->json('target_categories')->nullable()->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('skills', function (Blueprint $table) {
            $table->dropColumn('target_categories');
        });
    }
};
