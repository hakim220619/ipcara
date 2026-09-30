<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Latihan per materi berbasis mastery learning.
 * Setiap materi punya indikator kompetensi; setiap indikator punya soal utama, penguatan, dan remedial.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competency_indicators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            // Judul bagian (h2/h3) di konten materi yang membahas indikator ini.
            $table->string('section')->nullable();
            // Materi penguatan singkat yang ditampilkan saat skor 2/3.
            $table->text('reinforcement')->nullable();
            $table->unsignedTinyInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('indicator_id')->nullable()->after('material_id')->constrained('competency_indicators')->nullOnDelete();
            $table->enum('practice_role', ['utama', 'penguatan', 'remedial'])->nullable()->after('indicator_id');
            $table->index(['material_id', 'practice_role']);
        });

        Schema::create('material_practice_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->enum('round', ['utama', 'penguatan', 'remedial']);
            // Percobaan ke-n untuk materi ini (seluruh ronde).
            $table->unsignedSmallInteger('attempt_number');
            $table->enum('status', ['in_progress', 'completed'])->default('in_progress');
            $table->json('question_ids');
            $table->unsignedTinyInteger('total_questions')->default(0);
            $table->unsignedTinyInteger('correct_count')->default(0);
            $table->decimal('score', 5, 2)->nullable();
            $table->enum('outcome', ['mastery', 'penguatan', 'remedial', 'ulang_materi'])->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'material_id', 'status']);
        });

        Schema::create('material_practice_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_practice_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_correct');
            $table->unsignedInteger('time_spent_seconds')->nullable();
            $table->timestamp('answered_at');
            $table->timestamps();

            $table->unique(['material_practice_attempt_id', 'question_id'], 'practice_answer_unique');
        });

        Schema::table('material_progress', function (Blueprint $table) {
            $table->enum('mastery_status', ['belum', 'penguatan', 'remedial', 'mastery'])->default('belum')->after('status');
            $table->unsignedSmallInteger('practice_attempts')->default(0)->after('mastery_status');
            $table->decimal('last_practice_score', 5, 2)->nullable()->after('practice_attempts');
            $table->timestamp('mastered_at')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('material_progress', function (Blueprint $table) {
            $table->dropColumn(['mastery_status', 'practice_attempts', 'last_practice_score', 'mastered_at']);
        });

        Schema::dropIfExists('material_practice_answers');
        Schema::dropIfExists('material_practice_attempts');

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex(['material_id', 'practice_role']);
            $table->dropConstrainedForeignId('indicator_id');
            $table->dropColumn('practice_role');
        });

        Schema::dropIfExists('competency_indicators');
    }
};
