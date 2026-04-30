<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campers', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->foreignId('registration_code_id')
                  ->unique()
                  ->constrained('registration_codes')
                  ->restrictOnDelete();
            $table->string('camper_number', 20)->unique()
                  ->comment('Mirrors the registration code. Used as the human-readable ID.');

            // Pre-fill (copied from registration_codes at submission — server-enforced)
            $table->string('full_name', 191)
                  ->comment('Copied from registration_codes.prefill_name. Never from form POST.');
            $table->string('phone', 20)
                  ->comment('Copied from registration_codes.prefill_phone. NOT unique at camper level.');

            // Personal
            $table->date('date_of_birth');
            $table->enum('gender', ['male', 'female']);
            $table->enum('category', ['adventurer', 'pathfinder', 'senior_youth'])
                  ->comment('Computed server-side from DOB at submission. Never from client input.');
            $table->text('home_address')->nullable();

            // Church & Ministry
            $table->foreignId('church_id')
                  ->constrained('churches')
                  ->restrictOnDelete();
            $table->string('ministry', 100)->nullable()
                  ->comment('Club/ministry. Required for Adventurers and Pathfinders.');
            $table->string('club_rank', 100)->nullable()
                  ->comment('Class or rank within club. Optional.');
            $table->string('volunteer_role', 100)->nullable()
                  ->comment('Senior Youth only. Optional.');

            // Documents
            $table->string('photo_path', 500)->nullable()
                  ->comment('Managed by Spatie MediaLibrary. Resized to 400×400 on upload.');
            $table->string('badge_color', 20)->nullable()
                  ->comment('Resolved from category at generation time.');
            $table->string('id_card_path', 500)->nullable()
                  ->comment('Path to generated ID card PDF on private disk.');
            $table->string('consent_form_path', 500)->nullable()
                  ->comment('Path to consent form PDF. Null for 18+ campers.');

            // Check-in
            $table->boolean('consent_collected')->default(false)
                  ->comment('Set true by secretariat at check-in when physical form is received.');

            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index('category');
            $table->index('church_id');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campers');
    }
};
