<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Penugasan remedial: dipicu ketika skor latihan/post-test di bawah KKM.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('remedials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('triggered_by_attempt_id')->nullable()->constrained('test_attempts')->nullOnDelete();
            $table->foreignId('remedial_test_id')->nullable()->constrained('tests')->nullOnDelete();
            $table->enum('status', ['assigned', 'in_progress', 'passed', 'failed'])->default('assigned');
            $table->decimal('trigger_score', 5, 2)->nullable();
            $table->decimal('final_score', 5, 2)->nullable();
            $table->unsignedTinyInteger('attempts_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('assigned_at');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'topic_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('remedials');
    }
};
