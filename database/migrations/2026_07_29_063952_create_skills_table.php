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
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('requirement')->nullable();
            $table->text('curriculum')->nullable();
            $table->string('facilitator')->nullable();

            // NULL = General (available to all campers)
            // 'adventurer' | 'pathfinder' | 'senior_youth' = category-specific
            $table->string('category')->nullable()->index();

            // NULL = all ranks within the category
            $table->string('club_rank')->nullable();

            $table->unsignedSmallInteger('maximum_attendees')->default(30);
            $table->enum('status', ['active', 'inactive'])->default('active')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('skills');
    }
};
