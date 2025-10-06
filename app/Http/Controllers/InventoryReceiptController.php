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
        ));
    }

    public function store(Request $request)
    {
        try {
            \Log::info('Store receipt request received');
            \Log::info('Request method: ' . $request->method());
            \Log::info('Request headers: ' . json_encode($request->headers->all()));
            
            // Parse items from JSON string if it's FormData
            $items = $request->input('items');
            \Log::info('Items input: ' . ($items ? (is_string($items) ? $items : json_encode($items)) : 'null'));
            
            if (is_string($items)) {
                $items = json_decode($items, true);
                $request->merge(['items' => $items]);
                \Log::info('Items parsed from JSON');
            }
        } catch (\Exception $e) {
            \Log::error('Error in store method start: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error: ' . $e->getMessage()], 500);
        }

        try {
            $request->validate([
                'receipt_date' => 'required|date',
                'supplier_name' => 'required|string',
                'purchase_order_number' => 'nullable|string',
                'delivery_date' => 'nullable|date',
                'invoice_number' => 'nullable|string',
                'warehouse_location' => 'required|string',
                'received_by' => 'required|string',
                'notes' => 'nullable|string',
                'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string',
            'items.*.description' => 'nullable|string',
            'items.*.category' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.damaged_quantity' => 'nullable|integer|min:0',
            'items.*.unit' => 'required|string',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.condition' => 'required|string',
            'items.*.storage_location' => 'required|string',
            'items.*.image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'items.*.damage_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json(['success' => false, 'message' => 'Validation failed', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Error in validation: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error during validation: ' . $e->getMessage()], 500);
        }

        try {
            DB::beginTransaction();

            // Create receipt
            $receiptNumber = InventoryReceipt::generateReceiptNumber();
            if (!$receiptNumber) {
                $receiptNumber = 'REC-' . time(); // Fallback receipt number
            }
            
            $receipt = InventoryReceipt::create([
                'receipt_number' => $receiptNumber,
                'receipt_date' => $request->receipt_date,
                'supplier_name' => $request->supplier_name,
                'purchase_order_number' => $request->purchase_order_number,
                'delivery_date' => $request->delivery_date,
                'invoice_number' => $request->invoice_number,
                'warehouse_location' => $request->warehouse_location,
                'received_by' => $request->received_by,
                'notes' => $request->notes,
                'created_by' => Auth::id() ?? 1, // Fallback to user ID 1 if no auth
                'purchase_order_id' => $this->getPurchaseOrderId($request->purchase_order_number),
            ]);

            // Create receipt items
            foreach ($request->items as $index => $itemData) {
                $totalPrice = ($itemData['quantity'] ?? 0) * ($itemData['unit_price'] ?? 0);
                
                // Handle item image
                $imagePath = null;
                $imageName = null;
                $imageSize = null;
                
                if ($request->hasFile("items.{$index}.image")) {
                    $image = $request->file("items.{$index}.image");
                    $imageName = time() . '_item_' . $image->getClientOriginalName();
                    $imagePath = $image->storeAs('inventory_items', $imageName, 'public');
                    $imageSize = $image->getSize();
                    \Log::info("Item image uploaded: " . $imagePath);
                }

                // Handle damage image
                $damageImagePath = null;
                $damageImageName = null;
                $damageImageSize = null;
                
                if ($request->hasFile("items.{$index}.damage_image")) {
                    $damageImage = $request->file("items.{$index}.damage_image");
                    $damageImageName = time() . '_damage_' . $damageImage->getClientOriginalName();
                    $damageImagePath = $damageImage->storeAs('inventory_damage', $damageImageName, 'public');
                    $damageImageSize = $damageImage->getSize();
                    \Log::info("Damage image uploaded: " . $damageImagePath);
                } else {
                    \Log::info("No damage image found for item index: " . $index);
                }
                
                $damagedQty = $itemData['damaged_quantity'] ?? 0;
                $receiptItemData = [
                    'item_name' => $itemData['item_name'],
                    'description' => $itemData['description'] ?? null,
                    'quantity' => $itemData['quantity'],
                    'damaged_quantity' => $damagedQty,
                    'damage_reason' => $damagedQty > 0 ? ($itemData['damage_reason'] ?? 'Damaged on receipt') : null,
                    'return_to_vendor' => $damagedQty > 0,
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'] ?? 0,
                    'total_price' => $totalPrice,
                    'condition' => $damagedQty > 0 ? 'Partial Damage' : ($itemData['condition'] ?? 'Good'),
                    'storage_location' => $itemData['storage_location'],
                    'batch_number' => $itemData['batch_number'] ?? null,
                    'expiry_date' => $itemData['expiry_date'] ?? null,
                    'item_notes' => $itemData['item_notes'] ?? null,
                ];

                // Add image fields only if they have values
                if ($imagePath) {
                    $receiptItemData['image_path'] = $imagePath;
                    $receiptItemData['image_name'] = $imageName;
                    $receiptItemData['image_size'] = $imageSize;
                }

                // Add damage image fields only if they have values
                if ($damageImagePath) {
                    $receiptItemData['damage_image_path'] = $damageImagePath;
                    $receiptItemData['damage_image_name'] = $damageImageName;
                    $receiptItemData['damage_image_size'] = $damageImageSize;
                }

                $receiptItem = $receipt->items()->create($receiptItemData);

                // Update or create inventory item
                $this->updateInventoryItem($receiptItem, $itemData['category'] ?? 'General');
            }

            // Update receipt totals
            $receipt->updateTotals();
            
            // Complete the receipt and generate document
            $receipt->complete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Inventory receipt created and completed successfully',
                'receipt_id' => $receipt->id,
                'receipt_number' => $receipt->receipt_number,
                'document_generated' => !empty($receipt->document_path)
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the full error for debugging
            \Log::error('Inventory Receipt Creation Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error creating receipt: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
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

    public function completeReceipt(Request $request, $id)
    {
        try {
            $receipt = InventoryReceipt::findOrFail($id);
            
            if ($receipt->status === 'Completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt is already completed'
                ], 400);
            }
            
            // Complete the receipt and generate document
            $receipt->complete();
            
            return response()->json([
                'success' => true,
                'message' => 'Receipt completed successfully and document generated',
                'receipt_number' => $receipt->receipt_number,
                'document_generated' => !empty($receipt->document_path),
                'document_path' => $receipt->document_path
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error completing receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateDocument(Request $request, $id)
    {
        try {
            $receipt = InventoryReceipt::findOrFail($id);
            
            // Generate document
            $receipt->generateDocument();
            
            return response()->json([
                'success' => true,
                'message' => 'Document generated successfully',
                'receipt_number' => $receipt->receipt_number,
                'document_path' => $receipt->document_path
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating document: ' . $e->getMessage()
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

        // Get vendor by company name (try exact match first, then partial match)
        $vendor = Vendor::where('company_name', $supplierName)->first();
        
        if (!$vendor) {
            // Try partial match or case-insensitive match
            $vendor = Vendor::where('company_name', 'LIKE', "%{$supplierName}%")
                           ->orWhere('name', 'LIKE', "%{$supplierName}%")
                           ->first();
        }
        
        if (!$vendor) {
            return response()->json([
                'success' => true,
                'purchase_orders' => [],
                'invoices' => [],
                'debug' => [
                    'supplier_name' => $supplierName,
                    'vendor_found' => false,
                    'total_vendors' => Vendor::count(),
                    'available_vendors' => Vendor::pluck('company_name')->toArray()
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

        // Get invoices for this vendor (both tied to POs and standalone)
        $invoices = \App\Models\Invoice::where(function($query) use ($supplierName, $vendor) {
            $query->where('vendor_name', $supplierName)
                  ->orWhere('vendor_name', 'LIKE', "%{$supplierName}%")
                  ->orWhere('vendor_id', $vendor->id);
        })->get();

        // Also get all invoices for this vendor for debugging
        $allInvoices = $invoices;

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
                    'due_date' => $invoice->due_date?->format('Y-m-d'),
                    'purchase_order_number' => $invoice->po_number ?? $invoice->purchase_order_number ?? null
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

    private function getPurchaseOrderId($poNumber)
    {
        if (!$poNumber) {
            return null;
        }

        $purchaseOrder = PurchaseOrder::where('po_number', $poNumber)->first();
        return $purchaseOrder ? $purchaseOrder->id : null;
    }

    private function updateInventoryItem($receiptItem, $category = 'General')
    {
        // Find existing inventory item by name and supplier
        $inventoryItem = InventoryItem::where('name', $receiptItem->item_name)
            ->where('supplier', $receiptItem->receipt->supplier_name)
            ->first();

        if ($inventoryItem) {
            // Update existing item - only add good quantity to inventory
            $goodQuantity = $receiptItem->quantity - ($receiptItem->damaged_quantity ?? 0);
            if ($goodQuantity > 0) {
                $inventoryItem->addStock($goodQuantity);
            }
            if ($receiptItem->unit_price > 0) {
                $inventoryItem->update([
                    'unit_price' => $receiptItem->unit_price,
                    'storage_location' => $receiptItem->storage_location, // Update location
                ]);
            }
        } else {
            // Check if item with same name exists from different supplier
            $existingItem = InventoryItem::where('name', $receiptItem->item_name)->first();
            
            if ($existingItem) {
                // Create variant with supplier suffix
                $itemName = $receiptItem->item_name . ' (' . $receiptItem->receipt->supplier_name . ')';
            } else {
                $itemName = $receiptItem->item_name;
            }
            
            // Create new inventory item with retry logic for item code
            $maxRetries = 5;
            $retryCount = 0;
            
            while ($retryCount < $maxRetries) {
                try {
                    InventoryItem::create([
                        'item_code' => InventoryItem::generateItemCode(),
                        'name' => $itemName,
                        'description' => $receiptItem->description,
                        'category' => $category,
                        'supplier' => $receiptItem->receipt->supplier_name,
                        'current_stock' => $receiptItem->quantity - ($receiptItem->damaged_quantity ?? 0),
                        'minimum_stock' => 10, // Default minimum
                        'reorder_quantity' => 50, // Default reorder
                        'unit_of_measure' => $receiptItem->unit,
                        'unit_price' => $receiptItem->unit_price ?? 0,
                        'storage_location' => $receiptItem->storage_location,
                        'status' => 'Active',
                    ]);
                    break; // Success, exit retry loop
                } catch (\Illuminate\Database\QueryException $e) {
                    if ($e->errorInfo[1] == 1062) { // Duplicate entry error
                        $retryCount++;
                        if ($retryCount >= $maxRetries) {
                            // Final fallback - use timestamp in item code
                            $fallbackCode = 'ITM-' . time() . '-' . rand(100, 999);
                            InventoryItem::create([
                                'item_code' => $fallbackCode,
                                'name' => $itemName,
                                'description' => $receiptItem->description,
                                'category' => $category,
                                'supplier' => $receiptItem->receipt->supplier_name,
                                'current_stock' => $receiptItem->quantity - ($receiptItem->damaged_quantity ?? 0),
                                'minimum_stock' => 10,
                                'reorder_quantity' => 50,
                                'unit_of_measure' => $receiptItem->unit,
                                'unit_price' => $receiptItem->unit_price ?? 0,
                                'storage_location' => $receiptItem->storage_location,
                                'status' => 'Active',
                            ]);
                            break;
                        }
                        // Wait a bit before retry
                        usleep(100000); // 0.1 second
                    } else {
                        throw $e; // Re-throw if it's not a duplicate key error
                    }
                }
            }
        }
    }
}
