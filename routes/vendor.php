<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorController;

/*
|--------------------------------------------------------------------------
| Vendor Portal Routes
|--------------------------------------------------------------------------
|
| Here is where you can register vendor portal routes for your application.
| These routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Guest routes (no authentication required)
Route::middleware('guest')->group(function () {
    Route::get('/login', [VendorController::class, 'showLoginForm'])->name('vendor.login');
    Route::post('/login', [VendorController::class, 'login']);
    Route::get('/register', [VendorController::class, 'showRegisterForm'])->name('vendor.register');
    Route::post('/register', [VendorController::class, 'register']);
});

// Authenticated vendor routes
Route::middleware('auth:vendor')->group(function () {
    Route::get('/dashboard', [VendorController::class, 'dashboard'])->name('vendor.dashboard');
    Route::post('/logout', [VendorController::class, 'logout'])->name('vendor.logout');
    
    // Bidding routes
    Route::get('/bids', [VendorController::class, 'showBids'])->name('vendor.bids');
    Route::get('/opportunities/{id}/bid', [VendorController::class, 'showBidForm'])->name('vendor.bid.form');
    Route::post('/opportunities/{id}/bid', [VendorController::class, 'submitBid'])->name('vendor.bid.submit');
    Route::patch('/bids/{id}/withdraw', [VendorController::class, 'withdrawBid'])->name('vendor.bid.withdraw');
    
    // Orders routes
    Route::get('/orders', [VendorController::class, 'showOrders'])->name('vendor.orders');
    Route::get('/orders/{purchaseOrder}/details', [VendorController::class, 'getVendorOrderDetails'])->name('vendor.orders.details');
    Route::post('/orders/{purchaseOrder}/status', [VendorController::class, 'updateVendorDeliveryStatus'])->name('vendor.orders.status');
    
    // Invoices routes
    Route::get('/invoices', [VendorController::class, 'showInvoices'])->name('vendor.invoices');
    
    // Profile routes
    Route::get('/profile', [VendorController::class, 'showProfile'])->name('vendor.profile');
    Route::put('/profile', [VendorController::class, 'updateProfile'])->name('vendor.profile.update');
});

// Public routes
Route::get('/bidding', [VendorController::class, 'showBiddingLanding'])->name('vendor.bidding.landing');
