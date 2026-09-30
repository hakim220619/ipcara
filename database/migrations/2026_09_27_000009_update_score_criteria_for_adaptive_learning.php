<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Aturan adaptive learning berbasis pre-test:
 *  ≥80%   → sudah dikuasai, tidak menjadi prioritas.
 *  60–79% → perlu penguatan.
 *  <60%   → perlu dipelajari.
 */
return new class extends Migration
{
    private const CRITERIA = [
        'rendah' => [
            'label' => 'Perlu dipelajari', 'min_score' => 0, 'max_score' => 59.99,
            'recommended_material_level' => 'dasar', 'is_recommended' => true,
            'description' => 'Nilai di bawah 60%. Topik menjadi prioritas utama dan dipelajari dari materi dasar.',
        ],
        'sedang' => [
            'label' => 'Perlu penguatan', 'min_score' => 60, 'max_score' => 79.99,
            'recommended_material_level' => 'menengah', 'is_recommended' => true,
            'description' => 'Nilai 60–79%. Dasar sudah dipahami, perlu penguatan pada materi menengah.',
        ],
        'tinggi' => [
            'label' => 'Sudah dikuasai', 'min_score' => 80, 'max_score' => 100,
            'recommended_material_level' => 'lanjut', 'is_recommended' => false,
            'description' => 'Nilai 80% ke atas. Topik sudah dikuasai sehingga tidak menjadi prioritas.',
        ],
    ];

    public function up(): void
    {
        foreach (self::CRITERIA as $level => $values) {
            DB::table('score_criteria')->updateOrInsert(
                ['level' => $level],
                [...$values, 'updated_at' => now(), 'created_at' => now()],
            );
        }
    }

    public function down(): void
    {
        $labels = ['rendah' => 'Rendah', 'sedang' => 'Sedang', 'tinggi' => 'Tinggi'];

        foreach ($labels as $level => $label) {
            DB::table('score_criteria')->where('level', $level)->update(['label' => $label]);
        }
    }
};
