<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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
        // Parse document ID to get vendor and document type
        $parts = explode('_', $documentId);
        if (count($parts) < 3) {
            abort(404);
        }

        $vendorId = $parts[1];
        $docType = $parts[2];

        $vendor = Vendor::findOrFail($vendorId);

        $filePath = null;
        switch ($docType) {
            case 'business':
                $filePath = $vendor->business_license_path;
                break;
            case 'tax':
                $filePath = $vendor->tax_certificate_path;
                break;
            case 'insurance':
                $filePath = $vendor->insurance_certificate_path;
                break;
            default:
                if (str_starts_with($docType, 'additional')) {
                    $index = (int) str_replace('additional_', '', $docType);
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

    public function downloadDocument($documentId)
    {
        // Same logic as viewDocument but force download
        $parts = explode('_', $documentId);
        if (count($parts) < 3) {
            abort(404);
        }

        $vendorId = $parts[1];
        $docType = $parts[2];

        $vendor = Vendor::findOrFail($vendorId);

        $filePath = null;
        switch ($docType) {
            case 'business':
                $filePath = $vendor->business_license_path;
                break;
            case 'tax':
                $filePath = $vendor->tax_certificate_path;
                break;
            case 'insurance':
                $filePath = $vendor->insurance_certificate_path;
                break;
            default:
                if (str_starts_with($docType, 'additional')) {
                    $index = (int) str_replace('additional_', '', $docType);
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

    public function documentMetadata($documentId)
    {
        $parts = explode('_', $documentId);
        if (count($parts) < 3) {
            return response()->json(['error' => 'Invalid document ID'], 404);
        }

        $vendorId = $parts[1];
        $docType = $parts[2];

        $vendor = Vendor::findOrFail($vendorId);

        $filePath = null;
        $documentType = '';
        
        switch ($docType) {
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
                if (str_starts_with($docType, 'additional')) {
                    $index = (int) str_replace('additional_', '', $docType);
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

        return response()->json([
            'document_id' => 'DOC-V' . strtoupper(substr($docType, 0, 2)) . '-' . str_pad($vendorId, 4, '0', STR_PAD_LEFT),
            'filename' => basename($filePath),
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'created_at' => $vendor->created_at,
            'source_module' => 'PSM - Vendor Management',
            'vendor_name' => $vendor->company_name,
            'document_type' => $documentType,
            'checksum' => hash_file('md5', Storage::path($filePath)),
        ]);
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
