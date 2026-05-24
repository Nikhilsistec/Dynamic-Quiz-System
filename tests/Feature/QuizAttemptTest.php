<?php

namespace Tests\Feature;

use App\Models\Attempt;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizAttemptTest extends TestCase
{
    use RefreshDatabase;

    private Quiz $quiz;
    private Question $binaryQuestion;
    private Question $singleChoiceQuestion;
    private Option $correctOption;
    private Option $wrongOption;

    protected function setUp(): void
    {
        parent::setUp();

        $this->quiz = Quiz::create([
            'title' => 'Test Quiz',
            'description' => 'A test quiz',
            'is_published' => true,
            'time_limit_minutes' => 30,
        ]);

        $this->binaryQuestion = Question::create([
            'quiz_id' => $this->quiz->id,
            'type' => 'binary',
            'body' => 'Is PHP a programming language?',
            'marks' => 2,
            'order' => 1,
            'correct_value' => 'yes',
        ]);

        $this->singleChoiceQuestion = Question::create([
            'quiz_id' => $this->quiz->id,
            'type' => 'single_choice',
            'body' => 'What is 2 + 2?',
            'marks' => 3,
            'order' => 2,
        ]);

        $this->correctOption = Option::create([
            'question_id' => $this->singleChoiceQuestion->id,
            'body' => '4',
            'is_correct' => true,
            'order' => 1,
        ]);

        $this->wrongOption = Option::create([
            'question_id' => $this->singleChoiceQuestion->id,
            'body' => '5',
            'is_correct' => false,
            'order' => 2,
        ]);
    }

    public function test_quiz_listing_shows_published_quizzes(): void
    {
        $unpublished = Quiz::create([
            'title' => 'Draft Quiz',
            'description' => 'Not published',
            'is_published' => false,
        ]);

        $response = $this->get(route('quizzes.index'));

        $response->assertStatus(200);
        $response->assertSee('Test Quiz');
        $response->assertDontSee('Draft Quiz');
    }

    public function test_unpublished_quiz_returns_404(): void
    {
        $quiz = Quiz::create([
            'title' => 'Hidden Quiz',
            'description' => 'Not published',
            'is_published' => false,
        ]);

        $response = $this->get(route('quizzes.show', $quiz));

        $response->assertStatus(404);
    }

    public function test_user_can_begin_attempt(): void
    {
        $response = $this->post(route('quizzes.begin', $this->quiz), [
            'participant_name' => 'John Doe',
            'participant_email' => 'john@example.com',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('attempts', [
            'quiz_id' => $this->quiz->id,
            'participant_name' => 'John Doe',
            'participant_email' => 'john@example.com',
        ]);
    }

    public function test_begin_attempt_requires_name(): void
    {
        $response = $this->post(route('quizzes.begin', $this->quiz), [
            'participant_name' => '',
            'participant_email' => 'john@example.com',
        ]);

        $response->assertSessionHasErrors('participant_name');
    }

    public function test_submit_quiz_scores_correctly(): void
    {
        $attempt = Attempt::create([
            'quiz_id' => $this->quiz->id,
            'participant_name' => 'Jane',
            'participant_email' => 'jane@example.com',
            'started_at' => now(),
        ]);

        $response = $this->post(route('quizzes.submit', $attempt), [
            "answer_{$this->binaryQuestion->id}" => 'yes',
            "answer_{$this->singleChoiceQuestion->id}" => $this->correctOption->id,
        ]);

        $response->assertRedirect(route('quizzes.result', $attempt));

        $attempt->refresh();
        $this->assertNotNull($attempt->submitted_at);
        $this->assertEquals(5, $attempt->score); // 2 + 3
        $this->assertEquals(5, $attempt->max_score);
    }

    public function test_wrong_answers_score_zero(): void
    {
        $attempt = Attempt::create([
            'quiz_id' => $this->quiz->id,
            'participant_name' => 'Bob',
            'started_at' => now(),
        ]);

        $response = $this->post(route('quizzes.submit', $attempt), [
            "answer_{$this->binaryQuestion->id}" => 'no',
            "answer_{$this->singleChoiceQuestion->id}" => $this->wrongOption->id,
        ]);

        $attempt->refresh();
        $this->assertEquals(0, $attempt->score);
        $this->assertEquals(5, $attempt->max_score);
    }

    public function test_double_submit_redirects_to_result(): void
    {
        $attempt = Attempt::create([
            'quiz_id' => $this->quiz->id,
            'participant_name' => 'Already Done',
            'started_at' => now(),
            'submitted_at' => now(),
            'score' => 3,
            'max_score' => 5,
        ]);

        $response = $this->post(route('quizzes.submit', $attempt), [
            "answer_{$this->binaryQuestion->id}" => 'yes',
            "answer_{$this->singleChoiceQuestion->id}" => $this->correctOption->id,
        ]);

        $response->assertRedirect(route('quizzes.result', $attempt));
        // Score should not change
        $attempt->refresh();
        $this->assertEquals(3, $attempt->score);
    }

    public function test_result_page_loads_successfully(): void
    {
        $attempt = Attempt::create([
            'quiz_id' => $this->quiz->id,
            'participant_name' => 'Viewer',
            'started_at' => now(),
            'submitted_at' => now(),
            'score' => 5,
            'max_score' => 5,
        ]);

        $response = $this->get(route('quizzes.result', $attempt));

        $response->assertStatus(200);
        $response->assertSee('Viewer');
    }

    public function test_expired_time_limit_still_submits(): void
    {
        $attempt = Attempt::create([
            'quiz_id' => $this->quiz->id,
            'participant_name' => 'Late',
            'started_at' => now()->subMinutes(60), // way past 30 min limit
        ]);

        $response = $this->post(route('quizzes.submit', $attempt), [
            "answer_{$this->binaryQuestion->id}" => 'yes',
            "answer_{$this->singleChoiceQuestion->id}" => $this->correctOption->id,
        ]);

        $response->assertRedirect(route('quizzes.result', $attempt));
        $attempt->refresh();
        $this->assertNotNull($attempt->submitted_at);
        // Answers are still scored even if late
        $this->assertEquals(5, $attempt->score);
    }
}
