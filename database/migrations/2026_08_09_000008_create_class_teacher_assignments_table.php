<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ONLY source of truth for "current teacher of a class". Never a column on classes.
        Schema::create('class_teacher_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes');
            $table->foreignId('teacher_id')->constrained('users'); // role_type = teacher
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('reason', 300)->nullable();
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamps();

            $table->index(['class_id', 'is_active']);
        });

        // Exactly one active teacher per class.
        DB::statement(
            'ALTER TABLE class_teacher_assignments ADD COLUMN active_class_id BIGINT UNSIGNED
             GENERATED ALWAYS AS (IF(is_active = 1, class_id, NULL)) STORED'
        );
        DB::statement(
            'ALTER TABLE class_teacher_assignments ADD UNIQUE INDEX uq_one_active_teacher_per_class (active_class_id)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('class_teacher_assignments');
    }
};
