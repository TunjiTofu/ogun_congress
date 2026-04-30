<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The original unique(['phone','type']) constraint blocks a parent from
 * registering two children with the same phone number, which is a valid
 * and common scenario. Drop it.
 *
 * Emergency-contact phone uniqueness is enforced at the application level
 * (in SubmitRegistrationRequest) where we can check only the emergency_contact
 * type, leaving parent_guardian phones unconstrained.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('camper_contacts', function (Blueprint $table) {
            $table->dropUnique('unique_emergency_contact_phone');
        });
    }

    public function down(): void
    {
        Schema::table('camper_contacts', function (Blueprint $table) {
            $table->unique(['phone', 'type'], 'unique_emergency_contact_phone');
        });
    }
};
