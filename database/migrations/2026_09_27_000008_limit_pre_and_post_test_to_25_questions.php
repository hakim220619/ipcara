<?php

use App\Models\Test;
use Illuminate\Database\Migrations\Migration;

/**
 * Pre-test dan post-test dipersingkat menjadi 25 soal (5 per topik: 2 mudah, 2 sedang, 1 sulit).
 * Soal yang tidak terpilih tetap tersimpan di bank soal.
 */
return new class extends Migration
{
    public function up(): void
    {
        Test::query()
            ->whereIn('kind', [Test::KIND_PRE_TEST, Test::KIND_POST_TEST])
            ->get()
            ->each(fn (Test $test) => $test->keepBalancedQuestions());
    }

    public function down(): void
    {
        // Soal yang dilepas dapat dimasukkan kembali melalui Bank Soal.
    }
};
