<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campers', function (Blueprint $table) {
            if (! Schema::hasColumn('campers', 'tshirt_size')) {
                $table->string('tshirt_size')->nullable()->after('home_address')
                    ->comment('T-shirt size: XS, S, M, L, XL, XXL, XXXL');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campers', function (Blueprint $table) {
            $table->dropColumn('tshirt_size');
        });
    }
};
