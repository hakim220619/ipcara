<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'topic_id', 'material_id', 'indicator_id', 'practice_role', 'type', 'difficulty', 'question_text',
    'image_path', 'explanation', 'points', 'is_active', 'created_by',
])]
class Question extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    public function indicator(): BelongsTo
    {
        return $this->belongsTo(CompetencyIndicator::class, 'indicator_id');
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    public function correctOption(): HasOne
    {
        return $this->hasOne(QuestionOption::class)->where('is_correct', true);
    }

    public function tests(): BelongsToMany
    {
        return $this->belongsToMany(Test::class, 'test_questions')
            ->withPivot('order')
            ->withTimestamps();
    }
}
