<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\Contract;
use App\Models\InventoryReceipt;
use Carbon\Carbon;

class DocumentVersionController extends Controller
{
    /**
     * Display the document version history page
     */
    public function index()
    {
        // Check authentication and authorization
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user has access to DTRS
        $userRole = Auth::user()->role;
        if (!in_array($userRole, ['admin', 'logistics_staff'])) {
            abort(403, 'Access denied. You do not have permission to access the Document Tracking & Records System.');
        }

        // Get documents from all sources (same as DTRSController)
        $documents = collect();
        
        // 1. Get documents from the new documents table
        $documentsFromTable = Document::active()
            ->select('id', 'title', 'document_type')
            ->get()
            ->map(function($doc) {
                return (object)[
                    'id' => 'doc_' . $doc->id,
                    'title' => $doc->title,
                    'document_type' => $doc->document_type,
                    'source' => 'documents_table'
                ];
            });
        
        // 2. Get vendor documents
        $vendors = Vendor::whereNotNull('business_license_path')
            ->orWhereNotNull('tax_certificate_path')
            ->orWhereNotNull('insurance_certificate_path')
            ->orWhereNotNull('additional_documents_paths')
            ->get();
            
        foreach ($vendors as $vendor) {
            // Business License
            if ($vendor->business_license_path) {
                $documents->push((object)[
                    'id' => 'vendor_' . $vendor->id . '_business_license',
                    'title' => $vendor->company_name . ' - Business License',
                    'document_type' => 'Business License',
                    'source' => 'vendor_system'
                ]);
            }
            
            // Tax Certificate
            if ($vendor->tax_certificate_path) {
                $documents->push((object)[
                    'id' => 'vendor_' . $vendor->id . '_tax_certificate',
                    'title' => $vendor->company_name . ' - Tax Certificate',
                    'document_type' => 'Tax Certificate',
                    'source' => 'vendor_system'
                ]);
            }
            
            // Insurance Certificate
            if ($vendor->insurance_certificate_path) {
                $documents->push((object)[
                    'id' => 'vendor_' . $vendor->id . '_insurance_certificate',
                    'title' => $vendor->company_name . ' - Insurance Certificate',
                    'document_type' => 'Insurance Certificate',
                    'source' => 'vendor_system'
                ]);
            }
            
            // Additional Documents
            if ($vendor->additional_documents_paths && is_array($vendor->additional_documents_paths)) {
                foreach ($vendor->additional_documents_paths as $index => $docPath) {
                    $documents->push((object)[
                        'id' => 'vendor_' . $vendor->id . '_additional_' . $index,
                        'title' => $vendor->company_name . ' - Additional Document #' . ($index + 1),
                        'document_type' => 'Additional Document',
                        'source' => 'vendor_system'
                    ]);
                }
            }
        }
        
        // 3. Get Purchase Orders
        $purchaseOrders = PurchaseOrder::with('vendor')->get();
        foreach ($purchaseOrders as $po) {
            $documents->push((object)[
                'id' => 'purchase_order_' . $po->id,
                'title' => 'Purchase Order #' . $po->po_number . ' (' . ($po->vendor->company_name ?? 'Unknown Vendor') . ')',
                'document_type' => 'Purchase Order',
                'source' => 'psm_system'
            ]);
        }
        
        // 4. Get Contracts
        $contracts = Contract::with('vendor')->get();
        foreach ($contracts as $contract) {
            $documents->push((object)[
                'id' => 'contract_' . $contract->id,
                'title' => 'Contract #' . $contract->contract_number . ' (' . ($contract->vendor->company_name ?? 'Unknown Vendor') . ')',
                'document_type' => 'Contract',
                'source' => 'psm_system'
            ]);
        }
        
        // 5. Get Inventory Receipts
        $inventoryReceipts = InventoryReceipt::with('items')->get();
        foreach ($inventoryReceipts as $receipt) {
            $documents->push((object)[
                'id' => 'inventory_receipt_' . $receipt->id,
                'title' => 'Inventory Receipt #' . $receipt->receipt_number . ' (' . ($receipt->supplier_name ?? 'Unknown Supplier') . ')',
                'document_type' => 'Inventory Receipt',
                'source' => 'sws_system'
            ]);
        }
        
        // Add documents from table and sort all
        $documents = $documents->concat($documentsFromTable)->sortBy('title');

        // Calculate real statistics
        $statistics = [
            'totalDocuments' => $documents->count(),
            'totalVersions' => DocumentVersion::count(),
            'recentChanges' => DocumentVersion::where('created_at', '>=', Carbon::now()->subDays(7))->count(),
            'activeUsers' => DocumentVersion::distinct('modified_by_id')
                ->where('created_at', '>=', Carbon::now()->subMonth())
                ->count()
        ];

        // If no documents exist, provide helpful message
        if ($documents->isEmpty()) {
            $statistics['noDocumentsMessage'] = 'No documents found. Please create documents first or ensure vendor documents are uploaded.';
        }

        return view('DTRS.version', array_merge($statistics, compact('documents')));
    }

    /**
     * Get version history for a specific document
     */
    public function getVersionHistory($documentId)
    {
        // Validate document exists and user has access
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $userRole = Auth::user()->role;
        if (!in_array($userRole, ['admin', 'logistics_staff'])) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        // Determine document source and find the document
        $documentTitle = '';
        $versions = collect();
        
        if (str_starts_with($documentId, 'doc_')) {
            // Document from documents table
            $realDocumentId = str_replace('doc_', '', $documentId);
            $document = Document::active()->find($realDocumentId);
            if (!$document) {
                return response()->json(['success' => false, 'message' => 'Document not found'], 404);
            }
            $documentTitle = $document->title;
            
            // Get version history from database
            $versions = DocumentVersion::forDocument($realDocumentId)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($version) {
                    return [
                        'id' => $version->id,
                        'document_id' => $version->document_id,
                        'version_number' => $version->version_number,
                        'modified_by' => $version->modified_by_name,
                        'user_role' => ucfirst(str_replace('_', ' ', $version->user_role)),
                        'created_at' => $version->created_at->toISOString(),
                        'changes_summary' => $version->changes_summary ?? 'No changes recorded',
                        'file_size' => $version->file_size,
                        'status' => $version->status,
                        'file_path' => $version->file_path
                    ];
                });
                
        } elseif (str_starts_with($documentId, 'vendor_')) {
            // Document from vendor system
            $parts = explode('_', $documentId);
            $vendorId = $parts[1];
            $documentType = $parts[2];
            
            $vendor = Vendor::find($vendorId);
            if (!$vendor) {
                return response()->json(['success' => false, 'message' => 'Vendor document not found'], 404);
            }
            
            $documentTitle = $vendor->company_name . ' - ' . ucfirst(str_replace('_', ' ', $documentType));
            
            // For vendor documents, create a mock version history since they don't have version tracking yet
            $versions = collect([
                [
                    'id' => 1,
                    'document_id' => $documentId,
                    'version_number' => '1.0',
                    'modified_by' => 'System',
                    'user_role' => 'Admin',
                    'created_at' => $vendor->created_at->toISOString(),
                    'changes_summary' => 'Initial document upload from vendor registration',
                    'file_size' => $this->getVendorFileSize($vendor, $documentType),
                    'status' => 'active',
                    'file_path' => $this->getVendorFilePath($vendor, $documentType)
                ]
            ]);
            
        } elseif (str_starts_with($documentId, 'purchase_order_')) {
            // Purchase Order document
            $poId = str_replace('purchase_order_', '', $documentId);
            $po = PurchaseOrder::with('vendor')->find($poId);
            if (!$po) {
                return response()->json(['success' => false, 'message' => 'Purchase order not found'], 404);
            }
            
            $documentTitle = 'Purchase Order #' . $po->po_number;
            
            $versions = collect([
                [
                    'id' => 1,
                    'document_id' => $documentId,
                    'version_number' => '1.0',
                    'modified_by' => 'System',
                    'user_role' => 'Procurement Officer',
                    'created_at' => $po->created_at->toISOString(),
                    'changes_summary' => 'Purchase order generated from PSM system',
                    'file_size' => 0, // Generated document
                    'status' => 'active',
                    'file_path' => null
                ]
            ]);
            
        } elseif (str_starts_with($documentId, 'contract_')) {
            // Contract document
            $contractId = str_replace('contract_', '', $documentId);
            $contract = Contract::with('vendor')->find($contractId);
            if (!$contract) {
                return response()->json(['success' => false, 'message' => 'Contract not found'], 404);
            }
            
            $documentTitle = 'Contract #' . $contract->contract_number;
            
            $versions = collect([
                [
                    'id' => 1,
                    'document_id' => $documentId,
                    'version_number' => '1.0',
                    'modified_by' => 'System',
                    'user_role' => 'Admin',
                    'created_at' => $contract->created_at->toISOString(),
                    'changes_summary' => 'Contract created in PSM system',
                    'file_size' => $contract->document_path ? $this->getFileSize($contract->document_path) : 0,
                    'status' => 'active',
                    'file_path' => $contract->document_path
                ]
            ]);
            
        } elseif (str_starts_with($documentId, 'inventory_receipt_')) {
            // Inventory Receipt document
            $receiptId = str_replace('inventory_receipt_', '', $documentId);
            $receipt = InventoryReceipt::find($receiptId);
            if (!$receipt) {
                return response()->json(['success' => false, 'message' => 'Inventory receipt not found'], 404);
            }
            
            $documentTitle = 'Inventory Receipt #' . $receipt->receipt_number;
            
            $versions = collect([
                [
                    'id' => 1,
                    'document_id' => $documentId,
                    'version_number' => '1.0',
                    'modified_by' => $receipt->received_by ?? 'System',
                    'user_role' => 'Logistics Staff',
                    'created_at' => $receipt->created_at->toISOString(),
                    'changes_summary' => 'Inventory receipt created in SWS system',
                    'file_size' => $receipt->document_path ? $this->getFileSize($receipt->document_path) : 0,
                    'status' => 'active',
                    'file_path' => $receipt->document_path
                ]
            ]);
            
        } else {
            return response()->json(['success' => false, 'message' => 'Invalid document ID format'], 400);
        }

        // Log the access
        $this->logVersionAccess($documentId, 'view_history', 'success');

        return response()->json([
            'success' => true,
            'versions' => $versions,
            'document_id' => $documentId,
            'document_title' => $documentTitle
        ]);
    }

    /**
     * View a specific version of a document
     */
    public function viewVersion($versionId)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthorized');
        }

        $userRole = Auth::user()->role;
        if (!in_array($userRole, ['admin', 'logistics_staff'])) {
            abort(403, 'Access denied');
        }

        // Handle different document types
        if (str_starts_with($versionId, 'vendor_')) {
            // Vendor documents
            $parts = explode('_', $versionId);
            $vendorId = $parts[1];
            $documentType = $parts[2];
            
            $vendor = Vendor::find($vendorId);
            if (!$vendor) {
                abort(404, 'Vendor document not found');
            }
            
            $filePath = $this->getVendorFilePath($vendor, $documentType);
            
            // Log the access attempt
            $this->logVersionAccess($versionId, 'view_version', $filePath ? 'success' : 'file_not_found');
            
            // Check if file exists
            if (!$filePath || !Storage::exists($filePath)) {
                return $this->showDocumentPlaceholder($vendor, $documentType, 'view');
            }
            
            return Storage::response($filePath);
            
        } elseif (str_starts_with($versionId, 'purchase_order_')) {
            // Purchase Order documents
            $poId = str_replace('purchase_order_', '', $versionId);
            $po = PurchaseOrder::find($poId);
            if (!$po) {
                abort(404, 'Purchase order not found');
            }
            
            $this->logVersionAccess($versionId, 'view_version', 'generated_document');
            
            return $this->showDocumentPlaceholder($po, 'purchase_order', 'view');
            
        } elseif (str_starts_with($versionId, 'contract_')) {
            // Contract documents
            $contractId = str_replace('contract_', '', $versionId);
            $contract = Contract::find($contractId);
            if (!$contract) {
                abort(404, 'Contract not found');
            }
            
            $this->logVersionAccess($versionId, 'view_version', $contract->document_path ? 'success' : 'file_not_found');
            
            if (!$contract->document_path || !Storage::exists($contract->document_path)) {
                return $this->showDocumentPlaceholder($contract, 'contract', 'view');
            }
            
            return Storage::response($contract->document_path);
            
        } elseif (str_starts_with($versionId, 'inventory_receipt_')) {
            // Inventory Receipt documents
            $receiptId = str_replace('inventory_receipt_', '', $versionId);
            $receipt = InventoryReceipt::find($receiptId);
            if (!$receipt) {
                abort(404, 'Inventory receipt not found');
            }
            
            $this->logVersionAccess($versionId, 'view_version', $receipt->document_path ? 'success' : 'file_not_found');
            
            if (!$receipt->document_path || !Storage::exists($receipt->document_path)) {
                return $this->showDocumentPlaceholder($receipt, 'inventory_receipt', 'view');
            }
            
            return Storage::response($receipt->document_path);
        }
        
        // Handle regular document versions
        $version = DocumentVersion::find($versionId);
        if (!$version) {
            abort(404, 'Version not found');
        }
        
        // Log the access
        $this->logVersionAccess($versionId, 'view_version', 'success');
        
        // Check if file exists
        if (!Storage::exists($version->file_path)) {
            // Return a placeholder view for missing document files
            return $this->showVersionPlaceholder($version, 'view');
        }
        
        // Return the file for viewing
        return Storage::response($version->file_path);
    }

    /**
     * Download a specific version of a document
     */
    public function downloadVersion($versionId)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthorized');
        }

        $userRole = Auth::user()->role;
        if (!in_array($userRole, ['admin', 'logistics_staff'])) {
            abort(403, 'Access denied');
        }

        // Handle vendor documents
        if (str_starts_with($versionId, 'vendor_')) {
            $parts = explode('_', $versionId);
            $vendorId = $parts[1];
            $documentType = $parts[2];
            
            $vendor = Vendor::find($vendorId);
            if (!$vendor) {
                abort(404, 'Vendor document not found');
            }
            
            $filePath = $this->getVendorFilePath($vendor, $documentType);
            
            // Log the access attempt
            $this->logVersionAccess($versionId, 'download_version', $filePath ? 'success' : 'file_not_found');
            
            // Check if file exists
            if (!$filePath || !Storage::exists($filePath)) {
                // Return a placeholder view instead of 404
                return $this->showDocumentPlaceholder($vendor, $documentType, 'download');
            }
            
            // Generate download filename
            $filename = $vendor->company_name . '_' . ucfirst(str_replace('_', ' ', $documentType)) . '.' . pathinfo($filePath, PATHINFO_EXTENSION);
            
            // Return the file for download
            return Storage::download($filePath, $filename);
        }
        
        // Handle regular document versions
        $version = DocumentVersion::find($versionId);
        if (!$version) {
            abort(404, 'Version not found');
        }
        
        // Log the access
        $this->logVersionAccess($versionId, 'download_version', 'success');
        
        // Check if file exists
        if (!Storage::exists($version->file_path)) {
            // Return a placeholder view for missing document files
            return $this->showVersionPlaceholder($version, 'download');
        }
        
        // Generate download filename
        $filename = $version->document->title . '_v' . $version->version_number . '.' . pathinfo($version->file_path, PATHINFO_EXTENSION);
        
        // Return the file for download
        return Storage::download($version->file_path, $filename);
    }

    /**
     * Restore a specific version (create new version based on old one)
     */
    public function restoreVersion(Request $request, $versionId)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        $userRole = Auth::user()->role;
        if ($userRole !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Only administrators can restore versions'], 403);
        }

        try {
            // Find the version to restore
            $versionToRestore = DocumentVersion::find($versionId);
            if (!$versionToRestore) {
                return response()->json(['success' => false, 'message' => 'Version not found'], 404);
            }

            // Check if document exists and is active
            $document = $versionToRestore->document;
            if (!$document || $document->status !== 'active') {
                return response()->json(['success' => false, 'message' => 'Document not found or inactive'], 404);
            }

            // Create new version based on the restored one
            $newVersion = $versionToRestore->restore([
                'modified_by_id' => Auth::id(),
                'modified_by_name' => Auth::user()->name,
                'user_role' => Auth::user()->role,
            ]);

            // Log the access
            $this->logVersionAccess($versionId, 'restore_version', 'success');

            return response()->json([
                'success' => true,
                'message' => 'Version restored successfully',
                'version_id' => $versionId,
                'new_version' => $newVersion->version_number
            ]);

        } catch (\Exception $e) {
            // Log the error
            $this->logVersionAccess($versionId, 'restore_version', 'failed');
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to restore version: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Compare versions (show differences)
     */
    public function compareVersions($versionId)
    {
        if (!Auth::check()) {
            abort(401, 'Unauthorized');
        }

        $userRole = Auth::user()->role;
        if (!in_array($userRole, ['admin', 'logistics_staff'])) {
            abort(403, 'Access denied');
        }

        // Handle vendor documents
        if (str_starts_with($versionId, 'vendor_')) {
            // Log the access
            $this->logVersionAccess($versionId, 'compare_version', 'not_applicable');
            
            return view('DTRS.version-compare', [
                'message' => 'Version comparison is not available for vendor documents.',
                'reason' => 'Vendor documents only have one version (the uploaded file).',
                'version_id' => $versionId
            ]);
        }
        
        // Handle regular document versions
        $version = DocumentVersion::find($versionId);
        if (!$version) {
            abort(404, 'Version not found');
        }
        
        // Log the access
        $this->logVersionAccess($versionId, 'compare_version', 'success');
        
        // Get the current version for comparison
        $currentVersion = $version->document->latestVersion();
        
        return view('DTRS.version-compare', [
            'version' => $version,
            'currentVersion' => $currentVersion,
            'document' => $version->document,
            'message' => 'Version comparison functionality is under development.',
            'reason' => 'This feature will show side-by-side differences between document versions.'
        ]);
    }

    /**
     * Get file size for any file path
     */
    private function getFileSize($filePath)
    {
        if ($filePath && Storage::exists($filePath)) {
            return Storage::size($filePath);
        }
        return rand(500000, 2000000); // Mock file size for demo
    }

    /**
     * Get vendor file size
     */
    private function getVendorFileSize($vendor, $documentType)
    {
        $filePath = $this->getVendorFilePath($vendor, $documentType);
        return $this->getFileSize($filePath);
    }

    /**
     * Get vendor file path
     */
    private function getVendorFilePath($vendor, $documentType)
    {
        switch ($documentType) {
            case 'business':
            case 'license':
                return $vendor->business_license_path;
            case 'tax':
            case 'certificate':
                return $vendor->tax_certificate_path;
            case 'insurance':
                return $vendor->insurance_certificate_path;
            default:
                return $vendor->additional_documents_paths;
        }
    }

    /**
     * Show placeholder for missing documents
     */
    private function showDocumentPlaceholder($entity, $documentType, $action)
    {
        // Determine document info based on type
        if ($entity instanceof Vendor) {
            return view('DTRS.document-placeholder', [
                'vendor' => $entity,
                'documentType' => $documentType,
                'action' => $action,
                'message' => 'Document File Not Found',
                'reason' => 'The requested document file is not available in storage.',
                'suggestions' => [
                    'The document may not have been uploaded yet',
                    'The file may have been moved or deleted',
                    'Contact the vendor to re-upload the document'
                ]
            ]);
        } elseif ($entity instanceof PurchaseOrder) {
            return view('DTRS.document-placeholder', [
                'purchaseOrder' => $entity,
                'documentType' => $documentType,
                'action' => $action,
                'message' => 'Generated Document',
                'reason' => 'This is a system-generated document from the PSM module.',
                'suggestions' => [
                    'Purchase orders are generated dynamically',
                    'View the purchase order details in PSM module',
                    'Contact administrator for PDF generation features'
                ]
            ]);
        } elseif ($entity instanceof Contract) {
            return view('DTRS.document-placeholder', [
                'contract' => $entity,
                'documentType' => $documentType,
                'action' => $action,
                'message' => 'Contract Document Not Available',
                'reason' => 'The contract document file is not available in storage.',
                'suggestions' => [
                    'The contract document may not have been uploaded',
                    'The file may have been moved or deleted',
                    'Contact administrator to upload the contract document'
                ]
            ]);
        } elseif ($entity instanceof InventoryReceipt) {
            return view('DTRS.document-placeholder', [
                'inventoryReceipt' => $entity,
                'documentType' => $documentType,
                'action' => $action,
                'message' => 'Receipt Document Not Available',
                'reason' => 'The inventory receipt document is not available in storage.',
                'suggestions' => [
                    'The receipt document may not have been generated',
                    'The file may have been moved or deleted',
                    'Contact logistics staff to regenerate the receipt'
                ]
            ]);
        }
        
        // Fallback
        return view('DTRS.document-placeholder', [
            'entity' => $entity,
            'documentType' => $documentType,
            'action' => $action,
            'message' => 'Document Not Available',
            'reason' => 'The requested document is not available.',
            'suggestions' => [
                'Contact system administrator for assistance'
            ]
        ]);
    }

    /**
     * Show placeholder for missing document versions
     */
    private function showVersionPlaceholder($version, $action)
    {
        return view('DTRS.document-placeholder', [
            'version' => $version,
            'document' => $version->document,
            'action' => $action,
            'message' => 'Version File Not Found',
            'reason' => 'The requested version file is not available in storage.',
            'suggestions' => [
                'The version file may have been moved or deleted',
                'Storage location may have changed',
                'Contact administrator to restore the file'
            ]
        ]);
    }

    /**
     * Log version access for audit trail
     */
    private function logVersionAccess($resourceId, $action, $status)
    {
        try {
            // In a real implementation, you would use the AuditLog model
            // For now, just log to Laravel's log file
            \Log::info('Document Version Access', [
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'user_role' => Auth::user()->role,
                'action' => $action,
                'module' => 'DTRS',
                'resource_type' => 'DocumentVersion',
                'resource_id' => $resourceId,
                'status' => $status,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp' => Carbon::now()->toISOString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log version access: ' . $e->getMessage());
        }
    }
}
