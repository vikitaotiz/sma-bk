<?php

namespace App\Http\Resources\Measurements;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeasurementResource extends JsonResource
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
            "user" => $this->user ? $this->user->name : "No User",
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y')
        ];
    }
}
