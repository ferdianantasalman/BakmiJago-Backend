<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderedItemController;
use App\Http\Controllers\ReportController;
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
    Route::get('orders', [OrderedItemController::class, 'orderedItems']);
    Route::get('orders_invoice/{id}', [OrderedItemController::class, 'orderedItemsByInvoice']);
    Route::get('orders/{id}', [OrderedItemController::class, 'cekOrder']);
    Route::post('orders', [OrderedItemController::class, 'createOrder']);

    // Invoice
    Route::get('invoices', [InvoiceController::class, 'invoices']);
    Route::get('invoices/{time}', [InvoiceController::class, 'invoicesByTime']);

    // Report
    Route::get('reports', [ReportController::class, 'index']);
    Route::get('reports_time/{time}', [ReportController::class, 'reportsByTime']);
    Route::post('reports', [ReportController::class, 'store']);
    Route::get('reports/{id}', [ReportController::class, 'show']);
    Route::post('reports/{id}', [ReportController::class, 'update']);
    Route::delete('reports/{id}', [ReportController::class, 'destroy']);
    Route::get('reports_income/{time}', [ReportController::class, 'incomeReportByTime']);
    Route::get('reports_outcome/{time}', [ReportController::class, 'outcomeReportByTime']);
    Route::get('reports_revenue/{time}', [ReportController::class, 'revenueReportByTime']);
});
