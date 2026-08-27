<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('class_supervisor_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('supervisor_id')->constrained('users'); // role_type = supervisor
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamps();

            // A class may (per the source brief) have more than one active supervisor over time
            // and this is a plain M:N-with-history table, so no single-active-row constraint here.
            $table->index(['class_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('class_supervisor_assignments');
    }
};
