<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Replaces quran_score/hadith_score-style fixed columns.
        Schema::create('student_evaluation_item_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_weekly_evaluation_id')
                ->constrained('student_weekly_evaluations', 'id', 'fk_item_score_eval');
            $table->foreignId('weekly_plan_evaluation_item_id')
                ->constrained('weekly_plan_evaluation_items', 'id', 'fk_item_score_plan_item');
            $table->decimal('score', 5, 2)->nullable();
            $table->enum('attendance_status', ['present', 'absent', 'excused', 'other'])->nullable();
            $table->string('excuse_note', 300)->nullable();
            $table->timestamps();

            $table->unique(
                ['student_weekly_evaluation_id', 'weekly_plan_evaluation_item_id'],
                'uq_score_per_item_per_evaluation'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_evaluation_item_scores');
    }
};
