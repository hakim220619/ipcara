<?php

namespace App\Services;

use App\Models\LearningActivity;
use App\Models\Material;
use App\Models\MaterialPracticeAnswer;
use App\Models\MaterialPracticeAttempt;
use App\Models\MaterialProgress;
use App\Models\Question;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Latihan per materi dengan pendekatan mastery learning.
 *
 * Ronde utama (3 soal, satu per indikator):
 *   3/3 → mastery · 2/3 → penguatan (2 soal) · 0–1/3 → remedial (3 soal).
 * Ronde penguatan (2 soal): 2/2 → mastery · selain itu → remedial.
 * Ronde remedial (3 soal): 3/3 → mastery · 2/3 → penguatan · 0–1/3 → baca ulang materi lalu ronde utama.
 *
 * Soal tiap ronde memakai varian berbeda dari ronde sebelumnya tetapi menguji indikator yang sama.
 * Hasil latihan disimpan terpisah dan tidak memengaruhi nilai pre-test maupun post-test.
 */
class MasteryPractice
{
    public const QUESTION_COUNT = ['utama' => 3, 'penguatan' => 2, 'remedial' => 3];

    public static function hasPractice(Material $material): bool
    {
        return $material->practiceQuestions()->where('practice_role', 'utama')->exists();
    }

    public function current(User $user, Material $material): ?MaterialPracticeAttempt
    {
        return MaterialPracticeAttempt::query()
            ->where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->where('status', 'in_progress')
            ->latest('id')
            ->first();
    }

    public function latestCompleted(User $user, Material $material): ?MaterialPracticeAttempt
    {
        return MaterialPracticeAttempt::query()
            ->where('user_id', $user->id)
            ->where('material_id', $material->id)
            ->where('status', 'completed')
            ->latest('id')
            ->first();
    }

    /**
     * Ronde berikutnya ditentukan oleh hasil ronde terakhir.
     */
    public function nextRound(User $user, Material $material): string
    {
        return match ($this->latestCompleted($user, $material)?->outcome) {
            'penguatan' => 'penguatan',
            'remedial' => 'remedial',
            default => 'utama',
        };
    }

    /**
     * Mulai ronde berikutnya, atau lanjutkan ronde yang sedang berjalan.
     */
    public function start(User $user, Material $material): MaterialPracticeAttempt
    {
        if ($current = $this->current($user, $material)) {
            return $current;
        }

        $round = $this->nextRound($user, $material);
        $questions = $this->pickQuestions($material, $round, $this->missedIndicatorIds($user, $material));

        return MaterialPracticeAttempt::query()->create([
            'user_id' => $user->id,
            'material_id' => $material->id,
            'round' => $round,
            'attempt_number' => MaterialPracticeAttempt::query()
                ->where('user_id', $user->id)
                ->where('material_id', $material->id)
                ->count() + 1,
            'question_ids' => $questions->pluck('id')->all(),
            'total_questions' => $questions->count(),
            'started_at' => now(),
        ]);
    }

    /**
     * Simpan jawaban satu soal. Soal yang sudah dijawab tidak dapat diubah.
     */
    public function answer(MaterialPracticeAttempt $attempt, int $questionId, int $optionId, ?int $timeSpent = null): ?MaterialPracticeAnswer
    {
        if (! in_array($questionId, $attempt->question_ids, true)
            || $attempt->answers()->where('question_id', $questionId)->exists()) {
            return null;
        }

        $option = Question::query()->find($questionId)?->options()->whereKey($optionId)->first();

        if (! $option) {
            return null;
        }

        return $attempt->answers()->create([
            'user_id' => $attempt->user_id,
            'material_id' => $attempt->material_id,
            'question_id' => $questionId,
            'question_option_id' => $option->id,
            'is_correct' => $option->is_correct,
            'time_spent_seconds' => $timeSpent !== null ? max(0, min($timeSpent, 3600)) : null,
            'answered_at' => now(),
        ]);
    }

    public function isComplete(MaterialPracticeAttempt $attempt): bool
    {
        return $attempt->answers()->count() >= count($attempt->question_ids);
    }

    public function finish(MaterialPracticeAttempt $attempt): MaterialPracticeAttempt
    {
        return DB::transaction(function () use ($attempt) {
            $now = now();
            $total = count($attempt->question_ids);
            $correct = $attempt->answers()->where('is_correct', true)->count();
            $score = $total > 0 ? round($correct / $total * 100, 2) : 0;
            $outcome = $this->outcome($attempt->round, $correct, $total);

            $attempt->update([
                'status' => 'completed',
                'total_questions' => $total,
                'correct_count' => $correct,
                'score' => $score,
                'outcome' => $outcome,
                'finished_at' => $now,
                'duration_seconds' => (int) $attempt->started_at->diffInSeconds($now),
            ]);

            $progress = MaterialProgress::query()->firstOrCreate(
                ['user_id' => $attempt->user_id, 'material_id' => $attempt->material_id],
                ['status' => 'selesai', 'progress_percent' => 100, 'started_at' => $attempt->started_at, 'completed_at' => $now],
            );

            // Status mastery tidak turun lagi setelah tercapai; latihan ulang sesudahnya hanya sebagai pengayaan.
            $masteryStatus = $progress->mastery_status === 'mastery' || $outcome === 'mastery'
                ? 'mastery'
                : ($outcome === 'penguatan' ? 'penguatan' : 'remedial');

            $progress->update([
                'mastery_status' => $masteryStatus,
                'practice_attempts' => MaterialPracticeAttempt::query()
                    ->where('user_id', $attempt->user_id)
                    ->where('material_id', $attempt->material_id)
                    ->where('status', 'completed')
                    ->count(),
                'last_practice_score' => $score,
                'mastered_at' => $progress->mastered_at ?? ($masteryStatus === 'mastery' ? $now : null),
            ]);

            LearningActivity::query()->create([
                'user_id' => $attempt->user_id,
                'activity_type' => 'selesai_latihan_materi',
                'subject_type' => MaterialPracticeAttempt::class,
                'subject_id' => $attempt->id,
                'meta' => [
                    'material_id' => $attempt->material_id,
                    'round' => $attempt->round,
                    'score' => $score,
                    'correct' => $correct,
                    'total' => $total,
                    'outcome' => $outcome,
                ],
            ]);

            return $attempt->refresh();
        });
    }

    public function outcome(string $round, int $correct, int $total): string
    {
        if ($total > 0 && $correct === $total) {
            return 'mastery';
        }

        return match ($round) {
            'utama' => $correct === $total - 1 ? 'penguatan' : 'remedial',
            'penguatan' => 'remedial',
            default => $correct === $total - 1 ? 'penguatan' : 'ulang_materi',
        };
    }

    /**
     * Indikator yang dijawab salah pada ronde terakhir.
     *
     * @return Collection<int, int>
     */
    public function missedIndicatorIds(User $user, Material $material, ?MaterialPracticeAttempt $attempt = null): Collection
    {
        $attempt ??= $this->latestCompleted($user, $material);

        if (! $attempt) {
            return collect();
        }

        return Question::query()
            ->whereIn('id', $attempt->answers()->where('is_correct', false)->select('question_id'))
            ->pluck('indicator_id')
            ->filter()
            ->unique()
            ->values();
    }

    /**
     * @param  Collection<int, int>  $focus  indikator yang diprioritaskan (belum dikuasai)
     * @return Collection<int, Question>
     */
    private function pickQuestions(Material $material, string $round, Collection $focus): Collection
    {
        $pool = $material->practiceQuestions()
            ->where('practice_role', $round)
            ->with('indicator:id,order')
            ->get()
            ->sortBy(fn (Question $q) => [$q->indicator?->order ?? 99, $q->id])
            ->values();

        if ($round === 'penguatan') {
            // Utamakan indikator yang belum dikuasai, lalu lengkapi dengan indikator lain.
            $pool = $pool->sortBy(fn (Question $q) => [$focus->contains($q->indicator_id) ? 0 : 1, $q->indicator?->order ?? 99])->values();
        }

        return $pool->unique('indicator_id')->take(self::QUESTION_COUNT[$round])->values();
    }
}
