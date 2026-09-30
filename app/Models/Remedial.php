<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'topic_id', 'triggered_by_attempt_id', 'remedial_test_id',
    'status', 'trigger_score', 'final_score', 'attempts_count', 'notes',
    'assigned_at', 'resolved_at',
])]
class Remedial extends Model
{
    protected function casts(): array
    {
        return [
            'trigger_score' => 'decimal:2',
            'final_score' => 'decimal:2',
            'assigned_at' => 'datetime',
            'resolved_at' => 'datetime',
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

    public function triggeredByAttempt(): BelongsTo
    {
        return $this->belongsTo(TestAttempt::class, 'triggered_by_attempt_id');
    }

    public function remedialTest(): BelongsTo
    {
        return $this->belongsTo(Test::class, 'remedial_test_id');
    }
}
