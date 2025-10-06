<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use App\Models\Bid;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PDF;

class ContractSigningController extends Controller
{
    /**
     * Initiate contract negotiation after bid winner selection
     */
    public function initiateNegotiation($bidId, Request $request)
    {
        $bid = Bid::with('vendor')->findOrFail($bidId);
        
        // Create contract in draft status
        $contract = Contract::create([
            'contract_number' => $this->generateContractNumber(),
            'bid_id' => $bid->id,
            'vendor_id' => $bid->vendor_id,
            'title' => $bid->title ?? 'Contract for Bid #' . $bid->id,
            'description' => $bid->description,
            'value' => $bid->amount,
            'negotiated_value' => $bid->amount, // Initial value
            'workflow_status' => 'draft',
            'status' => 'Draft',
            'start_date' => now(),
            'end_date' => now()->addMonths(12),
            'procurement_officer_id' => Auth::id(),
            'terms' => 'Standard contract terms and conditions apply. This contract is subject to company policies and applicable laws.',
        ]);

        // Generate initial contract document
        // TODO: Re-enable when PDF templates are created
        // $this->generateContractDocument($contract);

        return response()->json([
            'success' => true,
            'message' => 'Contract negotiation initiated',
            'contract_id' => $contract->id,
            'contract_number' => $contract->contract_number
        ]);
    }

    /**
     * Update negotiated terms and pricing
     */
    public function updateNegotiatedTerms($contractId, Request $request)
    {
        $request->validate([
            'negotiated_value' => 'required|numeric|min:0',
            'terms' => 'required|array',
            'negotiation_notes' => 'nullable|string'
        ]);

        $contract = Contract::findOrFail($contractId);
        
        // Store revision history
        $revisionHistory = $contract->revision_history ?? [];
        $revisionHistory[] = [
            'timestamp' => now(),
            'user_id' => Auth::id(),
            'changes' => [
                'old_value' => $contract->negotiated_value,
                'new_value' => $request->negotiated_value,
                'old_terms' => $contract->negotiated_terms,
                'new_terms' => $request->terms
            ],
            'notes' => $request->negotiation_notes
        ];

        $contract->update([
            'negotiated_value' => $request->negotiated_value,
            'negotiated_terms' => $request->terms,
            'negotiation_notes' => $request->negotiation_notes,
            'revision_history' => $revisionHistory,
            'workflow_status' => 'under_negotiation'
        ]);

        // Regenerate contract document with new terms
        // TODO: Re-enable when PDF templates are created
        // $this->generateContractDocument($contract);

        return response()->json([
            'success' => true,
            'message' => 'Contract terms updated successfully'
        ]);
    }

    /**
     * Send contract to vendor for review and signature
     */
    public function sendForVendorSignature($contractId, Request $request)
    {
        $contract = Contract::with('vendor')->findOrFail($contractId);
        
        $contract->update([
            'workflow_status' => 'pending_vendor_signature',
            'sent_for_review_at' => now()
        ]);

        // Here you would typically send an email to the vendor
        // with a secure link to review and sign the contract
        
        Log::info('Contract sent to vendor for signature', [
            'contract_id' => $contract->id,
            'vendor_id' => $contract->vendor_id,
            'vendor_email' => $contract->vendor->email ?? 'N/A'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract sent to vendor for signature',
            'signing_url' => route('contract.vendor-sign', ['token' => $this->generateSigningToken($contract)])
        ]);
    }

    /**
     * Vendor signs the contract
     */
    public function vendorSign($contractId, Request $request)
    {
        try {
            $request->validate([
                'signature_data' => 'required|string',
                'agreed_terms' => 'required|boolean|accepted'
            ]);

            $contract = Contract::findOrFail($contractId);
        
        // Allow vendor signing if contract is in appropriate status
        $allowedStatuses = ['pending_vendor_signature', 'draft', 'under_negotiation'];
        if (!in_array($contract->workflow_status, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'error' => 'Contract is not ready for vendor signature. Current status: ' . $contract->workflow_status
            ], 400);
        }

        // Generate signature hash for security
        $signatureHash = Hash::make($request->signature_data . $contract->id . now());

        $contract->update([
            'vendor_signed_at' => now(),
            'vendor_signature_hash' => $signatureHash,
            'vendor_signature_ip' => $request->ip(),
            'vendor_signature_image' => $request->signature_data, // Store the actual signature image data
        ]);

        // Check if both vendor and procurement have signed
        if ($contract->vendor_signed_at && $contract->procurement_signed_at) {
            $contract->update([
                'workflow_status' => 'pending_approval',
                'status' => 'Pending Approval'
            ]);
        } else {
            $contract->update([
                'workflow_status' => 'pending_procurement_signature'
            ]);
        }

        // Store signature image/data securely
        $signaturePath = 'contracts/signatures/vendor_' . $contract->id . '_' . time() . '.png';
        
        // Remove data URL prefix if present
        $signatureData = $request->signature_data;
        if (strpos($signatureData, 'data:image/png;base64,') === 0) {
            $signatureData = substr($signatureData, strlen('data:image/png;base64,'));
        }
        
        Storage::disk('private')->put($signaturePath, base64_decode($signatureData));

        // Regenerate contract terms with signature information
        $contract->generateTerms();

        Log::info('Vendor signed contract', [
            'contract_id' => $contract->id,
            'vendor_id' => $contract->vendor_id,
            'signed_at' => $contract->vendor_signed_at
        ]);

        $message = $contract->workflow_status === 'pending_approval' 
            ? 'Contract signed successfully! Both parties have signed. Contract is now pending approval.'
            : 'Contract signed successfully! Waiting for procurement signature.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'workflow_status' => $contract->workflow_status
        ]);
        
        } catch (\Exception $e) {
            Log::error('Vendor signing error', [
                'contract_id' => $contractId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to sign contract: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procurement officer signs the contract
     */
    public function procurementSign($contractId, Request $request)
    {
        try {
            $request->validate([
                'signature_data' => 'required|string',
                'final_approval' => 'required|boolean|accepted'
            ]);

            $contract = Contract::findOrFail($contractId);
        
        // Allow signing if contract is in draft, under_negotiation, or pending_procurement_signature status
        $allowedStatuses = ['draft', 'under_negotiation', 'pending_procurement_signature', 'pending_vendor_signature'];
        if (!in_array($contract->workflow_status, $allowedStatuses)) {
            return response()->json([
                'success' => false,
                'error' => 'Contract is not ready for procurement signature. Current status: ' . $contract->workflow_status
            ], 400);
        }

        // Generate signature hash
        $signatureHash = Hash::make($request->signature_data . $contract->id . now());

        $contract->update([
            'procurement_signed_at' => now(),
            'procurement_signature_hash' => $signatureHash,
            'procurement_signature_ip' => $request->ip(),
            'procurement_signature_image' => $request->signature_data, // Store the actual signature image data
        ]);

        // Check if both vendor and procurement have signed
        if ($contract->vendor_signed_at && $contract->procurement_signed_at) {
            $contract->update([
                'workflow_status' => 'pending_approval',
                'status' => 'Pending Approval'
            ]);
        } else {
            $contract->update([
                'workflow_status' => 'pending_vendor_signature'
            ]);
        }

        // Store procurement signature
        $signaturePath = 'contracts/signatures/procurement_' . $contract->id . '_' . time() . '.png';
        
        // Remove data URL prefix if present
        $signatureData = $request->signature_data;
        if (strpos($signatureData, 'data:image/png;base64,') === 0) {
            $signatureData = substr($signatureData, strlen('data:image/png;base64,'));
        }
        
        Storage::disk('private')->put($signaturePath, base64_decode($signatureData));

        // Generate final signed contract document
        // TODO: Re-enable when PDF templates are created
        // $this->generateFinalContractDocument($contract);

        // Regenerate contract terms with signature information
        $contract->generateTerms();

        Log::info('Procurement officer signed contract', [
            'contract_id' => $contract->id,
            'procurement_officer' => Auth::id(),
            'signed_at' => $contract->procurement_signed_at,
            'workflow_status' => $contract->workflow_status
        ]);

        $message = $contract->workflow_status === 'pending_approval' 
            ? 'Contract signed successfully! Both parties have signed. Contract is now pending approval.'
            : 'Contract signed successfully! Waiting for vendor signature.';

        return response()->json([
            'success' => true,
            'message' => $message,
            'contract_number' => $contract->contract_number,
            'workflow_status' => $contract->workflow_status
        ]);
        
        } catch (\Exception $e) {
            Log::error('Procurement signing error', [
                'contract_id' => $contractId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to sign contract: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get contract signing status and details
     */
    public function getSigningStatus($contractId)
    {
        try {
            $contract = Contract::with(['vendor', 'bid'])->find($contractId);
            
            if (!$contract) {
                return response()->json([
                    'success' => false,
                    'error' => 'Contract not found',
                    'contract_id' => $contractId
                ], 404);
            }
        
            return response()->json([
            'success' => true,
            'contract' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'title' => $contract->title,
                'value' => $contract->value,
                'negotiated_value' => $contract->negotiated_value,
                'negotiated_terms' => $contract->negotiated_terms ? json_encode($contract->negotiated_terms) : null,
                'negotiation_notes' => $contract->negotiation_notes,
                'workflow_status' => $contract->workflow_status,
                'vendor_signed' => !is_null($contract->vendor_signed_at),
                'vendor_signed_at' => $contract->vendor_signed_at,
                'procurement_signed' => !is_null($contract->procurement_signed_at),
                'procurement_signed_at' => $contract->procurement_signed_at,
                'is_fully_signed' => $contract->workflow_status === 'fully_signed',
                'vendor' => $contract->vendor ? [
                    'name' => $contract->vendor->company_name ?? $contract->vendor->name,
                    'email' => $contract->vendor->email,
                    'business_type' => $contract->vendor->business_type ?? null
                ] : null,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'terms' => $contract->terms
            ],
            'signing_progress' => [
                'draft_created' => !is_null($contract->created_at),
                'terms_negotiated' => $contract->workflow_status !== 'draft',
                'vendor_signed' => !is_null($contract->vendor_signed_at),
                'procurement_signed' => !is_null($contract->procurement_signed_at),
                'fully_executed' => $contract->workflow_status === 'fully_signed'
            ]
        ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error retrieving contract: ' . $e->getMessage(),
                'contract_id' => $contractId
            ], 500);
        }
    }

    /**
     * Generate contract document PDF
     */
    private function generateContractDocument($contract)
    {
        $data = [
            'contract' => $contract,
            'vendor' => $contract->vendor,
            'bid' => $contract->bid,
            'terms' => $contract->negotiated_terms ?? [],
            'generated_at' => now()
        ];

        $pdf = PDF::loadView('contracts.template', $data);
        $filename = 'contract_draft_' . $contract->contract_number . '.pdf';
        $path = 'contracts/drafts/' . $filename;
        
        Storage::disk('private')->put($path, $pdf->output());
        
        $contract->update(['draft_document_path' => $path]);
    }

    /**
     * Generate final signed contract document
     */
    private function generateFinalContractDocument($contract)
    {
        $data = [
            'contract' => $contract,
            'vendor' => $contract->vendor,
            'bid' => $contract->bid,
            'terms' => $contract->negotiated_terms ?? [],
            'signatures' => [
                'vendor_signed_at' => $contract->vendor_signed_at,
                'procurement_signed_at' => $contract->procurement_signed_at
            ],
            'generated_at' => now()
        ];

        $pdf = PDF::loadView('contracts.final-template', $data);
        $filename = 'contract_final_' . $contract->contract_number . '.pdf';
        $path = 'contracts/final/' . $filename;
        
        Storage::disk('private')->put($path, $pdf->output());
        
        $contract->update(['final_document_path' => $path]);
    }

    /**
     * Generate unique contract number
     */
    private function generateContractNumber()
    {
        $year = date('Y');
        $count = Contract::whereYear('created_at', $year)->count() + 1;
        return 'CON-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate secure signing token
     */
    private function generateSigningToken($contract)
    {
        return Hash::make($contract->id . $contract->created_at . 'signing_token');
    }

    /**
     * Download contract document
     */
    public function downloadContract($contractId, $type = 'draft')
    {
        $contract = Contract::findOrFail($contractId);
        
        $path = $type === 'final' ? $contract->final_document_path : $contract->draft_document_path;
        
        if (!$path || !Storage::disk('private')->exists($path)) {
            abort(404, 'Contract document not found');
        }

        return Storage::disk('private')->download($path);
    }
}
