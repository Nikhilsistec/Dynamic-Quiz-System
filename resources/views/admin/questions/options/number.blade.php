{{-- Number input: correct value + optional tolerance --}}
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
            Correct Answer <span class="text-red-500">*</span>
        </label>
        <input type="number" name="correct_value" step="any"
               value="{{ old('correct_value', $question?->correct_value ?? '') }}"
               placeholder="e.g. 42 or 3.14"
               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">Tolerance (±)</label>
        <input type="number" name="settings[tolerance]" step="any" min="0"
               value="{{ old('settings.tolerance', $question?->settings['tolerance'] ?? '0.001') }}"
               placeholder="0.001"
               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Accepted deviation from correct answer (set 0 for exact match).</p>
    </div>
</div>
