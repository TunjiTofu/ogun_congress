<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->foreignId('prefill_church_id')
                ->nullable()
                ->after('prefill_category')
                ->constrained('churches')
                ->nullOnDelete()
                ->comment('When set, camper cannot change their church during registration');
        });
    }

    public function down(): void
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->dropForeign(['prefill_church_id']);
            $table->dropColumn('prefill_church_id');
        });
    }
};
