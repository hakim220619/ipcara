<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Log aktivitas belajar untuk analisis penelitian
 * (login, buka materi, tonton video, mulai tes, selesai tes, dsb).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('learning_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type', 50);
            $table->nullableMorphs('subject');
            $table->json('meta')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'activity_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_activities');
    }
};
