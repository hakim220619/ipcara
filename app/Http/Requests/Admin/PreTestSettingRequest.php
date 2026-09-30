<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PreTestSettingRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'duration_minutes' => ['required', 'integer', 'between:5,240'],
            'passing_score' => ['required', 'integer', 'between:0,100'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'duration_minutes' => 'durasi',
            'passing_score' => 'KKM',
        ];
    }
}
