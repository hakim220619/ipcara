<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Instrumen tes: pre-test, latihan, remedial, post-test.
 * topic_id null berarti tes mencakup semua topik (pre-test / post-test umum).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('kind', ['pre_test', 'latihan', 'remedial', 'post_test']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedTinyInteger('passing_score')->default(70);
            $table->unsignedTinyInteger('max_attempts')->nullable();
            $table->boolean('shuffle_questions')->default(true);
            $table->boolean('show_result_immediately')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['kind', 'is_active']);
        });

        Schema::create('test_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['test_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_questions');
        Schema::dropIfExists('tests');
    }
};
