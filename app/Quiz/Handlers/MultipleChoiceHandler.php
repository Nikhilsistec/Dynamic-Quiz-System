<?php

namespace App\Quiz\Handlers;

use App\Models\Answer;
use App\Models\Question;
use App\Quiz\Contracts\QuestionTypeHandlerInterface;

class MultipleChoiceHandler implements QuestionTypeHandlerInterface
{
    public function type(): string
    {
        return 'multiple_choice';
    }

    public function label(): string
    {
        return 'Multiple Choice';
    }

    public function inputView(): string
    {
        return 'quiz.inputs.multiple_choice';
    }

    public function adminOptionsView(): string
    {
        return 'admin.questions.options.multiple_choice';
    }

    public function usesOptions(): bool
    {
        return true;
    }

    public function validationRules(Question $question): array
    {
        return [
            "answer_{$question->id}"   => ['nullable', 'array'],
            "answer_{$question->id}.*" => ["exists:options,id,question_id,{$question->id}"],
        ];
    }

    public function saveAnswer(Question $question, int $attemptId, array $payload): Answer
    {
        $selected = $payload["answer_{$question->id}"] ?? [];

        return Answer::updateOrCreate(
            ['attempt_id' => $attemptId, 'question_id' => $question->id],
            ['answer_text' => json_encode(array_map('intval', (array) $selected))]
        );
    }

    /**
     * Partial credit: marks × (correct_selected − incorrect_selected) / total_correct
     * floored at 0.
     */
    public function score(Question $question, Answer $answer): float
    {
        $selected  = json_decode($answer->answer_text ?? '[]', true) ?? [];
        $options   = $question->options;
        $correct   = $options->where('is_correct', true)->pluck('id')->all();
        $incorrect = $options->where('is_correct', false)->pluck('id')->all();

        if (empty($correct)) {
            return 0.0;
        }

        $correctHit   = count(array_intersect($selected, $correct));
        $incorrectHit = count(array_intersect($selected, $incorrect));

        $raw = $question->marks * ($correctHit - $incorrectHit) / count($correct);

        return max(0.0, $raw);
    }
}
