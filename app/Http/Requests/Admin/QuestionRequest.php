<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class QuestionRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'topic_id' => ['required', 'integer', Rule::exists('topics', 'id')],
            'difficulty' => ['required', Rule::in(['mudah', 'sedang', 'sulit'])],
            'question_text' => ['required', 'string', 'max:2000'],
            'explanation' => ['nullable', 'string', 'max:2000'],
            'options' => ['required', 'array', 'size:4'],
            'options.*' => ['required', 'string', 'max:500'],
            'correct_option' => ['required', 'integer', 'between:0,3'],
            'is_active' => ['boolean'],
            'in_pre_test' => ['boolean'],
            'in_post_test' => ['boolean'],
            'practice_role' => ['nullable', Rule::in(['utama', 'penguatan', 'remedial'])],
            'material_id' => ['nullable', 'required_with:practice_role', 'integer', Rule::exists('materials', 'id')->where('topic_id', $this->input('topic_id'))],
            'indicator_id' => ['nullable', 'required_with:practice_role', 'integer', Rule::exists('competency_indicators', 'id')->where('material_id', $this->input('material_id'))],
            'tes' => ['nullable', Rule::in(['pre_test', 'post_test', 'latihan'])],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'topic_id' => 'topik',
            'difficulty' => 'tingkat kesulitan',
            'question_text' => 'teks soal',
            'explanation' => 'pembahasan',
            'options.0' => 'pilihan A',
            'options.1' => 'pilihan B',
            'options.2' => 'pilihan C',
            'options.3' => 'pilihan D',
            'correct_option' => 'kunci jawaban',
            'practice_role' => 'peran latihan',
            'material_id' => 'materi',
            'indicator_id' => 'indikator kompetensi',
        ];
    }
}
