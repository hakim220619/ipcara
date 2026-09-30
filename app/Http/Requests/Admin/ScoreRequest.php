<?php

namespace App\Http\Requests\Admin;

use App\Models\Test;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ScoreRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $creating = $this->route('attempt') === null;

        return [
            'user_id' => $creating ? [
                'required',
                'integer',
                Rule::exists('users', 'id')->where('role', 'mahasiswa'),
                Rule::unique('test_attempts', 'user_id')->where(fn ($q) => $q->whereIn(
                    'test_id',
                    Test::query()->where('kind', Test::KIND_PRE_TEST)->select('id'),
                )),
            ] : ['prohibited'],
            'topic_scores' => ['required', 'array'],
            'topic_scores.*' => ['required', 'numeric', 'between:0,100'],
            'finished_at' => ['required', 'date'],
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $expected = $this->preTestTopicIds();
                $given = array_map('intval', array_keys((array) $this->input('topic_scores', [])));

                if (array_diff($expected, $given) || array_diff($given, $expected)) {
                    $validator->errors()->add('topic_scores', 'Nilai harus diisi untuk setiap topik pre-test.');
                }
            },
        ];
    }

    /**
     * @return list<int>
     */
    public function preTestTopicIds(): array
    {
        $preTest = Test::query()->where('kind', Test::KIND_PRE_TEST)->first();

        return $preTest
            ? $preTest->questions()->where('is_active', true)->reorder()->pluck('questions.topic_id')->unique()->map(fn ($id) => (int) $id)->values()->all()
            : [];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_id.unique' => 'Mahasiswa ini sudah memiliki nilai pre-test.',
            'topic_scores.*.required' => 'Nilai topik wajib diisi.',
            'topic_scores.*.between' => 'Nilai topik harus antara 0 dan 100.',
            'topic_scores.*.numeric' => 'Nilai topik harus berupa angka.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'mahasiswa',
            'topic_scores' => 'nilai per topik',
            'finished_at' => 'tanggal pengerjaan',
        ];
    }
}
