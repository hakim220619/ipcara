<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TopicRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:20', Rule::unique('topics', 'code')->ignore($this->route('topic'))],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'learning_objectives' => ['nullable', 'string', 'max:2000'],
            'order' => ['required', 'integer', 'between:0,999'],
            'is_published' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => 'kode',
            'title' => 'judul topik',
            'description' => 'deskripsi',
            'learning_objectives' => 'tujuan pembelajaran',
            'order' => 'urutan',
        ];
    }
}
