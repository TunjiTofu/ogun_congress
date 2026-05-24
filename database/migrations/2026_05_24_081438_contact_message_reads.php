<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Per-admin read tracking — replaces the single is_read boolean
        Schema::create('contact_message_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_message_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('read_at')->useCurrent();

            $table->unique(['contact_message_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_message_reads');
    }
};
