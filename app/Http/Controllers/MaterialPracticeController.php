<?php

namespace App\Http\Controllers;

use App\Models\Material;
use App\Models\MaterialPracticeAttempt;
use App\Models\MaterialProgress;
use App\Models\Question;
use App\Models\Topic;
use App\Models\User;
use App\Services\MasteryPractice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class MaterialPracticeController extends Controller
{
    public function __construct(private MasteryPractice $practice) {}

    public function start(Request $request, Material $material): RedirectResponse
    {
        abort_unless($material->is_published && MasteryPractice::hasPractice($material), 404);

        $this->practice->start($request->user(), $material);

        return to_route('materi.latihan.take', $material);
    }

    public function take(Request $request, Material $material): Response|RedirectResponse
    {
        $user = $request->user();
        $attempt = $this->practice->current($user, $material);

        if (! $attempt) {
            $latest = $this->practice->latestCompleted($user, $material);

            return $latest
                ? to_route('materi.latihan.result', [$material, $latest])
                : to_route('materi.show', $material);
        }

        $answers = $attempt->answers()->get()->keyBy('question_id');
        $questions = Question::query()
            ->whereIn('id', $attempt->question_ids)
            ->with(['options:id,question_id,label,option_text,is_correct,order', 'indicator:id,description'])
            ->get()
            ->sortBy(fn (Question $q) => array_search($q->id, $attempt->question_ids, true))
            ->values();

        return Inertia::render('materi/latihan/Take', [
            'material' => $this->materialPayload($material),
            'attempt' => [
                'id' => $attempt->id,
                'round' => $attempt->round,
                'attempt_number' => $attempt->attempt_number,
            ],
            'questions' => $questions->map(fn (Question $q) => $this->questionPayload($q, $answers->get($q->id))),
        ]);
    }

    public function answer(Request $request, Material $material): RedirectResponse
    {
        $data = $request->validate([
            'question_id' => ['required', 'integer'],
            'option_id' => ['required', 'integer'],
            'time_spent' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($attempt = $this->practice->current($request->user(), $material)) {
            $this->practice->answer($attempt, (int) $data['question_id'], (int) $data['option_id'], $data['time_spent'] ?? null);
        }

        return to_route('materi.latihan.take', $material);
    }

    public function finish(Request $request, Material $material): RedirectResponse
    {
        $attempt = $this->practice->current($request->user(), $material);

        if (! $attempt) {
            return to_route('materi.latihan.take', $material);
        }

        if (! $this->practice->isComplete($attempt)) {
            Inertia::flash('toast', ['type' => 'info', 'message' => 'Jawab semua soal terlebih dahulu.']);

            return to_route('materi.latihan.take', $material);
        }

        $this->practice->finish($attempt);

        return to_route('materi.latihan.result', [$material, $attempt]);
    }

    public function result(Request $request, Material $material, MaterialPracticeAttempt $attempt): Response
    {
        $user = $request->user();

        abort_unless(
            $attempt->user_id === $user->id
                && $attempt->material_id === $material->id
                && $attempt->status === 'completed',
            404,
        );

        $answers = $attempt->answers()->get()->keyBy('question_id');
        $questions = Question::query()
            ->whereIn('id', $attempt->question_ids)
            ->with(['options:id,question_id,label,option_text,is_correct,order', 'indicator'])
            ->get()
            ->sortBy(fn (Question $q) => array_search($q->id, $attempt->question_ids, true))
            ->values();

        $missed = $this->practice->missedIndicatorIds($user, $material, $attempt);
        $isLatest = $this->practice->latestCompleted($user, $material)?->is($attempt)
            && ! $this->practice->current($user, $material);
        $progress = MaterialProgress::query()->where('user_id', $user->id)->where('material_id', $material->id)->first();

        return Inertia::render('materi/latihan/Result', [
            'material' => $this->materialPayload($material),
            'result' => [
                'round' => $attempt->round,
                'attempt_number' => $attempt->attempt_number,
                'correct' => $attempt->correct_count,
                'total' => $attempt->total_questions,
                'score' => (float) $attempt->score,
                'outcome' => $attempt->outcome,
                'duration_seconds' => $attempt->duration_seconds,
                'mastery_status' => $progress?->mastery_status ?? 'belum',
            ],
            'isLatest' => (bool) $isLatest,
            // Indikator yang belum dikuasai beserta bagian materi dan ringkasan penguatannya.
            'focus' => $material->indicators()
                ->whereIn('id', $missed)
                ->get()
                ->map(fn ($i) => [
                    'description' => $i->description,
                    'section' => $i->section,
                    'anchor' => $i->section ? Str::slug($i->section) : null,
                    'reinforcement' => $i->reinforcement,
                ]),
            'next' => $this->nextStep($user, $material),
            'questions' => $questions->map(fn (Question $q) => $this->questionPayload($q, $answers->get($q->id))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function materialPayload(Material $material): array
    {
        $material->loadMissing('topic:id,title');

        return [
            'title' => $material->title,
            'slug' => $material->slug,
            'topic' => $material->topic?->title,
        ];
    }

    /**
     * Soal beserta umpan balik; kunci dan pembahasan hanya dikirim untuk soal yang sudah dijawab.
     *
     * @return array<string, mixed>
     */
    private function questionPayload(Question $question, $answer): array
    {
        $correct = $question->options->firstWhere('is_correct', true);

        return [
            'id' => $question->id,
            'text' => $question->question_text,
            'indicator' => $question->indicator?->description,
            'options' => $question->options->map(fn ($o) => [
                'id' => $o->id,
                'label' => $o->label,
                'text' => $o->option_text,
            ]),
            'feedback' => $answer ? [
                'chosen_option_id' => $answer->question_option_id,
                'correct_option_id' => $correct?->id,
                'correct_label' => $correct?->label,
                'is_correct' => (bool) $answer->is_correct,
                'explanation' => $question->explanation,
            ] : null,
        ];
    }

    /**
     * Materi berikutnya sesuai urutan topik; setelah materi terakhir tuntas diarahkan ke post-test.
     *
     * @return array{type: string, title: string|null, slug: string|null}
     */
    private function nextStep(User $user, Material $material): array
    {
        $topicOrder = (int) Topic::query()->whereKey($material->topic_id)->value('order');

        $next = Material::query()
            ->select('materials.*')
            ->join('topics', 'topics.id', '=', 'materials.topic_id')
            ->where('materials.is_published', true)
            ->where('topics.is_published', true)
            ->where(fn ($q) => $q
                ->where('topics.order', '>', $topicOrder)
                ->orWhere(fn ($q) => $q->where('topics.order', $topicOrder)->where('materials.order', '>', $material->order)))
            ->orderBy('topics.order')
            ->orderBy('materials.order')
            ->first();

        if ($user->canTakePostTest()) {
            return ['type' => 'post-test', 'title' => null, 'slug' => null];
        }

        return $next
            ? ['type' => 'materi', 'title' => $next->title, 'slug' => $next->slug]
            : ['type' => 'materi-index', 'title' => null, 'slug' => null];
    }
}
