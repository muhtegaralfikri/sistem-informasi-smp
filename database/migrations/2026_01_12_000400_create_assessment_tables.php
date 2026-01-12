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
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_subject_id')->constrained('class_subjects')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->enum('type', ['tugas', 'uh', 'uts', 'uas']);
            $table->string('title');
            $table->unsignedTinyInteger('weight')->default(0);
            $table->unsignedSmallInteger('max_score')->default(100);
            $table->date('due_date')->nullable();
            $table->timestamps();
        });

        Schema::create('grade_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained('assessments')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('score', 5, 2)->unsigned()->default(0);
            $table->timestamps();
            $table->unique(['assessment_id', 'student_id']);
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grade_entries');
        Schema::dropIfExists('assessments');
    }
};
