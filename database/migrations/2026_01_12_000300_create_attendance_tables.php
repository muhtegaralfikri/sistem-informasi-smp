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
        Schema::create('attendance_sheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('subject_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('semester_id')->nullable()->constrained()->cascadeOnUpdate()->nullOnDelete();
            $table->date('date');
            $table->string('session', 30)->default('1');
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->index(['class_id', 'date']);
            $table->index(['subject_id', 'date']);
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_sheet_id')->constrained('attendance_sheets')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('student_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('status', ['hadir', 'izin', 'sakit', 'alfa'])->default('hadir');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['attendance_sheet_id', 'student_id']);
            $table->index(['student_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('attendance_sheets');
    }
};
