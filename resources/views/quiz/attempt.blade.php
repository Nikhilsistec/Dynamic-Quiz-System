@extends('layouts.app')

@section('title', $attempt->quiz->title . ' — In Progress')

@section('nav-links')
    <span class="text-sm text-gray-500 dark:text-gray-400 font-medium hidden sm:inline">{{ $attempt->participant_name }}</span>
    @if($attempt->quiz->time_limit_minutes)
        <span id="timer"
              class="inline-flex items-center gap-1.5 text-sm font-mono font-bold text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800 bg-indigo-50 dark:bg-indigo-900/30 px-3.5 py-1.5 rounded-xl shadow-sm transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span id="time-display">{{ str_pad($attempt->quiz->time_limit_minutes, 2, '0', STR_PAD_LEFT) }}:00</span>
        </span>
    @endif
@endsection

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Quiz header --}}
    <div class="mb-8 animate-fade-in-up">
        <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $attempt->quiz->title }}</h1>
        <div class="flex items-center gap-4 mt-2">
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $attempt->quiz->questions->count() }} questions &bull;
                {{ $attempt->quiz->questions->sum('marks') }} total marks
            </p>
        </div>
        {{-- Progress bar --}}
        <div class="mt-4 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
            <div id="progress-bar" class="progress-bar h-full bg-gradient-to-r from-indigo-500 to-purple-500 rounded-full" style="width: 0%"></div>
        </div>
        <p id="progress-text" class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">0 of {{ $attempt->quiz->questions->count() }} answered</p>
    </div>

    <form action="{{ route('quizzes.submit', $attempt) }}" method="POST" id="quiz-form">
        @csrf

        @foreach($attempt->quiz->questions as $index => $question)
            <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl border border-gray-200/60 dark:border-gray-700/60 shadow-sm p-6 sm:p-7 mb-5 animate-fade-in-up question-block" data-question-index="{{ $index }}">
                {{-- Question header --}}
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2.5 mb-3">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 text-xs font-bold text-indigo-700 dark:text-indigo-300">{{ $index + 1 }}</span>
                            <span class="text-xs px-2.5 py-1 rounded-lg font-semibold bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                {{ $question->marks }} {{ Str::plural('mark', $question->marks) }}
                            </span>
                        </div>
                        <div class="text-base text-gray-800 dark:text-gray-200 prose prose-sm dark:prose-invert max-w-none leading-relaxed">
                            {!! $question->body !!}
                        </div>
                    </div>
                </div>

                {{-- Question image --}}
                @if($question->image_path)
                    <img src="{{ Storage::disk('public')->url($question->image_path) }}"
                         class="mb-4 max-h-64 rounded-xl border border-gray-200 shadow-sm" alt="Question image">
                @endif

                {{-- YouTube embed --}}
                @if($question->youtube_embed_url)
                    <div class="mb-4">
                        <iframe width="100%" height="280"
                                src="{{ $question->youtube_embed_url }}"
                                frameborder="0" allowfullscreen
                                class="rounded-xl border border-gray-200 shadow-sm"></iframe>
                    </div>
                @endif

                {{-- Type-specific answer input --}}
                <div class="mt-4">
                    @php $handler = $registry->get($question->type); @endphp
                    @include($handler->inputView(), ['question' => $question, 'attempt' => $attempt])
                </div>

                @error("answer_{$question->id}")
                    <p class="mt-2 text-xs text-red-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>
        @endforeach

        <div class="sticky bottom-4 z-40">
            <div class="bg-white/90 dark:bg-gray-800/90 backdrop-blur-lg border border-gray-200/60 dark:border-gray-700/60 rounded-2xl p-4 shadow-xl shadow-gray-200/40 dark:shadow-black/30 flex justify-between items-center">
                <p class="text-sm text-gray-500 dark:text-gray-400 hidden sm:block">Review all answers before submitting.</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 sm:hidden" id="progress-text-mobile">0/{{ $attempt->quiz->questions->count() }} answered</p>
                <button type="button" id="submit-btn"
                        class="btn-glow bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold px-8 py-3 rounded-xl shadow-lg shadow-indigo-200/50 hover:shadow-xl transition-all flex items-center gap-2">
                    Submit Quiz
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Custom confirmation modal --}}
<div id="confirm-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="confirm-overlay"></div>
    <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 max-w-sm w-full mx-4 transform transition-all scale-95 opacity-0" id="confirm-box">
        <div class="text-center">
            <div class="w-14 h-14 bg-amber-100 dark:bg-amber-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">Submit Quiz?</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">You cannot change your answers after submitting.</p>
        </div>
        <div class="flex gap-3">
            <button type="button" id="confirm-cancel"
                    class="flex-1 px-4 py-2.5 border border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-xl text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Cancel
            </button>
            <button type="button" id="confirm-submit"
                    class="flex-1 px-4 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl text-sm font-bold hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md">
                Yes, Submit
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ── Custom confirmation modal ──
(function() {
    const modal = document.getElementById('confirm-modal');
    const box = document.getElementById('confirm-box');
    const submitBtn = document.getElementById('submit-btn');
    const cancelBtn = document.getElementById('confirm-cancel');
    const confirmBtn = document.getElementById('confirm-submit');
    const overlay = document.getElementById('confirm-overlay');

    function showModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            box.classList.remove('scale-95', 'opacity-0');
            box.classList.add('scale-100', 'opacity-100');
        });
    }

    function hideModal() {
        box.classList.remove('scale-100', 'opacity-100');
        box.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }, 200);
    }

    submitBtn.addEventListener('click', showModal);
    cancelBtn.addEventListener('click', hideModal);
    overlay.addEventListener('click', hideModal);
    confirmBtn.addEventListener('click', () => {
        document.getElementById('quiz-form').submit();
    });
})();

// ── Progress tracking ──
(function() {
    const form = document.getElementById('quiz-form');
    const total = {{ $attempt->quiz->questions->count() }};
    const bar = document.getElementById('progress-bar');
    const text = document.getElementById('progress-text');
    const textMobile = document.getElementById('progress-text-mobile');

    function updateProgress() {
        let answered = 0;
        document.querySelectorAll('.question-block').forEach(block => {
            const inputs = block.querySelectorAll('input[type="radio"]:checked, input[type="checkbox"]:checked, input[type="text"], input[type="number"], textarea');
            inputs.forEach(input => {
                if (input.type === 'radio' || input.type === 'checkbox') { answered++; }
                else if (input.value.trim()) { answered++; }
            });
        });
        // Deduplicate - count unique answered questions
        answered = Math.min(answered, total);
        const pct = Math.round((answered / total) * 100);
        bar.style.width = pct + '%';
        text.textContent = answered + ' of ' + total + ' answered';
        if (textMobile) textMobile.textContent = answered + '/' + total + ' answered';
    }

    form.addEventListener('input', updateProgress);
    form.addEventListener('change', updateProgress);
    updateProgress();
})();
</script>

@if($attempt->quiz->time_limit_minutes)
<script>
(function () {
    const totalSeconds = {{ $attempt->quiz->time_limit_minutes }} * 60;
    const startedAt    = {{ $attempt->started_at->timestamp }};
    const deadline     = startedAt + totalSeconds;

    const display = document.getElementById('time-display');
    const timer   = document.getElementById('timer');

    function tick() {
        const remaining = Math.max(0, deadline - Math.floor(Date.now() / 1000));
        const m = String(Math.floor(remaining / 60)).padStart(2, '0');
        const s = String(remaining % 60).padStart(2, '0');
        display.textContent = `${m}:${s}`;

        // Warning state when < 2 minutes
        if (remaining <= 120 && remaining > 0) {
            timer.classList.add('timer-warning');
        }

        if (remaining <= 0) {
            document.getElementById('quiz-form').submit();
        } else {
            setTimeout(tick, 1000);
        }
    }

    tick();
})();
</script>
@endif
@endpush
