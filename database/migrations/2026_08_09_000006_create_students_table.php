<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained('users');
            $table->foreignId('guardian_id')->constrained('guardians');
            $table->foreignId('application_id')->unique()->constrained('applications');
            $table->string('full_name', 150);
            $table->date('dob');
            $table->string('country', 80)->nullable();
            $table->string('governorate', 80)->nullable();
            $table->string('school', 150)->nullable();
            $table->string('grade_level', 50)->nullable();
            $table->string('academy_student_code', 30)->nullable()->unique();
            $table->enum('status', ['active', 'graduated', 'withdrawn'])->default('active');
            $table->timestamps();

            $table->index('full_name');
        });

        // Now that students exists, add the back-reference from applications.
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('student_id')->nullable()->unique()->after('id')
                  ->constrained('students');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('student_id');
        });
        Schema::dropIfExists('students');
    }
};
