<?php

use App\Http\Controllers\AdvanceController;
//use App\Http\Controllers\BankController;
//use App\Http\Controllers\ClientController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerGroupController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CollectionController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommissionController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\SubCategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\VariationController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SalePurchaseReturnController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(
    [
        'middleware' => 'api',
        'namespace'  => 'App\Http\Controllers',
        'prefix'     => 'auth',
    ],
    function ($router) {
        Route::post('login', 'ApiAuthController@login');
        Route::post('signup', 'ApiAuthController@signup');
        Route::post('logout', 'ApiAuthController@logout');
        Route::get('userinfo', 'ApiAuthController@me');
        Route::get('refresh', 'ApiAuthController@refresh');
    }
);
Route::group(
    [
        'middleware' => 'api',
        'namespace'  => 'App\Http\Controllers',
    ],
    function ($router) {
        Route::post('business-setting-and-location', "BusinessController@saveSettingsAndLocation");
        Route::get('get-currency', "BusinessController@getAllCurrency");
    }
);

Route::resource('product', ProductsController::class);
Route::post('product/search', [ProductsController::class, 'productSearch']);
Route::get('customer-search', [CustomerController::class, 'customerSearch']);
//Route::get('client-search', [ClientController::class, 'clientSearch']);
Route::post('contact-search', [ContactController::class, 'contactSearch']);

Route::resource('category', CategoryController::class);
Route::get('get-subcategories/{category}', [CategoryController::class, 'getSubcategories']);
Route::get('get-subunits/{unit}', [UnitController::class, 'getSubUnits']);


Route::resource('advance', AdvanceController::class);
//Route::resource('client', ClientController::class);
Route::resource('customer', CustomerController::class);
Route::resource('customer-group', CustomerGroupController::class);
Route::resource('contact', ContactController::class);
Route::resource('payment', PaymentController::class);
Route::resource('collection', CollectionController::class);
Route::resource('commission', CommissionController::class);

Route::resource('expense', ExpenseController::class);
Route::resource('expense-category', ExpenseCategoryController::class);

Route::get('customer-due', [CollectionController::class, 'customerDueMoney']);
Route::get('dashboard-data', [UserController::class, 'dashboardData']);


Route::post('order', [OrderController::class, 'store']);
Route::get('pos-prodcuts', [OrderController::class, 'posProducts']);
//Route::get('banks', [BankController::class, 'index']);

Route::group(
    [
        'middleware' => 'api',
        'namespace'  => 'App\Http\Controllers',
        'prefix'     => 'reports',
    ],
    function ($router) {
        Route::get('expense', 'ReportController@expenseReport');
        Route::get('collection', 'ReportController@collectionReport');
        Route::get('payment', 'ReportController@paymentReport');
        Route::get('sales', 'ReportController@salesReport');
    }
);

Route::patch('user/{id}', [UserController::class, 'update']);
Route::post('update-settings', [SettingController::class, 'updateSettings']);
Route::get('settings', [SettingController::class, 'index']);

Route::resource('paymentMethod', PaymentMethodController::class);

Route::resource('brand', BrandController::class);

Route::resource('unit', UnitController::class);

Route::resource('product-variation', VariationController::class);

Route::resource('purchase', PurchaseController::class);

Route::get('purchase-contacts', [PurchaseController::class, 'getContacts']);
Route::get('purchase-business-locations', [PurchaseController::class, 'getBusinessLocations']);
Route::get('purchase-products/{name}',[PurchaseController::class, 'getProducts']);

Route::patch('addpayment/{id}', [PurchaseController::class, 'addPayment']);

Route::resource('sale', SaleController::class);

Route::get('purchase-return-list', [SalePurchaseReturnController::class, 'index']);

Route::get('sale-return-list', [SalePurchaseReturnController::class, 'index2']);
Route::patch('purchase-return/{id}', [SalePurchaseReturnController::class, 'returnPurchase']);
Route::resource('sale-return', SalePurchaseReturnController::class);
Route::patch('sale-return/{id}', [SalePurchaseReturnController::class, 'returnSale']);

Route::group(['middleware' => ['auth']], function () {
});

Route::resource('role', RoleController::class);
Route::post('role-has-permission/{id}', [RoleController::class, 'permissionList']);
Route::post('user-role/{id}', [RoleController::class, 'userRole']);
Route::resource('permission', PermissionController::class);

Route::resource('vehicle', \App\Http\Controllers\VehicleController::class);
Route::resource('service', \App\Http\Controllers\ServiceController::class);
