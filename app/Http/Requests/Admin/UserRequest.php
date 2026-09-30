<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Validator;

class UserRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'nim' => $this->filled('nim') ? trim((string) $this->input('nim')) : null,
            'username' => $this->filled('username') ? trim((string) $this->input('username')) : null,
            'semester' => $this->filled('semester') ? $this->input('semester') : null,
            'email' => strtolower(trim((string) $this->input('email'))),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        /** @var User|null $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'min:3', 'max:50', 'alpha_dash:ascii', Rule::unique('users', 'username')->ignore($user)],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            'role' => ['required', Rule::in(['mahasiswa', 'admin'])],
            'nim' => ['nullable', 'string', 'max:30', Rule::unique('users', 'nim')->ignore($user)],
            'semester' => ['nullable', 'integer', 'between:1,14'],
            'program_studi' => ['nullable', 'string', 'max:100'],
            'password' => [$user ? 'nullable' : 'required', 'string', Password::defaults(), 'confirmed'],
        ];
    }

    /**
     * @return array<int, \Closure(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                /** @var User|null $user */
                $user = $this->route('user');

                if ($user && $user->is($this->user()) && $this->input('role') !== 'admin') {
                    $validator->errors()->add('role', 'Anda tidak dapat mengubah role akun Anda sendiri.');
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
            'name' => 'nama',
            'nim' => 'NIM',
            'program_studi' => 'program studi',
        ];
    }
}
