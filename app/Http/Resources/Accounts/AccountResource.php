<?php

namespace App\Http\Resources\Accounts;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Inventory;

class AccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $total_sales = $this->expected_cash + $this->expected_mpesa + $this->expected_card + $this->expected_mpesa_cash + $this->expected_debt;

        return [
            "uuid" => $this->uuid,
            "production_cost" => $this->production_cost,
            "total_sales" => $total_sales,
            "expected_cash" => $this->expected_cash,
            "expected_mpesa" => $this->expected_mpesa,
            "actual_cash" => $this->actual_cash,
            "actual_mpesa" => $this->actual_mpesa,

            "expected_card" => $this->expected_card,
            "expected_debt" => $this->expected_debt,
            "expected_mpesa_cash" => $this->expected_mpesa_cash,

            "actual_total_sales" => $this->actual_mpesa + $this->actual_cash,
            "expected_profit" => $total_sales - $this->production_cost,
            "actual_profit" => ($this->actual_mpesa + $this->actual_cash) - $this->production_cost,
            "user" => $this->user? $this->user->name: "No User",
            "department" => $this->department? $this->department->name: "No Department",
            "valid_date" => $this->created_at->format('d-m-Y'),
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y')
        ];
    }
}
