<?php

namespace Tests\Feature;

use App\Models\Quiz;
use App\Models\Question;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminQuizTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_list_quizzes(): void
    {
        Quiz::create(['title' => 'Quiz A', 'description' => 'Desc', 'is_published' => true]);
        Quiz::create(['title' => 'Quiz B', 'description' => 'Desc', 'is_published' => false]);

        $response = $this->get(route('admin.quizzes.index'));

        $response->assertStatus(200);
        $response->assertSee('Quiz A');
        $response->assertSee('Quiz B'); // Admin sees unpublished too
    }

    public function test_admin_can_create_quiz(): void
    {
        $response = $this->post(route('admin.quizzes.store'), [
            'title' => 'New Quiz',
            'description' => 'A brand new quiz',
            'is_published' => true,
            'time_limit_minutes' => 45,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quizzes', [
            'title' => 'New Quiz',
            'time_limit_minutes' => 45,
        ]);
    }

    public function test_admin_can_update_quiz(): void
    {
        $quiz = Quiz::create(['title' => 'Old Title', 'description' => 'Desc', 'is_published' => false]);

        $response = $this->put(route('admin.quizzes.update', $quiz), [
            'title' => 'New Title',
            'description' => 'Updated',
            'is_published' => true,
            'time_limit_minutes' => 60,
        ]);

        $response->assertRedirect();
        $quiz->refresh();
        $this->assertEquals('New Title', $quiz->title);
        $this->assertTrue($quiz->is_published);
    }

    public function test_admin_can_delete_quiz(): void
    {
        $quiz = Quiz::create(['title' => 'Delete Me', 'description' => 'Desc', 'is_published' => false]);

        $response = $this->delete(route('admin.quizzes.destroy', $quiz));

        $response->assertRedirect(route('admin.quizzes.index'));
        $this->assertDatabaseMissing('quizzes', ['id' => $quiz->id]);
    }

    public function test_admin_can_add_question(): void
    {
        $quiz = Quiz::create(['title' => 'Quiz', 'description' => 'Desc', 'is_published' => false]);

        $response = $this->post(route('admin.quizzes.questions.store', $quiz), [
            'type' => 'binary',
            'body' => 'Is the sky blue?',
            'marks' => 5,
            'correct_value' => 'yes',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('questions', [
            'quiz_id' => $quiz->id,
            'type' => 'binary',
            'body' => 'Is the sky blue?',
            'marks' => 5,
        ]);
    }

    public function test_create_quiz_requires_title(): void
    {
        $response = $this->post(route('admin.quizzes.store'), [
            'title' => '',
            'description' => 'Missing title',
        ]);

        $response->assertSessionHasErrors('title');
    }

    public function test_admin_can_delete_question(): void
    {
        $quiz = Quiz::create(['title' => 'Quiz', 'description' => 'Desc', 'is_published' => false]);
        $question = Question::create([
            'quiz_id' => $quiz->id,
            'type' => 'binary',
            'body' => 'Delete me?',
            'marks' => 1,
            'order' => 1,
            'correct_value' => 'yes',
        ]);

        $response = $this->delete(route('admin.quizzes.questions.destroy', [$quiz, $question]));

        $response->assertRedirect();
        $this->assertDatabaseMissing('questions', ['id' => $question->id]);
    }
}
