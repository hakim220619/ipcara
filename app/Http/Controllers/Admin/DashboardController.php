<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AbilityAssessment;
use App\Models\Question;
use App\Models\ScoreCriterion;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\Topic;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $preTest = Test::query()->where('kind', Test::KIND_PRE_TEST)->first();

        $attempts = TestAttempt::query()
            ->where('test_id', $preTest?->id)
            ->where('status', 'completed')
            ->whereHas('user', fn ($q) => $q->where('role', 'mahasiswa'))
            ->with('user:id,name,nim')
            ->get();

        $criteria = ScoreCriterion::ordered();

        $distribution = $criteria->map(fn (ScoreCriterion $c) => [
            'level' => $c->level,
            'label' => $c->label,
            'count' => $attempts->filter(fn (TestAttempt $a) => AbilityAssessment::classify((float) $a->score, $criteria)['level'] === $c->level)->count(),
        ]);

        $topicAverages = Topic::query()
            ->orderBy('order')
            ->get(['id', 'title'])
            ->map(fn (Topic $t) => [
                'title' => $t->title,
                'average' => round((float) AbilityAssessment::query()
                    ->where('topic_id', $t->id)
                    ->where('source', 'pre_test')
                    ->whereHas('user', fn ($q) => $q->where('role', 'mahasiswa'))
                    ->avg('score'), 1),
            ]);

        return Inertia::render('admin/Dashboard', [
            'stats' => [
                'questions' => Question::query()->count(),
                'activeQuestions' => $preTest ? $preTest->questions()->where('is_active', true)->count() : 0,
                'students' => User::query()->where('role', 'mahasiswa')->count(),
                'completed' => $attempts->count(),
                'average' => $attempts->isEmpty() ? null : round((float) $attempts->avg('score'), 1),
                'passed' => $attempts->where('is_passed', true)->count(),
                'passingScore' => $preTest?->passing_score,
            ],
            'distribution' => $distribution,
            'topicAverages' => $topicAverages,
            'recent' => $attempts->sortByDesc('finished_at')->take(5)->values()->map(fn (TestAttempt $a) => [
                'id' => $a->id,
                'name' => $a->user?->name,
                'nim' => $a->user?->nim,
                'score' => (float) $a->score,
                'is_passed' => $a->is_passed,
                'finished_at' => $a->finished_at?->locale('id')->isoFormat('D MMM YYYY, HH:mm'),
            ]),
        ]);
    }
}
