<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateQuizRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'               => ['required', 'string', 'max:255'],
            'description'         => ['nullable', 'string'],
            'is_published'        => ['nullable', 'boolean'],
            'time_limit_minutes'  => ['nullable', 'integer', 'min:1', 'max:480'],
        ];
    }
}
