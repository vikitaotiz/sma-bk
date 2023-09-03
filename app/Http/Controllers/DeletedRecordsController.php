<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Models\Bill;
use App\Models\Sale;
use App\Models\User;

use App\Http\Resources\Products\DeletedProductResource;
use App\Http\Resources\Bills\DeletedBillResource;
use App\Http\Resources\Sales\DeletedSaleResource;
use App\Http\Resources\Users\DeletedUserResource;

class DeletedRecordsController extends Controller
{
    private function isDepartmentAdministration($user) {
        return in_array("Administration", $user->departments->pluck("name")->toArray());
    }

    public function records() {
        $user = auth()->user();

        if($this->isDepartmentAdministration($user)){
            return [
                "deleted_products" => DeletedProductResource::collection(Product::onlyTrashed()->get()),
                "deleted_bills" => DeletedBillResource::collection(Bill::onlyTrashed()->get()),
                "deleted_sales" => DeletedSaleResource::collection(Sale::onlyTrashed()->get()),
                "deleted_users" => DeletedUserResource::collection(User::onlyTrashed()->get())
            ];
        } else {
            return response()->json([
                'message' => 'You are not authorized to access this resource.',
                'status' => 'error'
            ]);
        }
    }
}
