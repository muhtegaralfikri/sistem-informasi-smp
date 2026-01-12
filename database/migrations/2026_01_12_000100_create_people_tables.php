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
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('nip', 50)->nullable()->unique();
            $table->string('full_name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });

        Schema::create('guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('full_name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('relation_default', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guardians');
        Schema::dropIfExists('teachers');
    }
};
