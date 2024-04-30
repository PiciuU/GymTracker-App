<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Http\Resources\ExerciseResource;
use App\Http\Resources\ExerciseCollection;
use App\Http\Requests\ExerciseRequest;

class ExerciseController extends Controller
{
    /**
     * Display a listing of the exercises.
     *
     * Note: If the exercise is not public and the user does not have admin privileges, it will not be accessible.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasAdminPrivileges()) $exercises = new ExerciseCollection(Exercise::all());
        else $exercises = new ExerciseCollection(Exercise::where('user_id', $user->id)->orWhere('is_public', true)->get());

        return $this->successResponse("Exercises has been successfully found.", $exercises);
    }

    /**
     * Store a newly created exercise in storage.
     *
     * @param  \App\Http\Requests\ExerciseRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(ExerciseRequest $request)
    {
        $exercise = new ExerciseResource(Exercise::create($request->validated()));

        if (!$exercise) return $this->errorResponse("An error occurred while creating the exercise, please try again later.", 500);

        return $this->successResponse("Exercise has been created successfully.", $exercise);
    }

    /**
     * Display the specified exercise.
     *
     * Note: If the exercise is not public and the user does not have admin privileges, it will not be accessible.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     *
     */
    public function show($id)
    {
        $exercise = Exercise::find($id);

        if (!$exercise) return $this->errorResponse("Exercise not found.", 404);

        $user = auth()->user();

        if (!$exercise->is_public && $exercise->user_id !== $user->id && !$user->hasAdminPrivileges()) {
            return $this->errorResponse("Exercise not available.", 403);
        }

        return $this->successResponse("Exercise has been successfully found.", new ExerciseResource($exercise));
    }

    /**
     * Update the specified exercise in storage.
     *
     * Note: If the user has admin privileges, they can update exercises of other users.
     * Note: Users cannot update the visibility of a public exercise.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\ExerciseRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, ExerciseRequest $request)
    {
        $user = auth()->user();

        if ($user->hasAdminPrivileges()) $exercise = Exercise::find($id);
        else $exercise = $user->exercises()->find($id);

        if (!$exercise) return $this->errorResponse("Exercise not found.", 404);

        if ($exercise->is_public && $request->validated()['is_public'] == 0 && !$user->hasAdminPrivileges()) {
            return $this->errorResponse("You cannot change the visibility of a public exercise.", 403);
        }

        if(!$exercise->update($request->validated())) return $this->errorResponse("An error occurred while updating the exercise, please try again later.", 500);

        return $this->successResponse("Exercise has been successfully updated.", new ExerciseResource($exercise));
    }

    /**
     * Remove the specified exercise from storage.
     *
     * Note: If the user has admin privileges, they can delete exercises of other users.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id)
    {
        $user = auth()->user();

        if ($user->hasAdminPrivileges()) $exercise = Exercise::find($id);
        else $exercise = $user->exercises()->find($id);

        if (!$exercise) return $this->errorResponse("Exercise not found.", 404);

        if ($exercise->workoutExercises()->exists()) return $this->errorResponse("You cannot delete an exercise that is added to someone's workout plan.", 403);

        if (!$exercise->delete()) return $this->errorResponse("An error occurred while deleting the exercise, please try again later.", 500);

        return $this->successResponse("Exercise has been successfully deleted.");
    }
}
