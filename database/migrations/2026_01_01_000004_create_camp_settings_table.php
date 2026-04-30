<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camp_settings', function (Blueprint $table) {
            $table->string('key', 100)->primary();
            $table->text('value')->nullable();
            $table->string('label', 191)
                  ->comment('Human-readable label shown in the Filament settings form');
            $table->string('group', 100)->default('general')
                  ->comment('Groups settings into sections in the Filament UI');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camp_settings');
    }
};
