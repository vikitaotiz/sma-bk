<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotificationMail;

use App\Models\Sale;
use App\Models\User;
use App\Models\Bill;
use App\Models\Product;
use App\Models\Debtor;
use App\Models\Inventory;
use App\Models\DebtRecord;
use App\Models\PaymentMode;
use App\Http\Resources\Sales\SaleResource;
use App\Http\Resources\Bills\BillResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class SaleController extends Controller
{
    private function isDepartmentAdministration($user) {
        return in_array("Administration", $user->departments->pluck("name")->toArray());
     }

     private function isDepartmentCashier($user) {
         return in_array("Cashier", $user->departments->pluck("name")->toArray());
      }

    private function adminEmail() {
        $admin_email = null;
        $admin_email =  User::where([ 'name' => 'Administrator', 'email_notify' => true])->whereNotNull('email')->first()->email;
        return $admin_email;
    }

    private function sendMailNotification($topic, $subject, $data) {
        if ($this->adminEmail()) {
            $email_data = [
                'topic' => $topic,
                'subject' => $subject,
                'data' => $data
            ];

            Mail::to($this->adminEmail())->send(
                new NotificationMail($email_data)
            );
        } else {
            return null;
        }
    }

    public function index()
    {
        $user = auth()->user();

        if($this->isDepartmentAdministration($user) || $this->isDepartmentCashier($user)){
            return SaleResource::collection(Sale::whereDate('created_at', Carbon::now()->toDateString())->orderBy('created_at', 'desc')->get());
        }
    }

    public function get_older_sales(Request $request){
        $user = auth()->user();

        $sales_data = null;
        $startDate = Carbon::createFromFormat('Y-m-d', $request->to);
        $endDate = Carbon::createFromFormat('Y-m-d', $request->from);

        if($this->isDepartmentAdministration($user) || $this->isDepartmentCashier($user)){
            if($request->from === $request->to) {
                $sales_data = Sale::whereDate('created_at', '=', $startDate)
                    ->orderBy('created_at', 'desc')
                    ->get();
            } else {
                $sales_data = Sale::whereBetween('created_at', [$startDate, $endDate])
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        };

        return SaleResource::collection($sales_data);
    }

    public function generateBillRef() {
        return substr(str_shuffle(str_repeat("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 5).'/'.Carbon::now()->format('d');
    }

    public function add_product_to_bill(Request $request) {
        try {
            /***********************************************************/
            DB::beginTransaction(); // Begining of a laravel transaction
            /***********************************************************/

            $user = User::where("uuid", $request->user_uuid)->first();
            $bill = Bill::where("uuid", $request->bill_uuid)->first();

            if($user && $bill){
                $sale = Sale::create([
                    'uuid' => Str::uuid()->toString(),
                    'name' => $request->name,
                    'quantity' => $request->quantity,
                    'user_id' => $user->id,
                    'bill_id' => $bill->id,
                    'status' => "pending"
                ]);

                $prod = Product::where('uuid', $request->product_uuid)->first();
                if($prod) $prod->update([
                    'quantity' => $prod->quantity - $request->quantity,
                ]);

                /******************************************/
                DB::commit(); // End of database transactions (Success)
                /******************************************/

                return response()->json([
                    'status' => 'success',
                    'message' => 'Product added to bill successfully.',
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'There was an error.',
                ]);
            };


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

    public function remove_product_from_bill(Request $request) {
        try {
            /***********************************************************/
            DB::beginTransaction(); // Begining of a laravel transaction
            /***********************************************************/

            $user = User::where("uuid", $request->user_uuid)->first();
            $sale = Sale::where("uuid", $request->product_uuid)->first();

            if($user && $sale){
                $prod = Product::where('name', $request->product_name)->first();
                $prod->update([
                    'quantity' => $prod->quantity + $request->quantity,
                ]);

                $sale->delete();

                /******************************************/
                DB::commit(); // End of database transactions (Success)
                /******************************************/

                return response()->json([
                    'message' => 'Product returned successfully.',
                    'status' => 'success'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'There was an error.',
                ]);
            };



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

    public function remove_bill(Request $request) {
        try {
            /***********************************************************/
            DB::beginTransaction(); // TBegining of a laravel transaction
            /***********************************************************/

            $user = User::where("uuid", $request->user_uuid)->first();
            $bill = Bill::where("uuid", $request->bill_uuid)->first();

            if($user && $bill){

                foreach ($bill->sales as $product) {
                    $prod = Product::where('name', $product->name)->first();
                    $prod->update([
                        'quantity' => $prod->quantity + $product->quantity,
                    ]);

                    $sale = Sale::where('uuid', $product->uuid)->first();
                    $sale->delete();
                }

                $bill->delete();

                /******************************************/
                DB::commit(); // End of database transactions (Success)
                /******************************************/

                return response()->json([
                    'message' => 'Bill deleted successfully.',
                    'status' => 'success'
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'There was an error.',
                ]);
            };

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

    public function debtorId($debtor_uuid){
        $debtor = Debtor::where("uuid", $debtor_uuid)->first();
        if ($debtor) {
            return $debtor->id;
        } else {
            return null;
        }
    }

    public function create_bill_sales(Request $request) {
        try {
            /***********************************************************/
            DB::beginTransaction(); // TBegining of a laravel transaction
            /***********************************************************/

            $user = User::where("uuid", $request->uuid)->first();

            if (count($user->departments) >= 1) $department = $user->departments->first();
            $payment_mode_id = PaymentMode::where("uuid", $request->payment_mode_uuid)->first()->id;

            if(Bill::where([
                'user_id' => $user->id,
                'bill_status' => 'sold',
                'selling_price' => $request->selling_price,
                'actual_selling_price' => $request->actual_selling_price,
                'payment_mode_id' => $payment_mode_id,
                'department_id' => $department->id,
                'created_at' => Carbon::now()->toDateString()
            ])->first()) return response()->json([
                'status' => 'error',
                'message' => 'Network error. Bill already created, cancel and check in BILLS!',
            ]);

            $bill = Bill::create([
                'uuid' => Str::uuid()->toString(),
                'bill_ref' => $this->generateBillRef(),
                'debtor_id' => $this->debtorId($request->debtor_uuid),
                'status' => 'sold',
                'user_id' => $user->id,
                'selling_price' => $request->selling_price,
                'actual_selling_price' => $request->actual_selling_price,
                'payment_mode_id' => $payment_mode_id,
                'department_id' => $department->id
            ]);

            foreach ($request->products as $product) {
                $prod = Product::where('name', $product['name'])->first();
                if($prod) $prod->update([
                    'quantity' => $prod->quantity - $product['quantity'],
                ]);

                // if ($prod->quantity < 1 || $prod->quantity <= $prod->quantity_left ) {
                //     $this->sendMailNotification("restock_alert", "Product Restock Alert (".$prod->name.")", $prod);
                // }

                Sale::create([
                    'uuid' => Str::uuid()->toString(),
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'user_id' => $user->id,
                    'bill_id' => $bill->id,
                    'status' => "sold"
                ]);
            };

            // $this->sendMailNotification("sales_alert", "Sales Alert", new BillResource($bill));

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'status' => 'success',
                'message' => 'Sale created successfully.',
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

    public function update_bill_sales(Request $request) {
        try {
            /***********************************************************/
            DB::beginTransaction(); // TBegining of a laravel transaction
            /***********************************************************/

            $user = User::where("uuid", $request->user_uuid)->first();
            // $debtor = Debtor::where("uuid", $request->debtor_uuid)->first();
            if (count($user->departments) >= 1) $department = $user->departments->first();
            $payment_mode_id = PaymentMode::where("uuid", $request->payment_mode_uuid)->first()->id;

            $bill = Bill::where('uuid', $request->bill_uuid)->first();
            $bill->update([
                'status' => 'sold',
                'user_id' => $user->id,
                'selling_price' => $request->selling_price,
                'debtor_id' => $this->debtorId($request->debtor_uuid),
                'actual_selling_price' => $request->actual_selling_price,
                'payment_mode_id' => $payment_mode_id,
                'department_id' => $department->id
            ]);

            foreach ($request->products as $product) {

                $prod = Product::where('name', $product['name'])->first();

                $sale = Sale::where('uuid', $product['uuid'])->first();
                if ($sale) {
                    $sale->update([
                        'name' => $product['name'],
                        'quantity' => $product['quantity'],
                        'user_id' => $user->id,
                        'bill_id' => $bill->id,
                        'status' => "sold"
                    ]);
                }
            };

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'status' => 'success',
                'message' => 'Sale updated successfully.',
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

    public function create_bill_sales_pending(Request $request) {
        try {
            /***********************************************************/
            DB::beginTransaction(); // Begining of a laravel transaction
            /***********************************************************/

            $user = User::where("uuid", $request->uuid)->first();
            if (count($user->departments) >= 1) $department = $user->departments->first();
            $payment_mode_id = PaymentMode::where('name', 'Not Paid')->first()->id;

            if(Bill::where([
                'user_id' => $user->id,
                'selling_price' => 0,
                'payment_mode_id' => $payment_mode_id,
                'department_id' => $department->id,
                'created_at' => Carbon::now()->toDateString()
            ])->first()) return response()->json([
                'status' => 'error',
                'message' => 'Network error. Bill already created, cancel and check in BILLS!',
            ]);

            $bill = Bill::create([
                'uuid' => Str::uuid()->toString(),
                'bill_ref' => $this->generateBillRef(),
                'status' => 'pending',
                'user_id' => $user->id,
                'selling_price' => 0,
                'payment_mode_id' => $payment_mode_id,
                'department_id' => $department->id
            ]);

            foreach ($request->products as $product) {
                $prod = Product::where('name', $product['name'])->first();
                if($prod) $prod->update([
                    'quantity' => $prod->quantity - $product['quantity'],
                ]);

                Sale::create([
                    'uuid' => Str::uuid()->toString(),
                    'name' => $product['name'],
                    'quantity' => $product['quantity'],
                    'user_id' => $user->id,
                    'bill_id' => $bill->id,
                    'status' => "pending"
                ]);
            };

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'status' => 'success',
                'message' => 'Sale sent to pending',
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

    public function pay_uncleared_bill(Request $request)
    {
        try {
            /***********************************************************/
            DB::beginTransaction(); // Begining of a laravel transaction
            /***********************************************************/

            $bill = Bill::where('uuid', $request->bill_uuid)->first();
            $user = User::where("uuid", $request->user_uuid)->first();
            $payment_mode = PaymentMode::where("uuid", $request->payment_mode_uuid)->first();

            if ($request->balance === 0) {
                $bill->update([
                    'user_id' => $user->id,
                    'selling_price' => $request->selling_price,
                    'actual_selling_price' => $request->actual_selling_price,
                    'payment_mode_id' => $payment_mode->id
                ]);
            }

            DebtRecord::create([
                'uuid' => Str::uuid()->toString(),
                "debtor_id" => $bill->debtor->id,
                "amount_paid" => $request->actual_selling_price,
                "balance" => $request->balance,
                "bill_id" => $bill->id,
                "user_id" => $user->id,
                "payment_mode_id" => $payment_mode->id,
            ]);

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'message' => 'Debt record paid successfully.',
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

    private function dailyExpense($i) {
        $expense_data = Inventory::whereDate('created_at', Carbon::now()
                        ->subDays($i)->toDateString())
                        ->select(DB::raw('sum(quantity * buying_price) as total'))
                        ->first()->total;

        return $expense_data ? $expense_data : 0;
    }

    public function salesLastSevenDays(){
        $sales = array();

        for ($i=0; $i < 7; $i++) {
            $sales_data = Bill::whereDate('created_at', Carbon::now()->subDays($i)->toDateString())->sum('actual_selling_price');

            array_push($sales,
                array("day" => Carbon::now()->subDays($i)->format('jS / D'),
                      "sales" => $sales_data,
                      "expense" => $this->dailyExpense($i),
                      "difference" => $sales_data - $this->dailyExpense($i)
                )
            );
        };

        return array("data" => $sales);
    }
}
