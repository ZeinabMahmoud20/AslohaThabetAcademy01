<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // No in-system submission storage -- submitted via Telegram, outside the system.
        Schema::create('homeworks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_plan_id')->unique()->constrained('weekly_plans');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homeworks');
    }
};
