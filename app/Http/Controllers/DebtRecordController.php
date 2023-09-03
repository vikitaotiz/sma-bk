<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DebtRecord;
use App\Http\Resources\DebtRecords\DebtRecordResource;

class DebtRecordController extends Controller
{
    public function index()
    {
        // $user = auth()->user();

        // if(in_array("Administrator", $user->debtors->pluck("name")->toArray())){
            return DebtRecordResource::collection(DebtRecord::orderBy('created_at', 'desc')->get());
        // };
    }

    public function store(Request $request)
    {
        $debt_record = DebtRecord::where('name', $request->name)->first();
        $debtor = Debtor::where('name', $request->debtor_uuid)->first();

        if($debt_record) return response()->json([
            'status' => 'error',
            'message' => 'Debt record already exists.',
        ]);

        DebtRecord::create([
            'uuid' => Str::uuid()->toString(),
            "debtor_id" => $debtor->id,
            "amount_paid" => $request->amount_paid,
            "balance" => $request->balance,
            "bill_id" => $request->bill_id,
            "user_id" => $request->user_id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Debt record created successfully.'
        ]);
    }

    public function update(Request $request)
    {
        $debt_record = DebtRecord::where("uuid", $request->uuid)->first();

        if(!$debt_record) return response()->json([
            'status' => 'error',
            'message' => 'Debt record does not exists.',
        ]);

        $debt_record->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Debt record updated successfully.',
        ]);
    }

    public function destroy(Request $request)
    {
        $debt_record = DebtRecord::where("uuid", $request->uuid)->first();
        $debt_record->delete();

        return response()->json([
            'message' => 'Debt record deleted successfully.',
            'status' => 'success'
        ]);
    }
}
