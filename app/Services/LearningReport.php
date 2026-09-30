<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialPracticeAttempt;
use App\Models\MaterialProgress;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Laporan keseluruhan hasil belajar: pre-test → latihan per materi (mastery) → post-test,
 * beserta kesimpulan otomatis. Nilai latihan dilaporkan terpisah dan tidak menggantikan nilai post-test.
 */
class LearningReport
{
    /**
     * @return array<string, mixed>
     */
    public function build(User $user): array
    {
        $pre = $this->latest($user, Test::KIND_PRE_TEST);
        $post = $this->latest($user, Test::KIND_POST_TEST);
        $passingScore = (int) (Test::query()->where('kind', Test::KIND_POST_TEST)->value('passing_score') ?? 70);

        $preTopics = $pre?->abilityAssessments()->pluck('score', 'topic_id') ?? collect();
        $postTopics = $post?->topicScoresFromAnswers() ?? collect();

        $topics = Topic::query()
            ->where('is_published', true)
            ->orderBy('order')
            ->get(['id', 'code', 'title'])
            ->map(fn (Topic $t) => [
                'code' => $t->code,
                'title' => $t->title,
                'pre' => isset($preTopics[$t->id]) ? (float) $preTopics[$t->id] : null,
                'post' => $postTopics->get($t->id)['score'] ?? null,
            ]);

        $practice = $this->practice($user);

        $preScore = $pre ? (float) $pre->score : null;
        $postScore = $post ? (float) $post->score : null;
        $nGain = $preScore !== null && $postScore !== null && $preScore < 100
            ? round(($postScore - $preScore) / (100 - $preScore), 2)
            : null;
        $nGainInfo = $nGain === null ? null : ['value' => $nGain, 'category' => $this->nGainCategory($nGain)];

        return [
            'student' => ['name' => $user->name, 'nim' => $user->nim, 'program_studi' => $user->program_studi],
            'preTest' => $pre ? [
                'score' => $preScore,
                'correct' => $pre->correct_count,
                'total' => $pre->total_questions,
                'finished_at' => $pre->finished_at?->locale('id')->isoFormat('D MMM YYYY'),
            ] : null,
            'postTest' => $post ? [
                'score' => $postScore,
                'correct' => $post->correct_count,
                'total' => $post->total_questions,
                'is_passed' => $post->is_passed,
                'finished_at' => $post->finished_at?->locale('id')->isoFormat('D MMM YYYY'),
            ] : null,
            'passingScore' => $passingScore,
            'nGain' => $nGainInfo,
            'topics' => $topics,
            'practice' => $practice,
            'conclusion' => $this->conclusion($preScore, $postScore, $nGainInfo, $passingScore, $topics, $practice),
        ];
    }

    private function latest(User $user, string $kind): ?TestAttempt
    {
        return TestAttempt::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('test', fn ($q) => $q->where('kind', $kind))
            ->latest('id')
            ->first();
    }

    /**
     * @return array<string, mixed>
     */
    private function practice(User $user): array
    {
        $attempts = MaterialPracticeAttempt::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('id')
            ->get()
            ->groupBy('material_id');
        $progress = MaterialProgress::query()->where('user_id', $user->id)->get()->keyBy('material_id');

        $materials = Material::query()
            ->where('is_published', true)
            ->whereHas('indicators')
            ->with('topic:id,order,title')
            ->get()
            ->sortBy(fn (Material $m) => [$m->topic?->order, $m->order])
            ->values()
            ->map(function (Material $m) use ($attempts, $progress) {
                $mine = $attempts->get($m->id, collect());

                return [
                    'topic' => $m->topic?->title,
                    'title' => $m->title,
                    'slug' => $m->slug,
                    'mastery_status' => $progress[$m->id]->mastery_status ?? 'belum',
                    'attempts' => $mine->count(),
                    'first_score' => $mine->first() ? (float) $mine->first()->score : null,
                    'last_score' => $mine->last() ? (float) $mine->last()->score : null,
                    'first_try_mastery' => $mine->first()?->outcome === 'mastery',
                    'rounds' => [
                        'penguatan' => $mine->where('round', 'penguatan')->count(),
                        'remedial' => $mine->where('round', 'remedial')->count(),
                    ],
                    'duration_seconds' => (int) $mine->sum('duration_seconds'),
                ];
            });

        $practiced = $materials->where('attempts', '>', 0);

        return [
            'mastered' => $materials->where('mastery_status', 'mastery')->count(),
            'total' => $materials->count(),
            'first_try_mastery' => $materials->where('first_try_mastery', true)->count(),
            'with_penguatan' => $materials->filter(fn ($m) => $m['rounds']['penguatan'] > 0)->count(),
            'with_remedial' => $materials->filter(fn ($m) => $m['rounds']['remedial'] > 0)->count(),
            'attempts' => $materials->sum('attempts'),
            'avg_first_score' => $practiced->isNotEmpty() ? round($practiced->avg('first_score'), 1) : null,
            'duration_seconds' => $materials->sum('duration_seconds'),
            'materials' => $materials,
        ];
    }

    private function nGainCategory(float $nGain): string
    {
        return match (true) {
            $nGain >= 0.7 => 'Tinggi',
            $nGain >= 0.3 => 'Sedang',
            default => 'Rendah',
        };
    }

    /**
     * Kesimpulan otomatis dari seluruh hasil belajar.
     *
     * @param  array{value: float, category: string}|null  $nGain
     * @param  Collection<int, array<string, mixed>>  $topics
     * @param  array<string, mixed>  $practice
     * @return array{verdict: string, tone: string, summary: string, points: list<string>, recommendation: string}
     */
    private function conclusion(?float $pre, ?float $post, ?array $nGain, int $passingScore, Collection $topics, array $practice): array
    {
        $fmt = fn (?float $n) => $n === null ? '-' : rtrim(rtrim(number_format($n, 1, ',', '.'), '0'), ',');

        if ($post === null) {
            return [
                'verdict' => 'Belum Lengkap',
                'tone' => 'neutral',
                'summary' => 'Kesimpulan akhir tersedia setelah post-test dikerjakan.',
                'points' => [],
                'recommendation' => 'Selesaikan seluruh materi hingga Mastery, lalu kerjakan post-test.',
            ];
        }

        $passed = $post >= $passingScore;
        [$verdict, $tone] = match (true) {
            $passed && ($nGain === null || $nGain['category'] === 'Tinggi' || $post >= 85) => ['Sangat Baik', 'excellent'],
            $passed => ['Baik', 'good'],
            $nGain !== null && $nGain['category'] !== 'Rendah' => ['Cukup', 'fair'],
            default => ['Perlu Peningkatan', 'low'],
        };

        $change = $pre !== null ? $post - $pre : null;
        $summary = $pre === null
            ? "Nilai post-test Anda {$fmt($post)}."
            : sprintf(
                'Nilai Anda %s dari %s pada pre-test menjadi %s pada post-test (%s%s poin)%s.',
                $change >= 0 ? 'meningkat' : 'menurun',
                $fmt($pre),
                $fmt($post),
                $change >= 0 ? '+' : '',
                $fmt($change),
                $nGain ? ", dengan N-Gain {$nGain['value']} kategori ".strtolower($nGain['category']) : '',
            );
        $summary .= $passed
            ? " Anda telah mencapai KKM {$passingScore}."
            : " Nilai ini belum mencapai KKM {$passingScore}.";

        $points = [];
        $withPost = $topics->filter(fn ($t) => $t['post'] !== null);

        if ($withPost->isNotEmpty()) {
            $best = $withPost->sortByDesc('post')->first();
            $weak = $withPost->sortBy('post')->first();
            $points[] = "Topik paling dikuasai: {$best['title']} (nilai {$fmt($best['post'])}).";

            if ($weak['title'] !== $best['title']) {
                $points[] = "Topik dengan nilai terendah: {$weak['title']} (nilai {$fmt($weak['post'])}).";
            }

            $improved = $withPost->filter(fn ($t) => $t['pre'] !== null)->sortByDesc(fn ($t) => $t['post'] - $t['pre'])->first();

            if ($improved && $improved['post'] > $improved['pre']) {
                $points[] = sprintf('Peningkatan terbesar pada %s (+%s poin).', $improved['title'], $fmt($improved['post'] - $improved['pre']));
            }
        }

        if ($practice['total'] > 0) {
            $points[] = sprintf(
                'Mastery tercapai pada %d dari %d materi melalui %d kali latihan; %d materi langsung mastery pada percobaan pertama.',
                $practice['mastered'],
                $practice['total'],
                $practice['attempts'],
                $practice['first_try_mastery'],
            );

            if ($practice['with_remedial'] || $practice['with_penguatan']) {
                $points[] = sprintf(
                    '%d materi memerlukan soal penguatan dan %d materi memerlukan remedial sebelum dikuasai.',
                    $practice['with_penguatan'],
                    $practice['with_remedial'],
                );
            }
        }

        $below = $withPost->filter(fn ($t) => $t['post'] < $passingScore)->pluck('title');
        $recommendation = match (true) {
            $below->isNotEmpty() => 'Pelajari kembali topik '.$below->join(', ', ' dan ').' untuk memperkuat pemahaman sebelum praktik klinik.',
            $tone === 'excellent' => 'Pertahankan pemahaman ini dan terapkan prinsip PPI secara konsisten dalam praktik klinik.',
            default => 'Pemahaman Anda sudah baik. Terus biasakan penerapan PPI dan tinjau kembali pembahasan soal yang masih keliru.',
        };

        return compact('verdict', 'tone', 'summary', 'points', 'recommendation');
    }
}
