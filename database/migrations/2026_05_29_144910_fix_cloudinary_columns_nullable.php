<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camp_media', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable()->change();
            $table->string('cloudinary_url')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('camp_media', function (Blueprint $table) {
            $table->string('cloudinary_public_id')->nullable(false)->change();
            $table->string('cloudinary_url')->nullable(false)->change();
        });
    }
};
