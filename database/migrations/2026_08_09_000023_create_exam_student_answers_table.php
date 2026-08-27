<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_student_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_student_attempt_id')->constrained('exam_student_attempts');
            $table->foreignId('exam_question_id')->constrained('exam_questions');
            $table->foreignId('selected_option_id')->nullable()->constrained('exam_question_options');
            $table->boolean('is_correct')->nullable();   // computed at grading time
            $table->decimal('score_earned', 5, 2)->nullable(); // computed at grading time
            $table->timestamps();

            $table->unique(['exam_student_attempt_id', 'exam_question_id'], 'uq_answer_per_question_per_attempt');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_student_answers');
    }
};
