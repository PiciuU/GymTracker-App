<?php

namespace App\Http\Resources;

use App\Models\UserExerciseHistory;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutExerciseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->rest_time === null) $restTime = null;
        else $restTime = gmdate('i:s', $this->rest_time);

        $personalBest = UserExerciseHistory::where('exercise_id', $this->exercise->id)
            ->where('user_id', auth()->user()->id)
            ->orderBy('weight', 'desc')
            ->select('weight', 'date')
            ->first();

        $fields = [
            'name' => $this->exercise->name,
            'description' => $this->exercise->description,
            'muscleGroup' => $this->exercise->muscle_group,
            'thumbnailUrl' => $this->exercise->thumbnail_url,
            'attachmentUrl' => $this->exercise->attachment_url,
            'sets' => $this->sets,
            'reps' => $this->reps,
            'restTime' => $restTime,
            'weight' => $this->weight,
            'personalBest' => $personalBest
        ];

        if ($request->user()->tokenCan('admin')) {
            $fields += [
                'id' => $this->exercise->id,
                'userId' => $this->exercise->user_id,
                'isApproved' => $this->exercise->is_approved,
                'isPublic' => $this->exercise->is_public
            ];
        }

        return $fields;
    }
}
