<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'material_id', 'status', 'mastery_status', 'practice_attempts',
    'last_practice_score', 'progress_percent', 'last_position_seconds',
    'time_spent_seconds', 'started_at', 'completed_at', 'mastered_at',
])]
class MaterialProgress extends Model
{
    protected $table = 'material_progress';

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
            'mastered_at' => 'datetime',
            'last_practice_score' => 'decimal:2',
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
}
