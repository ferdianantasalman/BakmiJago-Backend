<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderedItemController;
use App\Http\Controllers\RoleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('me/{id}', [AuthController::class, 'edituser']);

    // Role
    Route::get('roles', [RoleController::class, 'index'])->middleware('role-2:admin,owner');

    // Product
    Route::get('products', [ProductController::class, 'index']);
    Route::post('products', [ProductController::class, 'store']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'destroy']);

    // Order
    // Route::get('orders', [OrderController::class, 'index']);
    // Route::post('orders', [OrderController::class, 'store']);
    // Route::get('orders/{id}', [OrderController::class, 'show']);
    // Route::post('orders/{id}', [OrderController::class, 'update']);
    // Route::delete('orders/{id}', [OrderController::class, 'destroy']);

    // Invoice
    Route::get('invoices', [InvoiceController::class, 'invoices']);
    Route::get('invoices/{time}', [InvoiceController::class, 'invoicesByTime']);
    Route::get('invoices/{id}', [InvoiceController::class, 'cekInvoice']);

    // Route::post('invoice/{id}', [InvoiceController::class, 'updateStatusPembayaran']);
    // Route::get('invoices/{status}', [InvoiceController::class, 'invoiceByStatus']);

    // Order
    Route::get('orders', [OrderedItemController::class, 'orderedItems']);
    Route::get('orders_invoice/{id}', [OrderedItemController::class, 'orderedItemsByInvoice']);
    Route::get('orders/{id}', [OrderedItemController::class, 'cekOrder']);

    Route::post('orders', [OrderedItemController::class, 'createOrder']);


    // Route::post('order/{id}', [OrderedItemControlleroller::class, 'updateStatusOrder']);
    // Route::get('orders/{status}', [InvoiceController::class, 'orderByStatus']);

});

// Route::apiResource('/products', ProductController::class);
