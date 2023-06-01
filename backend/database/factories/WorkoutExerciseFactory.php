<?php

namespace Database\Factories;

use App\Models\Exercise;
use App\Models\Workout;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkoutExercise>
 */
class WorkoutExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workout_id' => Workout::factory(),
            'exercise_id' => function (array $attributes) {
                return Exercise::whereNotIn('id', function ($query) use ($attributes) {
                    $query->select('exercise_id')
                        ->from('workout_exercises')
                        ->where('workout_id', $attributes['workout_id']);
                })->pluck('id')->random();
            },
            'sets' => $this->faker->numberBetween(1, 10),
            'reps' => $this->faker->numberBetween(1, 20),
            'rest_time' => $this->faker->numberBetween(30, 300),
            'weight' => $this->faker->randomFloat(2, 20, 150),
        ];
    }
}
