<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Exercise>
 */
class ExerciseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $exercises = [
            [
                'name' => 'Lat Pulldown',
                'description' => 'A popular exercise for targeting the latissimus dorsi muscles.',
                'muscle_group' => 'Back',
            ],
            [
                'name' => 'Bench Press',
                'description' => 'A compound exercise for the chest, shoulders, and triceps.',
                'muscle_group' => 'Chest',
            ],
            [
                'name' => 'Squat',
                'description' => 'A fundamental lower body exercise that targets the quadriceps, hamstrings, and glutes.',
                'muscle_group' => 'Legs',
            ],
            [
                'name' => 'Deadlift',
                'description' => 'A compound exercise that targets multiple muscle groups, including the back, legs, and core.',
                'muscle_group' => 'Full Body',
            ],
            [
                'name' => 'Shoulder Press',
                'description' => 'An exercise for developing the shoulder muscles, specifically the deltoids.',
                'muscle_group' => 'Shoulders',
            ],
            [
                'name' => 'Pull-Ups',
                'description' => 'A bodyweight exercise that targets the back and arm muscles.',
                'muscle_group' => 'Back',
            ],
            [
                'name' => 'Push-Ups',
                'description' => 'A classic bodyweight exercise that targets the chest, shoulders, and triceps.',
                'muscle_group' => 'Chest',
            ],
            [
                'name' => 'Lunges',
                'description' => 'An exercise that targets the quadriceps, hamstrings, and glutes.',
                'muscle_group' => 'Legs',
            ],
            [
                'name' => 'Bicep Curls',
                'description' => 'An isolation exercise for the biceps muscles.',
                'muscle_group' => 'Arms',
            ],
            [
                'name' => 'Tricep Dips',
                'description' => 'An exercise for targeting the triceps muscles.',
                'muscle_group' => 'Arms',
            ],
            [
                'name' => 'Crunches',
                'description' => 'An abdominal exercise that targets the rectus abdominis muscles.',
                'muscle_group' => 'Abs',
            ],
            [
                'name' => 'Russian Twists',
                'description' => 'An exercise that targets the oblique muscles.',
                'muscle_group' => 'Abs',
            ],
            [
                'name' => 'Calf Raises',
                'description' => 'An exercise for targeting the calf muscles.',
                'muscle_group' => 'Legs',
            ],
            [
                'name' => 'Plank',
                'description' => 'An isometric exercise for strengthening the core muscles.',
                'muscle_group' => 'Abs',
            ],
            [
                'name' => 'Incline Bench Press',
                'description' => 'A variation of the bench press that targets the upper chest muscles.',
                'muscle_group' => 'Chest',
            ],
            [
                'name' => 'Dumbbell Rows',
                'description' => 'An exercise for targeting the back muscles.',
                'muscle_group' => 'Back',
            ],
        ];

        $exercise = $this->faker->unique()->randomElement($exercises);

        return [
            'name' => $exercise['name'],
            'description' => $exercise['description'],
            'muscle_group' => $exercise['muscle_group'],
            'thumbnail_url' => null,
            'attachment_url' => null,
            'user_id' => 1,
            'is_public' => $this->faker->boolean,
            'is_approved' => $this->faker->boolean,
        ];
    }

    /**
     * Indicate that the exercise is not public.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function public()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_public' => true,
            ];
        });
    }

    /**
     * Indicate that the exercise is not approved.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function approved()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_approved' => true,
            ];
        });
    }

}
