<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
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

    Route::get('invoices', [InvoiceController::class, 'invoices']);
    Route::get('invoice/{id}', [InvoiceController::class, 'cekInvoice']);

    // Route::post('invoice/{id}', [InvoiceController::class, 'updateStatusPembayaran']);
    // Route::get('invoices/{status}', [InvoiceController::class, 'invoiceByStatus']);

    Route::get('orders', [InvoiceController::class, 'orderedItems']);
    // Route::get('orders/{status}', [InvoiceController::class, 'orderByStatus']);

    Route::post('order', [InvoiceController::class, 'createOrder']);
    Route::get('order/{id}', [InvoiceController::class, 'cekOrder']);
    Route::post('order/{id}', [InvoiceController::class, 'updateStatusOrder']);
});

// Route::apiResource('/products', ProductController::class);
