<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Which class a student's CURRENT level_attempt belongs to.
        // Independent of class_teacher_assignments -- a teacher swap never touches this table.
        Schema::create('student_class_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_attempt_id')->constrained('level_attempts');
            $table->foreignId('class_id')->constrained('classes');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamps();

            $table->index(['level_attempt_id', 'is_active']);
        });

        // Exactly one active class assignment per level_attempt.
        DB::statement(
            'ALTER TABLE student_class_assignments ADD COLUMN active_level_attempt_id BIGINT UNSIGNED
             GENERATED ALWAYS AS (IF(is_active = 1, level_attempt_id, NULL)) STORED'
        );
        DB::statement(
            'ALTER TABLE student_class_assignments ADD UNIQUE INDEX uq_one_active_class_per_attempt (active_level_attempt_id)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('student_class_assignments');
    }
};
