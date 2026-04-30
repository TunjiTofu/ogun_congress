<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programme_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->text('description')->nullable();
            $table->string('location', 191)->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_open')->default(false)
                  ->comment('Toggled by secretariat to start/stop attendance scanning');
            $table->timestamps();
        });

        Schema::create('checkin_events', function (Blueprint $table) {
            $table->id();

            $table->uuid('uuid')->unique()
                  ->comment('Client-generated UUID. Used for offline sync deduplication.');

            $table->foreignId('camper_id')
                  ->constrained('campers')
                  ->restrictOnDelete();

            $table->enum('event_type', ['check_in', 'check_out', 'programme_attendance']);

            $table->foreignId('session_id')->nullable()
                  ->constrained('programme_sessions')
                  ->nullOnDelete()
                  ->comment('Null for camp entry/exit events');

            $table->foreignId('scanned_by')->nullable()
                  ->constrained('users')
                  ->nullOnDelete()
                  ->comment('Null for PWA offline sync events');

            $table->string('device_id', 100)->nullable()
                  ->comment('UUID of the PWA device that recorded the event');

            $table->timestamp('scanned_at')
                  ->comment('Actual time of the scan. May differ from created_at for offline syncs.');

            $table->timestamp('synced_at')->nullable()
                  ->comment('When the event arrived via offline sync. Null for real-time events.');

            $table->boolean('consent_collected')->default(false)
                  ->comment('Set true at check_in if secretariat confirms form received.');

            $table->text('notes')->nullable()
                  ->comment('e.g. early departure reason');

            $table->timestamps();

            $table->index(['camper_id', 'event_type']);
            $table->index('scanned_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkin_events');
        Schema::dropIfExists('programme_sessions');
    }
};
