<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\User;
use App\Models\Product;
use App\Models\Department;
use App\Models\Measurement;
use App\Http\Resources\Inventories\InventoryResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InventoryController extends Controller
{
    private function isDepartmentAdministration($user) {
        return in_array("Administration", $user->departments->pluck("name")->toArray());
     }

     private function isDepartmentCashier($user) {
         return in_array("Cashier", $user->departments->pluck("name")->toArray());
      }

    public function index()
    {
        $user = auth()->user();

        if($this->isDepartmentAdministration($user) || $this->isDepartmentCashier($user)){
            return InventoryResource::collection(Inventory::whereDate('created_at', Carbon::now()->toDateString())->orderBy('created_at', 'desc')->get());
        }

        // $user = auth()->user();

        // if(in_array("Administration", $user->departments->pluck("name")->toArray())){
        //     return InventoryResource::collection(Inventory::whereDate('created_at', Carbon::now()->toDateString())->orderBy('created_at', 'desc')->get());
        // } else {
        //     return InventoryResource::collection(Inventory::orderBy('created_at', 'desc')
        //         ->where('user_id', $user->id)
        //         ->get());
        // };
    }

    public function get_older_inventories(Request $request){
        $user = auth()->user();

        $inventory_data = null;
        $startDate = Carbon::createFromFormat('Y-m-d', $request->to);
        $endDate = Carbon::createFromFormat('Y-m-d', $request->from);

        if($this->isDepartmentAdministration($user) || $this->isDepartmentCashier($user)){
            if($request->from === $request->to) {
                $inventory_data = Inventory::whereDate('created_at', '=', $startDate)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $inventory_data = Inventory::whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        };

        return InventoryResource::collection($inventory_data);


    }

    public function store(Request $request)
    {
        try {
            /***********************************************************/
            DB::beginTransaction(); // TBegining of a laravel transaction
            /***********************************************************/

            $product = Product::where("uuid", $request->product_uuid)->first();
            $user = User::where("uuid", $request->user_uuid)->first();
            if (count($user->departments) >= 1) $department = $user->departments->first();
            $measurement = Measurement::where("name", $request->measurement_name)->first();

            $inventory = Inventory::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'quantity' => $request->quantity,
                'buying_price' => $request->buying_price,
                'product_id' => $product->id,
                "measurement_id" => $measurement->id,
                "department_id" => $department->id
            ]);

            if($inventory) $product->update([
                'quantity' => $product->quantity + $request->quantity,
            ]);

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'status' => 'success',
                'message' => 'Inventory created successfully.',
            ]);


        } catch(\Exception $exp) {

            /*****************************************/
            DB::rollBack(); // Rollback
            /*****************************************/

            return response([
                'message' => $exp->getMessage(),
                'status' => 'error'
            ], 400);
        }
    }

    public function update(Request $request)
    {
        try {
            /***********************************************************/
            DB::beginTransaction(); // TBegining of a laravel transaction
            /***********************************************************/

            $inventory = Inventory::where("uuid", $request->inventory_uuid)->first();
            $product = Product::findOrFail($inventory->product_id);

            if(!$inventory) return response()->json([
                'status' => 'error',
                'message' => 'Inventory does not exists.',
            ]);

            if($inventory) $product->update([
                'quantity' => $inventory->actual_quantity + $request->quantity,
            ]);

            $inventory->update([
                'quantity' => $request->quantity,
                'actual_quantity' => $request->quantity
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Inventory updated successfully.',
            ]);

        } catch(\Exception $exp) {

            /*****************************************/
            DB::rollBack(); // Rollback
            /*****************************************/

            return response([
                'message' => $exp->getMessage(),
                'status' => 'error'
            ], 400);
        }
    }

    public function destroy(Request $request)
    {
        $inventory = Inventory::where("uuid", $request->uuid)->first();
        $inventory->delete();

        return response()->json([
            'message' => 'Inventory deleted successfully.',
            'status' => 'success'
        ]);
    }
}
