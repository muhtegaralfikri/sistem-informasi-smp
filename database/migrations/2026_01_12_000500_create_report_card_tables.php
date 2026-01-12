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
        Schema::create('report_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('semester_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->enum('status', ['draft', 'approved', 'published'])->default('draft');
            $table->decimal('total_score', 8, 2)->nullable();
            $table->decimal('average_score', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->cascadeOnUpdate()->nullOnDelete();
            $table->string('pdf_path')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->unique(['student_id', 'semester_id']);
        });

        Schema::create('report_card_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_card_id')->constrained('report_cards')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->string('predicate', 5)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['report_card_id', 'subject_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_card_items');
        Schema::dropIfExists('report_cards');
    }
};
