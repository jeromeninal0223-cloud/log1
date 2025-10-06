<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\ItemRequest;

class StockReplenishmentController extends Controller
{
    public function index()
    {
        // Get low stock items from database
        $lowStockItems = InventoryItem::active()
            ->lowStock()
            ->orderBy('current_stock', 'asc')
            ->get();

        // Get recent purchase requests
        $recentRequests = ItemRequest::with('requestedBy', 'stockItem')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Calculate statistics
        $totalLowStockItems = $lowStockItems->count();
        $criticalItems = $lowStockItems->filter(function($item) {
            return $item->isCriticallyLow();
        })->count();
        
        $pendingRequests = ItemRequest::where('status', 'PENDING')->count();
        
        $totalEstimatedCost = $lowStockItems->sum(function($item) {
            return $item->unit_price * $item->reorder_quantity;
        });

        $stats = [
            'pending_replenishment' => $totalLowStockItems,
            'pending_requests' => $pendingRequests,
            'critical_items' => $criticalItems,
            'total_estimated_cost' => $totalEstimatedCost,
        ];

        return view('SWS.stockreplenishment', compact('lowStockItems', 'recentRequests', 'stats'));
    }

    public function getLowStockItems()
    {
        $items = InventoryItem::active()->lowStock()->get();
        return response()->json(['items' => $items]);
    }

    public function createPurchaseRequest(Request $request)
    {
        try {
            $request->validate([
                'item_id' => 'required|exists:inventory_items,id'
            ]);

            $inventoryItem = InventoryItem::findOrFail($request->item_id);
            
            // Create item request
            $itemRequest = ItemRequest::create([
                'item_name' => $inventoryItem->name,
                'asset_code' => $inventoryItem->item_code,
                'category' => $inventoryItem->category,
                'storage_location' => $inventoryItem->storage_location ?? '',
                'requested_quantity' => $inventoryItem->reorder_quantity,
                'available_quantity' => $inventoryItem->current_stock,
                'picked_quantity' => 0,
                'priority' => $inventoryItem->isCriticallyLow() ? 'HIGH' : 'MEDIUM',
                'status' => 'PENDING',
                'requested_by' => auth()->id() ?? 1, // Default to user ID 1 if not authenticated
                'notes' => 'Auto-generated from stock replenishment'
            ]);

            return response()->json([
                'success' => true, 
                'message' => 'Purchase request created successfully',
                'request_id' => $itemRequest->id
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating purchase request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkCreatePurchaseRequests(Request $request)
    {
        try {
            $request->validate([
                'item_ids' => 'required|array',
                'item_ids.*' => 'exists:inventory_items,id'
            ]);

            $createdRequests = [];
            
            foreach ($request->item_ids as $itemId) {
                $inventoryItem = InventoryItem::findOrFail($itemId);
                
                // Check if request already exists
                if (!$inventoryItem->hasPendingPurchaseRequest()) {
                    $itemRequest = ItemRequest::create([
                        'item_name' => $inventoryItem->name,
                        'asset_code' => $inventoryItem->item_code,
                        'category' => $inventoryItem->category,
                        'storage_location' => $inventoryItem->storage_location ?? '',
                        'requested_quantity' => $inventoryItem->reorder_quantity,
                        'available_quantity' => $inventoryItem->current_stock,
                        'picked_quantity' => 0,
                        'priority' => $inventoryItem->isCriticallyLow() ? 'HIGH' : 'MEDIUM',
                        'status' => 'PENDING',
                        'requested_by' => auth()->id() ?? 1,
                        'notes' => 'Bulk auto-generated from stock replenishment'
                    ]);
                    
                    $createdRequests[] = $itemRequest->id;
                }
            }

            return response()->json([
                'success' => true, 
                'message' => count($createdRequests) . ' purchase requests created successfully',
                'created_requests' => $createdRequests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating bulk purchase requests: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approvePurchaseRequest(Request $request)
    {
        try {
            $request->validate([
                'request_id' => 'required|exists:item_requests,id'
            ]);

            $itemRequest = ItemRequest::findOrFail($request->request_id);
            $itemRequest->update(['status' => 'COMPLETED']);

            return response()->json([
                'success' => true, 
                'message' => 'Purchase request approved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error approving purchase request: ' . $e->getMessage()
            ], 500);
        }
    }

    public function sendToProcurement(Request $request)
    {
        try {
            $request->validate([
                'request_id' => 'required|exists:item_requests,id'
            ]);

            $itemRequest = ItemRequest::findOrFail($request->request_id);
            $itemRequest->update(['status' => 'IN_PROGRESS']);

            return response()->json([
                'success' => true, 
                'message' => 'Request sent to procurement successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error sending to procurement: ' . $e->getMessage()
            ], 500);
        }
    }

    public function autoGenerateRequests(Request $request)
    {
        try {
            $lowStockItems = InventoryItem::active()->lowStock()->get();
            $createdRequests = [];
            
            foreach ($lowStockItems as $item) {
                // Only create if no pending request exists
                if (!$item->hasPendingPurchaseRequest()) {
                    $itemRequest = ItemRequest::create([
                        'item_name' => $item->name,
                        'asset_code' => $item->item_code,
                        'category' => $item->category,
                        'storage_location' => $item->storage_location ?? '',
                        'requested_quantity' => $item->reorder_quantity,
                        'available_quantity' => $item->current_stock,
                        'picked_quantity' => 0,
                        'priority' => $item->isCriticallyLow() ? 'HIGH' : 'MEDIUM',
                        'status' => 'PENDING',
                        'requested_by' => auth()->id() ?? 1,
                        'notes' => 'Auto-generated from stock replenishment system'
                    ]);
                    
                    $createdRequests[] = $itemRequest->id;
                }
            }

            return response()->json([
                'success' => true, 
                'message' => count($createdRequests) . ' purchase requests auto-generated successfully',
                'created_requests' => $createdRequests
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error auto-generating requests: ' . $e->getMessage()
            ], 500);
        }
    }
}
