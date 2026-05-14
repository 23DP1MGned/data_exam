<?php

namespace App\Http\Requests\Sessions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'group_id' => ['required', 'integer', 'exists:groups,id'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['nullable', 'date', 'required_without:weekdays'],
            'weekdays' => ['nullable', 'array', 'min:1', 'required_without:date'],
            'weekdays.*' => ['string', Rule::in(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'])],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', Rule::in(['planned', 'completed', 'cancelled'])],
        ];
    }
}
