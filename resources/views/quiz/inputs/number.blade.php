{{-- Number input answer --}}
<div class="flex items-center gap-3">
    <input type="number"
           name="answer_{{ $question->id }}"
           step="any"
           class="w-48 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
           placeholder="Enter a number">
</div>
