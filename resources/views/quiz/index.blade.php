@extends('layouts.app')

@section('title', 'Available Quizzes')

@section('nav-links')
@endsection

@section('content')
    {{-- Hero section --}}
    <div class="text-center mb-12 animate-fade-in-up">
        <div class="inline-flex items-center gap-2 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-100 dark:border-indigo-800 text-indigo-700 dark:text-indigo-300 text-xs font-semibold px-4 py-1.5 rounded-full mb-4">
            <span class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse-soft"></span>
            {{ $quizzes->count() }} {{ Str::plural('quiz', $quizzes->count()) }} available
        </div>
        <h1 class="text-4xl sm:text-5xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-3">
            Test Your <span class="bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 bg-clip-text text-transparent animate-gradient">Knowledge</span>
        </h1>
        <p class="text-lg text-gray-500 dark:text-gray-400 max-w-xl mx-auto">
            Choose a quiz below to challenge yourself. Track your scores and improve over time.
        </p>
    </div>

    @if($quizzes->isEmpty())
        <div class="text-center py-20 animate-fade-in-up">
            <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <p class="text-xl text-gray-400 font-medium">No quizzes available yet</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Check back later for new quizzes.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 stagger-children">
            @foreach($quizzes as $quiz)
                <div class="quiz-card group bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm p-6 flex flex-col relative overflow-hidden">
                    {{-- Decorative gradient corner --}}
                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-bl from-indigo-50 dark:from-indigo-900/30 to-transparent rounded-bl-full opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                    <div class="flex-1 relative">
                        {{-- Question count badge --}}
                        <div class="flex items-center gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 px-2.5 py-1 rounded-lg">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $quiz->questions_count }} {{ Str::plural('question', $quiz->questions_count) }}
                            </span>
                            @if($quiz->time_limit_minutes)
                                <span class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-lg">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $quiz->time_limit_minutes }}m
                                </span>
                            @endif
                        </div>

                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2 group-hover:text-indigo-700 dark:group-hover:text-indigo-400 transition-colors">{{ $quiz->title }}</h2>
                        @if($quiz->description)
                            <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-2">{{ $quiz->description }}</p>
                        @endif
                    </div>

                    <div class="mt-5 pt-4 border-t border-gray-100 dark:border-gray-700">
                        <a href="{{ route('quizzes.show', $quiz) }}"
                           class="btn-glow flex items-center justify-center gap-2 w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-purple-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-md shadow-indigo-200/50 dark:shadow-indigo-900/30 hover:shadow-lg hover:shadow-indigo-300/50 transition-all">
                            Start Quiz
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                            </svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
