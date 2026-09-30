<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\QuestionRequest;
use App\Models\Material;
use App\Models\Question;
use App\Models\Test;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'topic', 'difficulty', 'tes']);
        $preTestQuestionIds = $this->testQuestionIds(Test::KIND_PRE_TEST);
        $postTestQuestionIds = $this->testQuestionIds(Test::KIND_POST_TEST);

        $questions = Question::query()
            ->with(['topic:id,title,code', 'material:id,title', 'correctOption:id,question_id,label'])
            ->when($filters['search'] ?? null, fn ($q, $search) => $q->where('question_text', 'like', "%{$search}%"))
            ->when($filters['topic'] ?? null, fn ($q, $topic) => $q->where('topic_id', $topic))
            ->when($filters['difficulty'] ?? null, fn ($q, $difficulty) => $q->where('difficulty', $difficulty))
            ->when(in_array($filters['tes'] ?? null, [Test::KIND_PRE_TEST, Test::KIND_POST_TEST], true), fn ($q) => $q
                ->whereHas('tests', fn ($t) => $t->where('kind', $filters['tes'])))
            ->when(($filters['tes'] ?? null) === 'latihan', fn ($q) => $q->whereNotNull('practice_role'))
            ->orderBy('topic_id')
            ->orderBy('id')
            ->paginate(15)
            ->withQueryString()
            ->through(fn (Question $q) => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'topic' => $q->topic?->title,
                'difficulty' => $q->difficulty,
                'correct' => $q->correctOption?->label,
                'is_active' => $q->is_active,
                'in_pre_test' => $preTestQuestionIds->contains($q->id),
                'in_post_test' => $postTestQuestionIds->contains($q->id),
                'practice_role' => $q->practice_role,
                'material' => $q->material?->title,
            ]);

        return Inertia::render('admin/soal/Index', [
            'questions' => $questions,
            'topics' => $this->topics(),
            'filters' => $filters,
            'preTestCount' => $preTestQuestionIds->count(),
            'postTestCount' => $postTestQuestionIds->count(),
            'latihanCount' => Question::query()->whereNotNull('practice_role')->count(),
        ]);
    }

    public function create(Request $request): Response
    {
        return Inertia::render('admin/soal/Form', [
            'question' => null,
            'topics' => $this->topics(),
            'materials' => $this->materials(),
            'tes' => $request->query('tes'),
        ]);
    }

    public function store(QuestionRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $question = Question::query()->create([
                ...$request->safe()->only(['topic_id', 'difficulty', 'question_text', 'explanation']),
                ...$this->practiceFields($request),
                'type' => 'pilihan_ganda',
                'points' => 1,
                'is_active' => $request->boolean('is_active', true),
                'created_by' => $request->user()->id,
            ]);

            $this->syncOptions($question, $request->validated('options'), (int) $request->validated('correct_option'));
            $this->syncTest($question, Test::KIND_PRE_TEST, $request->boolean('in_pre_test', true));
            $this->syncTest($question, Test::KIND_POST_TEST, $request->boolean('in_post_test'));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Soal berhasil ditambahkan.']);

        return $this->backToIndex($request);
    }

    public function edit(Request $request, Question $question): Response
    {
        $question->load('options');

        return Inertia::render('admin/soal/Form', [
            'question' => [
                'id' => $question->id,
                'topic_id' => $question->topic_id,
                'difficulty' => $question->difficulty,
                'question_text' => $question->question_text,
                'explanation' => $question->explanation,
                'is_active' => $question->is_active,
                'in_pre_test' => $this->testQuestionIds(Test::KIND_PRE_TEST)->contains($question->id),
                'in_post_test' => $this->testQuestionIds(Test::KIND_POST_TEST)->contains($question->id),
                'material_id' => $question->material_id,
                'indicator_id' => $question->indicator_id,
                'practice_role' => $question->practice_role,
                'options' => $question->options->pluck('option_text')->pad(4, '')->take(4)->values(),
                'correct_option' => max(0, (int) $question->options->search(fn ($o) => $o->is_correct)),
            ],
            'topics' => $this->topics(),
            'materials' => $this->materials(),
            'tes' => $request->query('tes'),
        ]);
    }

    public function update(QuestionRequest $request, Question $question): RedirectResponse
    {
        DB::transaction(function () use ($request, $question) {
            $question->update([
                ...$request->safe()->only(['topic_id', 'difficulty', 'question_text', 'explanation']),
                ...$this->practiceFields($request),
                'is_active' => $request->boolean('is_active'),
            ]);

            $this->syncOptions($question, $request->validated('options'), (int) $request->validated('correct_option'));
            $this->syncTest($question, Test::KIND_PRE_TEST, $request->boolean('in_pre_test'));
            $this->syncTest($question, Test::KIND_POST_TEST, $request->boolean('in_post_test'));
        });

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Soal berhasil diperbarui.']);

        return $this->backToIndex($request);
    }

    public function destroy(Question $question): RedirectResponse
    {
        $question->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Soal berhasil dihapus.']);

        return back();
    }

    /**
     * Opsi diperbarui per label (A–D) agar jawaban mahasiswa yang lama tetap terhubung.
     *
     * @param  list<string>  $options
     */
    private function syncOptions(Question $question, array $options, int $correct): void
    {
        foreach (array_values($options) as $idx => $text) {
            $question->options()->updateOrCreate(
                ['label' => chr(65 + $idx)],
                ['option_text' => $text, 'is_correct' => $idx === $correct, 'order' => $idx + 1],
            );
        }

        $question->options()->whereNotIn('label', ['A', 'B', 'C', 'D'])->delete();
    }

    private function syncTest(Question $question, string $kind, bool $include): void
    {
        $test = $this->test($kind);

        if (! $test) {
            return;
        }

        $attached = $test->questions()->whereKey($question->id)->exists();

        if ($include && ! $attached) {
            $nextOrder = (int) DB::table('test_questions')->where('test_id', $test->id)->max('order') + 1;
            $test->questions()->attach($question->id, ['order' => $nextOrder]);
        } elseif (! $include && $attached) {
            $test->questions()->detach($question->id);
        }
    }

    /**
     * Soal latihan materi: terikat pada materi, indikator kompetensi, dan perannya (utama/penguatan/remedial).
     *
     * @return array{material_id: int|null, indicator_id: int|null, practice_role: string|null}
     */
    private function practiceFields(QuestionRequest $request): array
    {
        $role = $request->validated('practice_role');

        return [
            'material_id' => $request->validated('material_id'),
            'indicator_id' => $role ? (int) $request->validated('indicator_id') : null,
            'practice_role' => $role ?: null,
        ];
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function materials(): Collection
    {
        return Material::query()
            ->with('indicators:id,material_id,description,order')
            ->orderBy('topic_id')
            ->orderBy('order')
            ->get(['id', 'topic_id', 'title'])
            ->map(fn (Material $m) => [
                'id' => $m->id,
                'topic_id' => $m->topic_id,
                'title' => $m->title,
                'indicators' => $m->indicators->map(fn ($i) => ['id' => $i->id, 'description' => $i->description]),
            ]);
    }

    private function test(string $kind): ?Test
    {
        return Test::query()->where('kind', $kind)->first();
    }

    /**
     * @return Collection<int, int>
     */
    private function testQuestionIds(string $kind): Collection
    {
        return DB::table('test_questions')
            ->join('tests', 'tests.id', '=', 'test_questions.test_id')
            ->where('tests.kind', $kind)
            ->pluck('test_questions.question_id')
            ->unique()
            ->values();
    }

    /**
     * Kembali ke tab bank soal asal (semua / pre-test / post-test).
     */
    private function backToIndex(Request $request): RedirectResponse
    {
        return to_route('admin.questions.index', array_filter(['tes' => $request->input('tes')]));
    }

    /**
     * @return Collection<int, array{id: int, title: string}>
     */
    private function topics()
    {
        return Topic::query()->orderBy('order')->get(['id', 'title'])->map(fn (Topic $t) => [
            'id' => $t->id,
            'title' => $t->title,
        ]);
    }
}
