<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Exercise;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserExerciseHistory>
 */
class UserExerciseHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exercise_id' => Exercise::inRandomOrder()->first()->id,
            'user_id' => User::inRandomOrder()->first()->id,
            'weight' => $this->faker->randomFloat(2, 20, 150),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
        ];
    }
}
