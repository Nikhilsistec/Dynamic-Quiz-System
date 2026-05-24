# AI Usage — Smart Quiz Platform

This document describes how AI assistance (Claude) was used during development of this project, as required by the assignment.

---

## Tool Used

**Claude (Anthropic)** via the Claude Code CLI — an interactive coding assistant that can read, write, and reason about code files, run shell commands, and generate multi-file implementations from a specification.

---

## What Was AI-Assisted

### 1. Architecture Design

**Prompt given (paraphrased):** "The system must be extensible — adding a new question type should not require changes to controllers, models, or existing views."

**AI contribution:** Proposed the Strategy Pattern as the solution: a `QuestionTypeHandlerInterface` contract, a `QuestionTypeRegistry` singleton, and five handler classes. Explained the trade-off between this approach and a simpler `switch`/`match` approach, noting that the registry approach satisfies the "zero existing-file changes" extensibility requirement while remaining readable.

**Human review:** The interface contract was reviewed to ensure all five handlers could realistically implement every method. The `saveAnswer` method signature was confirmed to be sufficient for both option-based and text-based types without overloading.

---

### 2. Database Schema

**Prompt given:** Full assignment requirements including all five question types.

**AI contribution:** Generated the five migration files. Key decisions made by AI:
- `correct_value` stored on the `questions` table (rather than a separate table) for binary/number/text types — avoids an extra join for simple types
- `settings` as a JSON column on questions — allows per-type configuration (e.g., `tolerance` for number questions) without adding new columns
- UNIQUE constraint on `(attempt_id, question_id)` in answers — idempotency guarantee at DB level

**Human review:** Confirmed that `selected_option_id` could be nullable (for non-choice question types) and that `ON DELETE SET NULL` was appropriate.

---

### 3. Handler Classes (all five)

**Prompt given:** The interface contract + assignment scoring rules.

**AI contribution:** Generated all five handler classes. The multiple-choice partial-credit formula was taken directly from the assignment spec:
```
marks × max(0, correctHits − incorrectHits) / totalCorrect
```

**Human review:** The formula and floor-at-zero behaviour were verified manually against example scenarios (e.g., 2 of 3 correct selected + 0 wrong = ⌊2/3 × marks⌋).

---

### 4. Controllers and Form Requests

**Prompt given:** Route list + requirement for dynamic validation.

**AI contribution:** Generated all five controllers and seven form request classes. The `SubmitAttemptRequest::rules()` method — which dynamically builds Laravel validation rules by calling `$handler->validationRules($question)` for each question in the attempt — was an AI-suggested approach to avoid hardcoding per-type validation.

**Human review:** Verified that the `attempt` relation was eager-loaded correctly before building rules, and that the method did not break if the attempt had already been submitted.

---

### 5. Admin Question Editor (Rich Text)

**Prompt given:** "Rich text question body editor, no npm dependencies, image upload, YouTube embed."

**AI contribution:** Designed the `contenteditable` + `document.execCommand` + sync-to-hidden-textarea approach. Generated the JavaScript toolbar and the YouTube URL → embed-URL conversion regex.

**Corrections made:**
- The initial YouTube regex only handled `youtube.com/watch?v=` URLs; it was updated to also handle `youtu.be/` short URLs.
- The `execCommand` deprecation warning in modern browsers was noted — for a production system, a maintained rich-text library (e.g., Trix) would be preferred.

---

### 6. Views and Blade Templates

**Prompt given:** Wireframe descriptions of each page (quiz list, question editor, attempt page, result page).

**AI contribution:** Generated all Blade partials including the per-type input partials (`quiz/inputs/*.blade.php`) and admin option partials (`admin/questions/options/*.blade.php`).

**Corrections made:**
- The result view initially showed `answer->answer_text` for multiple-choice questions (which stores a JSON array of IDs). This was corrected to decode the JSON and look up option bodies, matching what the single-choice branch already did.

---

### 7. DemoQuizSeeder

**Prompt given:** "Seed one quiz with one question of each type."

**AI contribution:** Generated the seeder with five questions, each having appropriate options and correct answers set. The seeder was verified by running `php artisan db:seed --class=DemoQuizSeeder` and taking the quiz end-to-end.

---

### 8. Documentation

**Prompt given:** "Write README.md, ARCHITECTURE.md, and AI_USAGE.md for the project."

**AI contribution:** Generated all three files including the extensibility walkthrough in `ARCHITECTURE.md` (the Rating Scale example demonstrating zero-change extensibility).

---

## What Was Not AI-Assisted

- **Environment setup** — PHP, MySQL, Node.js installation and `.env` configuration were done manually.
- **Database creation** — `CREATE DATABASE smart_quiz_platform` was run manually.
- **Dependency versions** — The choice to use Laravel 12 (not 13) was a constraint discovered manually: PHP 8.2 on the local machine; Laravel 13 requires PHP 8.3.
- **Storage symlink** — `php artisan storage:link` was run manually and verified.
- **Final end-to-end testing** — Quiz creation, question entry, attempt, and result were tested manually in a browser.

---

## Corrections and Limitations Noted

1. `document.execCommand` is deprecated in modern browsers. The editor works in current Chrome/Firefox/Edge but is not a long-term solution. A maintained library (Trix, Quill, ProseMirror) should replace it in production.

2. The admin panel has no authentication. This is intentional per the assignment spec ("no auth required") but would be a critical gap in any real deployment.

3. The rich-text editor stores raw HTML in the database. The `strip_tags()` call in the result view removes formatting for the answer breakdown — this is intentional (plain text comparison is cleaner). The attempt view renders full HTML.

4. Image upload stores files on the local `public` disk. A production system would use a cloud storage driver (S3, GCS) and the `MEDIA_DISK` env variable is already wired to support this.

---

## Summary

AI was used as a pair-programmer: given a requirement, it proposed an implementation which was then reviewed, tested, and corrected where needed. All architectural decisions (Strategy Pattern, registry singleton, partial-credit formula, UNIQUE constraint for idempotency) were understood and verified by the developer before being committed to the codebase.
