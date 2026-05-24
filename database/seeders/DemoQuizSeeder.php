<?php

namespace Database\Seeders;

use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use Illuminate\Database\Seeder;

class DemoQuizSeeder extends Seeder
{
    public function run(): void
    {
        $quiz = Quiz::create([
            'title'               => 'General Knowledge Quiz',
            'description'         => 'A sample quiz covering all question types. Test your knowledge!',
            'is_published'        => true,
            'time_limit_minutes'  => 10,
        ]);

        // 1. Binary question
        Question::create([
            'quiz_id'       => $quiz->id,
            'type'          => 'binary',
            'body'          => '<p>Is the Earth the third planet from the Sun?</p>',
            'marks'         => 1,
            'order'         => 1,
            'correct_value' => 'yes',
        ]);

        // 2. Single choice question
        $q2 = Question::create([
            'quiz_id' => $quiz->id,
            'type'    => 'single_choice',
            'body'    => '<p>Which planet is known as the Red Planet?</p>',
            'marks'   => 2,
            'order'   => 2,
        ]);
        Option::insert([
            ['question_id' => $q2->id, 'body' => 'Venus',   'is_correct' => false, 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q2->id, 'body' => 'Mars',    'is_correct' => true,  'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q2->id, 'body' => 'Jupiter', 'is_correct' => false, 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q2->id, 'body' => 'Saturn',  'is_correct' => false, 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Multiple choice question
        $q3 = Question::create([
            'quiz_id' => $quiz->id,
            'type'    => 'multiple_choice',
            'body'    => '<p>Which of the following are programming languages? <em>(Select all that apply)</em></p>',
            'marks'   => 3,
            'order'   => 3,
        ]);
        Option::insert([
            ['question_id' => $q3->id, 'body' => 'Python',     'is_correct' => true,  'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q3->id, 'body' => 'HTML',       'is_correct' => false, 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q3->id, 'body' => 'JavaScript', 'is_correct' => true,  'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['question_id' => $q3->id, 'body' => 'PHP',        'is_correct' => true,  'order' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 4. Number input question
        Question::create([
            'quiz_id'       => $quiz->id,
            'type'          => 'number',
            'body'          => '<p>How many days are in a leap year?</p>',
            'marks'         => 2,
            'order'         => 4,
            'correct_value' => '366',
            'settings'      => ['tolerance' => 0],
        ]);

        // 5. Text input question
        Question::create([
            'quiz_id'       => $quiz->id,
            'type'          => 'text',
            'body'          => '<p>What is the chemical symbol for water?</p>',
            'marks'         => 1,
            'order'         => 5,
            'correct_value' => 'h2o',
        ]);
    }
}
