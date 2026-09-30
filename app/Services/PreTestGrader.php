<?php

namespace App\Services;

use App\Models\AbilityAssessment;
use App\Models\AttemptAnswer;
use App\Models\LearningActivity;
use App\Models\Material;
use App\Models\Recommendation;
use App\Models\ScoreCriterion;
use App\Models\TestAttempt;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Penilaian pre-test dua tingkat:
 *  1. Nilai per topik (0–100) = benar / jumlah soal topik × 100.
 *  2. Total nilai = jumlah nilai semua topik (maks. jumlah topik × 100),
 *     persentase keseluruhan = total nilai / nilai maksimum × 100.
 */
class PreTestGrader
{
    /**
     * Nilai jawaban pre-test, simpan penilaian per topik, dan susun rekomendasi materi.
     *
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

                $perTopic[$question->topic_id] ??= ['correct' => 0, 'total' => 0, 'answered' => 0];
                $perTopic[$question->topic_id]['total']++;
                $perTopic[$question->topic_id]['correct'] += (int) $isCorrect;
                $perTopic[$question->topic_id]['answered'] += (int) ($option !== null);
            }

            $perTopic = array_map(
                fn (array $tally) => [...$tally, 'score' => round($tally['correct'] / $tally['total'] * 100, 2)],
                $perTopic,
            );

            $this->applyTopicScores($attempt, $perTopic, $now);

            $attempt->update(['duration_seconds' => (int) $attempt->started_at->diffInSeconds($now)]);

            LearningActivity::query()->create([
                'user_id' => $attempt->user_id,
                'activity_type' => 'selesai_pre_test',
                'subject_type' => TestAttempt::class,
                'subject_id' => $attempt->id,
                'meta' => [
                    'score' => (float) $attempt->score,
                    'correct' => $attempt->correct_count,
                    'total' => $attempt->total_questions,
                ],
            ]);

            return $attempt;
        });
    }

    /**
     * Simpan nilai per topik, hitung total nilai & persentase keseluruhan, lalu susun rekomendasi.
     * Dipakai oleh pengerjaan pre-test maupun input nilai manual oleh admin.
     *
     * @param  array<int, array{score: float|int, correct?: int, total?: int, answered?: int}>  $topicScores  topic_id => nilai
     */
    public function applyTopicScores(TestAttempt $attempt, array $topicScores, CarbonInterface $finishedAt): TestAttempt
    {
        return DB::transaction(function () use ($attempt, $topicScores, $finishedAt) {
            $this->clearAssessments($attempt);

            $weakTopics = [];
            $correct = 0;
            $total = 0;

            foreach ($topicScores as $topicId => $row) {
                $topicScore = round(min(100, max(0, (float) $row['score'])), 2);
                $classification = AbilityAssessment::classify($topicScore);
                $correct += (int) ($row['correct'] ?? 0);
                $total += (int) ($row['total'] ?? 0);

                $assessment = AbilityAssessment::query()->create([
                    'user_id' => $attempt->user_id,
                    'topic_id' => $topicId,
                    'test_attempt_id' => $attempt->id,
                    'source' => 'pre_test',
                    'score' => $topicScore,
                    'level' => $classification['level'],
                    'recommended_material_level' => $classification['recommended_material_level'],
                    'detail' => [
                        'correct' => $row['correct'] ?? null,
                        'total' => $row['total'] ?? null,
                        'answered' => $row['answered'] ?? null,
                        'recommended' => $classification['recommended'],
                    ],
                    'assessed_at' => $finishedAt,
                ]);

                if ($classification['recommended']) {
                    $weakTopics[] = $assessment;
                }
            }

            $maxPoints = count($topicScores) * 100;
            $totalPoints = array_sum(array_map(fn ($row) => round((float) $row['score'], 2), $topicScores));
            $percent = $maxPoints > 0 ? round($totalPoints / $maxPoints * 100, 2) : 0;

            $attempt->update([
                'status' => 'completed',
                'total_questions' => $total,
                'correct_count' => $correct,
                'score' => $percent,
                'is_passed' => $percent >= $attempt->test->passing_score,
                'finished_at' => $finishedAt,
            ]);

            // Prioritas: "perlu dipelajari" (<60%) lebih dulu, lalu "perlu penguatan" (60–79%), masing-masing dari nilai terendah.
            usort($weakTopics, fn ($a, $b) => (float) $a->score <=> (float) $b->score);

            foreach ($weakTopics as $priority => $assessment) {
                $material = Material::query()
                    ->where('topic_id', $assessment->topic_id)
                    ->where('is_published', true)
                    ->where('level', $assessment->recommended_material_level)
                    ->orderBy('order')
                    ->first();

                if (! $material) {
                    continue;
                }

                Recommendation::query()->create([
                    'user_id' => $attempt->user_id,
                    'topic_id' => $assessment->topic_id,
                    'material_id' => $material->id,
                    'ability_assessment_id' => $assessment->id,
                    'priority' => $priority + 1,
                    'reason' => (ScoreCriterion::forScore((float) $assessment->score)?->label ?? 'Perlu dipelajari')
                        .' · nilai pre-test '.round((float) $assessment->score).'%',
                ]);
            }

            return $attempt->refresh();
        });
    }

    /**
     * Hapus hasil pre-test beserta penilaian dan rekomendasi turunannya.
     */
    public function discard(TestAttempt $attempt): void
    {
        DB::transaction(function () use ($attempt) {
            $this->clearAssessments($attempt);

            LearningActivity::query()
                ->where('subject_type', TestAttempt::class)
                ->where('subject_id', $attempt->id)
                ->delete();

            $attempt->delete();
        });
    }

    private function clearAssessments(TestAttempt $attempt): void
    {
        $assessmentIds = $attempt->abilityAssessments()->pluck('id');

        Recommendation::query()->whereIn('ability_assessment_id', $assessmentIds)->delete();
        AbilityAssessment::query()->whereIn('id', $assessmentIds)->delete();
    }
}
