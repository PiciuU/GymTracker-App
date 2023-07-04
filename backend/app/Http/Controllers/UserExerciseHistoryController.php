<?php

namespace App\Http\Controllers;

use App\Models\UserExerciseHistory;
use App\Models\Exercise;
use App\Http\Resources\UserExerciseHistoryResource;
use App\Http\Resources\UserExerciseHistoryCollection;
use App\Http\Requests\UserExerciseHistoryRequest;

class UserExerciseHistoryController extends Controller
{
    /**
     * Display a listing of the user's exercise history.
     *
     * @return \App\Http\Traits\ResponseTrait
     */
    public function index()
    {
        $userExerciseHistory = new UserExerciseHistoryCollection(auth()->user()->history);
        return $this->successResponse("User exercise history has been successfully found.", $userExerciseHistory);
    }

    /**
     * Store a newly created user exercise history entry.
     *
     * @param  \App\Http\Requests\UserExerciseHistoryRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(UserExerciseHistoryRequest $request)
    {
        $exercise = Exercise::find($request->exerciseId);

        if (!$exercise) return $this->errorResponse("Exercise not found.", 404);

        $user = auth()->user();

        if (!$exercise->is_public && $exercise->user_id !== $user->id && !$user->hasAdminPrivileges()) {
            return $this->errorResponse("Exercise not available.", 404);
        }

        $userExerciseHistory = new UserExerciseHistoryResource(UserExerciseHistory::create($request->validated()));

        if (!$userExerciseHistory) return $this->errorResponse("An error occurred while adding the user exercise history entry, try again later.", 500);

        return $this->successResponse("User exercise history has been successfully created.", $userExerciseHistory);
    }

    /**
     * Display the specified user exercise history entry.
     *
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        $user = auth()->user();

        $userExerciseHistory = new UserExerciseHistoryCollection($user->history()->where('exercise_id', $id)->orderBy('date', 'desc')->get());

        if (!$userExerciseHistory) return $this->errorResponse("User exercise history not found.", 404);

        return $this->successResponse("User exercise history has been successfully found.", $userExerciseHistory);
    }

    /**
     * Update the specified user exercise history entry.
     *
     * Note: If the user has admin privileges, they can update any user exercise history entry.
     *
     * @param  int  $id
     * @param  \App\Http\Requests\UserExerciseHistoryRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function update($id, UserExerciseHistoryRequest $request)
    {
        $user = auth()->user();

        if ($user->tokenCan('admin')) $userExerciseHistory = UserExerciseHistory::find($id);
        else $userExerciseHistory = $user->history()->find($id);

        if (!$userExerciseHistory) return $this->errorResponse("User exercise history not found", 404);

        if(!$userExerciseHistory->update($request->validated())) return $this->errorResponse("An error occurred while updating the user exercise history, try again later.", 500);

        return $this->successResponse("User exercise history has been successfully updated.", new UserExerciseHistoryResource($userExerciseHistory));
    }

    /**
     * Remove the specified user exercise history entry.
     *
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id)
    {
        $userExerciseHistory = auth()->user()->history()->find($id);

        if (!$userExerciseHistory) return $this->errorResponse("User exercise history not found.", 404);

        if(!$userExerciseHistory->delete()) return $this->errorResponse("An error occurred while deleting the user exercise history, try again later.", 500);

        return $this->successResponse("User exercise history has been successfully deleted.");
    }
}
