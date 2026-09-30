<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'topic_id', 'test_attempt_id', 'source', 'score', 'level',
    'recommended_material_level', 'detail', 'assessed_at',
])]
class AbilityAssessment extends Model
{
    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'detail' => 'array',
            'assessed_at' => 'datetime',
        ];
    }

    /**
     * Tentukan tingkat kemampuan dan level materi yang direkomendasikan dari skor,
     * berdasarkan kriteria penilaian yang diatur admin.
     *
     * @param  Collection<int, ScoreCriterion>|null  $criteria
     * @return array{level: string, recommended_material_level: string, recommended: bool}
     */
    public static function classify(float $score, ?Collection $criteria = null): array
    {
        $criterion = ScoreCriterion::forScore($score, $criteria);

        if ($criterion) {
            return [
                'level' => $criterion->level,
                'recommended_material_level' => $criterion->recommended_material_level,
                'recommended' => $criterion->is_recommended,
            ];
        }

        return match (true) {
            $score < 60 => ['level' => 'rendah', 'recommended_material_level' => 'dasar', 'recommended' => true],
            $score < 80 => ['level' => 'sedang', 'recommended_material_level' => 'menengah', 'recommended' => true],
            default => ['level' => 'tinggi', 'recommended_material_level' => 'lanjut', 'recommended' => false],
        };
    }

    public function isRecommended(): bool
    {
        return (bool) ($this->detail['recommended'] ?? $this->level !== 'tinggi');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'test_attempt_id');
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }
}
