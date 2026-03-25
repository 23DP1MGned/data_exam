<?php

namespace App\Http\Requests\Attendance;

use Illuminate\Validation\Rule;

class UpdateAttendanceRequest extends StoreAttendanceRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['present', 'absent'])],
            'comment' => ['nullable', 'string'],
        ];
    }
}
