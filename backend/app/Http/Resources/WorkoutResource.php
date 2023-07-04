<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkoutResource extends JsonResource
{
    protected $daysMap = [
        1 => "Monday",
        2 => "Tuesday",
        3 => "Wednesday",
        4 => "Thursday",
        5 => "Friday",
        6 => "Saturday",
        7 => "Sunday"
    ];

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $fields = [
            'id' => $this->id,
            'dayOfWeek' => $this->daysMap[$this->day_of_week],
            'exercises' => new WorkoutExerciseCollection($this->workoutExercises),
        ];

        if ($request->user()->tokenCan('admin')) {
            $fields += [
                'userId' => $this->user_id
            ];
        }

        return $fields;
    }
}
