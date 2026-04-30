<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Backfill prefill_church_id on registration codes that were generated
 * from bulk batches before this column existed or before church enforcement
 * was working correctly.
 */
return new class extends Migration
{
    public function up(): void
    {
        // For codes tied to a batch whose batch has a church_id
        DB::statement("
            UPDATE registration_codes rc
            JOIN bulk_registration_batches b ON b.id = rc.bulk_batch_id
            SET rc.prefill_church_id = b.church_id
            WHERE rc.bulk_batch_id IS NOT NULL
              AND rc.prefill_church_id IS NULL
              AND b.church_id IS NOT NULL
        ");

        // For codes whose batch.church_id is also null, try the batch creator's church
        DB::statement("
            UPDATE registration_codes rc
            JOIN bulk_registration_batches b ON b.id = rc.bulk_batch_id
            JOIN users u ON u.id = b.created_by
            SET rc.prefill_church_id = u.church_id
            WHERE rc.bulk_batch_id IS NOT NULL
              AND rc.prefill_church_id IS NULL
              AND b.church_id IS NULL
              AND u.church_id IS NOT NULL
        ");

        // Also fix the batches themselves if church_id is null
        DB::statement("
            UPDATE bulk_registration_batches b
            JOIN users u ON u.id = b.created_by
            SET b.church_id = u.church_id
            WHERE b.church_id IS NULL
              AND u.church_id IS NOT NULL
        ");
    }

    public function down(): void
    {
        // No rollback — this is a data correction
    }
};
