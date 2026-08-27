<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One header row per (student's level_attempt, weekly_plan). Draft/Approved/Reopened.
        Schema::create('student_weekly_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_attempt_id')->constrained('level_attempts');
            $table->foreignId('weekly_plan_id')->constrained('weekly_plans');
            $table->foreignId('class_id')->constrained('classes'); // snapshot at entry time
            $table->decimal('total_score', 6, 2)->default(0); // system-maintained
            $table->decimal('max_score', 6, 2)->default(0);   // system-maintained
            $table->text('notes')->nullable();
            $table->enum('status', ['draft', 'approved', 'reopened'])->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();

$table->    unique(['level_attempt_id', 'weekly_plan_id'], 'uq_attempt_plan');            $table->index('weekly_plan_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_weekly_evaluations');
    }
};
