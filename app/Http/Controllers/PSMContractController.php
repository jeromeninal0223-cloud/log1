<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Services\ContractTermsService;
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
        \Log::info('Contract download requested', ['contract_id' => $id]);
        
        try {
            $contract = Contract::with(['vendor', 'bid'])->find($id);
            
            if (!$contract) {
                \Log::warning('Contract not found', ['contract_id' => $id]);
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Contract not found'
                    ], 404);
                }
                abort(404, 'Contract not found');
            }

            \Log::info('Contract found, generating PDF', [
                'contract_id' => $id,
                'contract_number' => $contract->contract_number,
                'has_vendor' => !is_null($contract->vendor),
                'has_bid' => !is_null($contract->bid)
            ]);

            // Check if contract has a document file path
            if (!empty($contract->document_path) && Storage::exists($contract->document_path)) {
                \Log::info('Using stored document', ['document_path' => $contract->document_path]);
                // Return the actual stored document
                $filePath = storage_path('app/' . $contract->document_path);
                $fileName = 'Contract_' . $contract->contract_number . '.pdf';
                return response()->download($filePath, $fileName);
            }

            \Log::info('Generating PDF dynamically');
            // Generate PDF using DomPDF
            return $this->generateContractPDF($contract);

        } catch (\Exception $e) {
            \Log::error('Contract download error: ' . $e->getMessage(), [
                'contract_id' => $id,
                'error_message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error downloading contract: ' . $e->getMessage()
                ], 500);
            }
            
            // For non-JSON requests, try to return HTML version
            try {
                $contract = Contract::with(['vendor', 'bid'])->find($id);
                if ($contract) {
                    $html = $this->generateContractHTML($contract);
                    return response($html, 200, [
                        'Content-Type' => 'text/html',
                        'Content-Disposition' => 'inline; filename="Contract_' . $contract->contract_number . '.html"'
                    ]);
                }
            } catch (\Exception $htmlError) {
                // Final fallback
                \Log::error('HTML generation also failed', ['error' => $htmlError->getMessage()]);
                abort(500, 'Unable to generate contract document');
            }
        }
    }

    /**
     * Generate contract PDF using DomPDF
     */
    private function generateContractPDF($contract)
    {
        try {
            \Log::info('Starting PDF generation', ['contract_id' => $contract->id]);
            
            // Generate HTML content
            $html = $this->generateContractHTML($contract);
            \Log::info('HTML generated successfully', ['html_length' => strlen($html)]);
            
            // Try to use DomPDF for PDF generation
            try {
                \Log::info('Attempting to load DomPDF');
                
                // Try to include DomPDF manually if autoload fails
                if (!class_exists('\Dompdf\Dompdf')) {
                    $vendorPath = base_path('vendor/dompdf/dompdf/autoload.inc.php');
                    \Log::info('Checking DomPDF path: ' . $vendorPath);
                    if (file_exists($vendorPath)) {
                        require_once $vendorPath;
                        \Log::info('DomPDF loaded manually');
                    } else {
                        \Log::warning('DomPDF autoload file not found at: ' . $vendorPath);
                    }
                }
                
                if (class_exists('\Dompdf\Dompdf')) {
                    \Log::info('DomPDF is available, creating PDF');
                    
                    $options = new \Dompdf\Options();
                    $options->set('defaultFont', 'Arial');
                    $options->set('isRemoteEnabled', false);
                    $options->set('isHtml5ParserEnabled', true);
                    $options->set('debugKeepTemp', false);
                    $options->set('debugCss', false);
                    $options->set('debugLayout', false);
                    $options->set('debugLayoutLines', false);
                    $options->set('debugLayoutBlocks', false);
                    $options->set('debugLayoutInline', false);
                    $options->set('debugLayoutPaddingBox', false);
                    
                    $dompdf = new \Dompdf\Dompdf($options);
                    
                    // Clean HTML for better PDF compatibility
                    $cleanHtml = $this->cleanHtmlForPdf($html);
                    
                    $dompdf->loadHtml($cleanHtml);
                    $dompdf->setPaper('A4', 'portrait');
                    $dompdf->render();
                    
                    $output = $dompdf->output();
                    $fileName = 'Contract_' . $contract->contract_number . '.pdf';
                    
                    \Log::info('PDF generated successfully', [
                        'filename' => $fileName,
                        'size' => strlen($output)
                    ]);
                    
                    // Return PDF with proper headers
                    return response($output, 200, [
                        'Content-Type' => 'application/pdf',
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                        'Content-Length' => strlen($output),
                        'Cache-Control' => 'no-cache, no-store, must-revalidate',
                        'Pragma' => 'no-cache',
                        'Expires' => '0'
                    ]);
                } else {
                    throw new \Exception('DomPDF class not found');
                }
                
            } catch (\Exception $pdfError) {
                \Log::error('PDF generation failed: ' . $pdfError->getMessage());
                \Log::warning('Falling back to HTML response');
                
                // Fallback to HTML response
                $fileName = 'Contract_' . $contract->contract_number . '.html';
                
                return response($html, 200, [
                    'Content-Type' => 'text/html',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ]);
            }
            
        } catch (\Exception $e) {
            \Log::error('PDF generation error: ' . $e->getMessage(), [
                'contract_id' => $contract->id,
                'trace' => $e->getTraceAsString()
            ]);
            
            // If PDF generation fails, return HTML
            $html = $this->generateContractHTML($contract);
            return response($html, 200, [
                'Content-Type' => 'text/html',
                'Content-Disposition' => 'attachment; filename="Contract_' . $contract->contract_number . '.html"'
            ]);
        }
    }

    /**
     * Generate contract HTML content using ContractTermsService
     */
    private function generateContractHTML($contract)
    {
        try {
            \Log::info('Attempting to use ContractTermsService');
            // Use the ContractTermsService to generate professional contract terms
            $contractTerms = ContractTermsService::generateContractTerms($contract);
            \Log::info('ContractTermsService succeeded');
            
            return "
            <!DOCTYPE html>
            <html>
            <head>
                <title>Contract {$contract->contract_number}</title>
                <meta charset='UTF-8'>
                <style>
                    body { 
                        font-family: Arial, sans-serif; 
                        margin: 20px; 
                        padding: 0;
                        line-height: 1.4;
                        color: #000;
                        font-size: 12px;
                    }
                    .contract-content {
                        max-width: 100%;
                    }
                </style>
            </head>
            <body>
                <div class='contract-content'>
                    {$contractTerms}
                </div>
            </body>
            </html>";
            
        } catch (\Exception $e) {
            \Log::error('ContractTermsService failed: ' . $e->getMessage());
            // Fallback to basic contract if ContractTermsService fails
            return $this->generateBasicContractHTML($contract);
        }
    }
    
    /**
     * Generate basic contract HTML as fallback
     */
    private function generateBasicContractHTML($contract)
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
                " . ($contract->terms ?? '<h3>Terms and Conditions</h3><p>No terms specified</p>') . "
            </div>
            
            <div style='margin-top: 50px;'>
                <p>Generated on: " . date('Y-m-d H:i:s') . "</p>
            </div>
        </body>
        </html>";
    }

    /**
     * Clean HTML for better PDF compatibility
     */
    private function cleanHtmlForPdf($html)
    {
        // Remove or replace problematic elements for PDF generation
        
        // Replace base64 images with placeholder text if they're causing issues
        $html = preg_replace_callback('/<img[^>]+src="data:image\/[^"]*"[^>]*>/i', function($matches) {
            // For now, replace with a simple signature placeholder
            // You can enhance this later to save images as files and reference them
            return '<div style="border: 2px solid #28a745; padding: 10px; text-align: center; background-color: #f8f9fa; margin: 10px 0;">
                        <strong style="color: #28a745;">✓ DIGITAL SIGNATURE APPLIED</strong>
                    </div>';
        }, $html);
        
        // Ensure proper CSS for PDF
        $html = str_replace('<style>', '<style>
            @page { margin: 20mm; }
            body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; }
            .page-break { page-break-before: always; }
        ', $html);
        
        return $html;
    }
}


