{{-- Single choice answer input --}}
<div class="space-y-2">
    @foreach($question->options as $option)
        <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 cursor-pointer transition-colors">
            <input type="radio"
                   name="answer_{{ $question->id }}"
                   value="{{ $option->id }}"
                   class="mt-0.5 h-4 w-4 text-indigo-600 border-gray-300 dark:border-gray-600 shrink-0">
            <span class="flex items-center gap-3">
                @if($option->image_path)
                    <img src="{{ $option->image_url }}"
                         class="h-12 w-12 object-cover rounded border border-gray-200 dark:border-gray-700" alt="">
                @endif
                @if($option->body)
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $option->body }}</span>
                @endif
            </span>
        </label>
    @endforeach
</div>
