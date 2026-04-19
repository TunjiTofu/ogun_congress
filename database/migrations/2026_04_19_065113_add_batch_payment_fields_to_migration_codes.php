<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->foreignId('bulk_batch_id')
                ->nullable()
                ->after('offline_payment_id')
                ->constrained('bulk_registration_batches')
                ->nullOnDelete()
                ->comment('Set when code was generated via bulk batch');
        });

        Schema::table('bulk_registration_batches', function (Blueprint $table) {
            $table->string('paystack_reference', 191)->nullable()->after('status');
            $table->string('payment_type', 20)->default('offline')->after('paystack_reference')
                ->comment('online or offline');
        });
    }

    public function down(): void
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->dropForeign(['bulk_batch_id']);
            $table->dropColumn('bulk_batch_id');
        });
        Schema::table('bulk_registration_batches', function (Blueprint $table) {
            $table->dropColumn(['paystack_reference', 'payment_type']);
        });
    }
};
