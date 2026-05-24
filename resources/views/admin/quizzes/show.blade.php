@extends('layouts.admin')

@section('title', $quiz->title . ' — Admin')

@section('page-title', $quiz->title)
@section('page-description', $quiz->questions->count() . ' questions · ' . $quiz->questions->sum('marks') . ' total marks' . ($quiz->time_limit_minutes ? ' · ' . $quiz->time_limit_minutes . ' min' : ''))

@section('header-actions')
    <a href="{{ route('admin.quizzes.edit', $quiz) }}"
       class="text-sm text-gray-600 hover:text-gray-900 bg-white border border-gray-200 px-4 py-2 rounded-xl shadow-sm hover:shadow transition-all font-medium">
        Edit Quiz
    </a>
    <a href="{{ route('admin.quizzes.questions.create', $quiz) }}"
       class="btn-glow bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white text-sm px-4 py-2.5 rounded-xl font-semibold shadow-md shadow-indigo-200/50 transition-all flex items-center gap-1.5">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        Add Question
    </a>
@endsection

@section('content')

    {{-- Quiz meta info --}}
    <div class="mb-8 animate-fade-in-up">
        <div class="flex items-center gap-3 mb-3">
            <form action="{{ route('admin.quizzes.togglePublish', $quiz) }}" method="POST" class="inline">
                @csrf @method('PATCH')
                @if($quiz->is_published)
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 transition-colors cursor-pointer" title="Click to unpublish">
                        <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span>
                        Published
                        <svg class="w-3 h-3 ml-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                @else
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-indigo-200 dark:hover:border-indigo-800 transition-colors cursor-pointer" title="Click to publish">
                        <span class="w-1.5 h-1.5 bg-gray-400 rounded-full"></span>
                        Draft — Click to Publish
                        <svg class="w-3 h-3 ml-1 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                    </button>
                @endif
            </form>
            @if($quiz->time_limit_minutes)
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $quiz->time_limit_minutes }} min time limit</span>
            @endif
        </div>
        @if($quiz->description)
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $quiz->description }}</p>
        @endif
    </div>

    {{-- Questions list --}}
    @if($quiz->questions->isEmpty())
        <div class="text-center py-20 border-2 border-dashed border-gray-200 dark:border-gray-700 rounded-2xl animate-fade-in-up">
            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium">No questions yet</p>
            <a href="{{ route('admin.quizzes.questions.create', $quiz) }}"
               class="mt-3 inline-flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 font-medium text-sm">
                Add your first question
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
            </a>
        </div>
    @else
        <div class="space-y-3 stagger-children" id="question-list">
            @foreach($quiz->questions as $index => $question)
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm border border-gray-200/60 dark:border-gray-700/60 rounded-2xl p-5 sm:p-6 shadow-sm hover:shadow-md transition-all group"
                     data-question-id="{{ $question->id }}">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-2.5">
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-gray-100 dark:bg-gray-700 text-xs font-bold text-gray-500 dark:text-gray-300">{{ $index + 1 }}</span>
                                @php
                                    $typeColors = [
                                        'binary' => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 border-blue-100 dark:border-blue-800',
                                        'single_choice' => 'bg-purple-50 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 border-purple-100 dark:border-purple-800',
                                        'multiple_choice' => 'bg-orange-50 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400 border-orange-100 dark:border-orange-800',
                                        'number' => 'bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 border-teal-100 dark:border-teal-800',
                                        'text' => 'bg-pink-50 dark:bg-pink-900/30 text-pink-700 dark:text-pink-400 border-pink-100 dark:border-pink-800',
                                    ];
                                    $colorClass = $typeColors[$question->type] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                @endphp
                                <span class="text-xs px-2.5 py-1 rounded-lg font-semibold border {{ $colorClass }}">
                                    {{ str_replace('_', ' ', ucfirst($question->type)) }}
                                </span>
                                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">{{ $question->marks }} {{ Str::plural('mark', $question->marks) }}</span>
                            </div>
                            <div class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2 leading-relaxed">
                                {!! strip_tags($question->body) !!}
                            </div>
                            @if($question->options->count())
                                <div class="mt-3 flex flex-wrap gap-1.5">
                                    @foreach($question->options as $option)
                                        <span class="text-xs px-2.5 py-1 rounded-lg border
                                            {{ $option->is_correct ? 'bg-emerald-50 dark:bg-emerald-900/30 border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 font-semibold' : 'bg-gray-50 dark:bg-gray-700 border-gray-200 dark:border-gray-600 text-gray-500 dark:text-gray-400' }}">
                                            {{ Str::limit($option->body, 30) }}
                                            @if($option->is_correct)
                                                <svg class="w-3 h-3 inline ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            @endif
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <div class="flex items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('admin.quizzes.questions.edit', [$quiz, $question]) }}"
                               class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('admin.quizzes.questions.destroy', [$quiz, $question]) }}"
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Delete this question?')">
                                @csrf @method('DELETE')
                                <button class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
