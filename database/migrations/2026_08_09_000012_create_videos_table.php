<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('weekly_plan_id')->unique()->constrained('weekly_plans');
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->string('video_url', 500)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->date('publish_date')->nullable();
            $table->enum('status', ['draft', 'published'])->default('draft');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videos');
    }
};
