<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use App\Http\Resources\WorkoutResource;
use App\Http\Resources\WorkoutCollection;
use App\Http\Requests\WorkoutRequest;

class WorkoutController extends Controller
{
    /**
     * Display a listing of the workout plans.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        $workouts = new WorkoutCollection(auth()->user()->workouts->sortBy('day_of_week'));
        return $this->successResponse("Workouts has been successfully found.", $workouts);
    }

    /**
     * Store a newly created workout plan in storage.
     *
     * @param  \App\Http\Requests\WorkoutRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(WorkoutRequest $request)
    {
        $workout = new WorkoutResource(Workout::create($request->validated()));

        if (!$workout) return $this->errorResponse("An error occurred while creating the workout, try again later.", 500);

        return $this->successResponse("Workout has been successfully created.", $workout);
    }

    /**
     * Display the specified workout plan.
     *
     * Note: If the user has admin privileges, they can view any workout plan.
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        $user = auth()->user();

        if ($user->tokenCan('admin')) $workout = Workout::find($id);
        else $workout = $user->workouts()->find($id);

        if (!$workout) return $this->errorResponse("Workout not found.", 404);

        return $this->successResponse("Workout has been successfully found.", new WorkoutResource($workout));
    }

    /**
     * Update the specified workout plan in storage.
     *
     * Note: If the user has admin privileges, they can update any workout plan.
     *
     * @param int $id
     * @param  \App\Http\Requests\WorkoutRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, WorkoutRequest $request)
    {
        $user = auth()->user();

        if ($user->tokenCan('admin')) $workout = Workout::find($id);
        else $workout = $user->workouts()->find($id);

        if (!$workout) return $this->errorResponse("Workout not found.", 404);

        if(!$workout->update($request->validated())) return $this->errorResponse("An error occurred while updating the workout, try again later.", 500);

        return $this->successResponse("Workout plan has been successfully updated.", new WorkoutResource($workout));
    }

    /**
     * Remove the specified workout plan from storage.
     *
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id)
    {
        $workout = auth()->user()->workouts()->find($id);

        if (!$workout) return $this->errorResponse("Workout not found.", 404);

        if ($workout->workoutExercises()->exists()) return $this->errorResponse("You cant delete workout that has chosen exercises.", 403);

        if (!$workout->delete()) return $this->errorResponse("An error occurred while deleting the workout, try again later.", 500);

        return $this->successResponse("Workout has been successfully deleted.");
    }
}
