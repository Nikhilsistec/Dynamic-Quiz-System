@extends('layouts.admin')

@section('title', 'Edit Quiz')

@section('page-title', 'Edit Quiz')
@section('page-description', 'Update the quiz details below.')

@section('content')
    <div class="max-w-2xl animate-fade-in-up">
        <form action="{{ route('admin.quizzes.update', $quiz) }}" method="POST"
              class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-2xl shadow-sm border border-gray-200/60 dark:border-gray-700/60 p-6 sm:p-8 space-y-6">
            @csrf @method('PUT')

            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">Title <span class="text-red-400">*</span></label>
                <input type="text" name="title" id="title" value="{{ old('title', $quiz->title) }}"
                       class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-sm bg-gray-50/50 dark:bg-gray-700/50 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all
                              @error('title') border-red-300 bg-red-50/30 @enderror"
                       required>
                @error('title')
                    <p class="mt-1.5 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">Description</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-sm bg-gray-50/50 dark:bg-gray-700/50 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all">{{ old('description', $quiz->description) }}</textarea>
            </div>

            <div>
                <label for="time_limit_minutes" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">Time Limit (minutes)</label>
                <input type="number" name="time_limit_minutes" id="time_limit_minutes"
                       value="{{ old('time_limit_minutes', $quiz->time_limit_minutes) }}" min="1" max="480"
                       class="w-36 border border-gray-200 dark:border-gray-600 rounded-xl px-4 py-3 text-sm bg-gray-50/50 dark:bg-gray-700/50 dark:text-gray-100 focus:bg-white dark:focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition-all"
                       placeholder="Unlimited">
                <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">Leave blank for unlimited time.</p>
            </div>

            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_published" id="is_published" value="1"
                       class="h-4.5 w-4.5 text-indigo-600 rounded-md border-gray-300 dark:border-gray-600 focus:ring-indigo-500 dark:bg-gray-700"
                       {{ old('is_published', $quiz->is_published) ? 'checked' : '' }}>
                <label for="is_published" class="text-sm font-medium text-gray-700 dark:text-gray-200">Published</label>
            </div>

            <div class="pt-4 flex gap-3 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                        class="btn-glow bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold shadow-lg shadow-indigo-200/50 dark:shadow-indigo-900/30 transition-all">
                    Save Changes
                </button>
                <a href="{{ route('admin.quizzes.show', $quiz) }}"
                   class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 px-5 py-2.5 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">Cancel</a>
            </div>
        </form>
    </div>
@endsection
