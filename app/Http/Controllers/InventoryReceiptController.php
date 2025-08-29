<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryReceipt;
use App\Models\InventoryReceiptItem;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InventoryReceiptController extends Controller
{
    public function index()
    {
        // Get real data from database
        $todayReceipts = InventoryReceipt::today()->count();
        $weekReceipts = InventoryReceipt::thisWeek()->count();
        $totalValue = InventoryItem::active()->sum(DB::raw('current_stock * unit_price'));
        
        // Get recent receipts with items
        $recentReceipts = InventoryReceipt::with(['items', 'purchaseOrder'])
            ->latest()
            ->take(10)
            ->get();
        
        // Get suppliers for dropdown
        $suppliers = Vendor::pluck('company_name', 'company_name')->toArray();
        
        // Get purchase orders for linking
        $purchaseOrders = PurchaseOrder::whereIn('status', ['Approved', 'Issued', 'In Progress'])
            ->with('vendor')
            ->get();
        
        return view('SWS.inventoryreceipt', compact(
            'todayReceipts', 
            'weekReceipts', 
            'totalValue', 
            'recentReceipts',
            'suppliers',
            'purchaseOrders'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'receipt_date' => 'required|date',
            'supplier_name' => 'required|string',
            'purchase_order_number' => 'nullable|string',
            'delivery_date' => 'required|date',
            'invoice_number' => 'nullable|string',
            'warehouse_location' => 'required|string',
            'received_by' => 'required|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.condition' => 'required|string',
            'items.*.storage_location' => 'required|string',
            'items.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // Create receipt
            $receipt = InventoryReceipt::create([
                'receipt_number' => InventoryReceipt::generateReceiptNumber(),
                'receipt_date' => $request->receipt_date,
                'supplier_name' => $request->supplier_name,
                'purchase_order_number' => $request->purchase_order_number,
                'delivery_date' => $request->delivery_date,
                'invoice_number' => $request->invoice_number,
                'warehouse_location' => $request->warehouse_location,
                'received_by' => $request->received_by,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
                'purchase_order_id' => $this->getPurchaseOrderId($request->purchase_order_number),
            ]);

            // Create receipt items
            foreach ($request->items as $index => $itemData) {
                $totalPrice = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                
                // Handle image upload
                $imagePath = null;
                $imageName = null;
                $imageSize = null;
                
                if ($request->hasFile("items.{$index}.image")) {
                    $image = $request->file("items.{$index}.image");
                    $imageName = time() . '_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('inventory_items', $imageName, 'public');
                    $imageSize = $image->getSize();
                }
                
                $receiptItem = $receipt->items()->create([
                    'item_name' => $itemData['item_name'],
                    'description' => $itemData['description'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'total_price' => $totalPrice,
                    'condition' => $itemData['condition'],
                    'storage_location' => $itemData['storage_location'],
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'item_notes' => $itemData['item_notes'] ?? null,
                    'image_path' => $imagePath,
                    'image_name' => $imageName,
                    'image_size' => $imageSize,
                ]);

                // Update or create inventory item
                $this->updateInventoryItem($receiptItem);
            }

            // Update receipt totals
            $receipt->updateTotals();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inventory receipt created successfully',
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error creating receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    public function addItem(Request $request)
    {
        $request->validate([
            'item_code' => 'required|string|unique:inventory_items,item_code',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'supplier' => 'nullable|string',
            'quantity_received' => 'required|integer|min:1',
            'unit_of_measure' => 'required|string',
            'unit_price' => 'required|numeric|min:0',
            'minimum_stock' => 'required|integer|min:0',
            'reorder_quantity' => 'required|integer|min:1',
            'batch_number' => 'nullable|string',
            'expiry_date' => 'nullable|date',
        ]);

        try {
            $item = InventoryItem::create([
                'item_code' => $request->item_code,
                'name' => $request->name,
                'description' => $request->description,
                'category' => $request->category,
                'supplier' => $request->supplier,
                'current_stock' => $request->quantity_received,
                'minimum_stock' => $request->minimum_stock,
                'reorder_quantity' => $request->reorder_quantity,
                'unit_of_measure' => $request->unit_of_measure,
                'unit_price' => $request->unit_price,
                'storage_location' => 'receiving_area', // Default location
                'status' => 'Active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item added to inventory successfully',
                'item' => $item
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateStock(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_items,id',
            'quantity' => 'required|integer|min:1',
            'action' => 'required|in:add,remove',
        ]);

        try {
            $item = InventoryItem::findOrFail($request->item_id);

            if ($request->action === 'add') {
                $item->addStock($request->quantity);
                $message = 'Stock added successfully';
            } else {
                $item->removeStock($request->quantity);
                $message = 'Stock removed successfully';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'current_stock' => $item->current_stock
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating stock: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getRecentReceipts()
    {
        $receipts = InventoryReceipt::with(['items', 'purchaseOrder'])
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'success' => true,
            'receipts' => $receipts
        ]);
    }

    public function getDashboardStats()
    {
        $stats = [
            'items_received_today' => InventoryReceipt::today()->sum('total_quantity'),
            'items_received_week' => InventoryReceipt::thisWeek()->sum('total_quantity'),
            'items_awaiting_storage' => InventoryReceipt::pending()->sum('total_quantity'),
            'total_inventory_value' => InventoryItem::active()->sum(DB::raw('current_stock * unit_price')),
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    public function getPurchaseOrdersBySupplier(Request $request)
    {
        $supplierName = $request->get('supplier');
        
        if (!$supplierName) {
            return response()->json([
                'success' => false,
                'message' => 'Supplier name is required'
            ]);
        }

        // Get vendor by company name
        $vendor = Vendor::where('company_name', $supplierName)->first();
        
        if (!$vendor) {
            return response()->json([
                'success' => true,
                'purchase_orders' => [],
                'invoices' => [],
                'debug' => [
                    'supplier_name' => $supplierName,
                    'vendor_found' => false,
                    'total_vendors' => Vendor::count()
                ]
            ]);
        }

        // Get completed purchase orders for this vendor (ready for receipt)
        $purchaseOrders = PurchaseOrder::where('vendor_id', $vendor->id)
            ->where('status', 'Completed')
            ->with('vendor')
            ->get();

        // Also get purchase orders with other statuses for debugging
        $allPurchaseOrders = PurchaseOrder::where('vendor_id', $vendor->id)->get();

        // Get invoices for this vendor that are tied to completed purchase orders
        $invoices = \App\Models\Invoice::where('vendor_name', $supplierName)
            ->orWhere('vendor_id', $vendor->id)
            ->whereIn('po_number', $purchaseOrders->pluck('po_number'))
            ->get();

        // Also get all invoices for this vendor for debugging
        $allInvoices = \App\Models\Invoice::where('vendor_name', $supplierName)
            ->orWhere('vendor_id', $vendor->id)
            ->get();

        return response()->json([
            'success' => true,
            'purchase_orders' => $purchaseOrders->map(function($po) {
                return [
                    'po_number' => $po->po_number,
                    'title' => $po->title,
                    'total_amount' => $po->total_amount,
                    'expected_delivery_date' => $po->expected_delivery_date?->format('Y-m-d'),
                    'status' => $po->status
                ];
            }),
            'invoices' => $invoices->map(function($invoice) {
                return [
                    'invoice_no' => $invoice->invoice_no,
                    'amount' => $invoice->amount,
                    'status' => $invoice->status,
                    'issued_date' => $invoice->issued_date?->format('Y-m-d'),
                    'due_date' => $invoice->due_date?->format('Y-m-d')
                ];
            }),
            'debug' => [
                'supplier_name' => $supplierName,
                'vendor_found' => true,
                'vendor_id' => $vendor->id,
                'completed_pos_count' => $purchaseOrders->count(),
                'all_pos_count' => $allPurchaseOrders->count(),
                'all_pos_statuses' => $allPurchaseOrders->pluck('status', 'po_number'),
                'invoices_count' => $invoices->count(),
                'all_invoices_count' => $allInvoices->count()
            ]
        ]);
    }

    public function getPurchaseOrderItems(Request $request)
    {
        $poNumber = $request->get('po_number');
        
        if (!$poNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order number is required'
            ]);
        }

        // Get purchase order with items
        $purchaseOrder = PurchaseOrder::where('po_number', $poNumber)
            ->with(['items', 'vendor'])
            ->first();
        
        if (!$purchaseOrder) {
            return response()->json([
                'success' => false,
                'message' => 'Purchase order not found'
            ]);
        }

        // Get items that are already in storage (have assigned storage locations other than receiving area)
        $itemsInStorage = InventoryReceiptItem::whereHas('receipt', function($query) use ($purchaseOrder) {
            $query->where('purchase_order_id', $purchaseOrder->id);
        })
        ->whereNotNull('storage_location')
        ->where('storage_location', '!=', 'receiving_area')
        ->where('storage_location', 'NOT LIKE', '%receiving%')
        ->pluck('item_name')
        ->toArray();

        return response()->json([
            'success' => true,
            'purchase_order' => [
                'po_number' => $purchaseOrder->po_number,
                'title' => $purchaseOrder->title,
                'vendor_name' => $purchaseOrder->vendor->name,
                'expected_delivery_date' => $purchaseOrder->expected_delivery_date?->format('Y-m-d'),
                'status' => $purchaseOrder->status
            ],
            'items' => $purchaseOrder->items->map(function($item) use ($itemsInStorage) {
                return [
                    'item_name' => $item->item_name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit' => $item->unit,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                    'specifications' => $item->specifications,
                    'in_storage' => in_array($item->item_name, $itemsInStorage),
                    'status' => in_array($item->item_name, $itemsInStorage) ? 'In Storage' : 'Available'
                ];
            })
        ]);
    }

    public function completeReceipt($id)
    {
        try {
            $receipt = InventoryReceipt::findOrFail($id);
            $receipt->complete();

            return response()->json([
                'success' => true,
                'message' => 'Receipt completed successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPurchaseOrderId($poNumber)
    {
        if (!$poNumber) {
            return null;
        }

        $purchaseOrder = PurchaseOrder::where('po_number', $poNumber)->first();
        return $purchaseOrder ? $purchaseOrder->id : null;
    }

    private function updateInventoryItem($receiptItem)
    {
        // Find existing inventory item or create new one
        $inventoryItem = InventoryItem::where('name', $receiptItem->item_name)
            ->where('category', 'General') // Default category
            ->first();

        if ($inventoryItem) {
            // Update existing item
            $inventoryItem->addStock($receiptItem->quantity);
            if ($receiptItem->unit_price > 0) {
                $inventoryItem->update(['unit_price' => $receiptItem->unit_price]);
            }
        } else {
            // Create new inventory item
            InventoryItem::create([
                'item_code' => InventoryItem::generateItemCode(),
                'name' => $receiptItem->item_name,
                'description' => $receiptItem->description,
                'category' => 'General',
                'supplier' => $receiptItem->receipt->supplier_name,
                'current_stock' => $receiptItem->quantity,
                'minimum_stock' => 10, // Default minimum
                'reorder_quantity' => 50, // Default reorder
                'unit_of_measure' => $receiptItem->unit,
                'unit_price' => $receiptItem->unit_price,
                'storage_location' => $receiptItem->storage_location,
                'status' => 'Active',
            ]);
        }
    }
}
