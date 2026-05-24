{{-- Single choice: manage options via JSON API (edit) or inline fields (create) --}}
<p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
    Add options below. Mark exactly <strong>one</strong> as correct.
</p>

@if($question)
{{-- Edit mode: uses API to add/update/delete options --}}
<div id="options-list" class="space-y-2 mb-3">
    @foreach($question->options as $option)
        <div class="flex items-center gap-2" data-option-id="{{ $option->id }}">
            <input type="radio" name="_correct_option_marker" value="{{ $option->id }}"
                   class="h-4 w-4 text-green-600 border-gray-300 dark:border-gray-600 correct-radio"
                   {{ $option->is_correct ? 'checked' : '' }}
                   onchange="markCorrect({{ $option->id }}, '{{ route('admin.questions.options.update', [$question, $option]) }}')">
            <input type="text" value="{{ $option->body }}" placeholder="Option text"
                   class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 option-body"
                   onblur="updateOption({{ $option->id }}, this.value, '{{ route('admin.questions.options.update', [$question, $option]) }}')">
            <button type="button"
                    onclick="deleteOption({{ $option->id }}, '{{ route('admin.questions.options.destroy', [$question, $option]) }}', this.closest('[data-option-id]'))"
                    class="text-red-400 hover:text-red-600 text-xs px-2 py-1">✕</button>
        </div>
    @endforeach
</div>

<button type="button"
        onclick="addOption('{{ route('admin.questions.options.store', $question) }}')"
        class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">+ Add Option</button>
@else
{{-- Create mode: inline fields submitted with the form --}}
<div id="sc-inline-options" class="space-y-2 mb-3">
    <div class="flex items-center gap-2">
        <input type="radio" name="correct_option" value="0"
               class="h-4 w-4 text-green-600 border-gray-300 dark:border-gray-600" checked>
        <input type="text" name="options[]" placeholder="Option A (correct)"
               class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        <button type="button" onclick="scRemoveOption(this)" class="text-red-400 hover:text-red-600 text-xs px-2 py-1">✕</button>
    </div>
    <div class="flex items-center gap-2">
        <input type="radio" name="correct_option" value="1"
               class="h-4 w-4 text-green-600 border-gray-300 dark:border-gray-600">
        <input type="text" name="options[]" placeholder="Option B"
               class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        <button type="button" onclick="scRemoveOption(this)" class="text-red-400 hover:text-red-600 text-xs px-2 py-1">✕</button>
    </div>
</div>

<button type="button" onclick="scAddOption()"
        class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">+ Add Option</button>

<script>
function scAddOption() {
    const list = document.getElementById('sc-inline-options');
    const idx = list.children.length;
    const row = document.createElement('div');
    row.className = 'flex items-center gap-2';
    row.innerHTML = `
        <input type="radio" name="correct_option" value="${idx}"
               class="h-4 w-4 text-green-600 border-gray-300 dark:border-gray-600">
        <input type="text" name="options[]" placeholder="Option ${String.fromCharCode(65 + idx)}"
               class="flex-1 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-1.5 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
        <button type="button" onclick="scRemoveOption(this)" class="text-red-400 hover:text-red-600 text-xs px-2 py-1">✕</button>
    `;
    list.appendChild(row);
}
function scRemoveOption(btn) {
    const list = document.getElementById('sc-inline-options');
    if (list.children.length <= 2) { showToast('Minimum 2 options required.', 'warning'); return; }
    btn.closest('.flex').remove();
    list.querySelectorAll('input[type=radio]').forEach((r, i) => r.value = i);
}
</script>
@endif
