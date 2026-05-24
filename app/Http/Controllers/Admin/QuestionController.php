<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuestionRequest;
use App\Http\Requests\Admin\UpdateQuestionRequest;
use App\Models\Question;
use App\Models\Quiz;
use App\Quiz\QuestionTypeRegistry;
use Illuminate\Support\Facades\Storage;

class QuestionController extends Controller
{
    public function __construct(private QuestionTypeRegistry $registry) {}

    public function create(Quiz $quiz)
    {
        $typeOptions = $this->registry->typeOptions();

        return view('admin.questions._form', [
            'quiz'        => $quiz,
            'question'    => null,
            'typeOptions' => $typeOptions,
        ]);
    }

    public function store(StoreQuestionRequest $request, Quiz $quiz)
    {
        $data = $request->validated();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/questions', 'public');
        }

        $order = $quiz->questions()->max('order') + 1;

        $question = Question::create([
            'quiz_id'       => $quiz->id,
            'type'          => $data['type'],
            'body'          => $data['body'],
            'image_path'    => $imagePath,
            'youtube_url'   => $data['youtube_url'] ?? null,
            'marks'         => $data['marks'],
            'order'         => $order,
            'correct_value' => $data['correct_value'] ?? null,
            'settings'      => $data['settings'] ?? null,
        ]);

        // Create options for single/multiple choice questions
        if (!empty($data['options'])) {
            $correctOption  = $data['correct_option'] ?? null;   // single choice (index)
            $correctOptions = $data['correct_options'] ?? [];    // multiple choice (indices)

            foreach ($data['options'] as $idx => $body) {
                if (trim($body) === '') {
                    continue;
                }

                $isCorrect = ($correctOption !== null && (int) $correctOption === $idx)
                          || in_array($idx, array_map('intval', $correctOptions));

                $question->options()->create([
                    'body'       => $body,
                    'is_correct' => $isCorrect,
                    'order'      => $idx + 1,
                ]);
            }
        }

        return redirect()->route('admin.quizzes.show', $quiz)
                         ->with('success', 'Question added.');
    }

    public function edit(Quiz $quiz, Question $question)
    {
        $question->load('options');
        $typeOptions = $this->registry->typeOptions();

        return view('admin.questions._form', compact('quiz', 'question', 'typeOptions'));
    }

    public function update(UpdateQuestionRequest $request, Quiz $quiz, Question $question)
    {
        $data = $request->validated();

        $imagePath = $question->image_path;
        if ($request->hasFile('image')) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('images/questions', 'public');
        }

        $question->update([
            'type'          => $data['type'],
            'body'          => $data['body'],
            'image_path'    => $imagePath,
            'youtube_url'   => $data['youtube_url'] ?? null,
            'marks'         => $data['marks'],
            'correct_value' => $data['correct_value'] ?? null,
            'settings'      => $data['settings'] ?? null,
        ]);

        return redirect()->route('admin.quizzes.show', $quiz)
                         ->with('success', 'Question updated.');
    }

    public function destroy(Quiz $quiz, Question $question)
    {
        if ($question->image_path) {
            Storage::disk('public')->delete($question->image_path);
        }

        $question->delete();

        return redirect()->route('admin.quizzes.show', $quiz)
                         ->with('success', 'Question deleted.');
    }
}
