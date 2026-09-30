<?php

namespace App\Http\Requests\Admin;

use App\Models\ScoreCriterion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ScoreCriterionRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'level' => [
                'required',
                Rule::in(ScoreCriterion::LEVELS),
                Rule::unique('score_criteria', 'level')->ignore($this->route('criterion')),
            ],
            'label' => ['required', 'string', 'max:50'],
            'min_score' => ['required', 'numeric', 'between:0,100'],
            'max_score' => ['required', 'numeric', 'between:0,100', 'gte:min_score'],
            'recommended_material_level' => ['required', Rule::in(['dasar', 'menengah', 'lanjut'])],
            'is_recommended' => ['boolean'],
            'description' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $min = (float) $this->input('min_score');
                $max = (float) $this->input('max_score');

                $overlap = ScoreCriterion::query()
                    ->when($this->route('criterion'), fn ($q, $c) => $q->whereKeyNot($c->id))
                    ->where('min_score', '<=', $max)
                    ->where('max_score', '>=', $min)
                    ->first();

                if ($overlap) {
                    $validator->errors()->add(
                        'min_score',
                        "Rentang nilai bertabrakan dengan kriteria {$overlap->label} ({$overlap->min_score}–{$overlap->max_score}).",
                    );
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'level' => 'kategori',
            'label' => 'nama kategori',
            'min_score' => 'nilai minimum',
            'max_score' => 'nilai maksimum',
            'recommended_material_level' => 'level materi',
            'description' => 'keterangan',
        ];
    }
}
