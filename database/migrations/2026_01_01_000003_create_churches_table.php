<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('churches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('district_id')
                  ->constrained('districts')
                  ->restrictOnDelete()
                  ->comment('A church must belong to one and only one district');
            $table->string('name', 191);
            $table->string('address', 500)->nullable();
            $table->timestamps();

            // A church name must be unique within its district
            $table->unique(['district_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('churches');
    }
};
