<?php

namespace App\Http\Resources\Users;

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
        return [
            "uuid" => $this->uuid,
            "name" => $this->name,
            "active" => $this->active,
            "email" => $this->email,
            "phone" => $this->phone,
            "email_notify" => $this->email_notify,
            "whatsapp_notify" => $this->whatsapp_notify,
            "roles" => $this->roles->map->only('uuid', 'name'),
            "departments" => $this->departments->map->only('uuid', 'name'),
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y'),
            "updated_at" => $this->updated_at->format('h:i:s A, jS D M Y')
        ];
    }
}
