# Smart Quiz Platform

A dynamic quiz system built with Laravel 12, supporting five question types, rich-text editing, image uploads, YouTube embeds, and per-question scoring with partial credit.

---

## Requirements

| Dependency | Version |
|---|---|
| PHP | 8.2+ |
| Composer | 2.x |
| Node.js | 18+ |
| MySQL | 8.0+ |

---

## Local Setup

### 1. Clone and install dependencies

```bash
git clone <repo-url> smart-quiz-platform
cd smart-quiz-platform
composer install
npm install
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Open `.env` and set your database credentials:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=smart_quiz_platform
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Create the database

```sql
CREATE DATABASE smart_quiz_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Run migrations and seed demo data

```bash
php artisan migrate
php artisan db:seed --class=DemoQuizSeeder
```

The seeder creates **"General Knowledge Quiz"** — one question of each type (binary, single choice, multiple choice, number, text) — so the full flow is testable immediately.

### 5. Link storage

```bash
php artisan storage:link
```

This makes `public/storage` point to `storage/app/public` so uploaded images are served.

### 6. Build frontend assets

```bash
npm run build
```

For hot-reload during development:

```bash
npm run dev
```

### 7. Start the development server

```bash
php artisan serve
```

Visit `http://localhost:8000`.

---

## Usage

### Admin panel (no login required)

| URL | Purpose |
|---|---|
| `/admin/quizzes` | List all quizzes; create / delete |
| `/admin/quizzes/create` | Create a new quiz |
| `/admin/quizzes/{id}/edit` | Edit quiz title, description, time limit, published state |
| `/admin/quizzes/{id}` | View questions; reorder; delete |
| `/admin/quizzes/{id}/questions/create` | Add a question |
| `/admin/quizzes/{quiz}/questions/{id}/edit` | Edit a question |

### Taking a quiz

| URL | Purpose |
|---|---|
| `/` | Grid of published quizzes |
| `/quizzes/{id}` | Quiz detail — question count, total marks, time limit |
| `/quizzes/{id}/start` | Enter participant name/email |
| `/quizzes/attempt/{id}` | Answer all questions; countdown timer if time limit set |
| `/quizzes/attempt/{id}/result` | Score banner + per-question breakdown |

---

## Question Types

| Type | Input shown to participant | Scoring |
|---|---|---|
| Binary | Yes / No radio buttons | Full marks or zero |
| Single choice | Radio buttons (text + optional image per option) | Full marks or zero |
| Multiple choice | Checkboxes (text + optional image per option) | Partial credit — see below |
| Number | Numeric input | Full marks if within tolerance (default ±0.001) |
| Text | Free-text textarea | Full marks if case-insensitive trim match |

**Multiple-choice partial credit formula:**

```
awarded = marks × max(0, correct_selected − incorrect_selected) / total_correct_options
```

Selecting all wrong options and none correct gives zero; selecting 2 of 3 correct options and no wrong options gives ⌊2/3 × marks⌋.

---

## Image Uploads

- Question body images: inserted inline via the rich-text toolbar → `POST /admin/upload-image` → returns `{url}`
- Option images: uploaded per-option via the question editor
- Files stored at `storage/app/public/images/questions/`
- Served via `APP_URL/storage/images/questions/...`

---

## Rich Text Editor

Question bodies support formatted HTML using a lightweight `contenteditable` editor (no npm packages):

- **B / I / U** — bold, italic, underline via `document.execCommand`
- **List** — unordered bullet list
- **Image** — uploads to `/admin/upload-image`, inserts `<img>` inline
- **YouTube** — paste URL, video ID is extracted and rendered as an `<iframe>` preview; stored URL is converted to embed URL at quiz-take time

---

## Environment Variables

| Variable | Default | Purpose |
|---|---|---|
| `APP_URL` | `http://localhost:8000` | Base URL used in storage asset URLs |
| `DB_*` | — | MySQL connection settings |
| `MEDIA_DISK` | `public` | Storage disk used for image uploads |

---

## Project Structure (key directories)

```
app/
  Quiz/
    Contracts/QuestionTypeHandlerInterface.php   ← handler contract
    Handlers/                                    ← one class per question type
    QuestionTypeRegistry.php                     ← singleton registry
    Services/QuizEvaluator.php                   ← scoring service
  Http/
    Controllers/Admin/                           ← quiz/question/option/image admin
    Controllers/Quiz/AttemptController.php       ← public quiz-taking flow
    Requests/                                    ← form request validation
  Models/                                        ← Quiz, Question, Option, Attempt, Answer
resources/views/
  admin/quizzes/                                 ← admin CRUD views
  admin/questions/options/                       ← per-type option editor partials
  quiz/                                          ← public quiz-taking views
  quiz/inputs/                                   ← per-type answer input partials
database/
  migrations/                                    ← 5 quiz tables + 3 Laravel defaults
  seeders/DemoQuizSeeder.php
```

See `ARCHITECTURE.md` for the full design rationale and extensibility guide.
