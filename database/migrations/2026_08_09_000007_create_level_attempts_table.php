<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Anchor entity for ALL evaluation/exam/result data. Never the student directly.
        Schema::create('level_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students');
            $table->foreignId('level_id')->constrained('levels');
            $table->unsignedTinyInteger('attempt_number')->default(1);
            $table->date('started_at');
            $table->date('ended_at')->nullable();
            $table->enum('status', ['active', 'completed_passed', 'completed_failed'])->default('active');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['student_id', 'is_active']);
        });

        // Enforce "exactly one active level_attempt per student" at the DB level.
        // MySQL 8 approach: a generated column that collapses to NULL when inactive
        // (NULLs are not compared for uniqueness), then a unique index on it.
        DB::statement(
            'ALTER TABLE level_attempts ADD COLUMN active_student_id BIGINT UNSIGNED
             GENERATED ALWAYS AS (IF(is_active = 1, student_id, NULL)) STORED'
        );
        DB::statement(
            'ALTER TABLE level_attempts ADD UNIQUE INDEX uq_one_active_attempt_per_student (active_student_id)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('level_attempts');
    }
};
