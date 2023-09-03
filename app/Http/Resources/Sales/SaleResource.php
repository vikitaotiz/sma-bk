<?php

namespace App\Http\Resources\Sales;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Product;

class SaleResource extends JsonResource
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
            "bill_ref" => $this->bill ? $this->bill->bill_ref : "No Bill",
            "payment_mode" => $this->bill ? $this->bill->payment_mode->name: "No Payment",
            "selling_price" => $product? $product->selling_price : 0,
            "quantity" => $this->quantity,
            "status" => $this->status,
            "user" => $this->user ? $this->user->name: "No user",
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y')
        ];
    }
}
