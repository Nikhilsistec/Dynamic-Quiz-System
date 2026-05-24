<?php

namespace App\Quiz\Handlers;

use App\Models\Answer;
use App\Models\Question;
use App\Quiz\Contracts\QuestionTypeHandlerInterface;

class TextHandler implements QuestionTypeHandlerInterface
{
    public function type(): string
    {
        return 'text';
    }

    public function label(): string
    {
        return 'Text Input';
    }

    public function inputView(): string
    {
        return 'quiz.inputs.text';
    }

    public function adminOptionsView(): string
    {
        return 'admin.questions.options.text';
    }

    public function usesOptions(): bool
    {
        return false;
    }

    public function validationRules(Question $question): array
    {
        return [
            "answer_{$question->id}" => ['required', 'string', 'max:5000'],
        ];
    }

    public function saveAnswer(Question $question, int $attemptId, array $payload): Answer
    {
        return Answer::updateOrCreate(
            ['attempt_id' => $attemptId, 'question_id' => $question->id],
            ['answer_text' => $payload["answer_{$question->id}"] ?? null]
        );
    }

    public function score(Question $question, Answer $answer): float
    {
        if ($answer->answer_text === null || $question->correct_value === null) {
            return 0.0;
        }

        $submitted = strtolower(trim($answer->answer_text));
        $correct   = strtolower(trim($question->correct_value));

        return $submitted === $correct ? (float) $question->marks : 0.0;
    }
}
