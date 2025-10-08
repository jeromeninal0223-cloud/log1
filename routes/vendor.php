<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorDamageReportController;

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

// All vendor routes should be prefixed with /vendor
Route::prefix('vendor')->group(function () {
    // Guest routes (no authentication required)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [VendorController::class, 'showLoginForm'])->name('vendor.login');
        Route::post('/login', [VendorController::class, 'login']);
        Route::get('/register', [VendorController::class, 'showRegisterForm'])->name('vendor.register');
        Route::post('/register', [VendorController::class, 'register'])->name('vendor.register.submit');
        
        // Password reset routes
        Route::post('/forgot-password', [VendorController::class, 'sendPasswordResetLink'])->name('vendor.forgot-password');
        Route::get('/reset-password/{token}', [VendorController::class, 'showResetForm'])->name('vendor.reset-password');
        Route::post('/reset-password', [VendorController::class, 'resetPassword'])->name('vendor.reset-password.submit');
    });

    // 2FA Challenge route (requires auth but not 2FA verification)
    Route::middleware(['auth:vendor'])->group(function () {
        Route::get('/2fa/challenge', function() {
            return view('VendorPortal.2fa-challenge');
        })->name('vendor.2fa.challenge');
        
        Route::post('/2fa/verify', [VendorController::class, 'verify2FA'])->name('vendor.2fa.verify');
    });

    // Authenticated vendor routes
    Route::middleware(['auth:vendor', 'check.vendor.status', 'require.2fa'])->group(function () {
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
        
        // Contracts routes
        Route::get('/contracts', [VendorController::class, 'showContracts'])->name('vendor.contracts');
        
        // Invoices routes
        Route::get('/invoices', [VendorController::class, 'showInvoices'])->name('vendor.invoices');
        
        // Profile routes
        Route::get('/profile', [VendorController::class, 'showProfile'])->name('vendor.profile');
        Route::put('/profile', [VendorController::class, 'updateProfile'])->name('vendor.profile.update');
        Route::put('/profile/password', [VendorController::class, 'updatePassword'])->name('vendor.password.update');
        
        // Damage Reports routes
        Route::get('/damage-reports', [VendorDamageReportController::class, 'index'])->name('vendor.damage.reports');
        Route::get('/damage-reports/{id}', [VendorDamageReportController::class, 'show'])->name('vendor.damage.reports.show');
        Route::post('/damage-reports/{id}/acknowledge', [VendorDamageReportController::class, 'acknowledge'])->name('vendor.damage.reports.acknowledge');
        Route::post('/damage-reports/{id}/replacement', [VendorDamageReportController::class, 'sendReplacement'])->name('vendor.damage.reports.replacement');
        Route::get('/damage-reports/export', [VendorDamageReportController::class, 'export'])->name('vendor.damage.reports.export');
        
        // Two-Factor Authentication routes
        Route::post('/2fa/generate', [VendorController::class, 'generate2FASecret'])->name('vendor.2fa.generate');
        Route::post('/2fa/enable', [VendorController::class, 'enable2FA'])->name('vendor.2fa.enable');
        Route::post('/2fa/disable', [VendorController::class, 'disable2FA'])->name('vendor.2fa.disable');
        Route::post('/2fa/verify', [VendorController::class, 'verify2FA'])->name('vendor.2fa.verify');
        
        // API routes for vendor portal
        Route::get('/api/bids/{id}', [VendorController::class, 'getBidDetails'])->name('vendor.api.bids.show');
        Route::get('/api/invoices/{invoice}', [VendorController::class, 'getVendorInvoiceDetails'])->name('vendor.api.invoices.show');
    });

    // Public routes
    Route::get('/bidding', [VendorController::class, 'showBiddingLanding'])->name('vendor.bidding.landing');
});
