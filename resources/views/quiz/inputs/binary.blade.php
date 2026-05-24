{{-- Binary (Yes / No) answer input --}}
<div class="flex gap-6">
    <label class="flex items-center gap-2 cursor-pointer group">
        <input type="radio" name="answer_{{ $question->id }}" value="yes"
               class="h-4 w-4 text-indigo-600 border-gray-300">
        <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">Yes</span>
    </label>
    <label class="flex items-center gap-2 cursor-pointer group">
        <input type="radio" name="answer_{{ $question->id }}" value="no"
               class="h-4 w-4 text-indigo-600 border-gray-300">
        <span class="text-sm font-medium text-gray-700 group-hover:text-indigo-600">No</span>
    </label>
</div>
