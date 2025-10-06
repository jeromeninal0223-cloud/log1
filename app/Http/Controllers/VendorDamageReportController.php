<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryReceiptItem;
use App\Models\InventoryReceipt;
use App\Models\Vendor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorDamageReportController extends Controller
{
    public function index(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        
        if (!$vendor) {
            return redirect()->route('vendor.login');
        }

        // Get date range filter
        $dateRange = $request->get('date_range', '30');
        $status = $request->get('status', 'all');
        $itemSearch = $request->get('item_search', '');

        // Build query for damage reports
        $query = InventoryReceiptItem::with(['receipt'])
            ->whereHas('receipt', function($q) use ($vendor) {
                $q->where('supplier_name', $vendor->company_name);
            })
            ->where('damaged_quantity', '>', 0);

        // Apply date filter
        if ($dateRange !== 'all') {
            $days = (int) $dateRange;
            $query->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        // Apply status filter
        if ($status !== 'all') {
            switch ($status) {
                case 'reported':
                    $query->where('return_to_vendor', true);
                    break;
                case 'acknowledged':
                    $query->where('return_to_vendor', true)
                          ->whereNotNull('acknowledged_at');
                    break;
                case 'replacement_sent':
                    $query->where('return_to_vendor', true)
                          ->whereNotNull('replacement_sent_at');
                    break;
                case 'resolved':
                    $query->where('return_to_vendor', false);
                    break;
            }
        }

        // Apply item search filter
        if (!empty($itemSearch)) {
            $query->where('item_name', 'LIKE', "%{$itemSearch}%");
        }

        $damageReports = $query->orderBy('created_at', 'desc')->paginate(20);

        // Calculate statistics
        $damageStats = $this->calculateDamageStats($vendor);

        return view('VendorPortal.damage_reports', compact('damageReports', 'damageStats'));
    }

    public function show($id)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $report = InventoryReceiptItem::with(['receipt'])
            ->whereHas('receipt', function($q) use ($vendor) {
                $q->where('supplier_name', $vendor->company_name);
            })
            ->where('id', $id)
            ->where('damaged_quantity', '>', 0)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'report' => [
                'id' => $report->id,
                'receipt_number' => $report->receipt->receipt_number,
                'supplier_name' => $report->receipt->supplier_name,
                'delivery_date' => $report->receipt->delivery_date->format('M d, Y'),
                'item_name' => $report->item_name,
                'description' => $report->description,
                'unit' => $report->unit,
                'quantity' => $report->quantity,
                'damaged_quantity' => $report->damaged_quantity,
                'damage_reason' => $report->damage_reason,
                'image_path' => $report->image_path,
                'damage_image_path' => $report->damage_image_path,
                'return_to_vendor' => $report->return_to_vendor,
                'acknowledged_at' => $report->acknowledged_at,
                'replacement_sent_at' => $report->replacement_sent_at,
                'created_at' => $report->created_at->format('M d, Y h:i A')
            ]
        ]);
    }

    public function acknowledge(Request $request, $id)
    {
        $vendor = Auth::guard('vendor')->user();
        
        $report = InventoryReceiptItem::whereHas('receipt', function($q) use ($vendor) {
                $q->where('supplier_name', $vendor->company_name);
            })
            ->where('id', $id)
            ->where('damaged_quantity', '>', 0)
            ->where('return_to_vendor', true)
            ->firstOrFail();

        $report->update([
            'acknowledged_at' => now(),
            'acknowledged_by' => $vendor->id
        ]);

        // Log the acknowledgment
        \Log::info('Damage report acknowledged', [
            'report_id' => $report->id,
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->company_name,
            'item_name' => $report->item_name,
            'damaged_quantity' => $report->damaged_quantity
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Damage report acknowledged successfully'
        ]);
    }

    public function sendReplacement(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'delivery_date' => 'required|date|after:today',
            'tracking_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000'
        ]);

        $vendor = Auth::guard('vendor')->user();
        
        $report = InventoryReceiptItem::whereHas('receipt', function($q) use ($vendor) {
                $q->where('supplier_name', $vendor->company_name);
            })
            ->where('id', $id)
            ->where('damaged_quantity', '>', 0)
            ->where('return_to_vendor', true)
            ->firstOrFail();

        // Update the report with replacement information
        $report->update([
            'replacement_quantity' => $request->quantity,
            'replacement_delivery_date' => $request->delivery_date,
            'replacement_tracking_number' => $request->tracking_number,
            'replacement_notes' => $request->notes,
            'replacement_sent_at' => now(),
            'replacement_sent_by' => $vendor->id
        ]);

        // Create a replacement record (you might want to create a separate table for this)
        DB::table('vendor_replacements')->insert([
            'inventory_receipt_item_id' => $report->id,
            'vendor_id' => $vendor->id,
            'quantity' => $request->quantity,
            'expected_delivery_date' => $request->delivery_date,
            'tracking_number' => $request->tracking_number,
            'notes' => $request->notes,
            'status' => 'sent',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Log the replacement
        \Log::info('Replacement sent for damage report', [
            'report_id' => $report->id,
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->company_name,
            'item_name' => $report->item_name,
            'replacement_quantity' => $request->quantity,
            'tracking_number' => $request->tracking_number
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Replacement information submitted successfully'
        ]);
    }

    public function export(Request $request)
    {
        $vendor = Auth::guard('vendor')->user();
        
        // Get the same filtered data as the index method
        $dateRange = $request->get('date_range', '30');
        $status = $request->get('status', 'all');
        $itemSearch = $request->get('item_search', '');

        $query = InventoryReceiptItem::with(['receipt'])
            ->whereHas('receipt', function($q) use ($vendor) {
                $q->where('supplier_name', $vendor->company_name);
            })
            ->where('damaged_quantity', '>', 0);

        // Apply filters (same logic as index method)
        if ($dateRange !== 'all') {
            $days = (int) $dateRange;
            $query->where('created_at', '>=', Carbon::now()->subDays($days));
        }

        if ($status !== 'all') {
            switch ($status) {
                case 'reported':
                    $query->where('return_to_vendor', true);
                    break;
                case 'acknowledged':
                    $query->where('return_to_vendor', true)
                          ->whereNotNull('acknowledged_at');
                    break;
                case 'replacement_sent':
                    $query->where('return_to_vendor', true)
                          ->whereNotNull('replacement_sent_at');
                    break;
                case 'resolved':
                    $query->where('return_to_vendor', false);
                    break;
            }
        }

        if (!empty($itemSearch)) {
            $query->where('item_name', 'LIKE', "%{$itemSearch}%");
        }

        $damageReports = $query->orderBy('created_at', 'desc')->get();

        // Generate CSV
        $filename = 'damage_reports_' . $vendor->company_name . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($damageReports) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'Report ID',
                'Receipt Number',
                'Item Name',
                'Description',
                'Total Quantity',
                'Damaged Quantity',
                'Good Quantity',
                'Damage Rate (%)',
                'Unit',
                'Unit Price',
                'Total Loss Value',
                'Damage Reason',
                'Return to Vendor',
                'Acknowledged At',
                'Replacement Sent At',
                'Reported Date'
            ]);

            // CSV data
            foreach ($damageReports as $report) {
                $damageRate = $report->quantity > 0 ? round(($report->damaged_quantity / $report->quantity) * 100, 2) : 0;
                $lossValue = $report->damaged_quantity * $report->unit_price;
                
                fputcsv($file, [
                    str_pad($report->id, 6, '0', STR_PAD_LEFT),
                    $report->receipt->receipt_number ?? 'N/A',
                    $report->item_name,
                    $report->description ?? '',
                    $report->quantity,
                    $report->damaged_quantity,
                    $report->quantity - $report->damaged_quantity,
                    $damageRate,
                    $report->unit,
                    $report->unit_price,
                    number_format($lossValue, 2),
                    $report->damage_reason ?? '',
                    $report->return_to_vendor ? 'Yes' : 'No',
                    $report->acknowledged_at ? $report->acknowledged_at->format('Y-m-d H:i:s') : '',
                    $report->replacement_sent_at ? $report->replacement_sent_at->format('Y-m-d H:i:s') : '',
                    $report->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateDamageStats($vendor)
    {
        $totalItems = InventoryReceiptItem::whereHas('receipt', function($q) use ($vendor) {
            $q->where('supplier_name', $vendor->company_name);
        })->sum('quantity');

        $totalDamaged = InventoryReceiptItem::whereHas('receipt', function($q) use ($vendor) {
            $q->where('supplier_name', $vendor->company_name);
        })->sum('damaged_quantity');

        $pendingReplacements = InventoryReceiptItem::whereHas('receipt', function($q) use ($vendor) {
            $q->where('supplier_name', $vendor->company_name);
        })
        ->where('damaged_quantity', '>', 0)
        ->where('return_to_vendor', true)
        ->whereNull('replacement_sent_at')
        ->count();

        $completedReplacements = InventoryReceiptItem::whereHas('receipt', function($q) use ($vendor) {
            $q->where('supplier_name', $vendor->company_name);
        })
        ->where('damaged_quantity', '>', 0)
        ->where('return_to_vendor', true)
        ->whereNotNull('replacement_sent_at')
        ->count();

        $damageRate = $totalItems > 0 ? ($totalDamaged / $totalItems) * 100 : 0;

        return [
            'total_damaged' => $totalDamaged,
            'pending_replacements' => $pendingReplacements,
            'completed_replacements' => $completedReplacements,
            'damage_rate' => $damageRate
        ];
    }
}
