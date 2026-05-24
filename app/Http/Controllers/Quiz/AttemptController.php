<?php

namespace App\Http\Controllers\Quiz;

use App\Http\Controllers\Controller;
use App\Http\Requests\Quiz\StartAttemptRequest;
use App\Http\Requests\Quiz\SubmitAttemptRequest;
use App\Models\Attempt;
use App\Models\Quiz;
use App\Quiz\QuestionTypeRegistry;
use App\Quiz\Services\QuizEvaluator;

class AttemptController extends Controller
{
    public function __construct(
        private QuestionTypeRegistry $registry,
        private QuizEvaluator $evaluator
    ) {}

    public function index()
    {
        $quizzes = Quiz::where('is_published', true)
                       ->withCount('questions')
                       ->latest()
                       ->get();

        return view('quiz.index', compact('quizzes'));
    }

    public function show(Quiz $quiz)
    {
        abort_if(! $quiz->is_published, 404);
        $quiz->loadCount('questions');
        $maxScore = $quiz->questions()->sum('marks');

        return view('quiz.show', compact('quiz', 'maxScore'));
    }

    public function start(Quiz $quiz)
    {
        abort_if(! $quiz->is_published, 404);

        return view('quiz.start', compact('quiz'));
    }

    public function begin(StartAttemptRequest $request, Quiz $quiz)
    {
        abort_if(! $quiz->is_published, 404);

        $attempt = Attempt::create([
            'quiz_id'           => $quiz->id,
            'participant_name'  => $request->participant_name,
            'participant_email' => $request->participant_email,
            'started_at'        => now(),
        ]);

        return redirect()->route('quizzes.take', $attempt);
    }

    public function take(Attempt $attempt)
    {
        if ($attempt->isSubmitted()) {
            return redirect()->route('quizzes.result', $attempt);
        }

        $attempt->loadMissing('quiz.questions.options');
        $registry = $this->registry;

        return view('quiz.attempt', compact('attempt', 'registry'));
    }

    public function submit(SubmitAttemptRequest $request, Attempt $attempt)
    {
        if ($attempt->isSubmitted()) {
            return redirect()->route('quizzes.result', $attempt);
        }

        $attempt->loadMissing('quiz.questions');

        // Server-side time limit enforcement
        if ($attempt->quiz->time_limit_minutes) {
            $deadline = $attempt->started_at->addMinutes($attempt->quiz->time_limit_minutes + 1); // +1 grace minute
            if (now()->greaterThan($deadline)) {
                // Still evaluate what was submitted, but flag as late
                $attempt->update(['submitted_at' => $deadline]);
            }
        }

        $payload = $request->all();

        foreach ($attempt->quiz->questions as $question) {
            $handler = $this->registry->get($question->type);
            $handler->saveAnswer($question, $attempt->id, $payload);
        }

        $this->evaluator->evaluate($attempt);

        return redirect()->route('quizzes.result', $attempt);
    }

    public function result(Attempt $attempt)
    {
        $attempt->load([
            'quiz.questions.options',
            'answers.selectedOption',
        ]);

        $registry = $this->registry;

        return view('quiz.result', compact('attempt', 'registry'));
    }
}
