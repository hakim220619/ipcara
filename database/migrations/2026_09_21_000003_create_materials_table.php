<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Materi belajar: teks, video, atau studi kasus.
 * Kolom `level` dipakai mesin adaptif untuk mencocokkan materi
 * dengan tingkat kemampuan mahasiswa hasil pre-test.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['teks', 'video', 'kasus'])->default('teks');
            $table->enum('level', ['dasar', 'menengah', 'lanjut'])->default('dasar');
            $table->text('summary')->nullable();
            $table->longText('content')->nullable();
            $table->string('video_url')->nullable();
            $table->unsignedSmallInteger('video_duration_seconds')->nullable();
            $table->string('attachment_path')->nullable();
            $table->unsignedSmallInteger('estimated_minutes')->default(10);
            $table->unsignedSmallInteger('order')->default(0);
            $table->boolean('is_published')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['topic_id', 'level', 'order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
