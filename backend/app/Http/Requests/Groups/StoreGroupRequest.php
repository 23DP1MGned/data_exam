<?php

namespace App\Http\Requests\Groups;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'group_number' => ['nullable', 'integer', 'min:1', Rule::unique('groups', 'group_number')],
            'age_category' => ['nullable', 'string', 'max:255'],
            'schedule_days' => ['nullable', 'string', 'max:255'],
            'default_time' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'coach_id' => ['nullable', 'integer', 'exists:users,id'],
            'child_ids' => ['nullable', 'array'],
            'child_ids.*' => ['integer', 'exists:users,id'],
        ];
    }
}
