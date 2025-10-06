<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\Contract;
use App\Models\InventoryReceipt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DTRSController extends Controller
{
    public function documents(Request $request)
    {
        // Get all vendors with their documents
        $vendors = Vendor::whereNotNull('business_license_path')
            ->orWhereNotNull('tax_certificate_path')
            ->orWhereNotNull('insurance_certificate_path')
            ->orWhereNotNull('additional_documents_paths')
            ->get();

        // Collect all documents from vendors
        $documents = collect();
        $logisticsRecords = collect();

        foreach ($vendors as $vendor) {
            // Business License
            if ($vendor->business_license_path) {
                $documents->push([
                    'id' => 'vendor_' . $vendor->id . '_business_license',
                    'document_id' => 'DOC-VBL-' . str_pad($vendor->id, 4, '0', STR_PAD_LEFT),
                    'filename' => basename($vendor->business_license_path),
                    'type' => 'business_license',
                    'file_path' => $vendor->business_license_path,
                    'file_size' => $this->getFileSize($vendor->business_license_path),
                    'created_at' => $vendor->created_at,
                    'source_module' => 'PSM',
                    'vendor_name' => $vendor->company_name,
                    'vendor_id' => $vendor->id,
                ]);
            }

            // Tax Certificate
            if ($vendor->tax_certificate_path) {
                $documents->push([
                    'id' => 'vendor_' . $vendor->id . '_tax_certificate',
                    'document_id' => 'DOC-VTC-' . str_pad($vendor->id, 4, '0', STR_PAD_LEFT),
                    'filename' => basename($vendor->tax_certificate_path),
                    'type' => 'tax_certificate',
                    'file_path' => $vendor->tax_certificate_path,
                    'file_size' => $this->getFileSize($vendor->tax_certificate_path),
                    'created_at' => $vendor->created_at,
                    'source_module' => 'PSM',
                    'vendor_name' => $vendor->company_name,
                    'vendor_id' => $vendor->id,
                ]);
            }

            // Insurance Certificate
            if ($vendor->insurance_certificate_path) {
                $documents->push([
                    'id' => 'vendor_' . $vendor->id . '_insurance_certificate',
                    'document_id' => 'DOC-VIC-' . str_pad($vendor->id, 4, '0', STR_PAD_LEFT),
                    'filename' => basename($vendor->insurance_certificate_path),
                    'type' => 'insurance_certificate',
                    'file_path' => $vendor->insurance_certificate_path,
                    'file_size' => $this->getFileSize($vendor->insurance_certificate_path),
                    'created_at' => $vendor->created_at,
                    'source_module' => 'PSM',
                    'vendor_name' => $vendor->company_name,
                    'vendor_id' => $vendor->id,
                ]);
            }

            // Additional Documents
            if ($vendor->additional_documents_paths && is_array($vendor->additional_documents_paths)) {
                foreach ($vendor->additional_documents_paths as $index => $docPath) {
                    $documents->push([
                        'id' => 'vendor_' . $vendor->id . '_additional_' . $index,
                        'document_id' => 'DOC-VAD-' . str_pad($vendor->id, 4, '0', STR_PAD_LEFT) . '-' . ($index + 1),
                        'filename' => basename($docPath),
                        'type' => 'additional_document',
                        'file_path' => $docPath,
                        'file_size' => $this->getFileSize($docPath),
                        'created_at' => $vendor->created_at,
                        'source_module' => 'PSM',
                        'vendor_name' => $vendor->company_name,
                        'vendor_id' => $vendor->id,
                    ]);
                }
            }

            // Create logistics record for vendor registration
            $logisticsRecords->push((object)[
                'id' => 'vendor_reg_' . $vendor->id,
                'record_id' => 'LR-VR-' . str_pad($vendor->id, 6, '0', STR_PAD_LEFT),
                'type' => 'vendor_registration',
                'status' => strtolower($vendor->status),
                'created_at' => $vendor->created_at,
                'vendor_name' => $vendor->company_name,
            ]);
        }

        // Fetch Purchase Orders
        $purchaseOrders = PurchaseOrder::with('vendor')->get();
        foreach ($purchaseOrders as $po) {
            $documents->push([
                'id' => 'purchase_order_' . $po->id,
                'document_id' => 'DOC-PO-' . str_pad($po->id, 6, '0', STR_PAD_LEFT),
                'filename' => 'Purchase Order #' . $po->po_number . '.pdf',
                'type' => 'purchase_order',
                'file_path' => null, // PO is generated document
                'file_size' => 0,
                'created_at' => $po->created_at,
                'source_module' => 'PSM',
                'vendor_name' => $po->vendor->company_name ?? 'Unknown Vendor',
                'vendor_id' => $po->vendor_id,
                'po_number' => $po->po_number,
                'total_amount' => $po->total_amount,
                'status' => $po->status,
            ]);

            // Create logistics record for purchase order
            $logisticsRecords->push((object)[
                'id' => 'po_' . $po->id,
                'record_id' => 'LR-PO-' . str_pad($po->id, 6, '0', STR_PAD_LEFT),
                'type' => 'purchase_order',
                'status' => strtolower($po->status),
                'created_at' => $po->created_at,
                'vendor_name' => $po->vendor->company_name ?? 'Unknown Vendor',
            ]);
        }

        // Fetch Contracts
        $contracts = Contract::with('vendor')->get();
        foreach ($contracts as $contract) {
            $documents->push([
                'id' => 'contract_' . $contract->id,
                'document_id' => 'DOC-CT-' . str_pad($contract->id, 6, '0', STR_PAD_LEFT),
                'filename' => 'Contract #' . $contract->contract_number . '.pdf',
                'type' => 'contract',
                'file_path' => $contract->document_path ?? null,
                'file_size' => $contract->document_path ? $this->getFileSize($contract->document_path) : 0,
                'created_at' => $contract->created_at,
                'source_module' => 'PSM',
                'vendor_name' => $contract->vendor->company_name ?? 'Unknown Vendor',
                'vendor_id' => $contract->vendor_id,
                'contract_number' => $contract->contract_number,
                'contract_value' => $contract->total_value ?? 0,
                'status' => $contract->status,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
            ]);

            // Create logistics record for contract
            $logisticsRecords->push((object)[
                'id' => 'contract_' . $contract->id,
                'record_id' => 'LR-CT-' . str_pad($contract->id, 6, '0', STR_PAD_LEFT),
                'type' => 'contract',
                'status' => strtolower($contract->status),
                'created_at' => $contract->created_at,
                'vendor_name' => $contract->vendor->company_name ?? 'Unknown Vendor',
            ]);
        }

        // Fetch Inventory Receipts
        $inventoryReceipts = InventoryReceipt::with('items')->get();
        foreach ($inventoryReceipts as $receipt) {
            $documents->push([
                'id' => 'inventory_receipt_' . $receipt->id,
                'document_id' => 'DOC-IR-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT),
                'filename' => 'Inventory Receipt #' . $receipt->receipt_number . '.pdf',
                'type' => 'inventory_receipt',
                'file_path' => $receipt->document_path ?? null,
                'file_size' => $receipt->document_path ? $this->getFileSize($receipt->document_path) : 0,
                'created_at' => $receipt->created_at,
                'source_module' => 'SWS',
                'vendor_name' => $receipt->supplier_name ?? 'Unknown Supplier',
                'vendor_id' => null,
                'receipt_number' => $receipt->receipt_number,
                'total_value' => $receipt->total_value ?? 0,
                'total_items' => $receipt->total_items ?? 0,
                'status' => $receipt->status,
                'received_by' => $receipt->received_by,
                'receipt_date' => $receipt->receipt_date,
            ]);

            // Create logistics record for inventory receipt
            $logisticsRecords->push((object)[
                'id' => 'inventory_receipt_' . $receipt->id,
                'record_id' => 'LR-IR-' . str_pad($receipt->id, 6, '0', STR_PAD_LEFT),
                'type' => 'inventory_receipt',
                'status' => strtolower($receipt->status),
                'created_at' => $receipt->created_at,
                'vendor_name' => $receipt->supplier_name ?? 'Unknown Supplier',
            ]);
        }

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = strtolower($request->search);
            $documents = $documents->filter(function ($doc) use ($search) {
                return str_contains(strtolower($doc['filename']), $search) ||
                       str_contains(strtolower($doc['vendor_name']), $search) ||
                       str_contains(strtolower($doc['type']), $search);
            });
        }

        if ($request->has('type') && $request->type) {
            $documents = $documents->filter(function ($doc) use ($request) {
                return $doc['type'] === $request->type;
            });
        }

        if ($request->has('date_range') && $request->date_range) {
            $documents = $documents->filter(function ($doc) use ($request) {
                $docDate = $doc['created_at'];
                if (!$docDate) return true;
                
                switch ($request->date_range) {
                    case 'today':
                        return $docDate->isToday();
                    case 'week':
                        return $docDate->isCurrentWeek();
                    case 'month':
                        return $docDate->isCurrentMonth();
                    case 'quarter':
                        return $docDate->isCurrentQuarter();
                    case 'year':
                        return $docDate->isCurrentYear();
                    default:
                        return true;
                }
            });
        }

        // Sort by created date (newest first)
        $documents = $documents->sortByDesc(function ($doc) {
            return $doc['created_at'] ?? now();
        });

        // Paginate documents manually
        $perPage = 15;
        $currentPage = $request->get('page', 1);
        $documentsArray = $documents->values()->all();
        $total = count($documentsArray);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedDocs = array_slice($documentsArray, $offset, $perPage);

        // Calculate statistics
        $totalDocuments = $documents->count();
        $totalFileSize = $documents->sum('file_size');
        $thisWeekDocuments = $documents->filter(function ($doc) {
            return $doc['created_at'] && $doc['created_at']->isCurrentWeek();
        })->count();

        $documentsByType = [
            'business_license' => $documents->where('type', 'business_license')->count(),
            'tax_certificate' => $documents->where('type', 'tax_certificate')->count(),
            'insurance_certificate' => $documents->where('type', 'insurance_certificate')->count(),
            'purchase_order' => $documents->where('type', 'purchase_order')->count(),
            'contract' => $documents->where('type', 'contract')->count(),
            'inventory_receipt' => $documents->where('type', 'inventory_receipt')->count(),
            'additional_document' => $documents->where('type', 'additional_document')->count(),
        ];

        $avgFileSize = $totalDocuments > 0 ? $totalFileSize / $totalDocuments : 0;
        $storagePercentage = min(($totalFileSize / (100 * 1024 * 1024)) * 100, 100); // Assume 100MB limit
        
        // Additional statistics
        $logisticsRecordsCount = $logisticsRecords->count();
        $documentsNeedingReview = 0; // Could be calculated based on document age or verification status
        $oldDocuments = $documents->filter(function ($doc) {
            return $doc['created_at'] && $doc['created_at']->diffInYears(now()) > 5;
        })->count();

        return view('DTRS.document', compact(
            'paginatedDocs',
            'totalDocuments',
            'thisWeekDocuments',
            'documentsByType',
            'avgFileSize',
            'storagePercentage',
            'currentPage',
            'perPage',
            'total',
            'totalFileSize',
            'logisticsRecordsCount',
            'documentsNeedingReview',
            'oldDocuments'
        ))->with([
            'logisticsRecords' => $logisticsRecords->take(10), // Limit to 10 recent records
        ]);
    }

    public function viewDocument($documentId)
    {
        $parts = explode('_', $documentId);
        if (count($parts) < 2) {
            abort(404);
        }

        $docType = $parts[0];
        $id = $parts[1];

        // Handle Purchase Orders
        if ($docType === 'purchase' && isset($parts[2]) && $parts[2] === 'order') {
            $purchaseOrder = PurchaseOrder::with('vendor')->find($id);
            if (!$purchaseOrder) {
                abort(404);
            }
            // Generate PDF or redirect to PO view page
            return redirect()->route('psm.order.show', $purchaseOrder->id);
        }

        // Handle Contracts
        if ($docType === 'contract') {
            $contract = Contract::find($id);
            if (!$contract) {
                abort(404);
            }
            
            if ($contract->document_path && Storage::exists($contract->document_path)) {
                return Storage::response($contract->document_path);
            }
            
            // If no file, redirect to contract view page
            return redirect()->route('psm.contract.show', $contract->id);
        }

        // Handle Inventory Receipts
        if ($docType === 'inventory' && isset($parts[1]) && $parts[1] === 'receipt') {
            $receiptId = $parts[2];
            $receipt = InventoryReceipt::find($receiptId);
            if (!$receipt) {
                abort(404);
            }
            
            if ($receipt->document_path && Storage::exists($receipt->document_path)) {
                return Storage::response($receipt->document_path);
            }
            
            // Generate document if it doesn't exist
            $receipt->generateDocument();
            
            if ($receipt->document_path && Storage::exists($receipt->document_path)) {
                return Storage::response($receipt->document_path);
            }
            
            abort(404, 'Document could not be generated');
        }

        // Handle Vendor Documents
        if ($docType === 'vendor' && count($parts) >= 3) {
            $vendorId = $parts[1];
            $vendorDocType = $parts[2];

            $vendor = Vendor::findOrFail($vendorId);

            $filePath = null;
            switch ($vendorDocType) {
                case 'business':
                case 'business_license':
                    $filePath = $vendor->business_license_path;
                    break;
                case 'tax':
                case 'tax_certificate':
                    $filePath = $vendor->tax_certificate_path;
                    break;
                case 'insurance':
                case 'insurance_certificate':
                    $filePath = $vendor->insurance_certificate_path;
                    break;
                default:
                    if (str_starts_with($vendorDocType, 'additional')) {
                        $index = (int) str_replace('additional_', '', $vendorDocType);
                        $additionalDocs = $vendor->additional_documents_paths;
                        if (isset($additionalDocs[$index])) {
                            $filePath = $additionalDocs[$index];
                        }
                    }
            }

            if (!$filePath || !Storage::exists($filePath)) {
                abort(404);
            }

            return Storage::response($filePath);
        }

        abort(404);
    }

    public function downloadDocument($documentId)
    {
        $parts = explode('_', $documentId);
        if (count($parts) < 2) {
            abort(404);
        }

        $docType = $parts[0];
        $id = $parts[1];

        // Handle Purchase Orders
        if ($docType === 'purchase' && isset($parts[2]) && $parts[2] === 'order') {
            $purchaseOrder = PurchaseOrder::with('vendor')->find($id);
            if (!$purchaseOrder) {
                abort(404);
            }
            // Generate PDF download or redirect to PO download
            return redirect()->route('psm.order.download', $purchaseOrder->id);
        }

        // Handle Contracts
        if ($docType === 'contract') {
            $contract = Contract::find($id);
            if (!$contract) {
                abort(404);
            }
            
            if ($contract->document_path && Storage::exists($contract->document_path)) {
                return Storage::download($contract->document_path);
            }
            
            // If no file, redirect to contract download
            return redirect()->route('psm.contract.download', $contract->id);
        }

        // Handle Inventory Receipts
        if ($docType === 'inventory' && isset($parts[1]) && $parts[1] === 'receipt') {
            $receiptId = $parts[2];
            $receipt = InventoryReceipt::find($receiptId);
            if (!$receipt) {
                abort(404);
            }
            
            if ($receipt->document_path && Storage::exists($receipt->document_path)) {
                return Storage::download($receipt->document_path, 'Inventory_Receipt_' . $receipt->receipt_number . '.pdf');
            }
            
            // Generate document if it doesn't exist
            $receipt->generateDocument();
            
            if ($receipt->document_path && Storage::exists($receipt->document_path)) {
                return Storage::download($receipt->document_path, 'Inventory_Receipt_' . $receipt->receipt_number . '.pdf');
            }
            
            abort(404, 'Document could not be generated');
        }

        // Handle Vendor Documents
        if ($docType === 'vendor' && count($parts) >= 3) {
            $vendorId = $parts[1];
            $vendorDocType = $parts[2];

            $vendor = Vendor::findOrFail($vendorId);

            $filePath = null;
            switch ($vendorDocType) {
                case 'business':
                case 'business_license':
                    $filePath = $vendor->business_license_path;
                    break;
                case 'tax':
                case 'tax_certificate':
                    $filePath = $vendor->tax_certificate_path;
                    break;
                case 'insurance':
                case 'insurance_certificate':
                    $filePath = $vendor->insurance_certificate_path;
                    break;
                default:
                    if (str_starts_with($vendorDocType, 'additional')) {
                        $index = (int) str_replace('additional_', '', $vendorDocType);
                        $additionalDocs = $vendor->additional_documents_paths;
                        if (isset($additionalDocs[$index])) {
                            $filePath = $additionalDocs[$index];
                        }
                    }
            }

            if (!$filePath || !Storage::exists($filePath)) {
                abort(404);
            }

            return Storage::download($filePath);
        }

        abort(404);
    }

    public function documentMetadata($documentId)
    {
        $parts = explode('_', $documentId);
        if (count($parts) < 2) {
            return response()->json(['error' => 'Invalid document ID'], 404);
        }

        $docType = $parts[0];
        $id = $parts[1];

        // Handle Purchase Orders
        if ($docType === 'purchase' && isset($parts[2]) && $parts[2] === 'order') {
            $purchaseOrder = PurchaseOrder::with('vendor')->find($id);
            if (!$purchaseOrder) {
                return response()->json(['error' => 'Purchase Order not found'], 404);
            }

            return response()->json([
                'document_id' => 'DOC-PO-' . str_pad($id, 6, '0', STR_PAD_LEFT),
                'filename' => 'Purchase Order #' . $purchaseOrder->po_number . '.pdf',
                'file_size' => 0, // Generated document
                'mime_type' => 'application/pdf',
                'created_at' => $purchaseOrder->created_at,
                'source_module' => 'PSM - Purchase Orders',
                'vendor_name' => $purchaseOrder->vendor->company_name ?? 'Unknown Vendor',
                'document_type' => 'Purchase Order',
                'po_number' => $purchaseOrder->po_number,
                'total_amount' => $purchaseOrder->total_amount,
                'status' => $purchaseOrder->status,
                'checksum' => 'N/A - Generated Document',
            ]);
        }

        // Handle Contracts
        if ($docType === 'contract') {
            $contract = Contract::with('vendor')->find($id);
            if (!$contract) {
                return response()->json(['error' => 'Contract not found'], 404);
            }

            $filePath = $contract->document_path;
            $fileSize = $filePath ? $this->getFileSize($filePath) : 0;
            $mimeType = $filePath ? (Storage::mimeType($filePath) ?? 'application/pdf') : 'application/pdf';
            $checksum = $filePath && Storage::exists($filePath) ? hash_file('md5', Storage::path($filePath)) : 'N/A';

            return response()->json([
                'document_id' => 'DOC-CT-' . str_pad($id, 6, '0', STR_PAD_LEFT),
                'filename' => 'Contract #' . $contract->contract_number . '.pdf',
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'created_at' => $contract->created_at,
                'source_module' => 'PSM - Contract Management',
                'vendor_name' => $contract->vendor->company_name ?? 'Unknown Vendor',
                'document_type' => 'Contract',
                'contract_number' => $contract->contract_number,
                'contract_value' => $contract->total_value ?? 0,
                'status' => $contract->status,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'checksum' => $checksum,
            ]);
        }

        // Handle Inventory Receipts
        if ($docType === 'inventory' && isset($parts[1]) && $parts[1] === 'receipt') {
            $receiptId = $parts[2];
            $receipt = InventoryReceipt::with('items')->find($receiptId);
            if (!$receipt) {
                return response()->json(['error' => 'Inventory Receipt not found'], 404);
            }

            $filePath = $receipt->document_path;
            $fileSize = $filePath ? $this->getFileSize($filePath) : 0;
            $mimeType = $filePath ? (Storage::mimeType($filePath) ?? 'application/pdf') : 'application/pdf';
            $checksum = $filePath && Storage::exists($filePath) ? hash_file('md5', Storage::path($filePath)) : 'N/A';

            return response()->json([
                'document_id' => 'DOC-IR-' . str_pad($receiptId, 6, '0', STR_PAD_LEFT),
                'filename' => 'Inventory Receipt #' . $receipt->receipt_number . '.pdf',
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'created_at' => $receipt->created_at,
                'source_module' => 'SWS - Smart Warehousing System',
                'vendor_name' => $receipt->supplier_name ?? 'Unknown Supplier',
                'document_type' => 'Inventory Receipt',
                'receipt_number' => $receipt->receipt_number,
                'total_value' => $receipt->total_value ?? 0,
                'total_items' => $receipt->total_items ?? 0,
                'status' => $receipt->status,
                'received_by' => $receipt->received_by,
                'receipt_date' => $receipt->receipt_date,
                'checksum' => $checksum,
            ]);
        }

        // Handle Vendor Documents (existing logic)
        if ($docType === 'vendor' && count($parts) >= 3) {
            $vendorId = $parts[1];
            $vendorDocType = $parts[2];

            $vendor = Vendor::find($vendorId);
            if (!$vendor) {
                return response()->json(['error' => 'Vendor not found'], 404);
            }

            $filePath = null;
            $documentType = '';
            
            switch ($vendorDocType) {
                case 'business':
                    $filePath = $vendor->business_license_path;
                    $documentType = 'Business License';
                    break;
                case 'tax':
                    $filePath = $vendor->tax_certificate_path;
                    $documentType = 'Tax Certificate';
                    break;
                case 'insurance':
                    $filePath = $vendor->insurance_certificate_path;
                    $documentType = 'Insurance Certificate';
                    break;
                default:
                    if (str_starts_with($vendorDocType, 'additional')) {
                        $index = (int) str_replace('additional_', '', $vendorDocType);
                        $additionalDocs = $vendor->additional_documents_paths;
                        if (isset($additionalDocs[$index])) {
                            $filePath = $additionalDocs[$index];
                            $documentType = 'Additional Document';
                        }
                    }
            }

            if (!$filePath) {
                return response()->json(['error' => 'Document not found'], 404);
            }

            $fileSize = $this->getFileSize($filePath);
            $mimeType = Storage::mimeType($filePath) ?? 'application/octet-stream';
            $checksum = Storage::exists($filePath) ? hash_file('md5', Storage::path($filePath)) : 'N/A';

            return response()->json([
                'document_id' => 'DOC-V' . strtoupper(substr($vendorDocType, 0, 2)) . '-' . str_pad($vendorId, 4, '0', STR_PAD_LEFT),
                'filename' => basename($filePath),
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'created_at' => $vendor->created_at,
                'source_module' => 'PSM - Vendor Management',
                'vendor_name' => $vendor->company_name,
                'document_type' => $documentType,
                'checksum' => $checksum,
            ]);
        }

        return response()->json(['error' => 'Document type not supported'], 404);
    }

    public function logAccess(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'document_id' => 'required|string',
                'action' => 'required|string|in:view,download,metadata,access_request',
                'user_id' => 'required|integer',
                'user_role' => 'required|string',
                'timestamp' => 'required|string'
            ]);

            // Log the access attempt
            Log::info('DTRS Document Access', [
                'document_id' => $validated['document_id'],
                'action' => $validated['action'],
                'user_id' => $validated['user_id'],
                'user_name' => Auth::user()->name,
                'user_role' => $validated['user_role'],
                'timestamp' => $validated['timestamp'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId()
            ]);

            // Additional security logging for sensitive actions
            if (in_array($validated['action'], ['metadata', 'access_request'])) {
                Log::warning('DTRS Sensitive Action Attempted', [
                    'document_id' => $validated['document_id'],
                    'action' => $validated['action'],
                    'user_id' => $validated['user_id'],
                    'user_name' => Auth::user()->name,
                    'user_role' => $validated['user_role'],
                    'authorized' => Auth::user()->role === 'admin',
                    'ip_address' => $request->ip(),
                    'timestamp' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Access logged successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to log DTRS access', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to log access'
            ], 500);
        }
    }

    public function saveFolderSettings(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'category_type' => 'required|string|in:business_license,tax_certificate,insurance_certificate,purchase_order,contract,inventory_receipt,additional_document',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'color' => 'required|string|in:primary,success,warning,danger,info,dark,purple,secondary',
                'visible' => 'boolean',
                'permissions' => 'array',
                'permissions.logistics' => 'boolean',
                'permissions.procurement' => 'boolean',
                'permissions.allowView' => 'boolean',
                'permissions.allowDownload' => 'boolean',
                'permissions.allowUpload' => 'boolean',
                'permissions.allowDelete' => 'boolean'
            ]);

            // For now, we'll store folder settings in session or cache
            // In a real implementation, you'd save to a database table
            $folderSettings = session()->get('folder_settings', []);
            
            $folderSettings[$validated['category_type']] = [
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'color' => $validated['color'],
                'visible' => $validated['visible'] ?? true,
                'permissions' => $validated['permissions'] ?? [],
                'updated_at' => now()->toDateTimeString(),
                'updated_by' => Auth::id()
            ];

            session()->put('folder_settings', $folderSettings);

            // Log the settings change
            Log::info('DTRS Folder Settings Updated', [
                'category_type' => $validated['category_type'],
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name ?? 'Unknown',
                'settings' => $validated,
                'timestamp' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Folder settings saved successfully',
                'data' => $folderSettings[$validated['category_type']]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Failed to save DTRS folder settings', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to save folder settings'
            ], 500);
        }
    }

    public function getFolderSettings($categoryType = null)
    {
        try {
            $folderSettings = session()->get('folder_settings', []);
            
            if ($categoryType) {
                return response()->json([
                    'success' => true,
                    'data' => $folderSettings[$categoryType] ?? null
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $folderSettings
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get DTRS folder settings', [
                'error' => $e->getMessage(),
                'category_type' => $categoryType,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to get folder settings'
            ], 500);
        }
    }

    private function getFileSize($filePath)
    {
        try {
            return Storage::exists($filePath) ? Storage::size($filePath) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
