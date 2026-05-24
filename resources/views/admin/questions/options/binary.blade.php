{{-- Binary question: just choose the correct answer --}}
<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
    The participant will see <strong>Yes</strong> and <strong>No</strong> as choices.
    Select the correct answer below.
</p>

<div class="flex gap-6">
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="radio" name="correct_value" value="yes"
               class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600"
               {{ old('correct_value', $question?->correct_value ?? 'yes') === 'yes' ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">Yes (correct)</span>
    </label>
    <label class="flex items-center gap-2 cursor-pointer">
        <input type="radio" name="correct_value" value="no"
               class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600"
               {{ old('correct_value', $question?->correct_value) === 'no' ? 'checked' : '' }}>
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">No (correct)</span>
    </label>
</div>
