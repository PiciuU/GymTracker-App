<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($request->user() && $request->user()->hasAdminPrivileges()) {
            return [
                'id' => $this->id,
                'login' => $this->login,
                'name' => $this->name,
                'email' => $this->email,
                'user_role_id' => $this->user_role_id,
            ];
        }
        else {
            return [
                'id' => $this->id,
                'login' => $this->login,
                'name' => $this->name,
                'email' => $this->email,
            ];
        }
    }
}
