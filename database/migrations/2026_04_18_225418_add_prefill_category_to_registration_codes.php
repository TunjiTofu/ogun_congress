<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->string('prefill_category', 20)->nullable()->after('prefill_phone')
                ->comment('Category set at payment initiation — pre-fills and locks the wizard category field');
        });
    }

    public function down(): void
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->dropColumn('prefill_category');
        });
    }
};
