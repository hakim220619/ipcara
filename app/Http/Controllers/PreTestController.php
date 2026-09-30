<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitPreTestRequest;
use App\Models\AbilityAssessment;
use App\Models\Material;
use App\Models\Question;
use App\Models\Recommendation;
use App\Models\ScoreCriterion;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\User;
use App\Services\PreTestGrader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Random\Engine\Mt19937;
use Random\Randomizer;

class PreTestController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $test = $this->preTest();

        if ($this->attempt($request->user(), $test, 'completed')) {
            return to_route('pre-test.result');
        }

        $questions = $test->questions()
            ->where('is_active', true)
            ->with('topic:id,title,order')
            ->get(['questions.id', 'questions.topic_id']);

        return Inertia::render('pre-test/Index', [
            'test' => [
                'title' => $test->title,
                'description' => $test->description,
                'duration_minutes' => $test->duration_minutes,
                'question_count' => $questions->count(),
                'topics' => $questions
                    ->groupBy('topic_id')
                    ->map(fn ($group) => [
                        'title' => $group->first()->topic->title,
                        'order' => $group->first()->topic->order,
                        'total' => $group->count(),
                    ])
                    ->sortBy('order')
                    ->values()
                    ->map(fn ($t) => ['title' => $t['title'], 'total' => $t['total']]),
            ],
            'inProgress' => $this->attempt($request->user(), $test, 'in_progress') !== null,
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $test = $this->preTest();
        $user = $request->user();

        if ($this->attempt($user, $test, 'completed')) {
            return to_route('pre-test.result');
        }

        TestAttempt::query()->firstOrCreate(
            ['user_id' => $user->id, 'test_id' => $test->id, 'status' => 'in_progress'],
            [
                'attempt_number' => 1,
                'total_questions' => $test->questions()->where('is_active', true)->count(),
                'started_at' => now(),
            ],
        );

        return to_route('pre-test.take');
    }

    public function take(Request $request): Response|RedirectResponse
    {
        $test = $this->preTest();
        $attempt = $this->attempt($request->user(), $test, 'in_progress');

        if (! $attempt) {
            return to_route('pre-test.index');
        }

        $questions = $test->questions()
            ->where('is_active', true)
            ->with(['options:id,question_id,label,option_text,order', 'topic:id,title'])
            ->get();

        if ($test->shuffle_questions) {
            $questions = collect((new Randomizer(new Mt19937($attempt->id)))->shuffleArray($questions->all()));
        }

        $deadline = $test->duration_minutes
            ? $attempt->started_at->copy()->addMinutes($test->duration_minutes)
            : null;

        return Inertia::render('pre-test/Take', [
            'attemptId' => $attempt->id,
            'title' => $test->title,
            'deadline' => $deadline?->toIso8601String(),
            'questions' => $questions->values()->map(fn (Question $q) => [
                'id' => $q->id,
                'text' => $q->question_text,
                'topic' => $q->topic?->title,
                'options' => $q->options->map(fn ($o) => [
                    'id' => $o->id,
                    'label' => $o->label,
                    'text' => $o->option_text,
                ]),
            ]),
        ]);
    }

    public function submit(SubmitPreTestRequest $request, PreTestGrader $grader): RedirectResponse
    {
        $attempt = $this->attempt($request->user(), $this->preTest(), 'in_progress');

        if (! $attempt) {
            return to_route('pre-test.index');
        }

        $grader->grade($attempt, collect($request->validated('answers', [])));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pre-test selesai. Berikut hasil Anda.']);

        return to_route('pre-test.result');
    }

    public function result(Request $request): Response|RedirectResponse
    {
        $test = $this->preTest();
        $attempt = $this->attempt($request->user(), $test, 'completed');

        if (! $attempt) {
            return to_route('pre-test.index');
        }

        $summary = $attempt->load('abilityAssessments.topic:id,code,title,order')->scoreSummary();

        // Titik awal belajar per topik: materi rekomendasi, atau materi pertama untuk topik yang sudah dikuasai.
        $recommended = Recommendation::query()
            ->whereIn('ability_assessment_id', $attempt->abilityAssessments->pluck('id'))
            ->with('material:id,title,slug')
            ->get()
            ->keyBy('topic_id');
        $firstMaterials = Material::query()
            ->where('is_published', true)
            ->orderBy('order')
            ->get(['id', 'topic_id', 'title', 'slug'])
            ->unique('topic_id')
            ->keyBy('topic_id');
        $criteria = ScoreCriterion::ordered();

        $topics = collect($summary['topics'])->map(function (array $topic) use ($recommended, $firstMaterials, $criteria) {
            $material = $recommended[$topic['id']]->material ?? $firstMaterials[$topic['id']] ?? null;

            return [
                ...$topic,
                'category_note' => $criteria->firstWhere('level', $topic['level'])?->description,
                'start_material' => $material ? ['title' => $material->title, 'slug' => $material->slug] : null,
            ];
        });

        return Inertia::render('pre-test/Result', [
            'result' => [
                'correct' => $attempt->correct_count,
                'total' => $attempt->total_questions,
                'total_points' => $summary['total_points'],
                'max_points' => $summary['max_points'],
                'percent' => (float) $attempt->score,
                'passing_score' => $test->passing_score,
                'is_passed' => $attempt->is_passed,
                'duration_seconds' => $attempt->duration_seconds,
                'finished_at' => $attempt->finished_at?->locale('id')->isoFormat('D MMMM YYYY, HH:mm'),
            ],
            'topics' => $topics,
            'criteria' => $criteria->map(fn (ScoreCriterion $c) => [
                'level' => $c->level,
                'label' => $c->label,
                'min_score' => (float) $c->min_score,
                'max_score' => (float) $c->max_score,
            ]),
            'overallLevel' => AbilityAssessment::classify((float) $attempt->score)['level'],
        ]);
    }

    private function preTest(): Test
    {
        return Test::query()
            ->where('kind', Test::KIND_PRE_TEST)
            ->where('is_active', true)
            ->firstOrFail();
    }

    private function attempt(User $user, Test $test, string $status): ?TestAttempt
    {
        return TestAttempt::query()
            ->where('user_id', $user->id)
            ->where('test_id', $test->id)
            ->where('status', $status)
            ->latest('id')
            ->first();
    }
}
