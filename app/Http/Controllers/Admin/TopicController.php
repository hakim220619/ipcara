<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TopicRequest;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TopicController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/topik/Index', [
            'topics' => Topic::query()
                ->withCount(['materials', 'questions'])
                ->orderBy('order')
                ->orderBy('id')
                ->get()
                ->map(fn (Topic $t) => [
                    'id' => $t->id,
                    'code' => $t->code,
                    'title' => $t->title,
                    'slug' => $t->slug,
                    'description' => $t->description,
                    'learning_objectives' => $t->learning_objectives,
                    'order' => $t->order,
                    'is_published' => $t->is_published,
                    'materials_count' => $t->materials_count,
                    'questions_count' => $t->questions_count,
                ]),
            'nextOrder' => (int) Topic::query()->max('order') + 1,
        ]);
    }

    public function store(TopicRequest $request): RedirectResponse
    {
        Topic::query()->create([
            ...$request->validated(),
            'slug' => $this->uniqueSlug($request->validated('title')),
            'is_published' => $request->boolean('is_published'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Topik berhasil ditambahkan.']);

        return back();
    }

    public function update(TopicRequest $request, Topic $topic): RedirectResponse
    {
        $topic->update([
            ...$request->validated(),
            'slug' => $topic->title === $request->validated('title')
                ? $topic->slug
                : $this->uniqueSlug($request->validated('title'), $topic->id),
            'is_published' => $request->boolean('is_published'),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Topik berhasil diperbarui.']);

        return back();
    }

    public function destroy(Topic $topic): RedirectResponse
    {
        $topic->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Topik beserta materi dan soalnya dihapus.']);

        return back();
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'topik';
        $slug = $base;
        $i = 2;

        while (Topic::query()->where('slug', $slug)->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
