<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bank soal per topik dengan tingkat kesulitan.
 * Soal yang sama bisa dipakai di pre-test, latihan, remedial, maupun post-test
 * melalui tabel pivot test_questions.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('type', ['pilihan_ganda', 'benar_salah'])->default('pilihan_ganda');
            $table->enum('difficulty', ['mudah', 'sedang', 'sulit'])->default('sedang');
            $table->text('question_text');
            $table->string('image_path')->nullable();
            $table->text('explanation')->nullable();
            $table->unsignedTinyInteger('points')->default(1);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['topic_id', 'difficulty', 'is_active']);
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->char('label', 1);
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['question_id', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
    }
};
