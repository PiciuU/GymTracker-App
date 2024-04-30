<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExerciseResource extends JsonResource
{
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
            'userId' => $this->user_id,
            'name' => $this->name,
            'description' => $this->description,
            'muscleGroup' => $this->muscle_group,
            'thumbnailUrl' => $this->thumbnail_url,
            'attachmentUrl' => $this->attachment_url,
            'isPublic' => $this->is_public,
        ];

        if ($request->user()->tokenCan('admin')) {
            $fields += [
                'isApproved' => $this->is_approved
            ];
        }

        return $fields;
    }
}
