<?php

namespace App\Http\Requests\Settings;

use App\Concerns\ProfileValidationRules;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    use ProfileValidationRules;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->user();
        $isMahasiswa = $user->isMahasiswa();

        return [
            ...$this->profileRules($user->id),
            'username' => $this->usernameRules($user->id, required: $isMahasiswa),
            'semester' => $this->semesterRules(required: $isMahasiswa),
        ];
    }
}
