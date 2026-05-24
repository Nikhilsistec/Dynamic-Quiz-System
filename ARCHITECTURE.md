# Architecture — Smart Quiz Platform

## 1. Design Goal

The assignment required that adding a sixth question type must not require changes to any controller, model, service, or existing view. This constraint drove every structural decision.

---

## 2. Strategy Pattern — The Core Architecture

All question-type-specific behaviour is isolated in **Handler** classes that implement a shared contract. The rest of the application is type-agnostic and resolves the correct handler at runtime from a **Registry** singleton.

### Contract

```php
// app/Quiz/Contracts/QuestionTypeHandlerInterface.php
interface QuestionTypeHandlerInterface
{
    public function type(): string;            // 'binary', 'single_choice', …
    public function label(): string;           // human-readable label for dropdowns
    public function inputView(): string;       // Blade partial path for quiz-taking
    public function adminOptionsView(): string;// Blade partial path for admin editor
    public function validationRules(Question $question): array;
    public function saveAnswer(Question $question, int $attemptId, array $payload): Answer;
    public function score(Question $question, Answer $answer): float;
    public function usesOptions(): bool;       // whether type uses the options table
}
```

### Registry

```php
// app/Quiz/QuestionTypeRegistry.php
class QuestionTypeRegistry
{
    private array $handlers = [];

    public function register(QuestionTypeHandlerInterface $handler): void
    {
        $this->handlers[$handler->type()] = $handler;
    }

    public function get(string $type): QuestionTypeHandlerInterface { … }
    public function all(): array { … }
    public function typeOptions(): array { … }  // for <select> dropdowns
}
```

### Registration (AppServiceProvider)

```php
$this->app->singleton(QuestionTypeRegistry::class, function () {
    $registry = new QuestionTypeRegistry();
    $registry->register(new BinaryHandler());
    $registry->register(new SingleChoiceHandler());
    $registry->register(new MultipleChoiceHandler());
    $registry->register(new NumberHandler());
    $registry->register(new TextHandler());
    return $registry;
});
```

---

## 3. Component Map

```
QuestionTypeHandlerInterface
    ├── BinaryHandler           correct_value = 'yes'|'no', case-insensitive
    ├── SingleChoiceHandler     option->is_correct = true, full marks or zero
    ├── MultipleChoiceHandler   partial credit; IDs JSON-encoded in answer_text
    ├── NumberHandler           float within tolerance (settings['tolerance'] ?? 0.001)
    └── TextHandler             case-insensitive trim match against correct_value

QuestionTypeRegistry            singleton — resolves handler by type string
QuizEvaluator (service)         loops questions, delegates score() to handler
AttemptController               injects registry + evaluator; type-agnostic
SubmitAttemptRequest            builds dynamic validation from handler->validationRules()
```

---

## 4. Request / Response Flow

```
POST /quizzes/{quiz}/begin
  └─ AttemptController::begin()  creates Attempt row (started_at)
     └─ redirect → /quizzes/attempt/{attempt}

GET  /quizzes/attempt/{attempt}
  └─ AttemptController::take()   loads quiz with questions+options
     view: quiz/attempt.blade.php
       └─ foreach question → @include($handler->inputView())

POST /quizzes/attempt/{attempt}/submit
  └─ SubmitAttemptRequest::rules()  dynamic per-handler validation
     AttemptController::submit()
       ├─ foreach question: $handler->saveAnswer(…)   writes answers rows
       └─ QuizEvaluator::evaluate($attempt)
            ├─ foreach question: $handler->score(…)   updates marks_awarded
            └─ attempt->update([score, max_score, submitted_at])
     └─ redirect → result

GET  /quizzes/attempt/{attempt}/result
  └─ AttemptController::result()  loads attempt with answers
     view: quiz/result.blade.php
```

---

## 5. Database Schema

```
quizzes
  id, title, description, is_published(bool), time_limit_minutes

questions
  id, quiz_id(FK→quizzes), type(varchar 30), body(longText/HTML),
  image_path, youtube_url, marks, order, correct_value, settings(json)

options
  id, question_id(FK→questions), body, image_path, is_correct(bool), order

attempts
  id, quiz_id(FK→quizzes), participant_name, participant_email,
  started_at, submitted_at, score, max_score

answers
  id, attempt_id(FK→attempts), question_id(FK→questions),
  answer_text(text/json), selected_option_id(FK→options nullable),
  marks_awarded
  UNIQUE(attempt_id, question_id)
```

`correct_value` on the questions table stores the ground-truth for binary, number, and text types.
Options with `is_correct = true` are ground-truth for single- and multiple-choice.

---

## 6. Scoring Rules

| Type | Rule |
|---|---|
| Binary | `strtolower(trim(answer_text)) === correct_value ? marks : 0` |
| Single choice | `option->is_correct ? marks : 0` |
| Multiple choice | `marks × max(0, correctHits − incorrectHits) / totalCorrect` (partial credit) |
| Number | `abs(submitted − correct) <= tolerance ? marks : 0` |
| Text | `strtolower(trim(answer_text)) === strtolower(trim(correct_value)) ? marks : 0` |

---

## 7. Idempotency

Both `take()` and `submit()` check `$attempt->isSubmitted()` on entry:

- `take()` → if submitted, redirect to result immediately
- `submit()` → if submitted, redirect to result (no double-scoring)

The `answers` table has a UNIQUE constraint on `(attempt_id, question_id)` so concurrent or duplicate POSTs cannot insert duplicate answer rows.

---

## 8. Rich Text Editor (zero npm dependencies)

The question-body editor uses native browser APIs:

```
contenteditable div
  ↓  toolbar buttons call document.execCommand('bold' | 'italic' | ...)
  ↓  Image button: fetch POST /admin/upload-image → execCommand('insertHTML', '<img …>')
  ↓  YouTube input: regex extracts video ID → inject <iframe> embed preview
  ↓  form submit: editor.innerHTML synced to hidden <textarea name="body">
```

Stored HTML is rendered with `{!! $question->body !!}` — safe because only admins write question bodies.

---

## 9. How to Add a Sixth Question Type

**Example: Rating Scale (1–5 stars)**

Zero changes to existing files. Four new files only:

**Step 1** — Handler class:

```php
// app/Quiz/Handlers/RatingScaleHandler.php
class RatingScaleHandler implements QuestionTypeHandlerInterface
{
    public function type(): string { return 'rating_scale'; }
    public function label(): string { return 'Rating Scale (1–5)'; }
    public function inputView(): string { return 'quiz.inputs.rating_scale'; }
    public function adminOptionsView(): string { return 'admin.questions.options.rating_scale'; }
    public function usesOptions(): bool { return false; }

    public function validationRules(Question $question): array
    {
        return ["answer_{$question->id}" => 'required|integer|min:1|max:5'];
    }

    public function saveAnswer(Question $question, int $attemptId, array $payload): Answer
    {
        return Answer::updateOrCreate(
            ['attempt_id' => $attemptId, 'question_id' => $question->id],
            ['answer_text' => $payload["answer_{$question->id}"] ?? null]
        );
    }

    public function score(Question $question, Answer $answer): float
    {
        return (int)($answer->answer_text ?? 0) === (int)$question->correct_value
            ? $question->marks
            : 0;
    }
}
```

**Step 2** — Input partial for quiz-taking:

```blade
{{-- resources/views/quiz/inputs/rating_scale.blade.php --}}
<div class="flex gap-3">
    @for($i = 1; $i <= 5; $i++)
        <label class="cursor-pointer">
            <input type="radio" name="answer_{{ $question->id }}" value="{{ $i }}">
            {{ $i }} ★
        </label>
    @endfor
</div>
```

**Step 3** — Admin options partial (correct value selector):

```blade
{{-- resources/views/admin/questions/options/rating_scale.blade.php --}}
<label class="block text-sm font-medium text-gray-700 mb-1">Correct Rating</label>
<select name="correct_value" class="border rounded-lg px-3 py-2 text-sm">
    @for($i = 1; $i <= 5; $i++)
        <option value="{{ $i }}" @selected(old('correct_value', $question->correct_value ?? '') == $i)>
            {{ $i }} ★
        </option>
    @endfor
</select>
```

**Step 4** — Register in AppServiceProvider (one line):

```php
$registry->register(new RatingScaleHandler());
```

That is all. The question-type dropdown in the admin editor, validation, answer saving, scoring, and result display all work automatically.

---

## 10. File Structure Summary

```
app/Quiz/
  Contracts/QuestionTypeHandlerInterface.php
  Handlers/
    BinaryHandler.php
    SingleChoiceHandler.php
    MultipleChoiceHandler.php
    NumberHandler.php
    TextHandler.php
  QuestionTypeRegistry.php
  Services/QuizEvaluator.php

app/Http/Controllers/
  Admin/QuizController.php
  Admin/QuestionController.php
  Admin/OptionController.php
  Admin/ImageUploadController.php
  Quiz/AttemptController.php

app/Http/Requests/
  Admin/{Store,Update}QuizRequest.php
  Admin/{Store,Update}QuestionRequest.php
  Admin/StoreOptionRequest.php
  Quiz/StartAttemptRequest.php
  Quiz/SubmitAttemptRequest.php        ← dynamic rules from registry

app/Models/
  Quiz.php  Question.php  Option.php  Attempt.php  Answer.php

resources/views/
  quiz/index | show | start | attempt | result
  quiz/inputs/  binary | single_choice | multiple_choice | number | text
  admin/quizzes/  index | create | edit | show
  admin/questions/  _form
  admin/questions/options/  binary | single_choice | multiple_choice | number | text

database/migrations/  (5 quiz tables)
database/seeders/DemoQuizSeeder.php
```
