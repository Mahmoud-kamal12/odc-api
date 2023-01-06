<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Plant>
 */
class PlantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "name" => fake()->name,
            "description" => fake()->paragraph(),
            "price" => fake()->numberBetween(200 , 500),
            "image" => fake()->imageUrl(270, 330 )
        ];
    }
}
