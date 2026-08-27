<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('final_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_attempt_id')->unique()->constrained('level_attempts');
            $table->decimal('weekly_component', 5, 2);   // (earned/max weekly) * 70
            $table->decimal('exam_component', 5, 2);     // (earned/max exam) * 30
            $table->decimal('final_score', 5, 2);
            $table->enum('pass_fail', ['passed', 'failed']);
            // grade_label is a point-in-time snapshot, not just an FK, so a later change
            // to grade_scales thresholds never silently rewrites history.
            $table->string('grade_label', 50);
            $table->foreignId('grade_scale_id')->nullable()->constrained('grade_scales');
            $table->enum('status', ['calculated', 'teacher_approved', 'admin_approved', 'final'])
                  ->default('calculated');
            $table->foreignId('teacher_approved_by')->nullable()->constrained('users');
            $table->dateTime('teacher_approved_at')->nullable();
            $table->foreignId('admin_approved_by')->nullable()->constrained('users');
            $table->dateTime('admin_approved_at')->nullable();
            $table->dateTime('calculated_at')->useCurrent();
            $table->timestamps();

            $table->index('final_score');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('final_results');
    }
};
