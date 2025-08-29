<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        try {
            // Check if tables exist first
            if (!Schema::hasTable('purchase_orders')) {
                return view('PSM.order', [
                    'purchaseOrders' => collect(),
                    'stats' => [
                        'total' => 0,
                        'pending_approval' => 0,
                        'approved' => 0,
                        'in_progress' => 0,
                        'completed' => 0,
                    ]
                ]);
            }

            // Get purchase orders with eager loading
            $purchaseOrders = PurchaseOrder::with(['contract', 'vendor', 'creator'])
                ->latest()
                ->paginate(15);

            // Get stats efficiently
            $stats = [
                'total' => PurchaseOrder::count(),
                'pending_approval' => PurchaseOrder::where('status', 'Pending Approval')->count(),
                'approved' => PurchaseOrder::where('status', 'Approved')->count(),
                'in_progress' => PurchaseOrder::whereIn('status', ['Issued', 'In Progress'])->count(),
                'completed' => PurchaseOrder::where('status', 'Completed')->count(),
            ];

            return view('PSM.order', compact('purchaseOrders', 'stats'));
            
        } catch (\Exception $e) {
            // Log the error and return empty data
            Log::error('Error in PurchaseOrderController@index: ' . $e->getMessage());
            
            return view('PSM.order', [
                'purchaseOrders' => collect(),
                'stats' => [
                    'total' => 0,
                    'pending_approval' => 0,
                    'approved' => 0,
                    'in_progress' => 0,
                    'completed' => 0,
                ]
            ]);
        }
    }



    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'contract_id' => 'required|exists:contracts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'delivery_address' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'currency' => 'required|string|max:3',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.specifications' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $contract = Contract::findOrFail($request->contract_id);
            
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => PurchaseOrder::generatePoNumber(),
                'contract_id' => $contract->id,
                'vendor_id' => $contract->vendor_id,
                'title' => $request->title,
                'description' => $request->description,
                'total_amount' => 0, // Will be calculated
                'status' => 'Draft',
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'delivery_address' => $request->delivery_address,
                'payment_terms' => $request->payment_terms,
                'currency' => $request->currency,
                'notes' => $request->notes,
                'created_by' => Auth::id(),
            ]);

            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                $item = PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_name' => $itemData['item_name'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'specifications' => $itemData['specifications'],
                ]);

                $totalAmount += $item->total_price;
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order created successfully',
                'purchase_order' => $purchaseOrder->load(['contract', 'vendor', 'items'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create Purchase Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['contract', 'vendor', 'creator', 'approver', 'items']);
        
        return view('PSM.order-show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['Draft'])) {
            return redirect()->route('psm.order.show', $purchaseOrder)
                ->with('error', 'Only draft orders can be edited');
        }

        $contracts = Contract::with(['vendor', 'bid'])
            ->where('status', 'Active')
            ->get();

        $purchaseOrder->load(['contract', 'vendor', 'items']);

        return view('PSM.order-edit', compact('purchaseOrder', 'contracts'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['Draft'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only draft orders can be edited'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'contract_id' => 'required|exists:contracts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after:order_date',
            'delivery_address' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'currency' => 'required|string|max:3',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.description' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.specifications' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $contract = Contract::findOrFail($request->contract_id);
            
            $purchaseOrder->update([
                'contract_id' => $contract->id,
                'vendor_id' => $contract->vendor_id,
                'title' => $request->title,
                'description' => $request->description,
                'order_date' => $request->order_date,
                'expected_delivery_date' => $request->expected_delivery_date,
                'delivery_address' => $request->delivery_address,
                'payment_terms' => $request->payment_terms,
                'currency' => $request->currency,
                'notes' => $request->notes,
            ]);

            // Delete existing items and recreate
            $purchaseOrder->items()->delete();

            $totalAmount = 0;

            foreach ($request->items as $itemData) {
                $item = PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_name' => $itemData['item_name'],
                    'description' => $itemData['description'],
                    'quantity' => $itemData['quantity'],
                    'unit' => $itemData['unit'],
                    'unit_price' => $itemData['unit_price'],
                    'specifications' => $itemData['specifications'],
                ]);

                $totalAmount += $item->total_price;
            }

            $purchaseOrder->update(['total_amount' => $totalAmount]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order updated successfully',
                'purchase_order' => $purchaseOrder->load(['contract', 'vendor', 'items'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update Purchase Order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function submitForApproval(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft orders can be submitted for approval'
            ], 422);
        }

        $purchaseOrder->update(['status' => 'Pending Approval']);

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order submitted for approval',
            'purchase_order' => $purchaseOrder->fresh()
        ]);
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Pending Approval') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending approval orders can be approved'
            ], 422);
        }

        $purchaseOrder->approve(Auth::user());

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order approved successfully',
            'purchase_order' => $purchaseOrder->fresh()
        ]);
    }

    public function issue(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved orders can be issued'
            ], 422);
        }

        $purchaseOrder->issue();

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order issued successfully',
            'purchase_order' => $purchaseOrder->fresh()
        ]);
    }

    public function getContracts()
    {
        $contracts = Contract::with(['vendor', 'bid'])
            ->where('status', 'Active')
            // Exclude expired contracts
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            // Exclude contracts that already have a completed purchase order
            ->whereDoesntHave('purchaseOrders', function ($q) {
                $q->where('status', 'Completed');
            })
            ->get()
            ->map(function ($contract) {
                return [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'title' => $contract->title,
                    'vendor_name' => $contract->vendor->company_name ?? $contract->vendor->name,
                    'value' => $contract->value,
                    'start_date' => $contract->start_date?->format('Y-m-d'),
                    'end_date' => $contract->end_date?->format('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'contracts' => $contracts
        ]);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['Draft'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only draft orders can be deleted'
            ], 422);
        }

        $purchaseOrder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order deleted successfully'
        ]);
    }
}
