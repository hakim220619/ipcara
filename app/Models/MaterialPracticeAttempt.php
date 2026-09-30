<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id', 'material_id', 'round', 'attempt_number', 'status', 'question_ids',
    'total_questions', 'correct_count', 'score', 'outcome', 'started_at',
    'finished_at', 'duration_seconds',
])]
class MaterialPracticeAttempt extends Model
{
    protected function casts(): array
    {
        return [
            'question_ids' => 'array',
            'score' => 'decimal:2',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(MaterialPracticeAnswer::class);
    }
}
