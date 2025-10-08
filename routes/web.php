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

// Password Reset Routes
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgot.password');

Route::get('/verify-otp', function () {
    if (!request('email')) {
        return redirect()->route('forgot.password');
    }
    return view('verify-otp');
})->name('verify.otp');

Route::get('/password-reset-success', function () {
    return view('password-reset-success');
})->name('password.reset.success');

Route::post('/password-reset/send-otp', [App\Http\Controllers\PasswordResetController::class, 'sendOtp'])->name('password.reset.send-otp');
Route::post('/password-reset/verify-otp', [App\Http\Controllers\PasswordResetController::class, 'resetPassword'])->name('password.reset.verify-otp');
Route::post('/password-reset/resend-otp', [App\Http\Controllers\PasswordResetController::class, 'resendOtp'])->name('password.reset.resend-otp');

Route::get('/register', function () {
    return view('register');
});

Route::post('/register', [App\Http\Controllers\AuthController::class, 'register'])->name('register');

// Main dashboard - accessible by admin and logistics staff only
Route::middleware(['auth', 'role:admin,logistics_staff'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/quick-stats', [App\Http\Controllers\DashboardController::class, 'getQuickStats'])->name('dashboard.quick-stats');
});

// Officer Dashboard Routes - Accessible by procurement officers only
Route::middleware(['auth', 'role:procurement_officer,admin'])->group(function () {
    Route::get('/officer/dashboard', function () {
        return view('Officer.dashboard');
    })->name('officer.dashboard');
    
    Route::get('/officer/purchaserequest', function () {
        return redirect()->route('psm.request');
    })->name('officer.purchaserequest');
    
    // Officer Purchase Request Form Submission
    Route::post('/officer/purchaserequest', function (\Illuminate\Http\Request $request) {
        // Validate the request
        $request->validate([
            'item_description' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'estimated_cost' => 'required|numeric|min:0',
            'required_date' => 'required|date',
            'priority' => 'required|string|in:Low,Medium,High',
            'justification' => 'required|string',
            'attachments.*' => 'nullable|file|max:10240' // 10MB max per file
        ]);
        
        try {
            // Try to create ItemRequest if model exists
            $itemRequest = new \App\Models\ItemRequest();
            $itemRequest->item_description = $request->item_description;
            $itemRequest->quantity = $request->quantity;
            $itemRequest->estimated_cost = $request->estimated_cost;
            $itemRequest->required_date = $request->required_date;
            $itemRequest->priority = $request->priority;
            $itemRequest->justification = $request->justification;
            $itemRequest->status = 'PENDING';
            $itemRequest->requested_by = auth()->id();
            $itemRequest->save();
            
            // Handle file attachments if any
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store('purchase-request-attachments', 'public');
                    // Store file path in database if needed
                }
            }
            
            return redirect()->route('officer.purchaserequest')
                ->with('success', 'Purchase request submitted successfully!');
                
        } catch (\Exception $e) {
            return redirect()->route('officer.purchaserequest')
                ->with('error', 'Failed to submit purchase request. Please try again.');
        }
    })->name('officer.request.store');
    
    Route::get('/officer/vendorlist', function () {
        return redirect()->route('psm.vendor');
    })->name('officer.vendorlist');
    
    Route::get('/officer/biddinglist', function () {
        return redirect()->route('psm.bidding');
    })->name('officer.biddinglist');
    
    Route::get('/officer/contractlist', function () {
        return redirect('/psm/contract');
    })->name('officer.contractlist');
    
    Route::get('/officer/orderlist', function () {
        return redirect()->route('psm.order.index');
    })->name('officer.orderlist');
    
    Route::get('/officer/trackinglist', function () {
        return redirect()->route('psm.delivery');
    })->name('officer.trackinglist');
    
    Route::get('/officer/invoicelist', function () {
        return redirect()->route('psm.invoice.index');
    })->name('officer.invoicelist');
});

// SWS Routes - Accessible by logistics staff and admin only (procurement officers restricted)
Route::middleware(['auth', 'role:logistics_staff,admin'])->group(function () {
    Route::get('/smartwarehousing', function () {
        return view('SWS.smartwarehousing');
    });

    Route::get('/inventory-receipt', [App\Http\Controllers\InventoryReceiptController::class, 'index'])->name('inventory.receipt');

    Route::get('/storage-organization', [App\Http\Controllers\StorageOrganizationController::class, 'index'])->name('storage.organization');

    Route::get('/picking-dispatch', [App\Http\Controllers\PickingDispatchController::class, 'index'])->name('picking-dispatch');
    
    // Inventory Receipt Item Routes for Picking
    Route::put('/inventory-items/{item}/picked', [App\Http\Controllers\PickingDispatchController::class, 'updatePicked'])->name('inventory-items.update-picked');
    
    // Picking and Dispatch Session Routes
    Route::post('/picking-dispatch/save-progress', [App\Http\Controllers\PickingDispatchController::class, 'saveProgress'])->name('picking-dispatch.save-progress');
    Route::post('/picking-dispatch/complete-session', [App\Http\Controllers\PickingDispatchController::class, 'completeSession'])->name('picking-dispatch.complete-session');
    Route::post('/picking-dispatch/remove-from-dispatch', [App\Http\Controllers\PickingDispatchController::class, 'removeFromDispatch'])->name('picking-dispatch.remove-from-dispatch');
    
    // Item Request Routes (Optional - for creating requests)
    Route::get('/item-requests/create', [App\Http\Controllers\ItemRequestController::class, 'create'])->name('item-requests.create');
    Route::post('/item-requests', [App\Http\Controllers\ItemRequestController::class, 'store'])->name('item-requests.store');
    
    // API endpoint for inventory items
    Route::get('/api/inventory-items', [App\Http\Controllers\PickingDispatchController::class, 'getInventoryItems'])->name('api.inventory-items');

    Route::get('/stock-replenishment', [App\Http\Controllers\StockReplenishmentController::class, 'index'])->name('stock.replenishment');
});

// PSM (Procurement & Sourcing Management) Routes - Accessible by procurement officers only
Route::middleware(['auth', 'role:procurement_officer,admin'])->group(function () {
    Route::get('/psm/request', [App\Http\Controllers\PurchaseRequestController::class, 'index'])->name('psm.request');
    Route::post('/psm/request', [App\Http\Controllers\PurchaseRequestController::class, 'store'])->name('psm.request.store');
    Route::get('/psm/request/{id}/bid-form', [App\Http\Controllers\PurchaseRequestController::class, 'showBidForm'])->name('psm.request.bid-form');
    Route::post('/psm/request/submit-bid', [App\Http\Controllers\PurchaseRequestController::class, 'submitBidForm'])->name('psm.request.submit-bid');
    Route::post('/psm/request/approve', [App\Http\Controllers\PurchaseRequestController::class, 'approve'])->name('psm.request.approve');
    Route::get('/psm/request/{id}/bid-data', [App\Http\Controllers\PurchaseRequestController::class, 'getBidFormData'])->name('psm.request.bid-data');
    
    Route::get('/psm/purchaserequest-approval', [App\Http\Controllers\PurchaseRequestController::class, 'approvalIndex'])->name('psm.purchaserequest.approval');
    Route::post('/psm/request/approve-item', [App\Http\Controllers\PurchaseRequestController::class, 'approve'])->name('psm.request.approve-item');
    Route::post('/psm/request/reject-item', [App\Http\Controllers\PurchaseRequestController::class, 'reject'])->name('psm.request.reject-item');

    Route::get('/psm/vendor', [App\Http\Controllers\PSMVendorController::class, 'index'])->name('psm.vendor');

    Route::get('/psm/bidding', [App\Http\Controllers\PSMBiddingController::class, 'index'])->name('psm.bidding');
    Route::post('/psm/opportunities', [App\Http\Controllers\PSMBiddingController::class, 'storeOpportunity'])->name('psm.opportunities.store');
    Route::post('/psm/opportunities/{id}/evaluate', [App\Http\Controllers\PSMBiddingController::class, 'evaluateOpportunity'])->name('psm.opportunities.evaluate');

    Route::get('/psm/contract', [App\Http\Controllers\PSMContractController::class, 'index']);
    Route::get('/psm/contract/{id}/download', [App\Http\Controllers\PSMContractController::class, 'download'])->name('psm.contract.download');
    Route::get('/psm/draft-contracts', [App\Http\Controllers\DraftContractController::class, 'index'])->name('psm.draft-contracts');
    Route::get('/psm/contract-approval', [App\Http\Controllers\DraftContractController::class, 'approval'])->name('psm.contract-approval');
    
    // Debug route to check and fix contract statuses
    Route::get('/debug/contracts', function() {
        $contracts = \App\Models\Contract::with('vendor')->get();
        
        $output = "<h2>Contract Status Debug</h2>";
        
        if ($contracts->count() > 0) {
            $output .= "<table border='1' style='border-collapse: collapse; width: 100%;'>";
            $output .= "<tr><th>ID</th><th>Number</th><th>Title</th><th>Status</th><th>Vendor</th><th>Action</th></tr>";
            
            foreach ($contracts as $contract) {
                $output .= "<tr>";
                $output .= "<td>{$contract->id}</td>";
                $output .= "<td>{$contract->contract_number}</td>";
                $output .= "<td>{$contract->title}</td>";
                $output .= "<td>{$contract->workflow_status}</td>";
                $output .= "<td>" . ($contract->vendor ? $contract->vendor->name : 'N/A') . "</td>";
                
                if ($contract->workflow_status !== 'pending_vendor_signature') {
                    $output .= "<td><a href='/debug/contracts/{$contract->id}/set-pending' style='color: blue;'>Set to Pending Vendor Signature</a></td>";
                } else {
                    $output .= "<td>Ready for vendor signing</td>";
                }
                $output .= "</tr>";
            }
            $output .= "</table>";
        } else {
            $output .= "<p>No contracts found in database.</p>";
        }
        
        return $output;
    });
    
    Route::get('/debug/contracts/{id}/set-pending', function($id) {
        $contract = \App\Models\Contract::findOrFail($id);
        $contract->update(['workflow_status' => 'pending_vendor_signature']);
        
        return redirect('/debug/contracts')->with('message', "Contract {$id} updated to pending_vendor_signature");
    });
    
    // Fix all contracts to show sign buttons
    Route::get('/fix-contracts', function() {
        $updated = \App\Models\Contract::where('workflow_status', 'draft')
            ->update(['workflow_status' => 'pending_vendor_signature']);
        
        return "Fixed! Updated {$updated} contracts to 'pending_vendor_signature' status. Sign buttons should now appear in vendor portal.";
    });
    
    // Fix invoice status from 'Generated' to 'Pending'
    Route::get('/fix-invoice-status', function() {
        $updated = \App\Models\Invoice::where('status', 'Generated')
            ->update(['status' => 'Pending']);
        
        return "Fixed! Updated {$updated} invoices from 'Generated' to 'Pending' status.";
    });

    Route::get('/psm/order', [App\Http\Controllers\PurchaseOrderController::class, 'index'])->name('psm.order.index');
    Route::post('/psm/order', [App\Http\Controllers\PurchaseOrderController::class, 'store'])->name('psm.order.store');
    Route::get('/psm/order/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'show'])->name('psm.order.show');
    Route::get('/psm/order/{purchaseOrder}/edit', [App\Http\Controllers\PurchaseOrderController::class, 'edit'])->name('psm.order.edit');
    Route::put('/psm/order/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'update'])->name('psm.order.update');
    Route::delete('/psm/order/{purchaseOrder}', [App\Http\Controllers\PurchaseOrderController::class, 'destroy'])->name('psm.order.destroy');
    Route::get('/psm/order/{purchaseOrder}/download-pdf', [App\Http\Controllers\PurchaseOrderController::class, 'downloadPdf'])->name('psm.order.download-pdf');
    
    // Purchase Order Actions
    Route::post('/psm/order/{purchaseOrder}/submit-approval', [App\Http\Controllers\PurchaseOrderController::class, 'submitForApproval'])->name('psm.order.submit-approval');
    Route::post('/psm/order/{purchaseOrder}/approve', [App\Http\Controllers\PurchaseOrderController::class, 'approve'])->name('psm.order.approve');
    Route::post('/psm/order/{purchaseOrder}/issue', [App\Http\Controllers\PurchaseOrderController::class, 'issue'])->name('psm.order.issue');
    Route::post('/psm/order/{purchaseOrder}/in-progress', [App\Http\Controllers\PurchaseOrderController::class, 'markInProgress'])->name('psm.order.in-progress');
    Route::post('/psm/order/{purchaseOrder}/complete', [App\Http\Controllers\PurchaseOrderController::class, 'complete'])->name('psm.order.complete');
    
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

// Project Logistics Tracker (PLT) Routes - Accessible by logistics staff and admin only
Route::middleware(['auth', 'role:logistics_staff,admin'])->group(function () {
    // Project Management Routes
    Route::get('/plt/toursetup', [App\Http\Controllers\ProjectController::class, 'index'])->name('plt.projects.index');
    Route::post('/plt/projects', [App\Http\Controllers\ProjectController::class, 'store'])->name('plt.projects.store');
    Route::get('/plt/projects/{project}', [App\Http\Controllers\ProjectController::class, 'show'])->name('plt.projects.show');
    Route::put('/plt/projects/{project}', [App\Http\Controllers\ProjectController::class, 'update'])->name('plt.projects.update');
    Route::delete('/plt/projects/{project}', [App\Http\Controllers\ProjectController::class, 'destroy'])->name('plt.projects.destroy');
    
    // Project Actions
    Route::post('/plt/projects/{project}/approve', [App\Http\Controllers\ProjectController::class, 'approve'])->name('plt.projects.approve');
    Route::post('/plt/projects/{project}/start', [App\Http\Controllers\ProjectController::class, 'start'])->name('plt.projects.start');
    Route::post('/plt/projects/{project}/complete', [App\Http\Controllers\ProjectController::class, 'complete'])->name('plt.projects.complete');
    Route::post('/plt/projects/save-draft', [App\Http\Controllers\ProjectController::class, 'saveDraft'])->name('plt.projects.save-draft');
    
    // Project API Routes
    Route::get('/api/plt/projects/stats', [App\Http\Controllers\ProjectController::class, 'getStats'])->name('plt.projects.stats');

    Route::get('/plt/execution', [App\Http\Controllers\ProjectController::class, 'execution'])->name('plt.execution');

    Route::get('/plt/closure', [App\Http\Controllers\ProjectController::class, 'reports'])->name('plt.reports');
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
    Route::delete('/alms/maintenance/schedules/{schedule}', [App\Http\Controllers\ALMS\MaintenanceController::class, 'destroy'])->name('alms.maintenance.destroy');
    Route::post('/alms/maintenance/schedules/bulk-reschedule', [App\Http\Controllers\ALMS\MaintenanceController::class, 'bulkReschedule'])->name('alms.maintenance.bulk-reschedule');
    Route::get('/alms/disposalretirement', [App\Http\Controllers\ALMS\DisposalController::class, 'index']);
    Route::post('/alms/disposal-requests', [App\Http\Controllers\ALMS\DisposalController::class, 'store'])->name('alms.disposal.store');
    
    // Vehicle Request Management Routes
    Route::get('/alms/vehicle-requests', [App\Http\Controllers\VehicleRequestController::class, 'index'])->name('alms.vehicle-requests');
    
    // Test route for debugging
    Route::get('/alms/vehicle-requests-test', function() {
        return 'Vehicle Requests Test Route Working!';
    })->name('alms.vehicle-requests.test-simple');
    Route::get('/alms/vehicle-requests/{id}', [App\Http\Controllers\VehicleRequestController::class, 'show'])->name('alms.vehicle-requests.show');
    Route::post('/alms/vehicle-requests/decide', [App\Http\Controllers\VehicleRequestController::class, 'decide'])->name('alms.vehicle-requests.decide');
    Route::get('/alms/vehicle-requests/test/connection', [App\Http\Controllers\VehicleRequestController::class, 'testConnection'])->name('alms.vehicle-requests.test');
    
    // External Vehicle Approvals Route (Legacy)
    Route::get('/alms/external-vehicle-approvals', function () {
        // Since this is a standalone PHP file, we need to include it directly
        $filePath = resource_path('views/ALMS/external-vehicle-approvals.php');
        if (file_exists($filePath)) {
            return response()->file($filePath);
        }
        abort(404, 'External vehicle approvals page not found');
    })->name('alms.external-vehicle-approvals');
});

// Document Tracking Routes - Accessible by logistics staff and admin only
Route::middleware(['auth', 'role:logistics_staff,admin'])->group(function () {
    Route::get('/dtrs/document', [App\Http\Controllers\DTRSController::class, 'documents']);
    Route::get('/dtrs/documents/{documentId}/view', [App\Http\Controllers\DTRSController::class, 'viewDocument']);
    Route::get('/dtrs/documents/{documentId}/download', [App\Http\Controllers\DTRSController::class, 'downloadDocument']);
    Route::get('/dtrs/documents/{documentId}/metadata', [App\Http\Controllers\DTRSController::class, 'documentMetadata']);
    Route::post('/dtrs/log-access', [App\Http\Controllers\DTRSController::class, 'logAccess']);
    Route::post('/dtrs/folder-settings', [App\Http\Controllers\DTRSController::class, 'saveFolderSettings']);
    Route::get('/dtrs/folder-settings/{categoryType?}', [App\Http\Controllers\DTRSController::class, 'getFolderSettings']);

    // Audit Trail Routes
    Route::get('/dtrs/audits', [App\Http\Controllers\AuditController::class, 'index'])->name('dtrs.audits');
    Route::get('/dtrs/audits/data', [App\Http\Controllers\AuditController::class, 'getData'])->name('dtrs.audits.data');
    Route::post('/dtrs/audits/export', [App\Http\Controllers\AuditController::class, 'export'])->name('dtrs.audits.export');
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
        Route::post('/receipts/{id}/complete', [App\Http\Controllers\InventoryReceiptController::class, 'completeReceipt'])->name('inventory.receipts.complete');
        Route::post('/receipts/{id}/generate-document', [App\Http\Controllers\InventoryReceiptController::class, 'generateDocument'])->name('inventory.receipts.generate-document');
        Route::get('/receipts/{id}/test-document', function($id) {
            $receipt = \App\Models\InventoryReceipt::findOrFail($id);
            $receipt->generateDocument();
            return response()->json([
                'success' => true,
                'message' => 'Document generated',
                'document_path' => $receipt->document_path,
                'receipt_number' => $receipt->receipt_number
            ]);
        });
        Route::post('/store-receipt', [App\Http\Controllers\InventoryReceiptController::class, 'store']);
        Route::get('/dashboard-stats', [App\Http\Controllers\InventoryReceiptController::class, 'getDashboardStats']);
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
        Route::get('/bids/{id}', [App\Http\Controllers\PSMBiddingController::class, 'getBid']);
        Route::post('/bids/{id}/status', [App\Http\Controllers\PSMBiddingController::class, 'updateBidStatus']);
        Route::post('/bids/{id}/select-winner', [App\Http\Controllers\PSMBiddingController::class, 'selectWinner']);
        Route::post('/bids/{id}/reject', [App\Http\Controllers\PSMBiddingController::class, 'rejectBid']);
        Route::post('/bids/{id}/start-evaluation', [App\Http\Controllers\PSMBiddingController::class, 'startEvaluation']);
        Route::post('/bids/bulk-update', [App\Http\Controllers\PSMBiddingController::class, 'bulkUpdateStatus']);
        Route::get('/statistics', [App\Http\Controllers\PSMBiddingController::class, 'getStatistics']);
        Route::get('/export', [App\Http\Controllers\PSMBiddingController::class, 'exportBids']);
        Route::post('/ai-analysis', [App\Http\Controllers\PSMBiddingController::class, 'aiAnalysis']);
        
        // AI-powered bid analysis routes
        Route::post('/ai/recommendations', [App\Http\Controllers\PSMBiddingController::class, 'getAiRecommendations']);
        Route::post('/ai/predict-winner', [App\Http\Controllers\PSMBiddingController::class, 'predictWinner']);
        Route::post('/ai/compare-bids', [App\Http\Controllers\PSMBiddingController::class, 'compareBids']);
        Route::get('/ai/analyze-bid/{id}', [App\Http\Controllers\PSMBiddingController::class, 'analyzeBidWithAi']);
        Route::get('/ai/model-performance', [App\Http\Controllers\PSMBiddingController::class, 'getModelPerformance']);
        Route::post('/ai/retrain-models', [App\Http\Controllers\PSMBiddingController::class, 'retrainModels']);
        Route::post('/ai/generate-sample-data', [App\Http\Controllers\PSMBiddingController::class, 'generateSampleData']);
        
        // Opportunity management routes
        Route::get('/opportunities/{id}', [App\Http\Controllers\PSMBiddingController::class, 'getOpportunity']);
        Route::put('/opportunities/{id}', [App\Http\Controllers\PSMBiddingController::class, 'updateOpportunity']);
        Route::delete('/opportunities/{id}', [App\Http\Controllers\PSMBiddingController::class, 'deleteOpportunity']);
    });
});
// Document Version History Routes
Route::get('/dtrs/version', [\App\Http\Controllers\DocumentVersionController::class, 'index'])->middleware('auth')->name('dtrs.version');
Route::get('/dtrs/document/{documentId}/versions', [\App\Http\Controllers\DocumentVersionController::class, 'getVersionHistory'])->middleware('auth')->name('dtrs.document.versions');
Route::get('/dtrs/document/version/{versionId}/view', [\App\Http\Controllers\DocumentVersionController::class, 'viewVersion'])->middleware('auth')->name('dtrs.version.view');
Route::get('/dtrs/document/version/{versionId}/download', [\App\Http\Controllers\DocumentVersionController::class, 'downloadVersion'])->middleware('auth')->name('dtrs.version.download');
Route::post('/dtrs/document/version/{versionId}/restore', [\App\Http\Controllers\DocumentVersionController::class, 'restoreVersion'])->middleware('auth')->name('dtrs.version.restore');
Route::get('/dtrs/document/version/{versionId}/compare', [\App\Http\Controllers\DocumentVersionController::class, 'compareVersions'])->middleware('auth')->name('dtrs.version.compare');

// Temporary route to run asset migration
Route::get('/migrate-assets', function () {
    try {
        Artisan::call('migrate', ['--path' => 'database/migrations/2025_10_06_021900_add_dynamic_asset_fields_to_assets_table.php']);
        return response()->json(['success' => true, 'message' => 'Asset migration completed successfully']);
    } catch (Exception $e) {
        return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
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

// SWS routes by direct view path (fallback) - Accessible by logistics staff and admin only
Route::middleware(['auth', 'role:logistics_staff,admin'])->group(function () {
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

// Include vendor routes
require __DIR__.'/vendor.php';

// Include test API routes for debugging
require __DIR__.'/test-api.php';

// Direct contract status route in web.php as fallback
Route::get('/api/contract-status/{id}', function($id) {
    try {
        $contract = \App\Models\Contract::find($id);
        
        if (!$contract) {
            return response()->json([
                'success' => false,
                'error' => 'Contract not found',
                'contract_id' => $id
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'contract' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number ?? 'N/A',
                'title' => $contract->title ?? 'Untitled Contract',
                'value' => $contract->value ?? 0,
                'negotiated_value' => $contract->negotiated_value,
                'negotiated_terms' => $contract->negotiated_terms ? json_encode($contract->negotiated_terms) : null,
                'negotiation_notes' => $contract->negotiation_notes,
                'workflow_status' => $contract->workflow_status ?? 'draft',
                'vendor_signed' => !is_null($contract->vendor_signed_at ?? null),
                'vendor_signed_at' => $contract->vendor_signed_at,
                'procurement_signed' => !is_null($contract->procurement_signed_at ?? null),
                'procurement_signed_at' => $contract->procurement_signed_at,
                'is_fully_signed' => ($contract->workflow_status ?? 'draft') === 'fully_signed'
            ],
            'signing_progress' => [
                'draft_created' => !is_null($contract->created_at),
                'terms_negotiated' => ($contract->workflow_status ?? 'draft') !== 'draft',
                'vendor_signed' => !is_null($contract->vendor_signed_at ?? null),
                'procurement_signed' => !is_null($contract->procurement_signed_at ?? null),
                'fully_executed' => ($contract->workflow_status ?? 'draft') === 'fully_signed'
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Database error: ' . $e->getMessage(),
            'contract_id' => $id
        ], 500);
    }
});
