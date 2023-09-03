<?php

namespace App\Http\Resources\Products;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DeletedProductResource extends JsonResource
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
            "measurement" => $this->measurement ? $this->measurement->name : "N/A",
            "department" => $this->department ? $this->department->name : "N/A",
            "category" => $this->category ? $this->category->name : "N/A",
            "user" => $this->user ? $this->user->name : "N/A",
            "deleted_at" => $this->deleted_at->format('h:i:s A, jS D M Y'),
            "action" => ""
        ];
    }
}
