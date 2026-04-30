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
        Schema::create('bulk_registration_entries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('batch_id')
                ->constrained('bulk_registration_batches')
                ->cascadeOnDelete();

            // Pre-fill data captured by coordinator
            $table->string('full_name', 191);
            $table->string('phone', 20);
            $table->enum('category', ['adventurer', 'pathfinder', 'senior_youth']);
            $table->decimal('fee', 10, 2)
                ->comment('Fee for this camper based on their category');

            // Set once batch is confirmed
            $table->foreignId('registration_code_id')->nullable()
                ->constrained('registration_codes')
                ->nullOnDelete();

            $table->enum('status', ['pending', 'code_issued', 'registered'])
                ->default('pending')
                ->comment('pending → code_issued (on batch confirm) → registered (after camper fills form)');

            $table->timestamps();

            $table->index(['batch_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_registration_entries');
    }
};
