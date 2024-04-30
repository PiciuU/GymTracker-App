<?php

namespace App\Http\Controllers;

use App\Models\UserExerciseHistory;
use App\Models\WorkoutExercise;
use App\Models\Workout;
use App\Models\Exercise;
use App\Http\Resources\WorkoutExerciseResource;
use App\Http\Resources\WorkoutExerciseCollection;
use App\Http\Requests\WorkoutExerciseRequest;

class WorkoutExerciseController extends Controller
{
    /**
     * Display a listing of workout exercises.
     *
     * @param  int  $workoutSlug
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index($workoutSlug)
    {
        $user = auth()->user();

        if ($user->hasAdminPrivileges()) $workoutExercises = new WorkoutExerciseCollection(WorkoutExercise::where('workout_id', $workoutSlug)->get());
        else $workoutExercises = new WorkoutExerciseCollection($user->workouts->firstWhere('id', $workoutSlug)->workoutExercises);

        return $this->successResponse("Workout exercises has been successfully found.", $workoutExercises);
    }

    /**
     * Store a newly created workout exercise in storage.
     *
     * @param  int  $workoutSlug
     * @param  \App\Http\Requests\WorkoutExerciseRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store($workoutSlug, WorkoutExerciseRequest $request)
    {
        $user = auth()->user();

        // Check if workout exists
        if ($user->hasAdminPrivileges()) $workout = Workout::find($workoutSlug);
        else $workout = $user->workouts()->find($workoutSlug);

        if (!$workout) return $this->errorResponse("Workout not found.", 404);

        // Find exercise
        $exercise = Exercise::find($request->validated()['exercise_id']);
        if (!$exercise) return $this->errorResponse("Exercise not found.", 404);

        if (!$exercise->is_public && $exercise->user_id !== $user->id && !$user->hasAdminPrivileges()) {
            return $this->errorResponse("Exercise not available.", 404);
        }

        // Check if exercise already exists in the workout
        $existingExercise = WorkoutExercise::where(['workout_id' => $workoutSlug, 'exercise_id' => $exercise->id])->first();

        if ($existingExercise) return $this->errorResponse("Exercise already exists in the workout.", 400);

        $workoutExercise = new WorkoutExerciseResource(WorkoutExercise::create($request->validated()));

        // Create entry in user exercise history
        $personalBest = UserExerciseHistory::where('exercise_id', $exercise->id)
            ->where('user_id', $user->id)
            ->orderBy('weight', 'desc')
            ->select('weight', 'date')
            ->first();

        if ($workoutExercise->weight && (!$personalBest || $personalBest->weight < $workoutExercise->weight)) {
            UserExerciseHistory::create([
                'exercise_id' => $exercise->id,
                'user_id' => $user->id,
                'weight' => $workoutExercise->weight,
                'date' => date('Y-m-d')
            ]);
        }

        if (!$workoutExercise) return $this->errorResponse("An error occurred while adding the exercise to the workout, try again later.", 500);

        return $this->successResponse("Exercise has been successfully added to the workout.", $workoutExercise);
    }

    /**
     * Display the specified workout exercise.
     *
     * @param  int  $workoutSlug
     * @param  int  $exerciseSlug
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($workoutSlug, $exerciseSlug)
    {
        $user = auth()->user();

        if ($user->hasAdminPrivileges()) $workoutExercise = WorkoutExercise::where(['workout_id' => $workoutSlug, 'exercise_id' => $exerciseSlug])->first();
        else $workoutExercise = $user->workouts->firstWhere('id', $workoutSlug)->workoutExercises->firstWhere('exercise_id', $exerciseSlug);

        if (!$workoutExercise) return $this->errorResponse("Workout exercise not found.", 404);

        return $this->successResponse("Workout exercise has been successfully found.", new WorkoutExerciseResource($workoutExercise));
    }

    /**
     * Update the specified workout exercise in storage.
     *
     * Note: If the user has admin privileges, they can update workout exercises of other users.
     *
     * @param  int  $workoutSlug
     * @param  int  $exerciseSlug
     * @param  \App\Http\Requests\WorkoutExerciseRequest  $request
     * @return \App\Http\Resources\WorkoutExerciseResource
     */
    public function update($workoutSlug, $exerciseSlug, WorkoutExerciseRequest $request)
    {
        $user = auth()->user();

        if ($user->hasAdminPrivileges()) $workoutExercise = WorkoutExercise::where(['workout_id' => $workoutSlug, 'exercise_id' => $exerciseSlug])->first();
        else $workoutExercise = $user->workouts->firstWhere('id', $workoutSlug)->workoutExercises->firstWhere('exercise_id', $exerciseSlug);

        if (!$workoutExercise) return $this->errorResponse("Workout exercise not found.", 404);

        if(!$workoutExercise->update($request->validated())) return $this->errorResponse("An error occurred while updating the workout exercise, try again later.", 500);

        // Create entry in user exercise history
        $personalBest = UserExerciseHistory::where('exercise_id', $exerciseSlug)
            ->where('user_id', $user->id)
            ->orderBy('weight', 'desc')
            ->select('weight', 'date')
            ->first();

        if (!$personalBest || $personalBest->weight < $workoutExercise->weight) {
            UserExerciseHistory::create([
                'exercise_id' => $exerciseSlug,
                'user_id' => $user->id,
                'weight' => $workoutExercise->weight,
                'date' => date('Y-m-d')
            ]);
        }

        return $this->successResponse("Workout exercise has been successfully updated.", new WorkoutExerciseResource($workoutExercise));
    }

    /**
     * Remove the specified workout exercise from storage.
     *
     * Note: If the user has admin privileges, they can delete workout exercises of other users.
     *
     * @param  int  $workoutSlug
     * @param  int  $exerciseSlug
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($workoutSlug, $exerciseSlug)
    {
        $user = auth()->user();

        if ($user->hasAdminPrivileges()) $workoutExercise = WorkoutExercise::where(['workout_id' => $workoutSlug, 'exercise_id' => $exerciseSlug])->first();
        else $workoutExercise = $user->workouts->firstWhere('id', $workoutSlug)->workoutExercises->firstWhere('exercise_id', $exerciseSlug);

        if (!$workoutExercise) return $this->errorResponse("Workout exercise not found.", 404);

        if(!$workoutExercise->delete()) return $this->errorResponse("An error occurred while deleting the workout exercise, try again later.", 500);

        return $this->successResponse("Workout exercise has been successfully deleted.");
    }
}
