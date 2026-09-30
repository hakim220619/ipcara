<?php

namespace App\Services;

use App\Models\AttemptAnswer;
use App\Models\LearningActivity;
use App\Models\TestAttempt;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Penilaian post-test dengan rumus yang sama seperti pre-test agar hasilnya sebanding:
 * nilai per topik = benar / jumlah soal topik × 100, nilai akhir = rata-rata nilai topik.
 * Post-test tidak mengubah penilaian kemampuan maupun rekomendasi materi.
 */
class PostTestGrader
{
    /**
     * @param  Collection<int|string, mixed>  $answers  question_id => question_option_id
     */
    public function grade(TestAttempt $attempt, Collection $answers, ?CarbonInterface $finishedAt = null): TestAttempt
    {
        $questions = $attempt->test->questions()
            ->where('is_active', true)
            ->with('options:id,question_id,is_correct')
            ->get();

        return DB::transaction(function () use ($questions, $answers, $attempt, $finishedAt) {
            $now = $finishedAt ?? now();
            $perTopic = [];
            $correct = 0;

            foreach ($questions as $question) {
                $option = $question->options->firstWhere('id', (int) $answers->get($question->id));
                $isCorrect = (bool) $option?->is_correct;

                AttemptAnswer::query()->updateOrCreate(
                    ['test_attempt_id' => $attempt->id, 'question_id' => $question->id],
                    [
                        'question_option_id' => $option?->id,
                        'is_correct' => $isCorrect,
                        'answered_at' => $option ? $now : null,
                    ],
                );

                $perTopic[$question->topic_id] ??= ['correct' => 0, 'total' => 0];
                $perTopic[$question->topic_id]['total']++;
                $perTopic[$question->topic_id]['correct'] += (int) $isCorrect;
                $correct += (int) $isCorrect;
            }

            $topicScores = array_map(fn (array $t) => $t['correct'] / $t['total'] * 100, $perTopic);
            $percent = $topicScores ? round(array_sum($topicScores) / count($topicScores), 2) : 0;

            $attempt->update([
                'status' => 'completed',
                'total_questions' => $questions->count(),
                'correct_count' => $correct,
                'score' => $percent,
                'is_passed' => $percent >= $attempt->test->passing_score,
                'finished_at' => $now,
                'duration_seconds' => (int) $attempt->started_at->diffInSeconds($now),
            ]);

            LearningActivity::query()->create([
                'user_id' => $attempt->user_id,
                'activity_type' => 'selesai_post_test',
                'subject_type' => TestAttempt::class,
                'subject_id' => $attempt->id,
                'meta' => ['score' => $percent, 'correct' => $correct, 'total' => $questions->count()],
            ]);

            return $attempt->refresh();
        });
    }
}
