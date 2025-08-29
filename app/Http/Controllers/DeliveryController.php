<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryController extends Controller
{
    public function index()
    {
        // Get all purchase orders that are in delivery stages
        $deliveryOrders = PurchaseOrder::with(['contract', 'vendor', 'creator'])
            ->whereIn('status', ['Issued', 'In Progress', 'Completed'])
            ->latest()
            ->get();

        // Get delivery statistics
        $stats = [
            'pending_receipts' => PurchaseOrder::whereIn('status', ['Issued', 'In Progress'])->count(),
            'completed_today' => PurchaseOrder::where('status', 'Completed')
                ->whereDate('actual_delivery_date', today())
                ->count(),
            'quality_issues' => 0, // This could be expanded with a separate table
            'avg_process_time' => '45min', // This could be calculated from actual data
        ];

        return view('PSM.delivery', compact('deliveryOrders', 'stats'));
    }

    public function updateDeliveryStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'status' => 'required|in:Issued,In Progress,Completed,Cancelled',
            'actual_delivery_date' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $purchaseOrder->update([
            'status' => $request->status,
            'actual_delivery_date' => $request->status === 'Completed' ? $request->actual_delivery_date : null,
            'notes' => $request->notes,
        ]);

        // Auto-generate invoice when admin marks order as Completed (if none exists yet)
        if ($request->status === 'Completed') {
            $purchaseOrder->loadMissing('vendor');
            $hasInvoice = Invoice::where('po_number', $purchaseOrder->po_number)->exists();
            if (!$hasInvoice) {
                Invoice::create([
                    'invoice_no' => 'INV-' . ($purchaseOrder->po_number ?: str_pad((string) $purchaseOrder->id, 6, '0', STR_PAD_LEFT)),
                    'vendor_id' => optional($purchaseOrder->vendor)->id,
                    'vendor_name' => optional($purchaseOrder->vendor)->company_name ?: optional($purchaseOrder->vendor)->name,
                    'po_number' => $purchaseOrder->po_number,
                    'amount' => (float) ($purchaseOrder->total_amount ?? 0),
                    'status' => 'Submitted',
                    'payment_status' => 'Unpaid',
                    'issued_date' => now()->toDateString(),
                    'due_date' => now()->addDays(30)->toDateString(),
                    'notes' => 'Auto-generated from completed PO #' . ($purchaseOrder->po_number ?? $purchaseOrder->id),
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Delivery status updated successfully',
            'purchase_order' => $purchaseOrder->fresh()
        ]);
    }

    public function getDeliveryDetails(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['contract', 'vendor', 'creator', 'items']);
        
        return response()->json([
            'success' => true,
            'purchase_order' => $purchaseOrder
        ]);
    }
}
