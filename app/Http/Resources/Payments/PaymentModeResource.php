<?php

namespace App\Http\Resources\Payments;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentModeResource extends JsonResource
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
            "sales" => $this->bills->count(),
            "user" => $this->user ? $this->user->name : "No user",
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y')
        ];
    }
}
