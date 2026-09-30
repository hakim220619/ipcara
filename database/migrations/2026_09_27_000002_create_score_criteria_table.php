<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('score_criteria', function (Blueprint $table) {
            $table->id();
            $table->enum('level', ['rendah', 'sedang', 'tinggi'])->unique();
            $table->string('label', 50);
            $table->decimal('min_score', 5, 2);
            $table->decimal('max_score', 5, 2);
            $table->enum('recommended_material_level', ['dasar', 'menengah', 'lanjut']);
            $table->boolean('is_recommended')->default(true);
            $table->string('description')->nullable();
            $table->timestamps();
        });

        $now = now();

        DB::table('score_criteria')->insert([
            [
                'level' => 'rendah', 'label' => 'Rendah', 'min_score' => 0, 'max_score' => 59.99,
                'recommended_material_level' => 'dasar', 'is_recommended' => true,
                'description' => 'Perlu mempelajari materi dasar terlebih dahulu.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'level' => 'sedang', 'label' => 'Sedang', 'min_score' => 60, 'max_score' => 79.99,
                'recommended_material_level' => 'menengah', 'is_recommended' => true,
                'description' => 'Sudah memahami dasar, perlu penguatan pada materi menengah.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'level' => 'tinggi', 'label' => 'Tinggi', 'min_score' => 80, 'max_score' => 100,
                'recommended_material_level' => 'lanjut', 'is_recommended' => false,
                'description' => 'Sudah menguasai topik dengan baik.',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('score_criteria');
    }
};
