<?php

namespace App\Http\Requests\Payments;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'child_id' => ['required', 'integer', 'exists:users,id'],
            'method' => ['required', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(['pending', 'paid', 'failed'])],
            'items' => ['required', 'array', 'min:1'],
            'items.*.type' => ['required', Rule::in(['session', 'month'])],
            'items.*.session_id' => [
                'nullable',
                'integer',
                'exists:sessions,id',
                Rule::requiredIf(fn () => collect($this->input('items', []))->contains(
                    fn ($item) => ($item['type'] ?? null) === 'session' && empty($item['session_id'])
                )),
            ],
            'items.*.month' => [
                'nullable',
                'string',
                'max:20',
                Rule::requiredIf(fn () => collect($this->input('items', []))->contains(
                    fn ($item) => ($item['type'] ?? null) === 'month' && empty($item['month'])
                )),
            ],
            'items.*.price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
