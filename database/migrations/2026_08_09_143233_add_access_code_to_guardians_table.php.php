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
        Schema::table('guardians', function (Blueprint $table) {
            //
                        // Hashed access code (never store plaintext). The Admin generates a
            // fresh one and hands it to the guardian out-of-band (phone/WhatsApp);
            // it is shown to the Admin exactly once at generation time.
            $table->string('access_code')->nullable()->after('phone_number');
            $table->timestamp('access_code_generated_at')->nullable()->after('access_code');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guardians', function (Blueprint $table) {
            //
            $table->dropColumn(['access_code', 'access_code_generated_at']);

        });
    }
};
