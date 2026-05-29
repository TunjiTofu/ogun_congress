<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('youtube_highlights', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('youtube_id');           // e.g. dQw4w9WgXcQ
            $table->string('thumbnail_url')->nullable();
            $table->enum('phase', ['before', 'during', 'after'])->default('before');
            $table->string('duration_label')->nullable(); // e.g. "2:14"
            $table->string('eyebrow')->nullable();        // e.g. "Official trailer · 2026"
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('youtube_highlights');
    }
};
