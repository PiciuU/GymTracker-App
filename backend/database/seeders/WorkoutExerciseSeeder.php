<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WorkoutExercise;
use App\Models\Workout;

class WorkoutExerciseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workouts = Workout::all();

        foreach ($workouts as $workout) {
            $exerciseCount = rand(1, 5);

            WorkoutExercise::factory()->count($exerciseCount)->create(['workout_id' => $workout->id]);
        }
    }
}
