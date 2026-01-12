<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('name', 20);
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->unique(['academic_year_id', 'name']);
        });

        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('grade_level');
            $table->string('major')->nullable();
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('teachers')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name');
            $table->unsignedTinyInteger('passing_grade')->default(75);
            $table->timestamps();
        });

        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 30)->unique();
            $table->string('nisn', 30)->unique();
            $table->string('full_name');
            $table->enum('gender', ['male', 'female']);
            $table->date('birth_date')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('guardian_primary_id')->nullable()->constrained('guardians')->cascadeOnUpdate()->nullOnDelete();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->index('class_id');
        });

        Schema::create('student_guardian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('guardian_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('relation', 50)->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'guardian_id']);
        });

        Schema::create('class_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->cascadeOnUpdate()->nullOnDelete();
            $table->timestamps();
            $table->unique(['class_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_subjects');
        Schema::dropIfExists('student_guardian');
        Schema::dropIfExists('students');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classes');
        Schema::dropIfExists('semesters');
        Schema::dropIfExists('academic_years');
    }
};
