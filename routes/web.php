<?php

use App\Http\Controllers\Admin\ImageUploadController;
use App\Http\Controllers\Admin\OptionController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\QuizController as AdminQuizController;
use App\Http\Controllers\Quiz\AttemptController;
use Illuminate\Support\Facades\Route;

// ── Public quiz routes ──────────────────────────────────────────────────────
Route::get('/', [AttemptController::class, 'index'])->name('home');

Route::prefix('quizzes')->name('quizzes.')->group(function () {
    Route::get('/',                                [AttemptController::class, 'index'] )->name('index');
    Route::get('/{quiz}',                          [AttemptController::class, 'show']  )->name('show');
    Route::get('/{quiz}/start',                    [AttemptController::class, 'start'] )->name('start');
    Route::post('/{quiz}/begin',                   [AttemptController::class, 'begin'] )->name('begin')->middleware('throttle:10,1');
    Route::get('/attempt/{attempt}',               [AttemptController::class, 'take']  )->name('take');
    Route::post('/attempt/{attempt}/submit',       [AttemptController::class, 'submit'])->name('submit')->middleware('throttle:5,1');
    Route::get('/attempt/{attempt}/result',        [AttemptController::class, 'result'])->name('result');
});

// ── Admin routes ────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->group(function () {

    // Quiz CRUD
    Route::resource('quizzes', AdminQuizController::class);
    Route::patch('quizzes/{quiz}/toggle-publish', [AdminQuizController::class, 'togglePublish'])->name('quizzes.togglePublish');

    // Questions (nested under quiz)
    Route::prefix('quizzes/{quiz}/questions')->name('quizzes.questions.')->group(function () {
        Route::get('/create',           [QuestionController::class, 'create'] )->name('create');
        Route::post('/',                [QuestionController::class, 'store']  )->name('store');
        Route::get('/{question}/edit',  [QuestionController::class, 'edit']   )->name('edit');
        Route::put('/{question}',       [QuestionController::class, 'update'] )->name('update');
        Route::delete('/{question}',    [QuestionController::class, 'destroy'])->name('destroy');
    });

    // Options (nested under question) — JSON API
    Route::prefix('questions/{question}/options')->name('questions.options.')->group(function () {
        Route::post('/',           [OptionController::class, 'store']  )->name('store');
        Route::put('/{option}',    [OptionController::class, 'update'] )->name('update');
        Route::delete('/{option}', [OptionController::class, 'destroy'])->name('destroy');
    });

    // Image upload endpoint for question body editor
    Route::post('/upload-image', [ImageUploadController::class, 'store'])->name('upload-image');
});
