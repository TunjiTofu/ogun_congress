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
        Schema::create('bulk_registration_batches', function (Blueprint $table) {
            $table->id();

            $table->foreignId('church_id')
                ->constrained('churches')
                ->restrictOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->restrictOnDelete()
                ->comment('The church_coordinator user who created this batch');

            $table->enum('status', ['draft', 'pending_payment', 'confirmed', 'rejected'])
                ->default('draft');

            $table->decimal('expected_total', 10, 2)->default(0)
                ->comment('Sum of individual fees — calculated from camper categories');

            $table->decimal('amount_paid', 10, 2)->nullable()
                ->comment('Amount actually received — must match expected_total');

            $table->string('bank_name', 100)->nullable();
            $table->date('deposit_date')->nullable();
            $table->string('proof_image_path', 500)->nullable();

            $table->foreignId('confirmed_by')->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('church_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulk_registration_batches');
    }
};
