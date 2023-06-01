<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Illuminate\Http\Request;
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
        $workouts = new WorkoutCollection(auth()->user()->workouts);
        return $this->successResponse('Workouts plan list found', $workouts);
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

        if (!$workout) return $this->errorResponse('An error occurred while creating the workout plan, try again later', 500);

        return $this->successResponse('Workout plan has been created successfully', $workout);
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

        if (!$workout) return $this->errorResponse('Workout plan not found.', 404);

        return $this->successResponse('Workout plan found', new WorkoutResource($workout));
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

        if (!$workout) return $this->errorResponse('Workout plan not found', 404);

        if(!$workout->update($request->validated())) return $this->errorResponse('An error occurred while updating the workout plan, try again later', 500);

        return $this->successResponse('Workout plan has been successfully updated', new WorkoutResource($workout));
    }

    /**
     * Remove the specified workout plan from storage.
     *
     * Note: If the user has admin privileges, they can delete any workout plan.
     *
     * @param int $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id)
    {
        $workout = auth()->user()->workouts()->find($id);

        if (!$workout) return $this->errorResponse('Workout plan not found', 404);

        if ($workout->workoutExercises()->exists()) return $this->errorResponse('You cant delete workout plan that has chosen exercises', 409);

        if (!$workout->delete()) return $this->errorResponse('An error occurred while deleting the workout plan, try again later', 500);

        return $this->successResponse('Workout plan has been successfully deleted');
    }
}
