<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camper_health', function (Blueprint $table) {
            $table->foreignId('camper_id')
                  ->primary()
                  ->constrained('campers')
                  ->cascadeOnDelete()
                  ->comment('One-to-one with campers');

            $table->text('medical_conditions')->nullable()
                  ->comment('e.g. Asthma, Sickle Cell Trait');
            $table->text('medications')->nullable()
                  ->comment('Medication name and dosage');
            $table->text('allergies')->nullable()
                  ->comment('Food or drug allergies');
            $table->text('dietary_restrictions')->nullable()
                  ->comment('e.g. vegetarian, nut-free');
            $table->string('doctor_name', 191)->nullable();
            $table->string('doctor_phone', 20)->nullable();
            $table->text('insurance_details')->nullable()
                  ->comment('Medical aid / HMO information');

            // Computed flag — true if any health field is populated
            $table->boolean('has_alert')->default(false)
                  ->comment('Set on save. Drives the Health Alerts dashboard panel.');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camper_health');
    }
};
