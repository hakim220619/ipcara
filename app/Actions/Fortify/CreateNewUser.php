<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'username' => $this->usernameRules(),
            'nim' => $this->nimRules(),
            'semester' => $this->semesterRules(),
            'password' => $this->passwordRules(),
        ], [], [
            'name' => 'nama',
            'nim' => 'NIM',
        ])->validate();

        return User::create([
            'name' => $input['name'],
            'username' => $input['username'],
            'email' => strtolower($input['email']),
            'nim' => $input['nim'],
            'semester' => (int) $input['semester'],
            'program_studi' => 'Keperawatan',
            'role' => 'mahasiswa',
            'password' => $input['password'],
        ]);
    }
}
