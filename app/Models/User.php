<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string|null $username
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string $role
 * @property string|null $nim
 * @property int|null $semester
 * @property string|null $program_studi
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['name', 'username', 'email', 'password', 'role', 'nim', 'semester', 'program_studi'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'semester' => 'integer',
            /* @chisel-2fa */
            'two_factor_confirmed_at' => 'datetime',
            /* @end-chisel-2fa */
        ];
    }

    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function needsPreTest(): bool
    {
        return $this->isMahasiswa() && ! $this->testAttempts()
            ->where('status', 'completed')
            ->whereHas('test', fn ($q) => $q->where('kind', Test::KIND_PRE_TEST))
            ->exists();
    }

    /**
     * Jumlah materi terbit yang sudah tuntas dari seluruh materi terbit.
     * Materi tuntas bila sudah selesai dibaca dan, jika memiliki latihan, sudah mencapai status mastery.
     *
     * @return array{completed: int, total: int}
     */
    public function materialCompletion(): array
    {
        $published = Material::query()->where('is_published', true);
        $withPractice = Question::query()->where('practice_role', 'utama')->where('is_active', true)->select('material_id');

        return [
            'completed' => MaterialProgress::query()
                ->where('user_id', $this->id)
                ->where('status', 'selesai')
                ->whereIn('material_id', (clone $published)->select('id'))
                ->where(fn ($q) => $q->where('mastery_status', 'mastery')->orWhereNotIn('material_id', $withPractice))
                ->count(),
            'total' => $published->count(),
        ];
    }

    /**
     * Post-test terbuka setelah seluruh materi terbit tuntas (mastery).
     */
    public function canTakePostTest(): bool
    {
        ['completed' => $completed, 'total' => $total] = $this->materialCompletion();

        return $total > 0 && $completed >= $total;
    }

    public function testAttempts(): HasMany
    {
        return $this->hasMany(TestAttempt::class);
    }

    public function abilityAssessments(): HasMany
    {
        return $this->hasMany(AbilityAssessment::class);
    }

    public function recommendations(): HasMany
    {
        return $this->hasMany(Recommendation::class);
    }

    public function materialProgress(): HasMany
    {
        return $this->hasMany(MaterialProgress::class);
    }

    public function remedials(): HasMany
    {
        return $this->hasMany(Remedial::class);
    }

    public function learningActivities(): HasMany
    {
        return $this->hasMany(LearningActivity::class);
    }
}
