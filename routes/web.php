<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    });
});

// SWS Routes - Accessible by procurement officers and logistics staff
Route::middleware(['auth', 'role:procurement_officer,logistics_staff,admin'])->group(function () {
    Route::get('/smartwarehousing', function () {
        return view('SWS.smartwarehousing');
    });

    Route::get('/inventory-receipt', [App\Http\Controllers\InventoryReceiptController::class, 'index'])->name('inventory.receipt');

    Route::get('/storage-organization', [App\Http\Controllers\StorageOrganizationController::class, 'index'])->name('storage.organization');

    Route::get('/picking-dispatch', [App\Http\Controllers\PickingDispatchController::class, 'index'])->name('picking.dispatch');

    Route::get('/stock-replenishment', [App\Http\Controllers\StockReplenishmentController::class, 'index'])->name('stock.replenishment');
});

// PSM (Procurement & Sourcing Management) Routes - Accessible by procurement officers only
Route::middleware(['auth', 'role:procurement_officer,admin'])->group(function () {
    Route::get('/psm/request', [App\Http\Controllers\PurchaseRequestController::class, 'index'])->name('psm.request');
    Route::get('/psm/request/{id}/bid-form', [App\Http\Controllers\PurchaseRequestController::class, 'showBidForm'])->name('psm.request.bid-form');
    Route::post('/psm/request/submit-bid', [App\Http\Controllers\PurchaseRequestController::class, 'submitBidForm'])->name('psm.request.submit-bid');
    Route::post('/psm/request/approve', [App\Http\Controllers\PurchaseRequestController::class, 'approve'])->name('psm.request.approve');
    Route::get('/psm/request/{id}/bid-data', [App\Http\Controllers\PurchaseRequestController::class, 'getBidFormData'])->name('psm.request.bid-data');

    Route::get('/psm/vendor', [App\Http\Controllers\PSMVendorController::class, 'index'])->name('psm.vendor');

    Route::get('/psm/bidding', [App\Http\Controllers\PSMBiddingController::class, 'index'])->name('psm.bidding');
    Route::post('/psm/opportunities', [App\Http\Controllers\PSMBiddingController::class, 'storeOpportunity'])->name('psm.opportunities.store');
    Route::post('/psm/opportunities/{id}/evaluate', [App\Http\Controllers\PSMBiddingController::class, 'evaluateOpportunity'])->name('psm.opportunities.evaluate');

    Route::get('/psm/contract', [App\Http\Controllers\PSMContractController::class, 'index']);

    Route::get('/psm/order', [App\Http\Controllers\PurchaseOrderController::class, 'index'])->name('psm.order.index');
    Route::post('/psm/order', [App\Http\Controllers\PurchaseOrderController::class, 'store'])->name('psm.order.store');
    Route::get('/psm/order/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'show'])->name('psm.order.show');
    Route::get('/psm/order/{purchaseOrder}/edit', [App\Http\Controllers\PurchaseOrderController::class, 'edit'])->name('psm.order.edit');
    Route::put('/psm/order/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'update'])->name('psm.order.update');
    Route::delete('/psm/order/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'destroy'])->name('psm.order.destroy');
    
    // Purchase Order Actions
    Route::post('/psm/order/{purchaseOrder}/submit-approval', [App\Http\Controllers\PurchaseOrderController::class, 'submitForApproval'])->name('psm.order.submit-approval');
    Route::post('/psm/order/{purchaseOrder}/approve', [App\Http\Controllers\PurchaseOrderController::class, 'approve'])->name('psm.order.approve');
    Route::post('/psm/order/{purchaseOrder}/issue', [App\Http\Controllers\PurchaseOrderController::class, 'issue'])->name('psm.order.issue');
    
    // API Routes
    Route::get('/api/psm/contracts', [App\Http\Controllers\PurchaseOrderController::class, 'getContracts'])->name('psm.api.contracts');

    Route::get('/psm/delivery', [App\Http\Controllers\DeliveryController::class, 'index'])->name('psm.delivery');
    Route::post('/psm/delivery/{purchaseOrder}/status', [App\Http\Controllers\DeliveryController::class, 'updateDeliveryStatus'])->name('psm.delivery.status');
    Route::get('/psm/delivery/{purchaseOrder}/details', [App\Http\Controllers\DeliveryController::class, 'getDeliveryDetails'])->name('psm.delivery.details');

    // PSM Invoice Routes
    Route::get('/psm/invoice', [App\Http\Controllers\PSMInvoiceController::class, 'index'])->name('psm.invoice.index');
    Route::get('/psm/invoice/export', [App\Http\Controllers\PSMInvoiceController::class, 'export'])->name('psm.invoice.export');
    Route::get('/psm/invoice/report', [App\Http\Controllers\PSMInvoiceController::class, 'report'])->name('psm.invoice.report');
    Route::get('/psm/invoice/{invoice}', [App\Http\Controllers\PSMInvoiceController::class, 'show'])->name('psm.invoice.show');
    Route::get('/psm/invoice/{invoice}/download', [App\Http\Controllers\PSMInvoiceController::class, 'download'])->name('psm.invoice.download');
    Route::get('/psm/invoice/{invoice}/record-payment', [App\Http\Controllers\PSMInvoiceController::class, 'recordPayment'])->name('psm.invoice.recordPayment');
});

// Project Logistics Tracker (PLT) Routes - Accessible by logistics staff, procurement officers and admin
Route::middleware(['auth', 'role:logistics_staff,procurement_officer,admin'])->group(function () {
    Route::get('/plt/toursetup', function () {
        return view('PLT.Toursetup');
    });

    Route::get('/plt/execution', function () {
        return view('PLT.execution');
    });

    Route::get('/plt/closure', function () {
        return view('PLT.closure');
    });
});

// Asset Life Cycle & Maintenance Routes - Accessible by logistics staff and admin
Route::middleware(['auth', 'role:logistics_staff,admin'])->group(function () {
    Route::get('/alms/assetregistration', [App\Http\Controllers\ALMS\AssetRegistrationController::class, 'index']);
    Route::post('/alms/assetregistration', [App\Http\Controllers\ALMS\AssetRegistrationController::class, 'store']);
    Route::get('/alms/assets/{asset}', [App\Http\Controllers\ALMS\AssetRegistrationController::class, 'show'])->name('alms.assets.show');
    Route::put('/alms/assets/{asset}', [App\Http\Controllers\ALMS\AssetRegistrationController::class, 'update'])->name('alms.assets.update');

    Route::get('/alms/maintenance', [App\Http\Controllers\ALMS\MaintenanceController::class, 'index']);
    Route::post('/alms/maintenance/schedules', [App\Http\Controllers\ALMS\MaintenanceController::class, 'store'])->name('alms.maintenance.store');
    Route::get('/alms/maintenance/schedules/{schedule}', [App\Http\Controllers\ALMS\MaintenanceController::class, 'show'])->name('alms.maintenance.show');
    Route::put('/alms/maintenance/schedules/{schedule}', [App\Http\Controllers\ALMS\MaintenanceController::class, 'update'])->name('alms.maintenance.update');
    Route::get('/alms/disposalretirement', function () {       return view('ALMS.disposalretirement');
    });
});

// Document Tracking Routes - Accessible by logistics staff and admin
Route::middleware(['auth', 'role:logistics_staff,admin'])->group(function () {
    Route::get('/dtrs/document', [App\Http\Controllers\DTRSController::class, 'documents']);
    Route::get('/dtrs/documents/{documentId}/view', [App\Http\Controllers\DTRSController::class, 'viewDocument']);
    Route::get('/dtrs/documents/{documentId}/download', [App\Http\Controllers\DTRSController::class, 'downloadDocument']);
    Route::get('/dtrs/documents/{documentId}/metadata', [App\Http\Controllers\DTRSController::class, 'documentMetadata']);

    Route::get('/dtrs/audits', function () {
        return view('DTRS.audits');
    });
});

// Vendor Portal Routes
Route::prefix('vendor')->group(function () {
    Route::get('/login', [App\Http\Controllers\VendorPortal\RegisterController::class, 'showLoginForm'])->name('vendor.login');
    Route::post('/login', [App\Http\Controllers\VendorController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\VendorPortal\RegisterController::class, 'showRegistrationForm'])->name('vendor.register');
    Route::post('/register', [App\Http\Controllers\VendorPortal\RegisterController::class, 'register'])->name('vendor.register.submit');
    
    Route::middleware(['auth:vendor', 'check.vendor.status'])->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\VendorController::class, 'dashboard'])->name('vendor.dashboard');
        Route::post('/logout', [App\Http\Controllers\VendorController::class, 'logout'])->name('vendor.logout');
        Route::get('/bids', [App\Http\Controllers\VendorController::class, 'showBids'])->name('vendor.bids');
        Route::get('/orders', [App\Http\Controllers\VendorController::class, 'showOrders'])->name('vendor.orders');
        Route::get('/orders/{purchaseOrder}/details', [App\Http\Controllers\VendorController::class, 'getVendorOrderDetails'])->name('vendor.orders.details');
        Route::post('/orders/{purchaseOrder}/status', [App\Http\Controllers\VendorController::class, 'updateVendorDeliveryStatus'])->name('vendor.orders.status');
        Route::get('/invoices', [App\Http\Controllers\VendorController::class, 'showInvoices'])->name('vendor.invoices');
        Route::get('/profile', [App\Http\Controllers\VendorController::class, 'showProfile'])->name('vendor.profile');
        Route::put('/profile', [App\Http\Controllers\VendorController::class, 'updateProfile'])->name('vendor.profile.update');
        Route::get('/opportunities/{id}/bid', [App\Http\Controllers\VendorController::class, 'showBidForm'])->name('vendor.bid.form');
        Route::post('/opportunities/{id}/bid', [App\Http\Controllers\VendorController::class, 'submitBid'])->name('vendor.bid.submit');
        // Vendor bid details API for modal
        Route::get('/api/bids/{id}', [App\Http\Controllers\VendorController::class, 'getBidDetails'])->name('vendor.api.bids.show');
        // Vendor invoice details API for modal
        Route::get('/api/invoices/{invoice}', [App\Http\Controllers\VendorController::class, 'getVendorInvoiceDetails'])->name('vendor.api.invoices.show');
    });
    
    Route::get('/bidding', [App\Http\Controllers\VendorController::class, 'showBiddingLanding'])->name('vendor.bidding.landing');
});

// Direct route for bidding landing page
Route::get('/bidding_landing', [App\Http\Controllers\VendorController::class, 'showBiddingLanding'])->name('bidding.landing');

// SWS API Routes
Route::prefix('api')->group(function () {
    // Stock Replenishment
    Route::prefix('stock')->group(function () {
        Route::get('/test', function() {
            return response()->json(['status' => 'API working', 'time' => now()]);
        });
        Route::get('/low-stock', [App\Http\Controllers\StockReplenishmentController::class, 'getLowStockItems']);
        Route::post('/purchase-request', [App\Http\Controllers\StockReplenishmentController::class, 'createPurchaseRequest']);
        Route::post('/bulk-purchase-requests', [App\Http\Controllers\StockReplenishmentController::class, 'bulkCreatePurchaseRequests']);
        Route::post('/approve-request', [App\Http\Controllers\StockReplenishmentController::class, 'approvePurchaseRequest']);
        Route::post('/send-to-procurement', [App\Http\Controllers\StockReplenishmentController::class, 'sendToProcurement']);
        Route::post('/auto-generate', [App\Http\Controllers\StockReplenishmentController::class, 'autoGenerateRequests']);
    });
    
    // Inventory Receipt
    Route::prefix('inventory')->group(function () {
        Route::post('/add-item', [App\Http\Controllers\InventoryReceiptController::class, 'addItem']);
        Route::post('/update-stock', [App\Http\Controllers\InventoryReceiptController::class, 'updateStock']);
        Route::get('/recent-receipts', [App\Http\Controllers\InventoryReceiptController::class, 'getRecentReceipts']);
        Route::post('/store-receipt', [App\Http\Controllers\InventoryReceiptController::class, 'store']);
        Route::get('/dashboard-stats', [App\Http\Controllers\InventoryReceiptController::class, 'getDashboardStats']);
        Route::post('/complete-receipt/{id}', [App\Http\Controllers\InventoryReceiptController::class, 'completeReceipt']);
        Route::get('/purchase-orders-by-supplier', [App\Http\Controllers\InventoryReceiptController::class, 'getPurchaseOrdersBySupplier']);
        Route::get('/purchase-order-items', [App\Http\Controllers\InventoryReceiptController::class, 'getPurchaseOrderItems']);
    });
    
    // Storage Organization
    Route::prefix('storage')->group(function () {
        Route::post('/assign-location', [App\Http\Controllers\StorageOrganizationController::class, 'assignLocation']);
        Route::post('/relocate-item', [App\Http\Controllers\StorageOrganizationController::class, 'relocateItem']);
        Route::post('/bulk-assign', [App\Http\Controllers\StorageOrganizationController::class, 'bulkAssignLocation']);
        Route::get('/items-by-zone', [App\Http\Controllers\StorageOrganizationController::class, 'getItemsByZone']);
        Route::get('/locations', [App\Http\Controllers\StorageOrganizationController::class, 'getStorageLocations']);
    });
    
    // Picking & Dispatch
    Route::prefix('dispatch')->group(function () {
        Route::post('/item', [App\Http\Controllers\PickingDispatchController::class, 'dispatchItem']);
        Route::post('/bulk', [App\Http\Controllers\PickingDispatchController::class, 'bulkDispatch']);
        Route::get('/items-by-location', [App\Http\Controllers\PickingDispatchController::class, 'getItemsByLocation']);
    });
    
    // Contract API routes
    Route::prefix('contracts')->group(function () {
        Route::get('/{id}/view', [App\Http\Controllers\PSMContractController::class, 'view']);
        Route::get('/{id}/download', [App\Http\Controllers\PSMContractController::class, 'download']);
    });
});

// Vendor Management API Routes (for admin)
Route::prefix('api')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class])->group(function () {
    Route::get('/vendors', [App\Http\Controllers\Api\VendorController::class, 'index']);
    Route::get('/vendors/{id}', [App\Http\Controllers\Api\VendorController::class, 'show']);
    Route::post('/vendors/{id}/approve', [App\Http\Controllers\Api\VendorController::class, 'approve']);
    Route::post('/vendors/{id}/suspend', [App\Http\Controllers\Api\VendorController::class, 'suspend']);
    Route::post('/vendors/{id}/activate', [App\Http\Controllers\Api\VendorController::class, 'activate']);
    Route::post('/vendors/{id}/verify-documents', [App\Http\Controllers\Api\VendorController::class, 'verifyDocuments']);
    Route::post('/vendors/approve-all', [App\Http\Controllers\Api\VendorController::class, 'approveAll']);
    
    // PSM Bidding API Routes
    Route::prefix('psm/bidding')->group(function () {
        Route::get('/bids', [App\Http\Controllers\PSMBiddingController::class, 'getBids']);
        Route::get('/bids/{id}', [App\Http\Controllers\PSMBiddingController::class, 'getBidDetails']);
        Route::post('/bids/{id}/status', [App\Http\Controllers\PSMBiddingController::class, 'updateBidStatus']);
        Route::post('/bids/{id}/select-winner', [App\Http\Controllers\PSMBiddingController::class, 'selectWinner']);
        Route::post('/bids/{id}/reject', [App\Http\Controllers\PSMBiddingController::class, 'rejectBid']);
        Route::post('/bids/{id}/start-evaluation', [App\Http\Controllers\PSMBiddingController::class, 'startEvaluation']);
        Route::post('/bids/bulk-update', [App\Http\Controllers\PSMBiddingController::class, 'bulkUpdateStatus']);
        Route::get('/statistics', [App\Http\Controllers\PSMBiddingController::class, 'getStatistics']);
        Route::get('/export', [App\Http\Controllers\PSMBiddingController::class, 'exportBids']);
        
        // AI-powered bid analysis routes
        Route::post('/ai/recommendations', [App\Http\Controllers\PSMBiddingController::class, 'getAiRecommendations']);
        Route::post('/ai/predict-winner', [App\Http\Controllers\PSMBiddingController::class, 'predictWinner']);
        Route::post('/ai/compare-bids', [App\Http\Controllers\PSMBiddingController::class, 'compareBids']);
        Route::get('/ai/analyze-bid/{id}', [App\Http\Controllers\PSMBiddingController::class, 'analyzeBidWithAi']);
        Route::get('/ai/model-performance', [App\Http\Controllers\PSMBiddingController::class, 'getModelPerformance']);
        Route::post('/ai/retrain-models', [App\Http\Controllers\PSMBiddingController::class, 'retrainModels']);
        Route::post('/ai/generate-sample-data', [App\Http\Controllers\PSMBiddingController::class, 'generateSampleData']);
    });
});
Route::get('/dtrs/version', function () {
    return view('DTRS.version');
});

// Temporary route to create vendors table
Route::get('/create-vendors-table', function () {
    try {
        $pdo = new PDO('sqlite:' . database_path('database.sqlite'));
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Create vendors table
        $sql = "CREATE TABLE IF NOT EXISTS vendors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            email_verified_at DATETIME NULL,
            password VARCHAR(255) NOT NULL,
            company_name VARCHAR(255) NOT NULL,
            business_type VARCHAR(255) NULL,
            phone VARCHAR(20) NULL,
            address TEXT NULL,
            status VARCHAR(50) NOT NULL DEFAULT 'pending',
            remember_token VARCHAR(100) NULL,
            created_at DATETIME NULL,
            updated_at DATETIME NULL
        )";
        
        $pdo->exec($sql);
        
        // Check if table was created
        $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='vendors'");
        if ($result->fetch()) {
            // Also create migrations table entry
            $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration VARCHAR(255) NOT NULL,
                batch INTEGER NOT NULL
            )");
            
            // Insert migration record
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO migrations (migration, batch) VALUES (?, 1)");
            $stmt->execute(['2024_01_01_000004_create_vendors_table']);
            
            return response()->json(['success' => true, 'message' => 'Vendors table created successfully']);
        } else {
            return response()->json(['success' => false, 'message' => 'Failed to create vendors table']);
        }
        
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
});

// SWS routes by direct view path (fallback) - Accessible by procurement officers and logistics staff
Route::middleware(['auth', 'role:procurement_officer,logistics_staff,admin'])->group(function () {
    Route::get('/sws/inventoryreceipt', [App\Http\Controllers\InventoryReceiptController::class, 'index'])->name('sws.inventoryreceipt');

    Route::get('/sws/picking-dispatch', function () {
        return view('SWS.picking and dispatch');
    })->name('sws.picking-dispatch');

    Route::get('/sws/stockreplenishment', function () {
        return view('SWS.stockreplenishment');
    })->name('sws.stockreplenishment');

    Route::get('/sws/storageorganization', [App\Http\Controllers\StorageOrganizationController::class, 'index'])->name('sws.storageorganization');
});

// Missing VendorPortal Routes (public access)
Route::get('/vendor/bid-form', function () {
    return view('VendorPortal.bid_form');
})->name('vendor.bid-form');

Route::get('/vendor/bidding-landing', function () {
    return view('VendorPortal.bidding_landing');
})->name('vendor.bidding-landing');

Route::get('/vendor/bids-view', function () {
    return view('VendorPortal.bids');
})->name('vendor.bids-view');

Route::get('/vendor/invoices-view', function () {
    return view('VendorPortal.vendor_invoices');
})->name('vendor.invoices-view');

Route::get('/vendor/orders-view', function () {
    return view('VendorPortal.vendor_orders');
})->name('vendor.orders-view');
