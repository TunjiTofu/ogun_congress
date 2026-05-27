<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        // Add district_id to users (for district coordinators)
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('district_id')->nullable()->after('church_id')
                ->constrained()->nullOnDelete();
        });

        // Backfill district_id from the user's church for existing users
        DB::statement('
            UPDATE users u
            INNER JOIN churches c ON c.id = u.church_id
            SET u.district_id = c.district_id
            WHERE u.church_id IS NOT NULL
              AND u.district_id IS NULL
        ');

        // Add photo_status to campers for moderation workflow
        Schema::table('campers', function (Blueprint $table) {
            $table->enum('photo_status', ['pending', 'approved', 'rejected'])
                ->default('pending')->after('consent_collected');
            $table->text('photo_rejection_reason')->nullable()->after('photo_status');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['district_id']);
            $table->dropColumn('district_id');
        });
        Schema::table('campers', function (Blueprint $table) {
            $table->dropColumn(['photo_status', 'photo_rejection_reason']);
        });
    }
};
