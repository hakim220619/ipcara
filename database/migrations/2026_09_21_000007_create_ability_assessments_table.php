<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hasil analisis kemampuan mahasiswa per topik.
 * Dihitung dari pre-test (source = pre_test) dan diperbarui setelah
 * latihan / remedial / post-test sehingga riwayat perkembangan tersimpan.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ability_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_attempt_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('source', ['pre_test', 'latihan', 'remedial', 'post_test']);
            $table->decimal('score', 5, 2);
            $table->enum('level', ['rendah', 'sedang', 'tinggi']);
            $table->enum('recommended_material_level', ['dasar', 'menengah', 'lanjut']);
            $table->json('detail')->nullable();
            $table->timestamp('assessed_at');
            $table->timestamps();

            $table->index(['user_id', 'topic_id', 'assessed_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ability_assessments');
    }
};
