{{-- Text input: correct value (case-insensitive match) --}}
<div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
        Correct Answer <span class="text-red-500">*</span>
    </label>
    <input type="text" name="correct_value"
           value="{{ old('correct_value', $question?->correct_value ?? '') }}"
           placeholder="e.g. Paris"
           class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500">
    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
        Matching is case-insensitive and ignores leading/trailing spaces.
        e.g. "h2o", "H2O", and " H2o " are all equivalent.
    </p>
</div>
