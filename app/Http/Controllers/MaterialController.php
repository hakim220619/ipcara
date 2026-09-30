<?php

namespace App\Http\Controllers;

use App\Models\LearningActivity;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Question;
use App\Models\Test;
use App\Models\Topic;
use App\Models\User;
use App\Services\MasteryPractice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

class MaterialController extends Controller
{
    public function __construct(private MasteryPractice $practice) {}

    public function index(Request $request): Response
    {
        $user = $request->user();

        $progress = MaterialProgress::query()
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('material_id');

        $assessments = $user->abilityAssessments()
            ->orderByDesc('assessed_at')
            ->orderByDesc('id')
            ->get()
            ->unique('topic_id')
            ->keyBy('topic_id');

        $recommendedMaterialIds = $user->recommendations()->pluck('material_id')->unique();
        $practiceMaterialIds = Question::query()->where('practice_role', 'utama')->where('is_active', true)->distinct()->pluck('material_id');

        $topics = Topic::query()
            ->where('is_published', true)
            ->orderBy('order')
            ->with(['materials' => fn ($q) => $q->where('is_published', true)->orderBy('order')])
            ->get()
            ->map(fn (Topic $topic) => [
                'id' => $topic->id,
                'code' => $topic->code,
                'title' => $topic->title,
                'description' => $topic->description,
                'score' => isset($assessments[$topic->id]) ? (float) $assessments[$topic->id]->score : null,
                'recommended' => isset($assessments[$topic->id]) && $assessments[$topic->id]->isRecommended(),
                // Kategori adaptive learning dari pre-test: rendah = perlu dipelajari, sedang = perlu penguatan, tinggi = sudah dikuasai.
                'category' => $assessments[$topic->id]->level ?? null,
                'materials' => $topic->materials->map(fn (Material $m) => [
                    'id' => $m->id,
                    'title' => $m->title,
                    'slug' => $m->slug,
                    'type' => $m->type,
                    'status' => $progress[$m->id]->status ?? 'belum',
                    'has_practice' => $practiceMaterialIds->contains($m->id),
                    'mastery' => $progress[$m->id]->mastery_status ?? 'belum',
                    'recommended' => $recommendedMaterialIds->contains($m->id),
                ]),
            ])
            ->sortBy(fn (array $t) => [$t['recommended'] ? 0 : 1, $t['recommended'] ? $t['score'] : 0])
            ->map(fn (array $t) => Arr::except($t, 'score'))
            ->values();

        return Inertia::render('materi/Index', [
            'topics' => $topics,
            'postTest' => [
                ...$user->materialCompletion(),
                'unlocked' => $user->canTakePostTest(),
                'done' => $this->postTestDone($user),
            ],
        ]);
    }

    public function show(Request $request, Material $material): Response
    {
        abort_unless($material->is_published, 404);

        $user = $request->user();

        $progress = MaterialProgress::query()->firstOrCreate(
            ['user_id' => $user->id, 'material_id' => $material->id],
            ['status' => 'sedang', 'started_at' => now()],
        );

        if ($progress->status === 'belum') {
            $progress->update(['status' => 'sedang', 'started_at' => now()]);
        }

        LearningActivity::query()->create([
            'user_id' => $user->id,
            'activity_type' => 'buka_materi',
            'subject_type' => Material::class,
            'subject_id' => $material->id,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 255),
        ]);

        $siblings = Material::query()
            ->where('topic_id', $material->topic_id)
            ->where('is_published', true)
            ->orderBy('order')
            ->get(['id', 'title', 'slug', 'order']);

        $index = $siblings->search(fn ($m) => $m->id === $material->id);
        $next = $index !== false ? $siblings->get($index + 1) : null;
        $prev = $index !== false && $index > 0 ? $siblings->get($index - 1) : null;

        return Inertia::render('materi/Show', [
            'material' => $material->load('topic:id,title,code,slug')->makeHidden(['estimated_minutes', 'level']),
            'progress' => $progress->only(['status', 'progress_percent', 'last_position_seconds']),
            'next' => $next?->only(['title', 'slug']),
            'prev' => $prev?->only(['title', 'slug']),
            'postTestUnlocked' => $user->canTakePostTest() && ! $this->postTestDone($user),
            'practice' => $this->practiceSummary($user, $material, $progress),
        ]);
    }

    public function complete(Request $request, Material $material): RedirectResponse
    {
        $user = $request->user();

        MaterialProgress::query()->updateOrCreate(
            ['user_id' => $user->id, 'material_id' => $material->id],
            ['status' => 'selesai', 'progress_percent' => 100, 'completed_at' => now()],
        );

        $user->recommendations()
            ->where('material_id', $material->id)
            ->whereIn('status', ['pending', 'viewed'])
            ->update(['status' => 'completed', 'completed_at' => now()]);

        LearningActivity::query()->create([
            'user_id' => $user->id,
            'activity_type' => 'selesai_materi',
            'subject_type' => Material::class,
            'subject_id' => $material->id,
        ]);

        // Setelah materi selesai, latihan materi langsung ditampilkan (kecuali sudah mastery).
        $status = MaterialProgress::query()->where('user_id', $user->id)->where('material_id', $material->id)->value('mastery_status');

        if (MasteryPractice::hasPractice($material) && $status !== 'mastery') {
            $this->practice->start($user, $material);

            return to_route('materi.latihan.take', $material);
        }

        return back()->with('success', 'Materi ditandai selesai.');
    }

    /**
     * @return array<string, mixed>|null
     */
    private function practiceSummary(User $user, Material $material, MaterialProgress $progress): ?array
    {
        if (! MasteryPractice::hasPractice($material)) {
            return null;
        }

        $latest = $this->practice->latestCompleted($user, $material);

        return [
            'mastery' => $progress->mastery_status,
            'attempts' => $progress->practice_attempts,
            'last_score' => $progress->last_practice_score !== null ? (float) $progress->last_practice_score : null,
            'in_progress' => $this->practice->current($user, $material) !== null,
            'next_round' => $this->practice->nextRound($user, $material),
            'last_attempt_id' => $latest?->id,
        ];
    }

    private function postTestDone(User $user): bool
    {
        return $user->testAttempts()
            ->where('status', 'completed')
            ->whereHas('test', fn ($q) => $q->where('kind', Test::KIND_POST_TEST))
            ->exists();
    }
}
