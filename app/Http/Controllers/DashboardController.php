<?php

namespace App\Http\Controllers;

use App\Models\AbilityAssessment;
use App\Models\LearningActivity;
use App\Models\Material;
use App\Models\MaterialProgress;
use App\Models\Recommendation;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response|RedirectResponse
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return to_route('admin.dashboard');
        }

        $topics = Topic::query()
            ->where('is_published', true)
            ->orderBy('order')
            ->withCount(['materials' => fn ($q) => $q->where('is_published', true)])
            ->get();

        $completedPerTopic = MaterialProgress::query()
            ->where('user_id', $user->id)
            ->where('status', 'selesai')
            ->join('materials', 'materials.id', '=', 'material_progress.material_id')
            ->selectRaw('materials.topic_id, count(*) as total')
            ->groupBy('materials.topic_id')
            ->pluck('total', 'topic_id');

        $latestAssessments = AbilityAssessment::query()
            ->where('user_id', $user->id)
            ->orderByDesc('assessed_at')
            ->get()
            ->unique('topic_id')
            ->keyBy('topic_id');

        $moduleCompletion = $topics->map(function (Topic $topic) use ($completedPerTopic, $latestAssessments) {
            $total = max(1, $topic->materials_count);
            $done = (int) ($completedPerTopic[$topic->id] ?? 0);
            $assessment = $latestAssessments->get($topic->id);

            return [
                'id' => $topic->id,
                'title' => $topic->title,
                'code' => $topic->code,
                'percent' => round(min(100, $done / $total * 100)),
                'level' => $assessment?->level,
                'score' => $assessment ? (float) $assessment->score : null,
            ];
        });

        $weekly = collect(range(6, 0))->map(function (int $daysAgo) use ($user) {
            $day = Carbon::today()->subDays($daysAgo);

            return [
                'label' => $day->locale('id')->isoFormat('ddd'),
                'value' => LearningActivity::query()
                    ->where('user_id', $user->id)
                    ->whereDate('created_at', $day)
                    ->count(),
            ];
        });

        $scoreHistory = TestAttempt::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->orderBy('finished_at')
            ->limit(8)
            ->get()
            ->map(fn (TestAttempt $a) => [
                'label' => $a->finished_at?->locale('id')->isoFormat('D MMM') ?? '-',
                'value' => (float) $a->score,
            ]);

        $recommendations = Recommendation::query()
            ->with(['material:id,topic_id,title,slug,type,level', 'topic:id,title'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'viewed'])
            ->orderBy('priority')
            ->limit(5)
            ->get()
            ->map(fn (Recommendation $r) => [
                'id' => $r->id,
                'material' => $r->material,
                'topic' => $r->topic?->title,
                'reason' => $r->reason,
                'status' => $r->status,
            ]);

        // Materi yang latihannya belum mastery: perlu penguatan atau remedial.
        $activeRemedials = MaterialProgress::query()
            ->with('material:id,title,slug')
            ->where('user_id', $user->id)
            ->whereIn('mastery_status', ['penguatan', 'remedial'])
            ->get()
            ->map(fn (MaterialProgress $p) => [
                'id' => $p->id,
                'material' => $p->material?->title,
                'slug' => $p->material?->slug,
                'score' => (float) $p->last_practice_score,
                'status' => $p->mastery_status,
            ]);

        $preTest = Test::query()->where('kind', Test::KIND_PRE_TEST)->where('is_active', true)->first();
        $preTestAttempt = $preTest
            ? TestAttempt::query()->where('user_id', $user->id)->where('test_id', $preTest->id)->where('status', 'completed')->latest('id')->first()
            : null;
        $preTestDone = $preTestAttempt !== null;

        $nextMaterial = Material::query()
            ->where('is_published', true)
            ->whereNotIn('id', MaterialProgress::query()->where('user_id', $user->id)->where('status', 'selesai')->select('material_id'))
            ->orderBy('topic_id')->orderBy('order')
            ->first(['id', 'title', 'slug', 'topic_id']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'preTestDone' => $preTestDone,
            ],
            'preTestSummary' => $preTestAttempt ? [
                ...collect($preTestAttempt->load('abilityAssessments.topic:id,title,order')->scoreSummary())
                    ->only(['total_points', 'max_points'])
                    ->all(),
                'correct' => $preTestAttempt->correct_count,
                'total' => $preTestAttempt->total_questions,
                'percent' => (float) $preTestAttempt->score,
                'is_passed' => $preTestAttempt->is_passed,
                'finished_at' => $preTestAttempt->finished_at?->locale('id')->isoFormat('D MMMM YYYY, HH:mm'),
                'recommended_topics' => $recommendations->pluck('topic')->filter()->unique()->values(),
            ] : null,
            'moduleCompletion' => $moduleCompletion,
            'weeklyActivity' => $weekly,
            'scoreHistory' => $scoreHistory,
            'recommendations' => $recommendations,
            'remedials' => $activeRemedials,
            'nextMaterial' => $nextMaterial,
            'timeline' => $moduleCompletion->map(fn ($m, $i) => [
                'title' => $m['title'],
                'status' => $m['percent'] >= 100 ? 'selesai' : ($i === 0 || ($moduleCompletion[$i - 1]['percent'] ?? 0) >= 100 ? 'aktif' : 'belum'),
            ])->values(),
        ]);
    }
}
