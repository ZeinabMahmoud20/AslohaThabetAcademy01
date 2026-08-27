<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Scoped to a Level, shared by every class within that level.
        Schema::create('weekly_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('level_id')->constrained('levels');
            $table->unsignedSmallInteger('week_number');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['draft', 'published', 'closed'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->unique(['level_id', 'week_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_plans');
    }
};
