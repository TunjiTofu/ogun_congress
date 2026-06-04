<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add indexes on columns that are queried constantly:
 *  - campers: category, church_id, photo_status, consent_collected, is_official, created_at
 *  - registration_codes: status, code
 *  - offline_payments: status
 *  - checkin_events: camper_id, event_type
 *
 * At 1000 campers + 35 admins polling the dashboard, COUNT(*) queries
 * on un-indexed columns cause full table scans. These indexes reduce
 * those queries from O(n) to O(log n).
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── campers ───────────────────────────────────────────────────────────
        Schema::table('campers', function (Blueprint $table) {
            if (! $this->hasIndex('campers', 'campers_category_index')) {
                $table->index('category');
            }
            // church_id: only add a named index if one doesn't already exist
            // (Laravel's FK migration may have already created an index on this column)
            if (! $this->hasIndex('campers', 'campers_church_id_index')
                && ! $this->hasIndex('campers', 'campers_church_id_foreign')) {
                $table->index('church_id', 'campers_church_id_index');
            }
            if (! $this->hasIndex('campers', 'campers_photo_status_index')) {
                $table->index('photo_status');
            }
            if (! $this->hasIndex('campers', 'campers_consent_collected_index')) {
                $table->index('consent_collected');
            }
            if (! $this->hasIndex('campers', 'campers_is_official_index')) {
                $table->index('is_official');
            }
            if (! $this->hasIndex('campers', 'campers_created_at_index')) {
                $table->index('created_at');
            }
            // Composite: dashboard counts category + consent together
            if (! $this->hasIndex('campers', 'campers_category_consent_index')) {
                $table->index(['category', 'consent_collected'], 'campers_category_consent_index');
            }
        });

        // ── registration_codes ────────────────────────────────────────────────
        Schema::table('registration_codes', function (Blueprint $table) {
            if (! $this->hasIndex('registration_codes', 'registration_codes_status_index')) {
                $table->index('status');
            }
        });

        // ── offline_payments ──────────────────────────────────────────────────
        Schema::table('offline_payments', function (Blueprint $table) {
            if (! $this->hasIndex('offline_payments', 'offline_payments_status_index')) {
                $table->index('status');
            }
        });

        // ── checkin_events ────────────────────────────────────────────────────
        Schema::table('checkin_events', function (Blueprint $table) {
            if (! $this->hasIndex('checkin_events', 'checkin_events_camper_event_index')) {
                $table->index(['camper_id', 'event_type'], 'checkin_events_camper_event_index');
            }
        });

        // ── media (spatie) ────────────────────────────────────────────────────
        Schema::table('media', function (Blueprint $table) {
            if (! $this->hasIndex('media', 'media_model_collection_index')) {
                $table->index(['model_type', 'model_id', 'collection_name'], 'media_model_collection_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campers', function (Blueprint $table) {
            $table->dropIndex(['category']);
            // church_id already has a FK constraint index — MySQL won't allow
            // dropping it independently; skip to avoid rollback failure.
            $table->dropIndex(['photo_status']);
            $table->dropIndex(['consent_collected']);
            $table->dropIndex(['is_official']);
            $table->dropIndex(['created_at']);
            $table->dropIndex('campers_category_consent_index');
        });
        Schema::table('registration_codes', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('offline_payments', function (Blueprint $table) {
            $table->dropIndex(['status']);
        });
        Schema::table('checkin_events', function (Blueprint $table) {
            $table->dropIndex('checkin_events_camper_event_index');
        });
        Schema::table('media', function (Blueprint $table) {
            if ($this->hasIndex('media', 'media_model_collection_index')) {
                $table->dropIndex('media_model_collection_index');
            }
        });
    }

    private function hasIndex(string $table, string $indexName): bool
    {
        return collect(\Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]
        ))->isNotEmpty();
    }
};
