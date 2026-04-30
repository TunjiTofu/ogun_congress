<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_ranks', function (Blueprint $table) {
            $table->id();
            $table->string('ministry')
                ->comment('Senior Youth have no structured club ranks');
            $table->string('rank_name', 100)
                ->comment('e.g. Busy Bee, Friend, Companion');
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['ministry', 'rank_name']);
            $table->index('ministry');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('club_ranks');
    }
};
