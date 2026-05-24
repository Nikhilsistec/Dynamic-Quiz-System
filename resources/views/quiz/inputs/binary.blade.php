{{-- Binary (Yes / No) answer input --}}
<div class="flex gap-6">
    <label class="flex items-center gap-2 cursor-pointer group">
        <input type="radio" name="answer_{{ $question->id }}" value="yes"
               class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">Yes</span>
    </label>
    <label class="flex items-center gap-2 cursor-pointer group">
        <input type="radio" name="answer_{{ $question->id }}" value="no"
               class="h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">No</span>
    </label>
</div>
