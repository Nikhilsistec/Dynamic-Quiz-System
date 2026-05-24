<?php

namespace App\Http\Requests\Admin;

use App\Quiz\QuestionTypeRegistry;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $registry = app(QuestionTypeRegistry::class);

        return [
            'type'            => ['required', Rule::in(array_keys($registry->typeOptions()))],
            'body'            => ['required', 'string'],
            'image'           => ['nullable', 'image', 'max:5120'],
            'youtube_url'     => ['nullable', 'string', 'max:500'],
            'marks'           => ['required', 'integer', 'min:1', 'max:100'],
            'correct_value'   => ['nullable', 'string', 'max:1000'],
            'settings'        => ['nullable', 'array'],
            'options'         => ['nullable', 'array', 'min:2'],
            'options.*'       => ['required', 'string', 'max:1000'],
            'correct_option'  => ['nullable', 'integer', 'min:0'],
            'correct_options' => ['nullable', 'array'],
            'correct_options.*' => ['integer', 'min:0'],
        ];
    }
}
