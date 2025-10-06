<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\ContractSigningController;
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

// Test the specific signing status route
Route::get('/test-signing/{id}', function($id) {
    return response()->json([
        'message' => 'Signing status route test working',
        'contract_id' => $id,
        'timestamp' => now()
    ]);
});

// Direct test for the exact endpoint being called
Route::get('/contracts/{contractId}/test-endpoint', function($contractId) {
    return response()->json([
        'success' => true,
        'message' => 'Direct contract endpoint test working',
        'contract_id' => $contractId,
        'timestamp' => now()
    ]);
});

// Test PSM route
Route::post('/test-psm', function () {
    return response()->json(['message' => 'PSM API test working!', 'timestamp' => now()]);
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
    Route::post('/{id}/verify-documents', [VendorController::class, 'verifyDocuments']);
    Route::post('/{id}/revoke-verification', [VendorController::class, 'revokeVerification']);
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

// Test route for debugging
Route::get('/contracts/test', function() {
    return response()->json(['success' => true, 'message' => 'Contract routes are working']);
});

// Debug route to check specific contract
Route::get('/contracts/debug/{id}', function($id) {
    $contract = \App\Models\Contract::find($id);
    return response()->json([
        'contract_id' => $id,
        'exists' => $contract ? true : false,
        'contract_data' => $contract ? $contract->toArray() : null,
        'total_contracts' => \App\Models\Contract::count()
    ]);
});

// Contract Signing API routes - Direct routes to avoid conflicts
Route::post('/contracts/initiate-negotiation/{bidId}', [ContractSigningController::class, 'initiateNegotiation']);
Route::put('/contracts/{contractId}/negotiate-terms', [ContractSigningController::class, 'updateNegotiatedTerms']);
Route::post('/contracts/{contractId}/send-for-vendor-signature', [ContractSigningController::class, 'sendForVendorSignature']);
Route::post('/contracts/{contractId}/vendor-sign', [ContractSigningController::class, 'vendorSign']);
Route::post('/contracts/{contractId}/procurement-sign', [ContractSigningController::class, 'procurementSign']);

// Contract signing status route - using controller method
Route::get('/contracts/{contractId}/signing-status', [ContractSigningController::class, 'getSigningStatus']);

// Draft contracts API routes
Route::get('/draft-contracts', [App\Http\Controllers\DraftContractController::class, 'index']);
Route::get('/draft-contracts/{id}', [App\Http\Controllers\DraftContractController::class, 'show']);
Route::post('/draft-contracts/{id}/send-for-vendor-signature', [App\Http\Controllers\DraftContractController::class, 'sendForVendorSignature']);
Route::post('/draft-contracts/{id}/approve', [App\Http\Controllers\DraftContractController::class, 'approve']);
Route::post('/draft-contracts/{id}/reject', [App\Http\Controllers\DraftContractController::class, 'reject']);

Route::get('/contracts/{contractId}/view', function($contractId) {
    try {
        $contract = \App\Models\Contract::with(['vendor', 'bid'])->find($contractId);
        
        if (!$contract) {
            return response()->json([
                'success' => false,
                'error' => 'Contract not found',
                'contract_id' => $contractId
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'contract' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'title' => $contract->title,
                'value' => $contract->value,
                'negotiated_value' => $contract->negotiated_value,
                'workflow_status' => $contract->workflow_status,
                'status' => $contract->status,
                'vendor_signed' => !is_null($contract->vendor_signed_at),
                'procurement_signed' => !is_null($contract->procurement_signed_at),
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'terms' => $contract->terms,
                'vendor' => $contract->vendor ? [
                    'name' => $contract->vendor->company_name ?? $contract->vendor->name,
                    'email' => $contract->vendor->email,
                    'company_name' => $contract->vendor->company_name,
                    'phone' => $contract->vendor->phone,
                    'business_type' => $contract->vendor->business_type,
                    'address' => $contract->vendor->address
                ] : null
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Error: ' . $e->getMessage(),
            'contract_id' => $contractId
        ], 500);
    }
});

Route::get('/contracts/{contractId}/download/{type?}', [ContractSigningController::class, 'downloadContract']);

// Simple contract status endpoint that works
Route::get('/contract-status/{id}', function($id) {
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
                'is_fully_signed' => ($contract->workflow_status ?? 'draft') === 'fully_signed',
                // Add missing date fields
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                // Add terms field
                'terms' => $contract->terms
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

// Test route to verify API is working
Route::get('/test-contract/{id}', function($id) {
    return response()->json([
        'success' => true,
        'message' => 'API is working',
        'contract_id' => $id,
        'timestamp' => now()
    ]);
});

Route::prefix('storage')->group(function () {
    Route::post('/assign-location', [App\Http\Controllers\StorageOrganizationController::class, 'assignLocation']);
    Route::post('/relocate-item', [App\Http\Controllers\StorageOrganizationController::class, 'relocateItem']);
    Route::post('/bulk-assign', [App\Http\Controllers\StorageOrganizationController::class, 'bulkAssignLocation']);
    Route::get('/items-by-zone', [App\Http\Controllers\StorageOrganizationController::class, 'getItemsByZone']);
    Route::get('/locations', [App\Http\Controllers\StorageOrganizationController::class, 'getStorageLocations']);
});

// Stock Replenishment API routes
Route::prefix('stock')->group(function () {
    Route::get('/low-stock-items', [App\Http\Controllers\StockReplenishmentController::class, 'getLowStockItems']);
    Route::post('/purchase-request', [App\Http\Controllers\StockReplenishmentController::class, 'createPurchaseRequest']);
    Route::post('/bulk-purchase-requests', [App\Http\Controllers\StockReplenishmentController::class, 'bulkCreatePurchaseRequests']);
    Route::post('/approve-request', [App\Http\Controllers\StockReplenishmentController::class, 'approvePurchaseRequest']);
    Route::post('/send-to-procurement', [App\Http\Controllers\StockReplenishmentController::class, 'sendToProcurement']);
    Route::post('/auto-generate-requests', [App\Http\Controllers\StockReplenishmentController::class, 'autoGenerateRequests']);
});

// Contract API routes (public for demo) - REMOVED TO AVOID CONFLICTS

// PSM Bidding API routes (public for demo)
Route::post('/psm/bidding/ai-analysis', [App\Http\Controllers\PSMBiddingController::class, 'aiAnalysis']);
Route::get('/psm/bidding/bids', [App\Http\Controllers\PSMBiddingController::class, 'getBids']);
Route::get('/psm/bidding/bids/{id}', [App\Http\Controllers\PSMBiddingController::class, 'getBid']);
Route::post('/psm/bidding/bids/{id}/select-winner', [App\Http\Controllers\PSMBiddingController::class, 'selectWinner']);
Route::post('/psm/bidding/bids/{id}/reject', [App\Http\Controllers\PSMBiddingController::class, 'rejectBid']);
Route::post('/psm/bidding/bids/{id}/start-evaluation', [App\Http\Controllers\PSMBiddingController::class, 'startEvaluation']);
Route::get('/psm/bidding/export', [App\Http\Controllers\PSMBiddingController::class, 'exportBids']);

// Real-time monitoring API routes
Route::get('/psm/bidding/bid-count', [App\Http\Controllers\PSMBiddingController::class, 'getBidCount']);
Route::get('/psm/bidding/recent-bids', [App\Http\Controllers\PSMBiddingController::class, 'getRecentBids']);

// PSM Opportunities API routes (public for demo)
Route::get('/psm/opportunities/{id}', [App\Http\Controllers\PSMBiddingController::class, 'getOpportunity']);
Route::put('/psm/opportunities/{id}', [App\Http\Controllers\PSMBiddingController::class, 'updateOpportunity']);
Route::delete('/psm/opportunities/{id}', [App\Http\Controllers\PSMBiddingController::class, 'deleteOpportunity']);

// AI-powered bid analysis routes
Route::post('/psm/bidding/ai/recommendations', [App\Http\Controllers\PSMBiddingController::class, 'getAiRecommendations']);
Route::post('/psm/bidding/ai/predict-winner', [App\Http\Controllers\PSMBiddingController::class, 'predictWinner']);
Route::post('/psm/bidding/ai/compare-bids', [App\Http\Controllers\PSMBiddingController::class, 'compareBids']);
Route::get('/psm/bidding/ai/analyze-bid/{id}', [App\Http\Controllers\PSMBiddingController::class, 'analyzeBidWithAi']);
Route::get('/psm/bidding/ai/model-performance', [App\Http\Controllers\PSMBiddingController::class, 'getModelPerformance']);
Route::post('/psm/bidding/ai/retrain-models', [App\Http\Controllers\PSMBiddingController::class, 'retrainModels']);
Route::post('/psm/bidding/ai/generate-sample-data', [App\Http\Controllers\PSMBiddingController::class, 'generateSampleData']);

// Asset Management API routes
Route::get('/assets', [App\Http\Controllers\ALMS\AssetRegistrationController::class, 'apiIndex']);
Route::get('/assets/{id}', [App\Http\Controllers\ALMS\AssetRegistrationController::class, 'apiShow']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/profile', [AuthController::class, 'profile']);
    
    // You can add more protected routes here
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
