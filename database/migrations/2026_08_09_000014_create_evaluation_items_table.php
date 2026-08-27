<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Master catalog: Attendance, Homework, Qur'an, Hadith, Dhikr, Aqeedah, Commitment, ...
        Schema::create('evaluation_items', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->decimal('default_max_score', 5, 2);
            $table->integer('default_order')->default(0);
            $table->boolean('is_attendance_type')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluation_items');
    }
};
