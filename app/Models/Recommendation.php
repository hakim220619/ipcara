<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'topic_id', 'material_id', 'ability_assessment_id',
    'priority', 'reason', 'status', 'viewed_at', 'completed_at',
])]
class Recommendation extends Model
{
    protected function casts(): array
    {
        return [
            'viewed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function abilityAssessment(): BelongsTo
    {
        return $this->belongsTo(AbilityAssessment::class);
    }
}
