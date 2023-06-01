<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workout>
 */
class WorkoutFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::doesntHave('workouts')->inRandomOrder()->first();

        return [
            'day_of_week' => $this->faker->numberBetween(1, 7),
            'user_id' => $user->id,
        ];
    }
}
