<?php

namespace App\Http\Requests\Users;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'surname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', Rule::in([
                User::ROLE_ADMIN,
                User::ROLE_COACH,
                User::ROLE_PARENT,
                User::ROLE_CHILD,
            ])],
            'phone' => ['nullable', 'string', 'max:30'],
            'birth_date' => ['nullable', 'date'],
            'specialization' => ['nullable', 'string', 'max:255'],
            'personal_code' => [
                Rule::requiredIf(fn () => $this->input('role') === User::ROLE_CHILD),
                'nullable',
                'string',
                'max:100',
                'unique:child_profiles,personal_code',
            ],
            'child_identifier' => ['nullable', 'string', 'max:255'],
            'account_balance' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
