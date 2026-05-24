@extends('layouts.admin')

@section('title', $question ? 'Edit Question' : 'Add Question')

@section('page-title', $question ? 'Edit Question' : 'Add Question')
@section('page-description', $question ? 'Update this question' : 'Add a new question to "' . $quiz->title . '"')

@section('content')
<div class="max-w-3xl">
    <form
        action="{{ $question
            ? route('admin.quizzes.questions.update', [$quiz, $question])
            : route('admin.quizzes.questions.store', $quiz) }}"
        method="POST"
        enctype="multipart/form-data"
        class="space-y-6"
    >
        @csrf
        @if($question) @method('PUT') @endif

        {{-- Question Type --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm space-y-4">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Question Setup</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Question Type <span class="text-red-500">*</span>
                    </label>
                    <select name="type" id="question-type"
                            class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($typeOptions as $key => $label)
                            <option value="{{ $key }}"
                                {{ old('type', $question?->type ?? 'binary') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                        Marks <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="marks" min="1" max="100"
                           value="{{ old('marks', $question?->marks ?? 1) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @error('marks')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Question Body --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm space-y-3">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide">Question Text</h2>

            {{-- Editor toolbar --}}
            <div class="flex items-center gap-1 border border-gray-200 dark:border-gray-600 rounded-lg px-2 py-1 bg-gray-50 dark:bg-gray-700 flex-wrap">
                <button type="button" onclick="fmt('bold')"
                        class="px-2 py-1 text-sm font-bold text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 rounded" title="Bold">B</button>
                <button type="button" onclick="fmt('italic')"
                        class="px-2 py-1 text-sm italic text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 rounded" title="Italic">I</button>
                <button type="button" onclick="fmt('underline')"
                        class="px-2 py-1 text-sm underline text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 rounded" title="Underline">U</button>
                <span class="border-l border-gray-300 dark:border-gray-500 mx-1 h-5"></span>
                <button type="button" onclick="fmt('insertUnorderedList')"
                        class="px-2 py-1 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 rounded" title="Bullet List">≡</button>
                <span class="border-l border-gray-300 dark:border-gray-500 mx-1 h-5"></span>
                <label class="px-2 py-1 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 rounded cursor-pointer" title="Insert Image">
                    🖼 Image
                    <input type="file" id="body-img-upload" accept="image/*" class="hidden">
                </label>
            </div>

            {{-- Editable area --}}
            <div id="editor"
                 contenteditable="true"
                 class="min-h-32 border border-gray-300 dark:border-gray-600 rounded-lg p-3 text-sm bg-white dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 prose prose-sm dark:prose-invert max-w-none">
            </div>

            {{-- Hidden textarea submitted with form --}}
            <textarea name="body" id="body-hidden" class="hidden">{{ old('body', $question?->body ?? '') }}</textarea>

            @error('body')
                <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror

            {{-- Question-level image upload --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Attach an image to this question (optional)</label>
                <input type="file" name="image" accept="image/*"
                       class="text-sm text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 dark:file:bg-indigo-900/30 file:text-indigo-700 dark:file:text-indigo-400 hover:file:bg-indigo-100">
                @if($question?->image_path)
                    <div class="mt-2">
                        <img src="{{ Storage::disk('public')->url($question->image_path) }}"
                             class="h-24 rounded border dark:border-gray-600" alt="Current image">
                        <p class="text-xs text-gray-400 mt-1">Upload a new file to replace.</p>
                    </div>
                @endif
            </div>

            {{-- YouTube URL --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">YouTube Video URL (optional)</label>
                <input type="text" name="youtube_url" id="youtube-url"
                       value="{{ old('youtube_url', $question?->youtube_url ?? '') }}"
                       placeholder="https://www.youtube.com/watch?v=..."
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <div id="yt-preview" class="mt-2"></div>
            </div>
        </div>

        {{-- Type-specific options (all types rendered, toggled by JS) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm" id="options-section">
            <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200 uppercase tracking-wide mb-4">Answer Configuration</h2>
            @php
                $currentType = old('type', $question?->type ?? 'binary');
                $registry = app(\App\Quiz\QuestionTypeRegistry::class);
            @endphp

            @foreach($registry->all() as $handler)
                <div class="type-panel" data-type="{{ $handler->type() }}"
                     style="{{ $handler->type() === $currentType ? '' : 'display:none' }}">
                    @include($handler->adminOptionsView(), ['question' => ($question && $question->type === $handler->type()) ? $question : null])
                </div>
            @endforeach
        </div>

        {{-- Submit --}}
        <div class="flex gap-3">
            <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg text-sm font-medium">
                {{ $question ? 'Save Changes' : 'Add Question' }}
            </button>
            <a href="{{ route('admin.quizzes.show', $quiz) }}"
               class="text-sm text-gray-500 hover:text-gray-700 px-5 py-2">Cancel</a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
// ── Rich text editor ──────────────────────────────────────────────────────────
const editor = document.getElementById('editor');
const hidden  = document.getElementById('body-hidden');

// Seed editor from existing body value
editor.innerHTML = hidden.value;

function fmt(cmd) {
    editor.focus();
    document.execCommand(cmd, false, null);
}

// Sync editor → hidden textarea before submit
document.querySelector('form').addEventListener('submit', () => {
    hidden.value = editor.innerHTML;
});

// ── Inline image upload into editor body ──────────────────────────────────────
document.getElementById('body-img-upload').addEventListener('change', async function () {
    if (!this.files.length) return;
    const fd = new FormData();
    fd.append('file', this.files[0]);
    fd.append('_token', document.querySelector('meta[name=csrf-token]').content);
    try {
        const res  = await fetch('{{ route('admin.upload-image') }}', { method: 'POST', body: fd });
        const data = await res.json();
        editor.focus();
        document.execCommand('insertHTML', false, `<img src="${data.url}" style="max-width:100%" alt="">`);
    } catch (e) {
        showToast('Image upload failed. Please try again.', 'error');
    }
    this.value = '';
});

// ── YouTube preview ───────────────────────────────────────────────────────────
const ytInput   = document.getElementById('youtube-url');
const ytPreview = document.getElementById('yt-preview');

function buildYtPreview(url) {
    const m = url.match(/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/);
    if (m) {
        ytPreview.innerHTML = `<iframe width="320" height="180"
            src="https://www.youtube.com/embed/${m[1]}"
            frameborder="0" allowfullscreen class="rounded border"></iframe>`;
    } else {
        ytPreview.innerHTML = '';
    }
}

ytInput.addEventListener('blur', () => buildYtPreview(ytInput.value));
if (ytInput.value) buildYtPreview(ytInput.value); // show on edit page load

// ── Dynamic options section reload on type change ─────────────────────────────
function toggleTypePanels(selectedType) {
    document.querySelectorAll('.type-panel').forEach(panel => {
        const isActive = panel.dataset.type === selectedType;
        panel.style.display = isActive ? '' : 'none';
        // Disable/enable inputs so hidden required fields don't block submission
        panel.querySelectorAll('input, select, textarea').forEach(input => {
            input.disabled = !isActive;
        });
    });
}

// Set initial state on page load
toggleTypePanels(document.getElementById('question-type').value);

document.getElementById('question-type').addEventListener('change', function () {
    toggleTypePanels(this.value);
});
</script>
@endpush
