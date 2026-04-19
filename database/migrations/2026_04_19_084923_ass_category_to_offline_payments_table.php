<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('offline_payments', function (Blueprint $table) {
            $table->enum('category', ['adventurer', 'pathfinder', 'senior_youth'])
                ->nullable()
                ->after('submitted_phone')
                ->comment('Camper department — set by accountant at time of payment confirmation');
        });
    }

    public function down(): void
    {
        Schema::table('offline_payments', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
};
