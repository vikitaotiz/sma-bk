<?php

namespace App\Http\Resources\Debtors;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DebtRecordResource extends JsonResource
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
            "debtor" => $this->debtor ? $this->debtor->name: "No Debtor",
            "amount_paid" => $this->amount_paid,
            "balance" => $this->balance,
            "bill_ref" => $this->bill ? $this->bill->bill_ref : "No bill",
            "payment_mode" => $this->payment_mode ? $this->payment_mode->name : "No Payment Mode",
            "user" => $this->user ? $this->user->name: "No user",
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y')
        ];
    }
}
