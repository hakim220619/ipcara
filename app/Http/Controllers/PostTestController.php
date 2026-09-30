<?php

namespace App\Http\Controllers;

use App\Http\Requests\SubmitPreTestRequest;
use App\Models\AbilityAssessment;
use App\Models\Question;
use App\Models\ScoreCriterion;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\User;
use App\Services\LearningReport;
use App\Services\PostTestGrader;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Random\Engine\Mt19937;
use Random\Randomizer;

class PostTestController extends Controller
{
    public function index(Request $request): Response|RedirectResponse
    {
        $user = $request->user();
        $test = $this->postTest();

        if ($this->attempt($user, $test, 'completed')) {
            return to_route('post-test.result');
        }

        $questions = $test->questions()
            ->where('is_active', true)
            ->with('topic:id,title,order')
            ->get(['questions.id', 'questions.topic_id']);

        return Inertia::render('post-test/Index', [
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
            'materials' => $user->materialCompletion(),
            'unlocked' => $user->canTakePostTest(),
            'inProgress' => $this->attempt($user, $test, 'in_progress') !== null,
        ]);
    }

    public function start(Request $request): RedirectResponse
    {
        $test = $this->postTest();
        $user = $request->user();

        if ($this->attempt($user, $test, 'completed')) {
            return to_route('post-test.result');
        }

        if (! $user->canTakePostTest()) {
            Inertia::flash('toast', ['type' => 'info', 'message' => 'Selesaikan semua materi terlebih dahulu untuk membuka post-test.']);

            return to_route('post-test.index');
        }

        TestAttempt::query()->firstOrCreate(
            ['user_id' => $user->id, 'test_id' => $test->id, 'status' => 'in_progress'],
            [
                'attempt_number' => 1,
                'total_questions' => $test->questions()->where('is_active', true)->count(),
                'started_at' => now(),
            ],
        );

        return to_route('post-test.take');
    }

    public function take(Request $request): Response|RedirectResponse
    {
        $test = $this->postTest();
        $attempt = $this->attempt($request->user(), $test, 'in_progress');

        if (! $attempt) {
            return to_route('post-test.index');
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

        return Inertia::render('post-test/Take', [
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

    public function submit(SubmitPreTestRequest $request, PostTestGrader $grader): RedirectResponse
    {
        $attempt = $this->attempt($request->user(), $this->postTest(), 'in_progress');

        if (! $attempt) {
            return to_route('post-test.index');
        }

        $grader->grade($attempt, collect($request->validated('answers', [])));

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Post-test selesai. Berikut hasil Anda.']);

        return to_route('post-test.result');
    }

    public function result(Request $request, LearningReport $report): Response|RedirectResponse
    {
        $user = $request->user();
        $test = $this->postTest();
        $attempt = $this->attempt($user, $test, 'completed');

        if (! $attempt) {
            return to_route('post-test.index');
        }

        $perTopic = $attempt->topicScoresFromAnswers();

        $preTestAttempt = TestAttempt::query()
            ->where('user_id', $user->id)
            ->where('status', 'completed')
            ->whereHas('test', fn ($q) => $q->where('kind', Test::KIND_PRE_TEST))
            ->latest('id')
            ->first();
        $preScores = $preTestAttempt?->abilityAssessments()->pluck('score', 'topic_id') ?? collect();

        $topics = Topic::query()
            ->whereIn('id', $perTopic->keys())
            ->orderBy('order')
            ->get(['id', 'code', 'title'])
            ->map(fn (Topic $topic) => [
                'id' => $topic->id,
                'code' => $topic->code,
                'title' => $topic->title,
                ...$perTopic[$topic->id],
                'percent' => $perTopic[$topic->id]['score'],
                'level' => AbilityAssessment::classify($perTopic[$topic->id]['score'])['level'],
                'pre_score' => isset($preScores[$topic->id]) ? (float) $preScores[$topic->id] : null,
            ]);

        return Inertia::render('post-test/Result', [
            'result' => [
                'correct' => $attempt->correct_count,
                'total' => $attempt->total_questions,
                'total_points' => round($topics->sum('score'), 2),
                'max_points' => $topics->count() * 100,
                'percent' => (float) $attempt->score,
                'pre_percent' => $preTestAttempt ? (float) $preTestAttempt->score : null,
                'passing_score' => $test->passing_score,
                'is_passed' => $attempt->is_passed,
                'duration_seconds' => $attempt->duration_seconds,
                'finished_at' => $attempt->finished_at?->locale('id')->isoFormat('D MMMM YYYY, HH:mm'),
            ],
            'topics' => $topics,
            'criteria' => ScoreCriterion::ordered()->map(fn (ScoreCriterion $c) => [
                'level' => $c->level,
                'label' => $c->label,
                'min_score' => (float) $c->min_score,
                'max_score' => (float) $c->max_score,
            ]),
            'overallLevel' => AbilityAssessment::classify((float) $attempt->score)['level'],
            'report' => $report->build($user),
        ]);
    }

    private function postTest(): Test
    {
        return Test::query()
            ->where('kind', Test::KIND_POST_TEST)
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
