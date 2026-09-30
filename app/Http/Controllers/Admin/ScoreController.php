<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ScoreRequest;
use App\Models\AbilityAssessment;
use App\Models\ScoreCriterion;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\User;
use App\Services\PreTestGrader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ScoreController extends Controller
{
    public function index(Request $request): Response
    {
        $preTest = $this->preTest();
        $filters = $request->only(['search', 'status']);
        $criteria = ScoreCriterion::ordered();
        $labels = $criteria->pluck('label', 'level');

        $attempts = TestAttempt::query()
            ->where('test_id', $preTest->id)
            ->where('status', 'completed')
            ->whereHas('user', fn ($q) => $q->where('role', 'mahasiswa'))
            ->with(['user:id,name,nim,email', 'abilityAssessments.topic:id,code,title,order'])
            ->withCount('answers')
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->whereHas('user', fn ($u) => $u
                ->where('name', 'like', "%{$search}%")
                ->orWhere('nim', 'like', "%{$search}%")))
            ->when(($filters['status'] ?? null) === 'tuntas', fn ($q) => $q->where('is_passed', true))
            ->when(($filters['status'] ?? null) === 'belum', fn ($q) => $q->where('is_passed', false))
            ->orderByDesc('finished_at')
            ->paginate(15)
            ->withQueryString()
            ->through(function (TestAttempt $a) use ($labels, $criteria) {
                $summary = $a->scoreSummary();
                $level = AbilityAssessment::classify((float) $a->score, $criteria)['level'];

                return [
                    'id' => $a->id,
                    'user' => $a->user?->only(['id', 'name', 'nim', 'email']),
                    'topic_scores' => (object) collect($summary['topics'])->mapWithKeys(fn ($t) => [$t['id'] => $t['score']])->all(),
                    'topic_levels' => (object) collect($summary['topics'])->mapWithKeys(fn ($t) => [$t['id'] => $t['level']])->all(),
                    'total_points' => $summary['total_points'],
                    'max_points' => $summary['max_points'],
                    'percent' => (float) $a->score,
                    'is_passed' => $a->is_passed,
                    'level' => $level,
                    'level_label' => $labels[$level] ?? ucfirst($level),
                    'is_manual' => $a->answers_count === 0,
                    'finished_at' => $a->finished_at?->format('Y-m-d\TH:i'),
                    'finished_label' => $a->finished_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm'),
                ];
            });

        $studentsWithoutScore = User::query()
            ->where('role', 'mahasiswa')
            ->whereDoesntHave('testAttempts', fn ($q) => $q->where('test_id', $preTest->id))
            ->orderBy('name')
            ->get(['id', 'name', 'nim']);

        return Inertia::render('admin/nilai/Index', [
            'attempts' => $attempts,
            'filters' => $filters,
            'students' => $studentsWithoutScore,
            'topics' => $this->topics($preTest),
            'passingScore' => $preTest->passing_score,
        ]);
    }

    public function store(ScoreRequest $request, PreTestGrader $grader): RedirectResponse
    {
        $preTest = $this->preTest();
        $finishedAt = Carbon::parse($request->validated('finished_at'));

        DB::transaction(function () use ($request, $grader, $preTest, $finishedAt) {
            $attempt = TestAttempt::query()->create([
                'user_id' => $request->validated('user_id'),
                'test_id' => $preTest->id,
                'attempt_number' => 1,
                'status' => 'in_progress',
                'started_at' => $finishedAt,
            ]);

            $grader->applyTopicScores($attempt, $this->topicRows($request, $preTest), $finishedAt);
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Nilai mahasiswa ditambahkan.']);

        return back();
    }

    public function update(ScoreRequest $request, TestAttempt $attempt, PreTestGrader $grader): RedirectResponse
    {
        $grader->applyTopicScores(
            $attempt,
            $this->topicRows($request, $attempt->test),
            Carbon::parse($request->validated('finished_at')),
        );

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Nilai mahasiswa diperbarui.']);

        return back();
    }

    public function destroy(TestAttempt $attempt, PreTestGrader $grader): RedirectResponse
    {
        $grader->discard($attempt);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Nilai dihapus. Mahasiswa dapat mengerjakan pre-test kembali.']);

        return back();
    }

    /**
     * Ubah nilai per topik dari form menjadi data penilaian. Jumlah benar diperkirakan
     * dari nilai × jumlah soal topik di pre-test.
     *
     * @return array<int, array{score: float, correct: int, total: int}>
     */
    private function topicRows(ScoreRequest $request, Test $test): array
    {
        $questionCounts = $test->questions()
            ->where('is_active', true)
            ->reorder()
            ->get(['questions.id', 'questions.topic_id'])
            ->countBy('topic_id');

        return collect($request->validated('topic_scores'))
            ->mapWithKeys(function ($score, $topicId) use ($questionCounts) {
                $total = (int) ($questionCounts[$topicId] ?? 0);

                return [(int) $topicId => [
                    'score' => (float) $score,
                    'correct' => (int) round((float) $score / 100 * $total),
                    'total' => $total,
                ]];
            })
            ->all();
    }

    /**
     * @return Collection<int, array{id: int, code: string, title: string}>
     */
    private function topics(Test $test)
    {
        $topicIds = $test->questions()->where('is_active', true)->reorder()->pluck('questions.topic_id')->unique();

        return Topic::query()
            ->whereIn('id', $topicIds)
            ->orderBy('order')
            ->get(['id', 'code', 'title'])
            ->map(fn (Topic $t) => ['id' => $t->id, 'code' => $t->code, 'title' => $t->title]);
    }

    private function preTest(): Test
    {
        return Test::query()->where('kind', Test::KIND_PRE_TEST)->firstOrFail();
    }
}
