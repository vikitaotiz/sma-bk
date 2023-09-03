<?php

namespace App\Http\Resources\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeletedUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "name" => $this->name,
            "active" => $this->active,
            "roles" => $this->roles->map->only('uuid', 'name'),
            "departments" => $this->departments->map->only('uuid', 'name'),
            "deleted_at" => $this->deleted_at->format('h:i:s A, jS D M Y'),
            "action" => ""
        ];
    }
}
