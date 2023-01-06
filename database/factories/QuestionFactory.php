<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "question" => fake()->text(200),
            "answers" => [ 'a11' => fake()->word() , 'a12' => fake()->word() , 'a13' => fake()->word() ,'a14' => fake()->word()],
            "correct_answer" => fake()->randomElement(['a11' , 'a12' , 'a13' , 'a14']),
            "points" => fake()->numberBetween(1, 5)
        ];
    }
}
