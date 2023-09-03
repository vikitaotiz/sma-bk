<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentMode;
use App\Models\User;
use App\Http\Resources\Payments\PaymentModeResource;
use Illuminate\Support\Str;

class PaymentModeController extends Controller
{
    public function index()
    {
        return PaymentModeResource::collection(PaymentMode::orderBy('created_at', 'desc')
                ->where('name','<>','Not Paid')
                ->get());
    }

    public function store(Request $request)
    {
        $payment_mode = PaymentMode::where('name', $request->name)->first();
        $user_id = User::where("uuid", $request->uuid)->first()->id;

        if($payment_mode) return response()->json([
            'status' => 'error',
            'message' => 'Payment mode already exists.',
        ]);

        PaymentMode::create([
            'name' => $request->name,
            'user_id' => $user_id,
            'uuid' => Str::uuid()->toString()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment mode created successfully.',
            'name' => $request->name
        ]);
    }

    public function update(Request $request)
    {
        $payment_mode = PaymentMode::where("uuid", $request->uuid)->first();

        if(!$payment_mode) return response()->json([
            'status' => 'error',
            'message' => 'Payment mode does not exists.',
        ]);

        $payment_mode->update([
            'name' => $request->name
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment mode updated successfully.',
        ]);
    }

    public function destroy(Request $request)
    {
        $payment_mode = PaymentMode::where("uuid", $request->uuid)->first();
        $payment_mode->delete();

        return response()->json([
            'message' => 'Payment mode deleted successfully.',
            'status' => 'success'
        ]);
    }
}
