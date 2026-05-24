<?php

namespace App\Quiz\Contracts;

use App\Models\Answer;
use App\Models\Question;

interface QuestionTypeHandlerInterface
{
    /**
     * Machine-readable key stored in questions.type column.
     */
    public function type(): string;

    /**
     * Human-readable label for admin dropdowns.
     */
    public function label(): string;

    /**
     * Blade partial path for rendering the answer input during a quiz attempt.
     * The partial receives $question and $attempt variables.
     */
    public function inputView(): string;

    /**
     * Blade partial path for the type-specific options section in the admin editor.
     */
    public function adminOptionsView(): string;

    /**
     * Laravel validation rules for this type's submitted answer.
     * Field naming convention: "answer_{$question->id}"
     */
    public function validationRules(Question $question): array;

    /**
     * Persist the raw submitted payload as an Answer row.
     * Returns the created Answer model.
     */
    public function saveAnswer(Question $question, int $attemptId, array $payload): Answer;

    /**
     * Score the stored Answer. Returns a float between 0 and $question->marks.
     */
    public function score(Question $question, Answer $answer): float;

    /**
     * Whether this type stores options in the options table.
     */
    public function usesOptions(): bool;
}
