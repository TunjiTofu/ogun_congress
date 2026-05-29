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
        Schema::create('camp_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('camper_id')->nullable()->constrained('campers')->nullOnDelete();
            $table->foreignId('district_id')->nullable()->constrained('districts')->nullOnDelete();
            $table->string('title')->nullable();
            $table->string('caption')->nullable();
            $table->enum('media_type', ['image', 'video'])->default('image');
            $table->enum('category', ['official', 'camper'])->default('camper');
            $table->string('cloudinary_public_id');
            $table->string('cloudinary_url');
            $table->string('thumbnail_url')->nullable();   // for videos
            $table->unsignedInteger('file_size')->nullable(); // bytes
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->string('congress_year', 4)->default(date('Y'));
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('rejection_reason')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('camp_media');
    }
};
