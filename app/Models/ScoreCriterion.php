<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $level
 * @property string $label
 * @property string $min_score
 * @property string $max_score
 * @property string $recommended_material_level
 * @property bool $is_recommended
 * @property string|null $description
 */
#[Fillable([
    'level', 'label', 'min_score', 'max_score',
    'recommended_material_level', 'is_recommended', 'description',
])]
class ScoreCriterion extends Model
{
    protected $table = 'score_criteria';

    public const LEVELS = ['rendah', 'sedang', 'tinggi'];

    protected function casts(): array
    {
        return [
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'is_recommended' => 'boolean',
        ];
    }

    /**
     * @return Collection<int, ScoreCriterion>
     */
    public static function ordered(): Collection
    {
        return static::query()->orderBy('min_score')->get();
    }

    /**
     * @param  Collection<int, ScoreCriterion>|null  $criteria
     */
    public static function forScore(float $score, ?Collection $criteria = null): ?self
    {
        $criteria ??= static::ordered();

        return $criteria->first(fn (self $c) => $score >= (float) $c->min_score && $score <= (float) $c->max_score)
            ?? $criteria->last(fn (self $c) => $score >= (float) $c->min_score)
            ?? $criteria->first();
    }
}
