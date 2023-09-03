<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            "measurement" => $this->measurement ? $this->measurement->name : "No measurement",
            "department" => $this->department ? $this->department->name : "No department",
            "quantity" => $this->quantity,
            "min_quantity" => $this->quantity_left,
            "buying_price" => $this->buying_price,
            "selling_price" => $this->selling_price,
            "category" => $this->category ? $this->category->name : "No Category",
            "user" => $this->user ? $this->user->name : "No User",
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y'),
            "updated_at" => $this->updated_at->format('h:i:s A, jS D M Y')
        ];
    }
}
