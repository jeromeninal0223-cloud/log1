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
use Dompdf\Dompdf;
use Dompdf\Options;

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

    public function show($id, Request $request)
    {
        try {
            // Check if tables exist first
            if (!Schema::hasTable('purchase_orders')) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Purchase orders table not found'
                    ], 404);
                }
                return redirect()->route('psm.order.index')->with('error', 'Purchase orders table not found');
            }

            // Find the purchase order or fail with proper error handling
            $purchaseOrder = PurchaseOrder::find($id);
            
            if (!$purchaseOrder) {
                if ($request->wantsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Purchase order not found'
                    ], 404);
                }
                return redirect()->route('psm.order.index')->with('error', 'Purchase order not found');
            }

            $purchaseOrder->load(['contract', 'vendor', 'creator', 'approver', 'items']);
            
            // If this is an AJAX request (for modal), return JSON
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'order' => [
                        'id' => $purchaseOrder->id,
                        'po_number' => $purchaseOrder->po_number,
                        'title' => $purchaseOrder->title,
                        'description' => $purchaseOrder->description,
                        'status' => $purchaseOrder->status,
                        'order_date' => $purchaseOrder->order_date?->format('M d, Y'),
                        'expected_delivery_date' => $purchaseOrder->expected_delivery_date?->format('M d, Y'),
                        'total_amount' => $purchaseOrder->total_amount,
                        'currency' => $purchaseOrder->currency,
                        'delivery_address' => $purchaseOrder->delivery_address,
                        'payment_terms' => $purchaseOrder->payment_terms,
                        'notes' => $purchaseOrder->notes,
                        'contract' => $purchaseOrder->contract ? [
                            'id' => $purchaseOrder->contract->id,
                            'contract_number' => $purchaseOrder->contract->contract_number,
                            'title' => $purchaseOrder->contract->title,
                        ] : null,
                        'vendor' => $purchaseOrder->vendor ? [
                            'id' => $purchaseOrder->vendor->id,
                            'name' => $purchaseOrder->vendor->name,
                            'company_name' => $purchaseOrder->vendor->company_name,
                        ] : null,
                        'items' => $purchaseOrder->items->map(function ($item) {
                            return [
                                'id' => $item->id,
                                'item_name' => $item->item_name,
                                'description' => $item->description,
                                'quantity' => $item->quantity,
                                'unit' => $item->unit,
                                'unit_price' => $item->unit_price,
                                'total_price' => $item->total_price,
                                'specifications' => $item->specifications,
                            ];
                        }),
                        'created_at' => $purchaseOrder->created_at?->format('M d, Y H:i'),
                        'updated_at' => $purchaseOrder->updated_at?->format('M d, Y H:i'),
                    ]
                ]);
            }
            
            // Otherwise return the view (for direct page access)
            return view('PSM.order-show', compact('purchaseOrder'));
            
        } catch (\Exception $e) {
            Log::error('Error in PurchaseOrderController@show: ' . $e->getMessage(), [
                'purchase_order_id' => $purchaseOrder->id ?? 'unknown',
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load purchase order details: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->route('psm.order.index')->with('error', 'Failed to load purchase order details');
        }
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

    public function complete(PurchaseOrder $purchaseOrder)
    {
        if (!in_array($purchaseOrder->status, ['Issued', 'In Progress', 'Delivered'])) {
            return response()->json([
                'success' => false,
                'message' => 'Only issued, in-progress, or delivered orders can be completed'
            ], 422);
        }

        try {
            $purchaseOrder->complete();
            
            \Log::info('Purchase order completed via controller', [
                'po_id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'status' => $purchaseOrder->fresh()->status
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Purchase Order completed successfully and invoice generated',
                'purchase_order' => $purchaseOrder->fresh()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to complete purchase order', [
                'po_id' => $purchaseOrder->id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    public function markInProgress(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Issued') {
            return response()->json([
                'success' => false,
                'message' => 'Only issued orders can be marked as in progress'
            ], 422);
        }

        $purchaseOrder->markInProgress();

        return response()->json([
            'success' => true,
            'message' => 'Purchase Order marked as in progress',
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

    public function downloadPdf(PurchaseOrder $purchaseOrder)
    {
        try {
            // Load relationships
            $purchaseOrder->load(['contract', 'vendor', 'creator', 'items']);
            
            // Configure Dompdf options
            $options = new Options();
            $options->set('defaultFont', 'Arial');
            $options->set('isRemoteEnabled', true);
            $options->set('isHtml5ParserEnabled', true);
            
            // Create Dompdf instance
            $dompdf = new Dompdf($options);
            
            // Generate HTML content for PDF
            $html = $this->generatePdfHtml($purchaseOrder);
            
            // Load HTML into Dompdf
            $dompdf->loadHtml($html);
            
            // Set paper size and orientation
            $dompdf->setPaper('A4', 'portrait');
            
            // Render PDF
            $dompdf->render();
            
            // Generate filename
            $filename = 'Purchase_Order_' . $purchaseOrder->po_number . '.pdf';
            
            // Return PDF as download
            return response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error generating PDF for Purchase Order: ' . $e->getMessage(), [
                'purchase_order_id' => $purchaseOrder->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    private function generatePdfHtml(PurchaseOrder $purchaseOrder)
    {
        $currencySymbol = match($purchaseOrder->currency) {
            'USD' => '$',
            'EUR' => '€',
            'PHP' => '₱',
            default => '₱'
        };

        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="utf-8">
            <title>Purchase Order - ' . $purchaseOrder->po_number . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    line-height: 1.4;
                    color: #333;
                    margin: 0;
                    padding: 20px;
                }
                .header {
                    text-align: center;
                    margin-bottom: 30px;
                    border-bottom: 2px solid #0066cc;
                    padding-bottom: 20px;
                }
                .company-name {
                    font-size: 24px;
                    font-weight: bold;
                    color: #0066cc;
                    margin-bottom: 5px;
                }
                .document-title {
                    font-size: 18px;
                    font-weight: bold;
                    margin-top: 10px;
                }
                .info-section {
                    display: table;
                    width: 100%;
                    margin-bottom: 20px;
                }
                .info-left, .info-right {
                    display: table-cell;
                    width: 50%;
                    vertical-align: top;
                    padding: 10px;
                }
                .info-box {
                    border: 1px solid #ddd;
                    padding: 15px;
                    margin-bottom: 10px;
                    background-color: #f9f9f9;
                }
                .info-title {
                    font-weight: bold;
                    font-size: 14px;
                    color: #0066cc;
                    margin-bottom: 10px;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5px;
                }
                .info-row {
                    margin-bottom: 5px;
                }
                .info-label {
                    font-weight: bold;
                    display: inline-block;
                    width: 120px;
                }
                .items-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                    margin-bottom: 20px;
                }
                .items-table th,
                .items-table td {
                    border: 1px solid #ddd;
                    padding: 8px;
                    text-align: left;
                }
                .items-table th {
                    background-color: #0066cc;
                    color: white;
                    font-weight: bold;
                    text-align: center;
                }
                .items-table td.number {
                    text-align: right;
                }
                .items-table td.center {
                    text-align: center;
                }
                .total-section {
                    margin-top: 20px;
                    text-align: right;
                }
                .total-amount {
                    font-size: 16px;
                    font-weight: bold;
                    color: #0066cc;
                    border: 2px solid #0066cc;
                    padding: 10px;
                    display: inline-block;
                    margin-top: 10px;
                }
                .footer {
                    margin-top: 40px;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                .signature-section {
                    display: table;
                    width: 100%;
                    margin-top: 30px;
                }
                .signature-box {
                    display: table-cell;
                    width: 50%;
                    padding: 20px;
                    text-align: center;
                    border: 1px solid #ddd;
                    margin: 0 10px;
                }
                .signature-line {
                    border-bottom: 1px solid #333;
                    margin-bottom: 5px;
                    height: 40px;
                }
                .status-badge {
                    display: inline-block;
                    padding: 5px 10px;
                    border-radius: 15px;
                    font-weight: bold;
                    font-size: 11px;
                    text-transform: uppercase;
                }
                .status-draft { background-color: #6c757d; color: white; }
                .status-pending { background-color: #ffc107; color: #333; }
                .status-approved { background-color: #28a745; color: white; }
                .status-issued { background-color: #007bff; color: white; }
                .status-completed { background-color: #17a2b8; color: white; }
                .notes-section {
                    margin-top: 20px;
                    border: 1px solid #ddd;
                    padding: 15px;
                    background-color: #f9f9f9;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="company-name">Jetlouge Travels</div>
                <div>Procurement & Sourcing Management</div>
                <div class="document-title">PURCHASE ORDER</div>
            </div>

            <div class="info-section">
                <div class="info-left">
                    <div class="info-box">
                        <div class="info-title">Purchase Order Details</div>
                        <div class="info-row">
                            <span class="info-label">PO Number:</span>
                            <strong>' . $purchaseOrder->po_number . '</strong>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Status:</span>
                            <span class="status-badge status-' . strtolower(str_replace(' ', '-', $purchaseOrder->status)) . '">' . $purchaseOrder->status . '</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Order Date:</span>
                            ' . $purchaseOrder->order_date->format('F d, Y') . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Expected Delivery:</span>
                            ' . ($purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('F d, Y') : 'Not specified') . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Currency:</span>
                            ' . $purchaseOrder->currency . '
                        </div>
                    </div>
                </div>
                
                <div class="info-right">
                    <div class="info-box">
                        <div class="info-title">Vendor Information</div>
                        <div class="info-row">
                            <span class="info-label">Company:</span>
                            ' . ($purchaseOrder->vendor->company_name ?? $purchaseOrder->vendor->name ?? 'N/A') . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Contact:</span>
                            ' . ($purchaseOrder->vendor->name ?? 'N/A') . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            ' . ($purchaseOrder->vendor->email ?? 'N/A') . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            ' . ($purchaseOrder->vendor->phone ?? 'N/A') . '
                        </div>
                    </div>
                </div>
            </div>

            <div class="info-section">
                <div class="info-left">
                    <div class="info-box">
                        <div class="info-title">Contract Information</div>
                        <div class="info-row">
                            <span class="info-label">Contract No:</span>
                            ' . ($purchaseOrder->contract->contract_number ?? 'N/A') . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Title:</span>
                            ' . ($purchaseOrder->contract->title ?? 'N/A') . '
                        </div>
                    </div>
                </div>
                
                <div class="info-right">
                    <div class="info-box">
                        <div class="info-title">Order Information</div>
                        <div class="info-row">
                            <span class="info-label">Title:</span>
                            ' . $purchaseOrder->title . '
                        </div>
                        <div class="info-row">
                            <span class="info-label">Created By:</span>
                            ' . ($purchaseOrder->creator->name ?? 'System') . '
                        </div>
                    </div>
                </div>
            </div>';

        if ($purchaseOrder->description) {
            $html .= '
            <div class="info-box">
                <div class="info-title">Description</div>
                <p>' . nl2br(htmlspecialchars($purchaseOrder->description)) . '</p>
            </div>';
        }

        if ($purchaseOrder->delivery_address) {
            $html .= '
            <div class="info-section">
                <div class="info-left">
                    <div class="info-box">
                        <div class="info-title">Delivery Address</div>
                        <p>' . nl2br(htmlspecialchars($purchaseOrder->delivery_address)) . '</p>
                    </div>
                </div>';
                
            if ($purchaseOrder->payment_terms) {
                $html .= '
                <div class="info-right">
                    <div class="info-box">
                        <div class="info-title">Payment Terms</div>
                        <p>' . nl2br(htmlspecialchars($purchaseOrder->payment_terms)) . '</p>
                    </div>
                </div>';
            }
            
            $html .= '</div>';
        }

        // Items table
        $html .= '
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 25%;">Item Name</th>
                        <th style="width: 30%;">Description</th>
                        <th style="width: 8%;">Qty</th>
                        <th style="width: 8%;">Unit</th>
                        <th style="width: 12%;">Unit Price</th>
                        <th style="width: 12%;">Total</th>
                    </tr>
                </thead>
                <tbody>';

        $itemNumber = 1;
        foreach ($purchaseOrder->items as $item) {
            $html .= '
                    <tr>
                        <td class="center">' . $itemNumber++ . '</td>
                        <td><strong>' . htmlspecialchars($item->item_name) . '</strong></td>
                        <td>' . ($item->description ? htmlspecialchars($item->description) : '-') . '</td>
                        <td class="center">' . number_format($item->quantity) . '</td>
                        <td class="center">' . htmlspecialchars($item->unit) . '</td>
                        <td class="number">' . $currencySymbol . number_format($item->unit_price, 2) . '</td>
                        <td class="number">' . $currencySymbol . number_format($item->total_price, 2) . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>

            <div class="total-section">
                <div class="total-amount">
                    Total Amount: ' . $currencySymbol . number_format($purchaseOrder->total_amount, 2) . '
                </div>
            </div>';

        if ($purchaseOrder->notes) {
            $html .= '
            <div class="notes-section">
                <div class="info-title">Additional Notes</div>
                <p>' . nl2br(htmlspecialchars($purchaseOrder->notes)) . '</p>
            </div>';
        }

        $html .= '
            <div class="footer">
                <div class="signature-section">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <strong>Procurement Manager</strong><br>
                        ' . ($purchaseOrder->creator->name ?? 'System') . '<br>
                        Date: _______________
                    </div>
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <strong>Vendor Representative</strong><br>
                        ' . ($purchaseOrder->vendor->company_name ?? $purchaseOrder->vendor->name ?? 'N/A') . '<br>
                        Date: _______________
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 30px; font-size: 10px; color: #666;">
                    Generated on ' . now()->format('F d, Y \a\t g:i A') . ' | Jetlouge Travels Procurement System
                </div>
            </div>
        </body>
        </html>';

        return $html;
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
