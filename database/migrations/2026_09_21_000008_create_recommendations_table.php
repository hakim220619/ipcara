<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rekomendasi materi yang dihasilkan sistem untuk tiap mahasiswa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ability_assessment_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('priority')->default(1);
            $table->string('reason')->nullable();
            $table->enum('status', ['pending', 'viewed', 'completed', 'skipped'])->default('pending');
            $table->timestamp('viewed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'material_id', 'ability_assessment_id'], 'recommendations_unique');
            $table->index(['user_id', 'status', 'priority']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
