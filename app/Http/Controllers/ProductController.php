<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Category;
use App\Models\User;
use App\Models\Measurement;
use App\Models\Department;
use App\Http\Resources\Products\ProductResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        // $user = auth()->user();

        return ProductResource::collection(Product::orderBy('created_at', 'desc')->get());


        // if(in_array("Administration", $user->departments->pluck("name")->toArray())){
        //     return ProductResource::collection(Product::orderBy('created_at', 'desc')->get());
        // } else {
        //     return ProductResource::collection(Product::orderBy('created_at', 'desc')
        //         ->where('user_id', 1)
        //         ->orWhere('user_id', $user->id)
        //         ->get());
        // };
    }

    public function finished_products() {

        $products = Product::whereColumn('quantity', '<=', 'quantity_left')
                        ->orWhere('quantity','<', 1)
                        ->orderBy('created_at', 'desc')
                        ->get();

        return ProductResource::collection($products);
    }

    public function store(Request $request)
    {
        try {
            /***********************************************************/
            DB::beginTransaction(); // TBegining of a laravel transaction
            /***********************************************************/

            $product = Product::where(['name'=> $request->name])->first();
            if($product) return response()->json([
                'status' => 'error',
                'message' => 'Product already exists.',
            ]);

            $user = User::where("uuid", $request->user_uuid)->first();
            $category = Category::where("uuid", $request->category_uuid)->first();
            $measurement = Measurement::where("uuid", $request->measurement_uuid)->first();
            $department = Department::where("uuid", $request->department_uuid)->first();

            $product = Product::create([
                'uuid' => Str::uuid()->toString(),
                'name' => $request->name,
                'quantity' => $request->quantity,
                'quantity_left' => $request->min_quantity,
                'user_id' => $user->id,
                'category_id' => $category->id,
                'measurement_id' => $measurement->id,
                'department_id' => $department->id,
                "product_code" => $request->product_code,
                "buying_price" => $request->buying_price,
                "selling_price" => $request->selling_price
            ]);

            $inventory = Inventory::create([
                'uuid' => Str::uuid()->toString(),
                'user_id' => $user->id,
                'quantity' => $request->quantity,
                'actual_quantity' => $request->quantity,
                'buying_price' => $request->buying_price,
                'product_id' => $product->id,
                "measurement_id" => $measurement->id,
                "department_id" => $department->id
            ]);

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'status' => 'success',
                'message' => 'Product and inventory created successfully.'
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

            $product = Product::where("uuid", $request->product_uuid)->first();

            if(!$product) return response()->json([
                'status' => 'error',
                'message' => 'Product does not exists.',
            ]);

            $user = User::where("uuid", $request->user_uuid)->first();
            $category = Category::where("uuid", $request->category_uuid)->first();
            $measurement = Measurement::where("uuid", $request->measurement_uuid)->first();
            $department = Department::where("uuid", $request->department_uuid)->first();

            $product->update([
                'name' => $request->name,
                'quantity' => $request->quantity,
                'quantity_left' => $request->min_quantity,
                'actual_quantity' => $request->quantity,
                'user_id' => $user->id,
                'category_id' => $category->id,
                'measurement_id' => $measurement->id,
                'department_id' => $department->id,
                "product_code" => $request->product_code,
                "buying_price" => $request->buying_price,
                "selling_price" => $request->selling_price
            ]);

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'status' => 'success',
                'message' => 'Product updated successfully.',
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
        try {
            /***********************************************************/
            DB::beginTransaction(); // TBegining of a laravel transaction
            /***********************************************************/
            $product = Product::where("uuid", $request->uuid)->first();
            $product->delete();

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'message' => 'Product deleted successfully.',
                'status' => 'success'
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
}
