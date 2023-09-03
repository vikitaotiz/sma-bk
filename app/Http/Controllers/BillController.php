<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\PaymentMode;
use App\Http\Resources\Bills\BillResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BillController extends Controller
{
    private function isDepartmentAdministration($user) {
       return in_array("Administration", $user->departments->pluck("name")->toArray());
    }

    private function isDepartmentCashier($user) {
        return in_array("Cashier", $user->departments->pluck("name")->toArray());
     }

    public function get_older_bills(Request $request) {
        $user = auth()->user();
        $bill_data = null;
        $startDate = Carbon::createFromFormat('Y-m-d', $request->to);
        $endDate = Carbon::createFromFormat('Y-m-d', $request->from);

        if($this->isDepartmentAdministration($user) || $this->isDepartmentCashier($user)){
            if($request->from === $request->to) {
                $bill_data = Bill::whereDate('created_at', '=', $startDate)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $bill_data = Bill::whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

        };
        // else {
        //     if($request->from === $request->to) {
        //         $bill_data = Bill::whereDate('created_at', '=', $startDate)
        //             ->orderBy('created_at', 'desc')
        //             ->where('user_id', $user->id)
        //             ->get();
        //     } else {
        //         $bill_data = Bill::whereBetween('created_at', [$startDate, $endDate])
        //             ->orderBy('created_at', 'desc')
        //             ->where('user_id', $user->id)
        //             ->get();
        //     }
        // }

        return BillResource::collection($bill_data);
    }

    public function index()
    {
        $user = auth()->user();

        if($this->isDepartmentAdministration($user) || $this->isDepartmentCashier($user)){
            return BillResource::collection(Bill::whereDate('created_at', Carbon::now()->toDateString())->orderBy('created_at', 'desc')->get());

        } else {
            return BillResource::collection(Bill::whereDate('created_at', Carbon::now()->toDateString())->orderBy('created_at', 'desc')
                ->where('user_id', $user->id)
                ->get());
        };
    }



    public function uncleared_bills()
    {
        $user = auth()->user();

        $payment_mode_id = PaymentMode::where('name', 'Debt')->first()->id;

        if($this->isDepartmentAdministration($user) || $this->isDepartmentCashier()){
            return BillResource::collection(Bill::orderBy('created_at', 'desc')
                    ->where('payment_mode_id', $payment_mode_id)
                    ->get());

        // } else if($this->isDepartmentCashier()){
        //     return BillResource::collection(Bill::orderBy('created_at', 'desc')
        //             ->where('payment_mode_id', $payment_mode_id)
        //             ->get());
        }
        else {
            return BillResource::collection(Bill::orderBy('created_at', 'desc')
                ->where('payment_mode_id', $payment_mode_id)
                ->where('user_id', $user->id)
                ->get());
        };
    }

    public function destroy(Request $request)
    {
        $role = Bill::where("uuid", $request->uuid)->first();
        $role->delete();

        return response()->json([
            'message' => 'Bill deleted successfully.',
            'status' => 'success'
        ]);
    }
}
