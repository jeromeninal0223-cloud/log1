<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Test route
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!', 'timestamp' => now()]);
});

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Vendor API routes (public for demo purposes)
Route::prefix('vendors')->group(function () {
    Route::get('/', [VendorController::class, 'index']);
    Route::get('/{id}', [VendorController::class, 'show']);
    Route::post('/{id}/approve', [VendorController::class, 'approve']);
    Route::post('/{id}/suspend', [VendorController::class, 'suspend']);
    Route::post('/{id}/activate', [VendorController::class, 'activate']);
    Route::post('/approve-all', [VendorController::class, 'approveAll']);
});

// Inventory and Storage API routes (public for demo)
Route::prefix('inventory')->group(function () {
    Route::post('/store-receipt', [App\Http\Controllers\InventoryReceiptController::class, 'store']);
    Route::post('/add-item', [App\Http\Controllers\InventoryReceiptController::class, 'addItem']);
    Route::get('/dashboard-stats', [App\Http\Controllers\InventoryReceiptController::class, 'getDashboardStats']);
    Route::get('/purchase-orders-by-supplier', [App\Http\Controllers\InventoryReceiptController::class, 'getPurchaseOrdersBySupplier']);
    Route::get('/purchase-order-items', [App\Http\Controllers\InventoryReceiptController::class, 'getPurchaseOrderItems']);
});

Route::prefix('storage')->group(function () {
    Route::post('/assign-location', [App\Http\Controllers\StorageOrganizationController::class, 'assignLocation']);
    Route::post('/relocate-item', [App\Http\Controllers\StorageOrganizationController::class, 'relocateItem']);
    Route::post('/bulk-assign', [App\Http\Controllers\StorageOrganizationController::class, 'bulkAssignLocation']);
    Route::get('/items-by-zone', [App\Http\Controllers\StorageOrganizationController::class, 'getItemsByZone']);
    Route::get('/locations', [App\Http\Controllers\StorageOrganizationController::class, 'getStorageLocations']);
});

// Contract API routes (public for demo)
Route::prefix('contracts')->group(function () {
    Route::get('/{id}/view', [App\Http\Controllers\PSMContractController::class, 'view']);
    Route::get('/{id}/download', [App\Http\Controllers\PSMContractController::class, 'download']);
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // You can add more protected routes here
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
