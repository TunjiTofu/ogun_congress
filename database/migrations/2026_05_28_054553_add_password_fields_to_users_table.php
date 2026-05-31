<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'must_change_password')) {
                $table->boolean('must_change_password')->default(false)->after('is_active')
                    ->comment('Forces password change on next login');
            }
            if (! Schema::hasColumn('users', 'temp_password')) {
                $table->string('temp_password')->nullable()->after('must_change_password')
                    ->comment('Stores plain-text temporary password until changed');
            }
            if (! Schema::hasColumn('users', 'login_attempts')) {
                $table->unsignedTinyInteger('login_attempts')->default(0)->after('temp_password');
            }
            if (! Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('login_attempts');
            }
            if (! Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip')->nullable()->after('locked_until');
            }
            if (! Schema::hasColumn('users', 'last_login_agent')) {
                $table->string('last_login_agent')->nullable()->after('last_login_ip');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'must_change_password', 'temp_password',
                'login_attempts', 'locked_until',
                'last_login_ip', 'last_login_agent',
            ]);
        });
    }
};
