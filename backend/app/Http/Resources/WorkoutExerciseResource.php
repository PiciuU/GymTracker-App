<?php

namespace App\Http\Resources;

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
        if ($request->user()->tokenCan('admin')) {
            return [
                'id' => $this->id,
                'workoutId' => $this->workout_id,
                'exercise' => [
                    'id' => $this->exercise_id,
                    'name' => $this->exercise->name,
                    'description' => $this->exercise->description,
                    'muscleGroup' => $this->exercise->muscle_group,
                    'thumbnailUrl' => $this->exercise->thumbnail_url,
                    'attachmentUrl' => $this->exercise->attachment_url,
                    'userId' => $this->exercise->user_id,
                    'isPublic' => $this->exercise->is_public,
                    'isApproved' => $this->exercise->is_approved,
                    'sets' => $this->sets,
                    'reps' => $this->reps,
                    'restTime' => $this->rest_time,
                    'weight' => $this->weight
                ],
            ];
        }
        else {
            return [
                'id' => $this->id,
                'exercise' => [
                    'name' => $this->exercise->name,
                    'description' => $this->exercise->description,
                    'muscleGroup' => $this->exercise->muscle_group,
                    'thumbnailUrl' => $this->exercise->thumbnail_url,
                    'attachmentUrl' => $this->exercise->attachment_url,
                    'sets' => $this->sets,
                    'reps' => $this->reps,
                    'restTime' => $this->rest_time,
                    'weight' => $this->weight
                ],
            ];
        }
    }
}
