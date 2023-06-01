<?php

namespace App\Http\Controllers;

use App\Models\UserExerciseHistory;
use App\Models\Exercise;
use Illuminate\Http\Request;
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
        $exerciseHistoryCollection = new UserExerciseHistoryCollection(auth()->user()->history);
        return $this->successResponse('User exercise history list found', $exerciseHistoryCollection);
    }

    /**
     * Store a newly created user exercise history entry.
     *
     * @param  \App\Http\Requests\UserExerciseHistoryRequest  $request
     * @return \App\Http\Traits\ResponseTrait
     */
    public function store(UserExerciseHistoryRequest $request)
    {
        $user = auth()->user();

        // Find exercise
        $exercise = Exercise::find($request->exerciseId);
        if (!$exercise) return $this->errorResponse('Exercise not found.', 404);

        // Check exercise availability
        if (!$exercise->is_public && $exercise->user_id !== auth()->user()->id && !auth()->user()->tokenCan('admin')) {
            return $this->errorResponse('Exercise not available.', 404);
        }

        $createdExerciseHistory = new UserExerciseHistoryResource(UserExerciseHistory::create($request->validated()));

        if (!$createdExerciseHistory) return $this->errorResponse('An error occurred while adding the user exercise history entry, try again later', 500);

        return $this->successResponse('User exercise history entry has been added successfully', $createdExerciseHistory);
    }

    /**
     * Display the specified user exercise history entry.
     *
     * Note: If the user has admin privileges, they can view any user exercise history entry.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function show($id)
    {
        $user = auth()->user();

        if ($user->tokenCan('admin')) $exerciseHistory = UserExerciseHistory::find($id);
        else $exerciseHistory = $user->history()->find($id);

        if (!$exerciseHistory) return $this->errorResponse('User exercise history not found.', 404);

        return $this->successResponse('User exercise history found', new UserExerciseHistoryResource($exerciseHistory));
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

        if ($user->tokenCan('admin')) $exerciseHistory = UserExerciseHistory::find($id);
        else $exerciseHistory = $user->history()->find($id);

        if (!$exerciseHistory) return $this->errorResponse('User exercise history not found', 404);

        if(!$exerciseHistory->update($request->validated())) return $this->errorResponse('An error occurred while updating the user exercise history entry, try again later', 500);

        return $this->successResponse('User exercise history entry has been updated successfully', new UserExerciseHistoryResource($exerciseHistory));
    }

    /**
     * Remove the specified user exercise history entry.
     *
     * Note: If the user has admin privileges, they can delete any user exercise history entry.
     *
     * @param  int  $id
     * @return \App\Http\Traits\ResponseTrait
     */
    public function destroy($id)
    {
        $exerciseHistory = auth()->user()->history()->find($id);

        if (!$exerciseHistory) return $this->errorResponse('User exercise history not found', 404);

        if(!$exerciseHistory->delete()) return $this->errorResponse('An error occurred while deleting the user exercise history entry, try again later', 500);

        return $this->successResponse('User exercise history entry has been deleted successfully');
    }
}
