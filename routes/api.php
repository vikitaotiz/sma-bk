<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\PaymentModeController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\DebtRecordController;
use App\Http\Controllers\DeletedRecordsController;

Route::group(['prefix' => 'v1'], function(){
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('finished_products', [ProductController::class, 'finished_products']);

    Route::group(['middleware' => ['auth:sanctum']], function(){
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);

        Route::resource('users', UserController::class);
        Route::post('update_user', [UserController::class, 'update']);
        Route::get('logged_in_users', [UserController::class, 'logged_in_users']);

        Route::resource('roles', RoleController::class);
        Route::post('delete_role', [RoleController::class, 'destroy']);
        Route::post('update_role', [RoleController::class, 'update']);

        Route::resource('departments', DepartmentController::class);
        Route::post('delete_department', [DepartmentController::class, 'destroy']);
        Route::post('update_department', [DepartmentController::class, 'update']);

        Route::resource('categories', CategoryController::class);
        Route::post('delete_category', [CategoryController::class, 'destroy']);
        Route::post('update_category', [CategoryController::class, 'update']);

        Route::resource('products', ProductController::class);
        Route::post('delete_product', [ProductController::class, 'destroy']);
        Route::post('update_product', [ProductController::class, 'update']);

        Route::resource('sales', SaleController::class);
        Route::post('delete_sale', [SaleController::class, 'destroy']);
        Route::post('update_sale', [SaleController::class, 'update']);
        Route::get('sales_last_seven_days', [SaleController::class, 'salesLastSevenDays']);
        Route::post('create_bill_sales', [SaleController::class, 'create_bill_sales']);
        Route::post('create_bill_sales_pending', [SaleController::class, 'create_bill_sales_pending']);
        Route::post('add_product_to_bill', [SaleController::class, 'add_product_to_bill']);
        Route::post('remove_product_from_bill', [SaleController::class, 'remove_product_from_bill']);
        Route::post('remove_bill', [SaleController::class, 'remove_bill']);
        Route::post('update_bill_sales', [SaleController::class, 'update_bill_sales']);
        Route::post('pay_uncleared_bill', [SaleController::class, 'pay_uncleared_bill']);
        Route::post('get_older_sales', [SaleController::class, 'get_older_sales']);

        Route::resource('inventories', InventoryController::class);
        Route::post('delete_inventory', [InventoryController::class, 'destroy']);
        Route::post('update_inventory', [InventoryController::class, 'update']);
        Route::post('get_older_inventories', [InventoryController::class, 'get_older_inventories']);

        Route::resource('payment_modes', PaymentModeController::class);
        Route::post('delete_payment_mode', [PaymentModeController::class, 'destroy']);
        Route::post('update_payment_mode', [PaymentModeController::class, 'update']);

        Route::resource('bills', BillController::class);
        Route::post('delete_bill', [BillController::class, 'destroy']);
        Route::post('update_bill', [BillController::class, 'update']);
        Route::get('uncleared_bills', [BillController::class, 'uncleared_bills']);
        Route::post('get_older_bills', [BillController::class, 'get_older_bills']);

        Route::resource('measurements', MeasurementController::class);
        Route::post('delete_measurement', [MeasurementController::class, 'destroy']);
        Route::post('update_measurement', [MeasurementController::class, 'update']);

        Route::resource('accounts', AccountController::class);
        Route::get('sales_stats', [AccountController::class, 'sales_stats']);
        Route::get('all_sales_stats', [AccountController::class, 'all_sales_stats']);
        Route::get('today_product_sales', [AccountController::class, 'today_product_sales']);
        Route::get('sales_expense', [AccountController::class, 'sales_expense']);
        Route::post('update_accounts', [AccountController::class, 'update_accounts']);

        Route::resource('debtors', DebtorController::class);
        Route::resource('debt_records', DebtRecordController::class);

        Route::get('deleted_records', [DeletedRecordsController::class, 'records']);
    });
});
