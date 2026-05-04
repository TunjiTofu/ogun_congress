<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('checkin_events');

        Schema::create('checkin_events', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->comment('Client-generated UUID for idempotency');
            $table->foreignId('camper_id')->constrained()->cascadeOnDelete();
            $table->string('event_type')->default('check_in')->comment('check_in|check_out|programme_attendance');
            $table->foreignId('programme_session_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamp('occurred_at');
            $table->string('device_id')->nullable()->comment('PWA device identifier');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['camper_id', 'event_type']);
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checkin_events');
    }
};
