<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'session_id' => ['required', 'integer', 'exists:sessions,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'status' => ['required', Rule::in(['present', 'absent'])],
            'comment' => ['nullable', 'string'],
        ];
    }
}
