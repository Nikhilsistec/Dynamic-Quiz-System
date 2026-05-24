<?php

namespace App\Http\Requests\Quiz;

use App\Quiz\QuestionTypeRegistry;
use Illuminate\Foundation\Http\FormRequest;

class SubmitAttemptRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $attempt  = $this->route('attempt');
        $attempt->loadMissing('quiz.questions');
        $registry = app(QuestionTypeRegistry::class);
        $rules    = [];

        foreach ($attempt->quiz->questions as $question) {
            $handler = $registry->get($question->type);
            $rules   = array_merge($rules, $handler->validationRules($question));
        }

        return $rules;
    }
}
