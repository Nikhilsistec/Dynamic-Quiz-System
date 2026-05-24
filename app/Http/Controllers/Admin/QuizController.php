<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreQuizRequest;
use App\Http\Requests\Admin\UpdateQuizRequest;
use App\Models\Quiz;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::withCount('questions')->latest()->paginate(20);

        return view('admin.quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        return view('admin.quizzes.create');
    }

    public function store(StoreQuizRequest $request)
    {
        $quiz = Quiz::create([
            'title'              => $request->title,
            'description'        => $request->description,
            'is_published'       => $request->boolean('is_published'),
            'time_limit_minutes' => $request->time_limit_minutes,
        ]);

        return redirect()->route('admin.quizzes.show', $quiz)
                         ->with('success', 'Quiz created successfully.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions.options');

        return view('admin.quizzes.show', compact('quiz'));
    }

    public function edit(Quiz $quiz)
    {
        return view('admin.quizzes.edit', compact('quiz'));
    }

    public function update(UpdateQuizRequest $request, Quiz $quiz)
    {
        $quiz->update([
            'title'              => $request->title,
            'description'        => $request->description,
            'is_published'       => $request->boolean('is_published'),
            'time_limit_minutes' => $request->time_limit_minutes,
        ]);

        return redirect()->route('admin.quizzes.show', $quiz)
                         ->with('success', 'Quiz updated.');
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();

        return redirect()->route('admin.quizzes.index')
                         ->with('success', 'Quiz deleted.');
    }

    public function togglePublish(Quiz $quiz)
    {
        $quiz->update(['is_published' => !$quiz->is_published]);

        $status = $quiz->is_published ? 'published' : 'unpublished';

        return redirect()->back()
                         ->with('success', "Quiz {$status} successfully.");
    }
}
