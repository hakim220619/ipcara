<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

#[Fillable([
    'user_id', 'test_id', 'attempt_number', 'status', 'total_questions',
    'correct_count', 'score', 'is_passed', 'started_at', 'finished_at',
    'duration_seconds',
])]
class TestAttempt extends Model
{
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'is_passed' => 'boolean',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(AttemptAnswer::class);
    }

    public function abilityAssessments(): HasMany
    {
        return $this->hasMany(AbilityAssessment::class);
    }

    /**
     * Ringkasan penilaian: nilai per topik, total nilai, dan persentase keseluruhan.
     * Membutuhkan relasi abilityAssessments.topic yang sudah dimuat.
     *
     * @return array{topics: list<array<string, mixed>>, total_points: float, max_points: int, percent: float}
     */
    public function scoreSummary(): array
    {
        $topics = $this->abilityAssessments
            ->sortBy(fn (AbilityAssessment $a) => $a->topic?->order)
            ->values()
            ->map(fn (AbilityAssessment $a) => [
                'id' => $a->topic_id,
                'code' => $a->topic?->code,
                'title' => $a->topic?->title ?? 'Topik terhapus',
                'correct' => $a->detail['correct'] ?? null,
                'total' => $a->detail['total'] ?? null,
                'score' => (float) $a->score,
                'percent' => (float) $a->score,
                'level' => $a->level,
                'recommended' => $a->isRecommended(),
            ])
            ->all();

        $maxPoints = count($topics) * 100;
        $totalPoints = round(array_sum(array_column($topics, 'score')), 2);

        return [
            'topics' => $topics,
            'total_points' => $totalPoints,
            'max_points' => $maxPoints,
            'percent' => $maxPoints > 0 ? round($totalPoints / $maxPoints * 100, 2) : (float) $this->score,
        ];
    }

    /**
     * Nilai per topik dihitung dari jawaban (dipakai post-test yang tidak menyimpan penilaian kemampuan).
     *
     * @return Collection<int, array{correct: int, total: int, score: float}>
     */
    public function topicScoresFromAnswers()
    {
        return $this->answers()
            ->join('questions', 'questions.id', '=', 'attempt_answers.question_id')
            ->selectRaw('questions.topic_id, COUNT(*) as total, SUM(CASE WHEN attempt_answers.is_correct THEN 1 ELSE 0 END) as correct')
            ->groupBy('questions.topic_id')
            ->get()
            ->mapWithKeys(fn ($row) => [(int) $row->topic_id => [
                'correct' => (int) $row->correct,
                'total' => (int) $row->total,
                'score' => round($row->correct / $row->total * 100, 2),
            ]]);
    }
}
