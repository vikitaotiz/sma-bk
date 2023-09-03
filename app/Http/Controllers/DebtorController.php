<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Debtor;
use App\Models\User;
use App\Http\Resources\Debtors\DebtorResource;
use Illuminate\Support\Str;

class DebtorController extends Controller
{
    public function index()
    {
        // $user = auth()->user();

        // if(in_array("Administrator", $user->debtors->pluck("name")->toArray())){
            return DebtorResource::collection(Debtor::orderBy('created_at', 'desc')->get());
        // };
    }

    public function store(Request $request)
    {

        $debtor = Debtor::where('name', $request->name)->first();

        if($debtor) return response()->json([
            'status' => 'error',
            'message' => 'Debtor already exists.',
        ]);

        $fields = $request->validate([
            'name' => 'required|string|unique:debtors',
            'phone' => 'required|string'
        ]);

        $user = User::where('uuid', $request->user_uuid)->first();

        Debtor::create([
            'uuid' => Str::uuid()->toString(),
            "name" => $fields['name'],
            "phone" => $fields['phone'],
            "email" => $request->email,
            "user_id" => $user->id
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Debtor created successfully.'
        ]);
    }

    public function update(Request $request)
    {
        $debtor = Debtor::where("uuid", $request->uuid)->first();

        if(!$debtor) return response()->json([
            'status' => 'error',
            'message' => 'Debtor does not exists.',
        ]);

        $debtor->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Debtor updated successfully.',
        ]);
    }

    public function destroy(Request $request)
    {
        $debtor = Debtor::where("uuid", $request->uuid)->first();
        $debtor->delete();

        return response()->json([
            'message' => 'Debtor deleted successfully.',
            'status' => 'success'
        ]);
    }
}
