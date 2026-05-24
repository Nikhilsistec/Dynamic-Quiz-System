<?php

namespace App\Quiz\Handlers;

use App\Models\Answer;
use App\Models\Question;
use App\Quiz\Contracts\QuestionTypeHandlerInterface;

class BinaryHandler implements QuestionTypeHandlerInterface
{
    public function type(): string
    {
        return 'binary';
    }

    public function label(): string
    {
        return 'Binary (Yes / No)';
    }

    public function inputView(): string
    {
        return 'quiz.inputs.binary';
    }

    public function adminOptionsView(): string
    {
        return 'admin.questions.options.binary';
    }

    public function usesOptions(): bool
    {
        return false;
    }

    public function validationRules(Question $question): array
    {
        return [
            "answer_{$question->id}" => ['required', 'in:yes,no'],
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

        return strtolower($answer->answer_text) === strtolower($question->correct_value)
            ? (float) $question->marks
            : 0.0;
    }
}
