@extends('layouts.app')

@section('title', 'Result — ' . $attempt->quiz->title)

@section('nav-links')
    <a href="{{ route('home') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400 font-medium flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        All Quizzes
    </a>
@endsection

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Score banner with circular ring --}}
    @php
        $pct    = $attempt->percentageScore();
        $passed = $pct >= 50;
        $circumference = 2 * 3.14159 * 54;
        $offset = $circumference - ($pct / 100) * $circumference;
    @endphp
    <div class="animate-scale-in rounded-3xl p-8 sm:p-10 mb-10 text-center relative overflow-hidden
                {{ $passed ? 'bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/20 border border-emerald-200/60 dark:border-emerald-800/60' : 'bg-gradient-to-br from-red-50 to-orange-50 dark:from-red-900/20 dark:to-orange-900/20 border border-red-200/60 dark:border-red-800/60' }}
                shadow-xl {{ $passed ? 'shadow-emerald-100/50 dark:shadow-emerald-900/20' : 'shadow-red-100/50 dark:shadow-red-900/20' }}">

        {{-- Background decoration --}}
        <div class="absolute top-0 right-0 w-40 h-40 {{ $passed ? 'bg-emerald-100' : 'bg-red-100' }} rounded-full opacity-30 blur-3xl -translate-y-1/2 translate-x-1/2"></div>

        {{-- Score ring --}}
        <div class="relative inline-block mb-4">
            <svg width="140" height="140" class="score-ring">
                <circle cx="70" cy="70" r="54" fill="none" stroke="{{ $passed ? '#d1fae5' : '#fecaca' }}" stroke-width="12"/>
                <circle cx="70" cy="70" r="54" fill="none"
                        stroke="{{ $passed ? '#10b981' : '#ef4444' }}" stroke-width="12"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $offset }}"
                        id="score-circle"/>
            </svg>
            <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-3xl font-black {{ $passed ? 'text-emerald-700' : 'text-red-600' }}">{{ number_format($pct, 0) }}%</span>
            </div>
        </div>

        <p class="text-4xl font-black {{ $passed ? 'text-emerald-700' : 'text-red-600' }} mb-2">
            {{ $attempt->score }} / {{ $attempt->max_score }}
        </p>
        <p class="text-lg font-semibold {{ $passed ? 'text-emerald-600' : 'text-red-500' }} mb-3">
            @if($pct >= 80)
                🎉 Excellent performance!
            @elseif($pct >= 50)
                👍 Good job!
            @else
                Keep practicing, you'll get better!
            @endif
        </p>
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Submitted by <strong class="text-gray-700 dark:text-gray-200">{{ $attempt->participant_name }}</strong>
            on {{ $attempt->submitted_at->format('d M Y, H:i') }}
        </p>
    </div>

    {{-- Per-question breakdown --}}
    <div class="flex items-center gap-3 mb-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-white">Answer Breakdown</h2>
        <div class="flex-1 h-px bg-gray-200 dark:bg-gray-700"></div>
    </div>

    @php
        $answersMap = $attempt->answers->keyBy('question_id');
    @endphp

    <div class="space-y-4 stagger-children">
    @foreach($attempt->quiz->questions as $index => $question)
        @php
            $answer       = $answersMap->get($question->id);
            $awarded      = $answer?->marks_awarded ?? 0;
            $isFullMarks  = $awarded >= $question->marks;
            $isPartial    = $awarded > 0 && $awarded < $question->marks;
            $isZero       = $awarded === 0;
        @endphp
        <div class="rounded-2xl border p-5 sm:p-6 transition-all
            {{ $isFullMarks ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50/50 dark:bg-emerald-900/20' : ($isPartial ? 'border-amber-200 dark:border-amber-800 bg-amber-50/50 dark:bg-amber-900/20' : 'border-red-200 dark:border-red-800 bg-red-50/50 dark:bg-red-900/20') }}">
            <div class="flex items-start justify-between gap-4 mb-4">
                <div class="flex items-start gap-3">
                    <div class="shrink-0 w-8 h-8 rounded-lg flex items-center justify-center
                        {{ $isFullMarks ? 'bg-emerald-100' : ($isPartial ? 'bg-amber-100' : 'bg-red-100') }}">
                        @if($isFullMarks)
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        @elseif($isPartial)
                            <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"/></svg>
                        @else
                            <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-400 dark:text-gray-500">Question {{ $index + 1 }}</span>
                        <div class="text-sm text-gray-800 dark:text-gray-200 mt-0.5 leading-relaxed">
                            {!! strip_tags($question->body) !!}
                        </div>
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    <span class="text-lg font-black
                        {{ $isFullMarks ? 'text-emerald-700' : ($isPartial ? 'text-amber-700' : 'text-red-600') }}">
                        {{ $awarded }}/{{ $question->marks }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 ml-11">
                {{-- Your answer --}}
                <div class="bg-white/60 dark:bg-gray-800/60 rounded-xl p-3 border border-gray-200/50 dark:border-gray-700/50">
                    <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">Your Answer</p>
                    @if(! $answer)
                        <span class="text-gray-400 italic text-sm">Not answered</span>
                    @elseif($question->type === 'single_choice' && $answer->selectedOption)
                        <span class="text-gray-800 dark:text-gray-200 text-sm">{{ $answer->selectedOption->body }}</span>
                    @elseif($question->type === 'multiple_choice' && $answer->answer_text)
                        @php
                            $selectedIds = json_decode($answer->answer_text, true) ?? [];
                            $selectedBodies = $question->options
                                ->whereIn('id', $selectedIds)
                                ->pluck('body');
                        @endphp
                        <span class="text-gray-800 dark:text-gray-200 text-sm">{{ $selectedBodies->join(', ') ?: '—' }}</span>
                    @else
                        <span class="text-gray-800 dark:text-gray-200 text-sm">{{ $answer->answer_text ?? '—' }}</span>
                    @endif
                </div>

                {{-- Correct answer --}}
                <div class="bg-white/60 dark:bg-gray-800/60 rounded-xl p-3 border border-emerald-100 dark:border-emerald-800/50">
                    <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wide mb-1">Correct Answer</p>
                    @if(in_array($question->type, ['single_choice', 'multiple_choice']))
                        @php
                            $correctOptions = $question->options->where('is_correct', true)->pluck('body');
                        @endphp
                        <span class="text-emerald-700 text-sm font-medium">{{ $correctOptions->join(', ') }}</span>
                    @elseif($question->type === 'binary')
                        <span class="text-emerald-700 text-sm font-medium">{{ ucfirst($question->correct_value ?? '—') }}</span>
                    @else
                        <span class="text-emerald-700 text-sm font-medium">{{ $question->correct_value ?? '—' }}</span>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
    </div>

    <div class="mt-10 flex gap-3 justify-center animate-fade-in-up">
        <a href="{{ route('quizzes.start', $attempt->quiz) }}"
           class="btn-glow bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-3 rounded-xl font-semibold text-sm shadow-lg shadow-indigo-200/50 hover:shadow-xl transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            Try Again
        </a>
        <a href="{{ route('home') }}"
           class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 text-gray-700 dark:text-gray-200 px-6 py-3 rounded-xl font-semibold text-sm shadow-sm hover:shadow transition-all">
            All Quizzes
        </a>
    </div>
</div>
@endsection
