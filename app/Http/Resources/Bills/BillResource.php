<?php

namespace App\Http\Resources\Bills;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Sales\SaleResource;
use App\Http\Resources\Debtors\DebtorResource;
use App\Http\Resources\Debtors\DebtRecordResource;
use App\Models\Debtor;
use App\Models\DebtRecord;
use Carbon\Carbon;

class BillResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function sumDebtRecords($debtor, $bill_id){
        $total_debts = 0;
        $debtor_id = Debtor::where('uuid', $debtor->uuid)->first()->id;
        if($debtor_id){
            $total_debts = DebtRecord::where(['debtor_id'=> $debtor_id, 'bill_id' => $bill_id])->sum('amount_paid');
        }

        return $total_debts;
    }

    public function debtRecords($debtor, $bill_id){
        $debt_records = [];
        $debtor_id = Debtor::where('uuid', $debtor->uuid)->first()->id;
        $debts = DebtRecord::where(['debtor_id' => $debtor_id, 'bill_id' => $bill_id])->get();
        if($debts->count() > 0) {
            $debt_records = $debts;
            return DebtRecordResource::collection($debt_records);
        }
        return $debt_records;
    }

    public function toArray(Request $request): array
    {
        return [
            "uuid" => $this->uuid,
            "bill_ref" => $this->bill_ref,
            "status" => $this->status,
            "selling_price" => $this->actual_selling_price,
            "actual_selling_price" => $this->actual_selling_price,
            "debtor_name" => $this->debtor ? $this->debtor->name : "No debtor",
            "total_debt_paid" => $this->debtor ? $this->sumDebtRecords($this->debtor, $this->id) : 0,
            "debt_records" => $this->debtor ? $this->debtRecords($this->debtor, $this->id) : [],
            "sales" => SaleResource::collection($this->sales),
            "payment_mode" => $this->payment_mode ? $this->payment_mode->name : "No Payment mode",
            "department" => $this->department ? $this->department->name : "No Department",
            "user" => $this->user ? $this->user->name : "No User",
            "created_at" => $this->created_at->format('h:i:s A, jS D M Y')
        ];
    }
}
