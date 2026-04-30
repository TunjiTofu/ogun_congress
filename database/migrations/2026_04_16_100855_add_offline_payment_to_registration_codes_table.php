<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adds the foreign key from registration_codes.offline_payment_id
 * to offline_payments.id.
 *
 * This must run AFTER both tables are created:
 *   000005 — registration_codes
 *   000006 — offline_payments
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->foreign('offline_payment_id')
                ->references('id')
                ->on('offline_payments')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->dropForeign(['offline_payment_id']);
        });
    }
};
