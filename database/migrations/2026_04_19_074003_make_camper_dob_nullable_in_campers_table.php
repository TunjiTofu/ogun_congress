<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campers', function (Blueprint $table) {
            // date_of_birth is not collected for coordinator batch registrations
            // and is no longer required in the registration form
            $table->date('date_of_birth')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('campers', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable(false)->change();
        });
    }
};
