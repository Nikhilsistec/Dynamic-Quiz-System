<?php

namespace App\Http\Requests\Quiz;

use Illuminate\Foundation\Http\FormRequest;

class StartAttemptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'participant_name'  => ['required', 'string', 'max:255'],
            'participant_email' => ['nullable', 'email', 'max:255'],
        ];
    }
}
