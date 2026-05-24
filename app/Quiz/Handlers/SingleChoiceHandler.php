<?php

namespace App\Quiz\Handlers;

use App\Models\Answer;
use App\Models\Option;
use App\Models\Question;
use App\Quiz\Contracts\QuestionTypeHandlerInterface;

class SingleChoiceHandler implements QuestionTypeHandlerInterface
{
    public function type(): string
    {
        return 'single_choice';
    }

    public function label(): string
    {
        return 'Single Choice';
    }

    public function inputView(): string
    {
        return 'quiz.inputs.single_choice';
    }

    public function adminOptionsView(): string
    {
        return 'admin.questions.options.single_choice';
    }

    public function usesOptions(): bool
    {
        return true;
    }

    public function validationRules(Question $question): array
    {
        return [
            "answer_{$question->id}" => [
                'required',
                "exists:options,id,question_id,{$question->id}",
            ],
        ];
    }

    public function saveAnswer(Question $question, int $attemptId, array $payload): Answer
    {
        $optionId = $payload["answer_{$question->id}"] ?? null;

        return Answer::updateOrCreate(
            ['attempt_id' => $attemptId, 'question_id' => $question->id],
            ['selected_option_id' => $optionId]
        );
    }

    public function score(Question $question, Answer $answer): float
    {
        if ($answer->selected_option_id === null) {
            return 0.0;
        }

        $option = Option::find($answer->selected_option_id);

        return ($option && $option->is_correct) ? (float) $question->marks : 0.0;
    }
}
