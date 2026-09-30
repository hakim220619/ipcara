<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialPracticeAttempt;
use App\Models\MaterialProgress;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Topic;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Halaman "Tes & Latihan": pre-test, latihan per materi (mastery), dan post-test.
 */
class TesController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $latest = fn (string $kind) => TestAttempt::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('test', fn ($q) => $q->where('kind', $kind))
            ->latest('id')
            ->first();

        $progress = MaterialProgress::query()->where('user_id', $user->id)->get()->keyBy('material_id');
        $attempts = MaterialPracticeAttempt::query()
            ->where('user_id', $user->id)
            ->orderBy('id')
            ->get()
            ->groupBy('material_id');

        $latihan = Topic::query()
            ->where('is_published', true)
            ->orderBy('order')
            ->with(['materials' => fn ($q) => $q
                ->where('is_published', true)
                ->whereHas('practiceQuestions', fn ($q) => $q->where('practice_role', 'utama'))
                ->orderBy('order')])
            ->get()
            ->filter(fn (Topic $t) => $t->materials->isNotEmpty())
            ->map(fn (Topic $topic) => [
                'title' => $topic->title,
                'materials' => $topic->materials->map(function (Material $m) use ($progress, $attempts) {
                    $mine = $attempts->get($m->id, collect());
                    $done = $mine->where('status', 'completed');

                    return [
                        'title' => $m->title,
                        'slug' => $m->slug,
                        'read' => ($progress[$m->id]->status ?? 'belum') === 'selesai',
                        'mastery' => $progress[$m->id]->mastery_status ?? 'belum',
                        'attempts' => $done->count(),
                        'last_score' => $done->last() ? (float) $done->last()->score : null,
                        'in_progress' => $mine->contains('status', 'in_progress'),
                        'last_attempt_id' => $done->last()?->id,
                    ];
                })->values(),
            ])
            ->values();

        $pre = $latest(Test::KIND_PRE_TEST);
        $post = $latest(Test::KIND_POST_TEST);

        return Inertia::render('tes/Index', [
            'preTest' => [
                'done' => $pre !== null,
                'score' => $pre ? (float) $pre->score : null,
            ],
            'latihan' => $latihan,
            'postTest' => [
                ...$user->materialCompletion(),
                'unlocked' => $user->canTakePostTest(),
                'done' => $post !== null,
                'score' => $post ? (float) $post->score : null,
            ],
        ]);
    }
}
