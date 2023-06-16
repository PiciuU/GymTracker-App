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
        if ($this->rest_time === null) {
            $formattedTime = null;
        } else {
            $seconds = $this->rest_time;
            $minutes = floor($seconds / 60);
            $seconds = $seconds % 60;
            $formattedTime = sprintf('%02d:%02d', $minutes, $seconds);
        }

        if ($request->user()->tokenCan('admin')) {
            return [
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
                'restTime' => $formattedTime,
                'weight' => $this->weight
            ];
        }
        else {
            return [
                'name' => $this->exercise->name,
                'description' => $this->exercise->description,
                'muscleGroup' => $this->exercise->muscle_group,
                'thumbnailUrl' => $this->exercise->thumbnail_url,
                'attachmentUrl' => $this->exercise->attachment_url,
                'sets' => $this->sets,
                'reps' => $this->reps,
                'restTime' => $formattedTime,
                'weight' => $this->weight
            ];
        }
    }
}
