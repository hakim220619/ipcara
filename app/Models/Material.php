<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'topic_id', 'title', 'slug', 'type', 'level', 'summary', 'content',
    'video_url', 'video_duration_seconds', 'attachment_path',
    'estimated_minutes', 'order', 'is_published', 'created_by',
])]
class Material extends Model
{
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class);
    }

    public function indicators(): HasMany
    {
        return $this->hasMany(CompetencyIndicator::class)->orderBy('order');
    }

    /**
     * Soal latihan materi (utama, penguatan, remedial) yang aktif.
     */
    public function practiceQuestions(): HasMany
    {
        return $this->hasMany(Question::class)->whereNotNull('practice_role')->where('is_active', true);
    }

    public function progress(): HasMany
    {
        return $this->hasMany(MaterialProgress::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }
}
