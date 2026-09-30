<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'topic_id', 'kind', 'title', 'description', 'duration_minutes',
    'passing_score', 'max_attempts', 'shuffle_questions',
    'show_result_immediately', 'is_active', 'created_by',
])]
class Test extends Model
{
    public const KIND_PRE_TEST = 'pre_test';

    public const KIND_LATIHAN = 'latihan';

    public const KIND_REMEDIAL = 'remedial';

    public const KIND_POST_TEST = 'post_test';

    protected function casts(): array
    {
        return [
            'shuffle_questions' => 'boolean',
            'show_result_immediately' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function questions(): BelongsToMany
    {
        return $this->belongsToMany(Question::class, 'test_questions')
            ->withPivot('order')
            ->withTimestamps()
            ->orderByPivot('order');
    }

    /**
     * Komposisi soal per topik untuk pre-test dan post-test (5 soal per topik = 25 soal).
     */
    public const QUESTION_MIX = ['mudah' => 2, 'sedang' => 2, 'sulit' => 1];

    /**
     * Pertahankan soal sesuai komposisi per topik, lepaskan sisanya dari tes (soal tetap ada di bank soal).
     */
    public function keepBalancedQuestions(array $mix = self::QUESTION_MIX): void
    {
        $perTopic = array_sum($mix);
        $keep = collect();

        foreach ($this->questions()->get()->groupBy('topic_id') as $questions) {
            $picked = collect($mix)->flatMap(fn (int $count, string $level) => $questions->where('difficulty', $level)->take($count));
            // Bila salah satu tingkat kurang, lengkapi dengan soal lain sesuai urutan.
            $picked = $picked->concat($questions->diff($picked))->take($perTopic);
            $keep = $keep->concat($picked->sortBy('pivot.order')->pluck('id'));
        }

        $this->questions()->sync(
            $keep->values()->mapWithKeys(fn (int $id, int $i) => [$id => ['order' => $i + 1]])->all(),
        );
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }
}
