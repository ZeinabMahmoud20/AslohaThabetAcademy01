<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guardian_id')->constrained('guardians');
            $table->string('student_full_name', 150);
            $table->date('student_dob');
            $table->string('country', 80)->nullable();
            $table->string('governorate', 80)->nullable();
            $table->string('school', 150)->nullable();
            $table->string('grade_level', 50)->nullable();
            $table->dateTime('submitted_at')->useCurrent();
            $table->unsignedTinyInteger('computed_age_years');
            $table->boolean('is_age_exception')->default(false);
            $table->string('exception_reason', 500)->nullable();
            $table->foreignId('exception_approved_by')->nullable()->constrained('users');
            $table->dateTime('exception_approved_at')->nullable();
            $table->enum('status', ['pending_review', 'admin_review', 'accepted', 'rejected'])
                  ->default('pending_review');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->dateTime('reviewed_at')->nullable();
            $table->string('rejection_reason', 500)->nullable();
            $table->timestamps();

            $table->index('submitted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
