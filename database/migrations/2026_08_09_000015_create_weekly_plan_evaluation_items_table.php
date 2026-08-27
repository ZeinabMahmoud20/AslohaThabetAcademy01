<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // THE table that makes the model dynamic: which items apply to THIS week,
        // with THIS week's max score, order, required flag. No quran_score-style columns.
        Schema::create('weekly_plan_evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_plan_id')->constrained('weekly_plans');
            $table->foreignId('evaluation_item_id')->constrained('evaluation_items');
            $table->decimal('max_score', 5, 2);
            $table->integer('display_order')->default(0);
            $table->boolean('is_required')->default(true);
            $table->timestamps();

            $table->unique(['weekly_plan_id', 'evaluation_item_id'], 'uq_plan_item');        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_plan_evaluation_items');
    }
};
