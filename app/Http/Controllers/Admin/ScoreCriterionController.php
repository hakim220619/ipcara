<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PreTestSettingRequest;
use App\Http\Requests\Admin\ScoreCriterionRequest;
use App\Models\ScoreCriterion;
use App\Models\Test;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ScoreCriterionController extends Controller
{
    public function index(): Response
    {
        $preTest = Test::query()->where('kind', Test::KIND_PRE_TEST)->first();

        return Inertia::render('admin/penilaian/Index', [
            'criteria' => ScoreCriterion::ordered()->map(fn (ScoreCriterion $c) => [
                'id' => $c->id,
                'level' => $c->level,
                'label' => $c->label,
                'min_score' => (float) $c->min_score,
                'max_score' => (float) $c->max_score,
                'recommended_material_level' => $c->recommended_material_level,
                'is_recommended' => $c->is_recommended,
                'description' => $c->description,
            ]),
            'settings' => $preTest ? [
                'duration_minutes' => $preTest->duration_minutes,
                'passing_score' => $preTest->passing_score,
            ] : null,
        ]);
    }

    public function store(ScoreCriterionRequest $request): RedirectResponse
    {
        ScoreCriterion::query()->create($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kriteria penilaian ditambahkan.']);

        return back();
    }

    public function update(ScoreCriterionRequest $request, ScoreCriterion $criterion): RedirectResponse
    {
        $criterion->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kriteria penilaian diperbarui.']);

        return back();
    }

    public function destroy(ScoreCriterion $criterion): RedirectResponse
    {
        $criterion->delete();

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Kriteria penilaian dihapus.']);

        return back();
    }

    public function updateSettings(PreTestSettingRequest $request): RedirectResponse
    {
        Test::query()->where('kind', Test::KIND_PRE_TEST)->firstOrFail()->update($request->validated());

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pengaturan pre-test disimpan.']);

        return back();
    }
}
