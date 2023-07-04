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
        $fields = [
            'id' => $this->id,
            'weight' => $this->weight,
            'date' => $this->date
        ];

        if ($request->user()->tokenCan('admin')) {
            $fields += [
                'exerciseId' => $this->exercise_id,
                'userId' => $this->user_id,
            ];
        }

        return $fields;
    }
}
