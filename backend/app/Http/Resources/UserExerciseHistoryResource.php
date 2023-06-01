<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserExerciseHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \App\Http\Requests\UserExerciseHistoryRequest  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->user()->tokenCan('admin')) {
            return [
                'id' => $this->id,
                'exerciseId' => $this->exercise_id,
                'userId' => $this->user_id,
                'weight' => $this->weight,
                'date' => $this->date
            ];
        }
        else {
            return [
                'id' => $this->id,
                'weight' => $this->weight,
                'date' => $this->date
            ];
        }
    }
}
