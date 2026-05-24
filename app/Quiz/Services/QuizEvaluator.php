<?php

namespace App\Quiz\Services;

use App\Models\Attempt;
use App\Quiz\QuestionTypeRegistry;

class QuizEvaluator
{
    public function __construct(private QuestionTypeRegistry $registry) {}

    public function evaluate(Attempt $attempt): void
    {
        $attempt->loadMissing(['quiz.questions.options', 'answers']);

        $totalScore = 0;
        $maxScore   = 0;

        foreach ($attempt->quiz->questions as $question) {
            $maxScore += $question->marks;

            $answer = $attempt->answers->firstWhere('question_id', $question->id);

            if ($answer === null) {
                continue;
            }

            $handler = $this->registry->get($question->type);
            $awarded = $handler->score($question, $answer);

            $answer->update(['marks_awarded' => (int) round($awarded)]);
            $totalScore += $awarded;
        }

        $attempt->update([
            'score'        => (int) round($totalScore),
            'max_score'    => $maxScore,
            'submitted_at' => now(),
        ]);
    }
}
