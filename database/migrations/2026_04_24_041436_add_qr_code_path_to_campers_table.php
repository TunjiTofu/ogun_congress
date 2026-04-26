<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campers', function (Blueprint $table) {
            $table->string('qr_code_path', 500)->nullable()
                ->after('consent_form_path')
                ->comment('Path to stored QR code PNG — encodes the public verification URL');
        });
    }

    public function down(): void
    {
        Schema::table('campers', function (Blueprint $table) {
            $table->dropColumn('qr_code_path');
        });
    }
};
