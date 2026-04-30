<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('badge_color_config', function (Blueprint $table) {
            $table->id();
            $table->enum('category', ['adventurer', 'pathfinder', 'senior_youth'])->unique();
            $table->string('color_hex', 7)
                  ->comment('CSS hex colour e.g. #1B3A6B');
            $table->string('label', 100)
                  ->comment('Display label e.g. Blue — Adventurers');
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_phone', 20);
            $table->enum('channel', ['sms', 'whatsapp'])->default('sms');
            $table->text('message');
            $table->string('trigger', 100)
                  ->comment('e.g. payment_confirmed, registration_complete, reminder_7day');
            $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
            $table->text('provider_response')->nullable()
                  ->comment('Raw response from SMS provider for debugging');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();

            $table->index(['recipient_phone', 'trigger']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('badge_color_config');
    }
};
