<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Account;
use App\Models\User;
use App\Models\Sale;
use App\Models\Department;
use App\Models\Role;
use App\Models\Inventory;
use App\Models\PaymentMode;
use App\Models\Product;
use App\Http\Resources\Accounts\AccountResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;


class AccountController extends Controller
{
    private function paymentModeId($payment_mode_name) {
        return PaymentMode::where('name', $payment_mode_name)->first()->id;
    }

    private function roundUpNumberTo2dp($number) { return number_format((float)$number, 2, '.', ''); }

    private function calculateExpense($i) {
        $expense_records = [];

        if($i === null){
            $expense_records = Inventory::all()->map->only('quantity', 'buying_price')->toArray();
        } else {
            $expense_records = Inventory::whereDate('created_at', Carbon::now()
                    ->subDays($i)->toDateString())
                    ->get()
                    ->map->only('quantity', 'buying_price')
                    ->toArray();
        }

        $expense = 0;

        foreach ($expense_records as $val) {
            $expense += ($val['quantity'] * $val['buying_price']);
        };

        return $this->roundUpNumberTo2dp($expense);
    }

    private function productSales($day) {
        return Sale::groupBy('name')
            ->selectRaw('sum(quantity) as quantity, name')
            ->whereDate( 'created_at', Carbon::now()->subDays($day)->toDateString())
            ->get();
    }

    public function sales_stats() {
        $current_total = Bill::where('payment_mode_id', '<>', $this->paymentModeId("Mpesa & Cash"))->whereDate( 'created_at', Carbon::now()->toDateString())->get()
                        ->sum("actual_selling_price");

        $current_cash = Bill::where('payment_mode_id', $this->paymentModeId("Cash"))->whereDate( 'created_at', Carbon::now()->toDateString())
                        ->get()->sum("actual_selling_price");

        $current_mpesa = Bill::where('payment_mode_id', $this->paymentModeId("Mpesa"))->whereDate( 'created_at', Carbon::now()->toDateString())
                        ->get()->sum("actual_selling_price");

        $current_mpesa_cash = Bill::where('payment_mode_id', $this->paymentModeId("Mpesa & Cash"))->whereDate( 'created_at', Carbon::now()->toDateString())
                        ->get()->sum("actual_selling_price");

        $current_card = Bill::where('payment_mode_id', $this->paymentModeId("Card"))->whereDate( 'created_at', Carbon::now()->toDateString())
                        ->get()->sum("actual_selling_price");

        $current_debt = Bill::where('payment_mode_id', $this->paymentModeId("Debt"))->whereDate( 'created_at', Carbon::now()->toDateString())
                        ->get()->sum("actual_selling_price");

        $current_pending = Bill::where('payment_mode_id', $this->paymentModeId("Not Paid"))->whereDate( 'created_at', Carbon::now()->toDateString())
                        ->count();

        return array(
            "total_daily_sales" => $this->roundUpNumberTo2dp($current_total),
            "total_daily_cash_sales" => $this->roundUpNumberTo2dp($current_cash),
            "total_daily_mpesa_sales" => $this->roundUpNumberTo2dp($current_mpesa),
            "total_daily_mpesa_cash_sales" => $this->roundUpNumberTo2dp($current_mpesa_cash),
            "total_daily_card_sales" => $this->roundUpNumberTo2dp($current_card),
            "total_daily_debt_sales" => $this->roundUpNumberTo2dp($current_debt),
            "total_daily_pending_sales" => $current_pending,
            "total_daily_expense" => $this->roundUpNumberTo2dp($this->calculateExpense(0))
        );
    }

    public function all_sales_stats() {
        $all_total = Bill::where('payment_mode_id', '<>', $this->paymentModeId("Mpesa & Cash"))->get()->sum("actual_selling_price");
        $all_cash = Bill::where('payment_mode_id', $this->paymentModeId("Cash"))->get()->sum("actual_selling_price");
        $all_mpesa = Bill::where('payment_mode_id', $this->paymentModeId("Cash"))->get()->sum("actual_selling_price");
        $all_mpesa_cash = Bill::where('payment_mode_id', $this->paymentModeId("Mpesa & Cash"))->get()->sum("actual_selling_price");
        $all_debt = Bill::where('payment_mode_id', $this->paymentModeId("Debt"))->get()->sum("actual_selling_price");
        $all_card = Bill::where('payment_mode_id', $this->paymentModeId("Card"))->get()->sum("actual_selling_price");
        $all_pending = Bill::where('payment_mode_id', $this->paymentModeId("Not Paid"))->count();

        return array(
            "total_sales" => $this->roundUpNumberTo2dp($all_total),
            "total_cash_sales" => $this->roundUpNumberTo2dp($all_cash),
            "total_mpesa_sales" => $this->roundUpNumberTo2dp($all_mpesa),
            "total_mpesa_cash_sales" => $this->roundUpNumberTo2dp($all_mpesa_cash),
            "total_card_sales" => $this->roundUpNumberTo2dp($all_card),
            "total_debt_sales" => $this->roundUpNumberTo2dp($all_debt),
            "total_pending_sales" => $all_pending,
            "total_expense" => $this->roundUpNumberTo2dp($this->calculateExpense(null))
        );
    }

    public function index() {

        $user = auth()->user();

        if(in_array("Administration", $user->departments->pluck("name")->toArray())){
            return AccountResource::collection(Account::orderBy('created_at', 'desc')->get());

        } else {
            return AccountResource::collection(Account::orderBy('created_at', 'desc')
                ->where('user_id', $user->id)
                ->get());
        };
    }

    public function store(Request $request)
    {
        try {
            /***********************************************************/
            DB::beginTransaction(); // Begining of a laravel transaction
            /***********************************************************/

            $user = User::where("uuid", $request->user_uuid)->first();
            $account = Account::where('user_id', $user->id)
                ->whereDate( 'created_at', Carbon::now()->toDateString())
                ->first();

            if($account) return response()->json([
                'status' => 'error',
                'message' => 'Account already closed. You can edit an existing one'
            ]);

            if(in_array("Administration", $request->user_departments)){
                Account::create([
                    'uuid' => Str::uuid()->toString(),
                    "production_cost" => $request->total_expense,
                    "expected_cash" => $request->expected_cash,
                    "expected_card" => $request->expected_card,
                    "expected_debt" => $request->expected_debt,
                    "expected_mpesa" => $request->expected_mpesa,
                    "expected_mpesa_cash" => $request->expected_mpesa_cash,
                    "actual_cash" => $request->actual_cash,
                    "actual_mpesa" => $request->actual_mpesa,
                    "department_id" => 1,
                    "user_id" => $user->id
                ]);

                /******************************************/
                DB::commit(); // End of database transactions (Success)
                /******************************************/

                return response()->json([
                    'status' => 'success',
                    'message' => 'Account created successfully.',
                ]);
            } else {

                if(current($request->user_departments)){
                    $department_id = Department::where('name', current($request->user_departments))->first()->id;

                    Account::create([
                        'uuid' => Str::uuid()->toString(),
                        "production_cost" => $request->total_expense,
                        "expected_cash" => $request->expected_cash,
                        "expected_card" => $request->expected_card,
                        "expected_debt" => $request->expected_debt,
                        "expected_mpesa" => $request->expected_mpesa,
                        "expected_mpesa_cash" => $request->expected_mpesa_cash,
                        "actual_cash" => $request->actual_cash,
                        "actual_mpesa" => $request->actual_mpesa,
                        "department_id" => $department_id,
                        "user_id" => $user->id
                    ]);

                    /******************************************/
                    DB::commit(); // End of database transactions (Success)
                    /******************************************/

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Account created successfully.',
                    ]);
                } else {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You need to be assigned to a department.',
                    ]);
                }
            }

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

    public function today_product_sales() {
        return $this->productSales(0);
    }

    public function sales_expense(){
        $sales = array();

        for ($i=0; $i < 7; $i++) {
            $sales = Bill::whereDate('created_at', Carbon::now()->subDays($i)->toDateString())
                        ->sum('actual_selling_price');

            array_push($sales,
                array(
                    "day" => Carbon::now()->subDays($i)->format('l'),
                    "sales" => $sales,
                    "expense" => $this->calculateExpense($i),
                    "net_profit" => $sales - $this->calculateExpense($i)
                )
            );
        };

        return array("data" =>$sales);
    }

    public function update_accounts(Request $request) {
        try {
            /***********************************************************/
            DB::beginTransaction(); // Begining of a laravel transaction
            /***********************************************************/

            $account = Account::where('uuid', $request->account_uuid)->first();

            $account->update([
                "production_cost" => $request->total_expense,
                "expected_cash" => $request->expected_cash,
                "expected_card" => $request->expected_card,
                "expected_debt" => $request->expected_debt,
                "expected_mpesa" => $request->expected_mpesa,
                "expected_mpesa_cash" => $request->expected_mpesa_cash,
                "actual_cash" => $request->actual_cash,
                "actual_mpesa" => $request->actual_mpesa,
            ]);

            /******************************************/
            DB::commit(); // End of database transactions (Success)
            /******************************************/

            return response()->json([
                'status' => 'success',
                'message' => 'Account updated successfully',
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
