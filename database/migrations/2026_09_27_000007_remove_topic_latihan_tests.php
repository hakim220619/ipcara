<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Latihan per topik digantikan latihan per materi (mastery learning).
 * Hapus tes latihan per topik beserta soal yang hanya dipakai di latihan tersebut.
 */
return new class extends Migration
{
    public function up(): void
    {
        $testIds = DB::table('tests')->where('kind', 'latihan')->pluck('id');

        if ($testIds->isEmpty()) {
            return;
        }

        $questionIds = DB::table('test_questions')
            ->whereIn('test_id', $testIds)
            ->whereNotIn('question_id', DB::table('test_questions')->whereNotIn('test_id', $testIds)->select('question_id'))
            ->pluck('question_id');

        $attemptIds = DB::table('test_attempts')->whereIn('test_id', $testIds)->pluck('id');

        DB::table('remedials')->whereIn('triggered_by_attempt_id', $attemptIds)->delete();
        DB::table('learning_activities')->where('activity_type', 'selesai_latihan')->delete();
        DB::table('tests')->whereIn('id', $testIds)->delete();
        DB::table('questions')->whereIn('id', $questionIds)->delete();
    }

    public function down(): void
    {
        // Data latihan per topik tidak dipulihkan.
    }
};
