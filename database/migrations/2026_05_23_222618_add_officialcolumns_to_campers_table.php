<?php

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
        Schema::table('campers', function (Blueprint $table) {
            //
        });
        Schema::table('campers', function (Blueprint $table) {
            $table->boolean('is_official')->default(false)->after('consent_collected');
            $table->foreignId('camp_role_id')->nullable()->after('is_official')
                ->constrained('camp_roles')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campers', function (Blueprint $table) {
            $table->dropForeign(['camp_role_id']);
            $table->dropColumn(['is_official', 'camp_role_id']);
        });
    }
};
