<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\Theme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AppearanceController extends Controller
{
    public function index(): Response
    {
        $overrides = Theme::overrides();

        return Inertia::render('admin/tampilan/Index', [
            'colors' => collect(Theme::colors())
                ->map(fn (array $c, string $key) => [
                    'key' => $key,
                    'label' => $c['label'],
                    'group' => $c['group'],
                    'vars' => $c['vars'],
                    'default' => $c['default'],
                    'value' => $overrides[$key] ?? $c['default'],
                ])
                ->values(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $colors = Theme::colors();

        $rules = ['colors' => ['required', 'array']];
        foreach (array_keys($colors) as $key) {
            $rules["colors.{$key}"] = ['nullable', 'string', 'regex:/^#[0-9a-fA-F]{6}$/'];
        }

        $validated = $request->validate($rules, [
            'colors.*.regex' => 'Warna harus berformat hex, misalnya #90CAF9.',
        ]);

        $values = [];
        foreach ($colors as $key => $color) {
            $value = isset($validated['colors'][$key]) ? strtolower($validated['colors'][$key]) : null;
            $values[Theme::PREFIX.$key] = $value === null || $value === $color['default'] ? null : $value;
        }

        Setting::putMany($values);

        Inertia::flash('toast', ['type' => 'success', 'message' => 'Pengaturan warna disimpan.']);

        return back();
    }
}
