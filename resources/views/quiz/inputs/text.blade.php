{{-- Text input answer --}}
<textarea name="answer_{{ $question->id }}"
          rows="3"
          class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm dark:bg-gray-700 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-none"
          placeholder="Type your answer here…"
          maxlength="5000"></textarea>
