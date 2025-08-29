<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PSMContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::with('vendor')->latest()->get();
        return view('PSM.contract', compact('contracts'));
    }

    /**
     * Get contract details for viewing
     */
    public function view($id)
    {
        try {
            $contract = Contract::with('vendor')->find($id);
            
            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contract not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'contract' => [
                    'id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'status' => $contract->status,
                    'value' => $contract->value,
                    'start_date' => $contract->start_date,
                    'end_date' => $contract->end_date,
                    'terms' => $contract->terms,
                    'vendor' => $contract->vendor ? [
                        'id' => $contract->vendor->id,
                        'name' => $contract->vendor->name,
                        'company_name' => $contract->vendor->company_name,
                        'email' => $contract->vendor->email,
                        'phone' => $contract->vendor->phone,
                        'address' => $contract->vendor->address,
                        'business_type' => $contract->vendor->business_type
                    ] : null,
                    'documents' => [] // Add document relationships if they exist
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving contract details: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Download contract document
     */
    public function download($id, Request $request)
    {
        try {
            $contract = Contract::find($id);
            
            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'message' => 'Contract not found'
                ], 404);
            }

            // Check if contract has a document file path
            if (!$contract->document_path || !Storage::exists($contract->document_path)) {
                // Generate a sample PDF response for now
                return $this->generateContractPDF($contract);
            }

            // Return the actual stored document
            $filePath = storage_path('app/' . $contract->document_path);
            $fileName = 'Contract_' . $contract->contract_number . '.pdf';

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading contract: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a sample contract PDF (fallback when no document exists)
     */
    private function generateContractPDF($contract)
    {
        $html = $this->generateContractHTML($contract);
        
        // For now, return HTML content as PDF-like response
        // In production, you'd use a PDF library like DomPDF or wkhtmltopdf
        return response($html, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="Contract_' . $contract->contract_number . '.pdf"'
        ]);
    }

    /**
     * Generate contract HTML content
     */
    private function generateContractHTML($contract)
    {
        $vendor = $contract->vendor;
        
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <title>Contract {$contract->contract_number}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; margin-bottom: 30px; }
                .contract-info { margin-bottom: 20px; }
                .vendor-info { margin-bottom: 20px; }
                .terms { margin-top: 20px; }
                table { width: 100%; border-collapse: collapse; }
                td { padding: 8px; border-bottom: 1px solid #ddd; }
                .label { font-weight: bold; width: 150px; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>CONTRACT AGREEMENT</h1>
                <h2>Contract No: {$contract->contract_number}</h2>
            </div>
            
            <div class='contract-info'>
                <h3>Contract Information</h3>
                <table>
                    <tr><td class='label'>Contract Number:</td><td>{$contract->contract_number}</td></tr>
                    <tr><td class='label'>Status:</td><td>{$contract->status}</td></tr>
                    <tr><td class='label'>Contract Value:</td><td>₱" . number_format($contract->value, 2) . "</td></tr>
                    <tr><td class='label'>Start Date:</td><td>{$contract->start_date}</td></tr>
                    <tr><td class='label'>End Date:</td><td>{$contract->end_date}</td></tr>
                </table>
            </div>
            
            <div class='vendor-info'>
                <h3>Vendor Information</h3>
                <table>
                    <tr><td class='label'>Company:</td><td>" . ($vendor->company_name ?? 'N/A') . "</td></tr>
                    <tr><td class='label'>Contact Person:</td><td>" . ($vendor->name ?? 'N/A') . "</td></tr>
                    <tr><td class='label'>Email:</td><td>" . ($vendor->email ?? 'N/A') . "</td></tr>
                    <tr><td class='label'>Phone:</td><td>" . ($vendor->phone ?? 'N/A') . "</td></tr>
                    <tr><td class='label'>Address:</td><td>" . ($vendor->address ?? 'N/A') . "</td></tr>
                </table>
            </div>
            
            <div class='terms'>
                <h3>Terms and Conditions</h3>
                <p>" . ($contract->terms ?? 'No terms specified') . "</p>
            </div>
            
            <div style='margin-top: 50px;'>
                <p>Generated on: " . date('Y-m-d H:i:s') . "</p>
            </div>
        </body>
        </html>";
    }
}


