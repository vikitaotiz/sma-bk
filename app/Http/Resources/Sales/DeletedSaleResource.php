<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Product;

class DeletedSaleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $product = Product::where('name', $this->name)->first();

        return [
            "uuid" => $this->uuid,
            "name" => $this->name,
            "bill_ref" => $this->bill ? $this->bill->bill_ref : "N/A",
            "payment_mode" => $this->bill ? $this->bill->payment_mode->name: "N/A",
            "selling_price" => $product? $product->selling_price : 0,
            "status" => $this->status,
            "user" => $this->user ? $this->user->name: "N/A",
            "deleted_at" => $this->deleted_at->format('h:i:s A, jS D M Y'),
            "action" => ""
        ];
    }
}
