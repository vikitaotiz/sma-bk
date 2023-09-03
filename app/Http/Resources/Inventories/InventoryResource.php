<?php

namespace App\Http\Resources\Inventories;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
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
            "quantity" => $this->quantity,
            "total" => $this->buying_price * $this->quantity,
            "buying_price" => $this->buying_price,
            "product" => $this->product ? $this->product->name: "No product",
            "measurement" => $this->measurement ? $this->measurement->name : "No Measurement",
            "department" => $this->department ? $this->department->name : "No Measurement",
            "user" => $this->user ? $this->user->name: "No user",
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y')
        ];
    }
}
