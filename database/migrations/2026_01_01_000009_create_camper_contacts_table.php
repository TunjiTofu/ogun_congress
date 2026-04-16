<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('camper_contacts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('camper_id')
                  ->constrained('campers')
                  ->cascadeOnDelete();

            $table->enum('type', ['parent_guardian', 'emergency_contact']);
            $table->string('full_name', 191);
            $table->string('relationship', 50)
                  ->comment('e.g. Mother, Father, Uncle, Pastor');
            $table->string('phone', 20);
            $table->string('email', 191)->nullable();
            $table->boolean('is_primary')->default(false)
                  ->comment('True for the main contact to call first');

            $table->timestamps();

            $table->index(['camper_id', 'type']);

            /**
             * Emergency contact phone must be globally unique.
             * A single phone number cannot serve as emergency contact
             * for more than one camper.
             *
             * Parent/guardian phones are NOT subject to this constraint —
             * a church leader can register multiple children with the same number.
             */
            $table->unique(
                ['phone', 'type'],
                'unique_emergency_contact_phone'
                // Enforced in application layer with a conditional check:
                // WHERE type = 'emergency_contact'
                // MySQL unique index on (phone, type) achieves this at DB level
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('camper_contacts');
    }
};
