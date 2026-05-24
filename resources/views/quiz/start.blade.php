@extends('layouts.app')

@section('title', 'Start — ' . $quiz->title)

@section('nav-links')
    <a href="{{ route('quizzes.show', $quiz) }}" class="text-sm text-gray-500 hover:text-indigo-600 font-medium flex items-center gap-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Back
    </a>
@endsection

@section('content')
    <div class="max-w-md mx-auto animate-fade-in-up">
        <div class="bg-white/80 backdrop-blur-sm rounded-3xl border border-gray-200/60 shadow-xl shadow-gray-200/30 p-8 relative overflow-hidden">
            {{-- Top gradient bar --}}
            <div class="absolute top-0 left-0 right-0 h-1.5 bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500"></div>

            <div class="text-center mb-6 mt-2">
                <div class="w-14 h-14 bg-gradient-to-br from-indigo-100 to-purple-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Enter Your Details</h1>
                <p class="text-sm text-gray-500 mt-1">You're about to start <strong class="text-gray-700">{{ $quiz->title }}</strong></p>
            </div>

            <form action="{{ route('quizzes.begin', $quiz) }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="participant_name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Your Name <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="participant_name" id="participant_name"
                           value="{{ old('participant_name') }}"
                           class="w-full border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-3 text-sm bg-gray-50/50 dark:bg-gray-800 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all
                                  @error('participant_name') border-red-300 bg-red-50/30 @enderror"
                           placeholder="e.g. John Doe" autofocus>
                    @error('participant_name')
                        <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="participant_email" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Email <span class="text-gray-400 font-normal">(optional)</span>
                    </label>
                    <input type="email" name="participant_email" id="participant_email"
                           value="{{ old('participant_email') }}"
                           class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm bg-gray-50/50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all"
                           placeholder="e.g. john@example.com">
                </div>

                <button type="submit"
                        class="btn-glow w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold px-6 py-3.5 rounded-xl shadow-lg shadow-indigo-200/50 hover:shadow-xl hover:shadow-indigo-300/50 transition-all flex items-center justify-center gap-2">
                    Start Quiz
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    const name = document.getElementById('participant_name').value.trim();
    if (!name) {
        e.preventDefault();
        showToast('Please enter your name to start the quiz.', 'warning');
        document.getElementById('participant_name').focus();
    }
});
</script>
@endpush
