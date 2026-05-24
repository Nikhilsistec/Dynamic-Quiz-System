<?php

namespace App\Quiz\Handlers;

use App\Models\Answer;
use App\Models\Question;
use App\Quiz\Contracts\QuestionTypeHandlerInterface;

class NumberHandler implements QuestionTypeHandlerInterface
{
    public function type(): string
    {
        return 'number';
    }

    public function label(): string
    {
        return 'Number Input';
    }

    public function inputView(): string
    {
        return 'quiz.inputs.number';
    }

    public function adminOptionsView(): string
    {
        return 'admin.questions.options.number';
    }

    public function usesOptions(): bool
    {
        return false;
    }

    public function validationRules(Question $question): array
    {
        return [
            "answer_{$question->id}" => ['required', 'numeric'],
        ];
    }

    public function saveAnswer(Question $question, int $attemptId, array $payload): Answer
    {
        return Answer::updateOrCreate(
            ['attempt_id' => $attemptId, 'question_id' => $question->id],
            ['answer_text' => (string) ($payload["answer_{$question->id}"] ?? '')]
        );
    }

    public function score(Question $question, Answer $answer): float
    {
        if ($answer->answer_text === null || $question->correct_value === null) {
            return 0.0;
        }

        $settings  = $question->settings ?? [];
        $tolerance = (float) ($settings['tolerance'] ?? 0.001);

        $submitted = (float) $answer->answer_text;
        $correct   = (float) $question->correct_value;

        return abs($submitted - $correct) <= $tolerance
            ? (float) $question->marks
            : 0.0;
    }
}
