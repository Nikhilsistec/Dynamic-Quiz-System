@extends('layouts.app')

@section('title', $quiz->title)

@section('nav-links')
    <a href="{{ route('home') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        All Quizzes
    </a>
@endsection

@section('content')
    <div class="max-w-2xl mx-auto animate-fade-in-up">
        <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-3xl border border-gray-200/60 dark:border-gray-700/60 shadow-xl shadow-gray-200/30 dark:shadow-black/20 p-8 sm:p-10 relative overflow-hidden">
            {{-- Decorative bg --}}
            <div class="absolute top-0 left-0 right-0 h-2 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

            <div class="mt-2">
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3 tracking-tight">{{ $quiz->title }}</h1>

                @if($quiz->description)
                    <p class="text-gray-500 dark:text-gray-400 leading-relaxed mb-6">{{ $quiz->description }}</p>
                @endif

                {{-- Stats cards --}}
                <div class="grid grid-cols-3 gap-3 sm:gap-4 mb-8">
                    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100/50 dark:from-indigo-900/30 dark:to-indigo-800/20 rounded-2xl p-4 text-center border border-indigo-100 dark:border-indigo-800">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-black text-indigo-700 dark:text-indigo-300">{{ $quiz->questions_count }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">Questions</p>
                    </div>
                    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/30 dark:to-emerald-800/20 rounded-2xl p-4 text-center border border-emerald-100 dark:border-emerald-800">
                        <div class="w-10 h-10 bg-emerald-100 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-black text-emerald-700 dark:text-emerald-300">{{ $maxScore }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">Total Marks</p>
                    </div>
                    <div class="bg-gradient-to-br from-amber-50 to-amber-100/50 dark:from-amber-900/30 dark:to-amber-800/20 rounded-2xl p-4 text-center border border-amber-100 dark:border-amber-800">
                        <div class="w-10 h-10 bg-amber-100 dark:bg-amber-900/50 rounded-xl flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="text-2xl font-black text-amber-700 dark:text-amber-300">
                            {{ $quiz->time_limit_minutes ? $quiz->time_limit_minutes . 'm' : '∞' }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium mt-0.5">Time Limit</p>
                    </div>
                </div>

                <a href="{{ route('quizzes.start', $quiz) }}"
                   class="btn-glow flex items-center justify-center gap-2 w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold px-6 py-4 rounded-2xl shadow-lg shadow-indigo-200/60 dark:shadow-indigo-900/30 hover:shadow-xl hover:shadow-indigo-300/60 transition-all text-lg">
                    Begin Quiz
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
@endsection
