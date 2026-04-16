<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('registration_codes', function (Blueprint $table) {
            $table->id();

            // The code itself
            $table->string('code', 20)->unique()
                  ->comment('Format: OGN-YYYY-XXXXXX. Uppercase alphanumeric, no 0/O/I/1');

            // Payment linkage
            $table->enum('payment_type', ['online', 'offline']);
            $table->enum('status', ['PENDING', 'ACTIVE', 'CLAIMED', 'EXPIRED', 'VOID'])
                  ->default('PENDING');

            // Pre-fill data captured at payment initiation
            $table->string('prefill_name', 191)
                  ->comment('Name captured at payment. Rendered read-only on registration form.');
            $table->string('prefill_phone', 20)
                  ->comment('Phone captured at payment. Rendered read-only on registration form.');

            // Payment confirmation data
            $table->decimal('amount_paid', 10, 2)->nullable()
                  ->comment('Confirmed amount in NGN. Set when payment is confirmed.');

            // Online payment (Paystack)
            $table->string('paystack_reference', 100)->nullable()->unique()
                  ->comment('Paystack transaction reference. Null for offline payments.');

            // Offline payment
            $table->foreignId('offline_payment_id')->nullable()
                  ->constrained('offline_payments')
                  ->nullOnDelete()
                  ->comment('Null for online payments.');

            // Lifecycle timestamps
            $table->timestamp('activated_at')->nullable()
                  ->comment('When the code became ACTIVE');
            $table->timestamp('expires_at')->nullable()
                  ->comment('ACTIVE codes expire after N configurable days');
            $table->timestamp('claimed_at')->nullable()
                  ->comment('When registration was successfully completed');

            // Audit
            $table->foreignId('created_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Null for self-service online. Accountant user ID for offline.');

            $table->timestamps();

            // Indexes for common lookups
            $table->index('status');
            $table->index('payment_type');
            $table->index('prefill_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registration_codes');
    }
};
