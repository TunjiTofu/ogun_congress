<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NOTE: offline_payments is created BEFORE registration_codes even though
 * registration_codes has an offline_payment_id FK. We add that FK in a
 * separate migration after both tables exist.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offline_payments', function (Blueprint $table) {
            $table->id();

            // Submitted by camper / entered by accountant
            $table->string('submitted_name', 191)
                  ->comment('Name provided when submitting proof of payment');
            $table->string('submitted_phone', 20);
            $table->decimal('amount', 10, 2)
                  ->comment('Amount verified by accountant against bank statement');
            $table->string('bank_name', 100)->nullable();
            $table->date('deposit_date')->nullable()
                  ->comment('Date the deposit was made, per the teller');
            $table->string('proof_image_path', 500)->nullable()
                  ->comment('Path to uploaded teller/screenshot on private disk');
            $table->text('notes')->nullable()
                  ->comment('Internal accountant notes');

            // Status tracking
            $table->enum('status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->foreignId('confirmed_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Accountant who confirmed or rejected');
            $table->timestamp('confirmed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('submitted_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offline_payments');
    }
};
