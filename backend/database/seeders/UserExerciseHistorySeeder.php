<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserExerciseHistory;
use App\Models\Workout;

class UserExerciseHistorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $workouts = Workout::all();

        foreach ($workouts as $workout) {
            $user = $workout->user;
            $workoutExercises = $workout->workoutExercises;

            foreach ($workoutExercises as $workoutExercise) {
                $exerciseId = $workoutExercise->exercise_id;
                $historyCount = rand(1, 2);

                for ($i = 0; $i < $historyCount; $i++) {
                    UserExerciseHistory::factory()->create([
                        'user_id' => $user->id,
                        'exercise_id' => $exerciseId,
                    ]);
                }
            }
        }
    }
}
