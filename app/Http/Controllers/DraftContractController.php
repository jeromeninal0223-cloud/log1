<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DraftContractController extends Controller
{
    /**
     * Display the draft contracts page
     */
    public function index()
    {
        $contracts = Contract::with(['vendor', 'bid'])
            ->whereIn('workflow_status', [
                'draft', 
                'under_negotiation', 
                'pending_vendor_signature', 
                'pending_procurement_signature',
                'pending_approval'
            ])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('PSM.draft-contracts', compact('contracts'));
    }

    /**
     * Display the contract approval page
     */
    public function approval()
    {
        $contracts = Contract::with(['vendor', 'bid', 'approvedBy'])
            ->whereIn('workflow_status', [
                'pending_approval',
                'approved',
                'rejected'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('PSM.contract-approval', compact('contracts'));
    }

    /**
     * Get contract details for viewing
     */
    public function show($id)
    {
        $contract = Contract::with(['vendor', 'bid'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'contract' => [
                'id' => $contract->id,
                'contract_number' => $contract->contract_number,
                'title' => $contract->title,
                'description' => $contract->description,
                'value' => $contract->value,
                'negotiated_value' => $contract->negotiated_value,
                'workflow_status' => $contract->workflow_status,
                'status' => $contract->status,
                'vendor_signed' => !is_null($contract->vendor_signed_at),
                'vendor_signed_at' => $contract->vendor_signed_at,
                'procurement_signed' => !is_null($contract->procurement_signed_at),
                'procurement_signed_at' => $contract->procurement_signed_at,
                'start_date' => $contract->start_date,
                'end_date' => $contract->end_date,
                'terms' => $contract->terms,
                'vendor' => $contract->vendor ? [
                    'name' => $contract->vendor->company_name ?? $contract->vendor->name,
                    'email' => $contract->vendor->email,
                    'phone' => $contract->vendor->phone,
                    'address' => $contract->vendor->address,
                    'business_type' => $contract->vendor->business_type
                ] : null,
                'created_at' => $contract->created_at,
                'updated_at' => $contract->updated_at
            ]
        ]);
    }

    /**
     * Send contract to vendor for signature
     */
    public function sendForVendorSignature($id)
    {
        $contract = Contract::with('vendor')->findOrFail($id);
        
        // Update workflow status
        $contract->update([
            'workflow_status' => 'pending_vendor_signature',
            'sent_for_review_at' => now()
        ]);

        // Here you would typically send an email notification to the vendor
        // For now, we'll just log it
        \Log::info('Contract sent to vendor for signature', [
            'contract_id' => $contract->id,
            'vendor_id' => $contract->vendor_id,
            'vendor_email' => $contract->vendor->email ?? 'N/A'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract sent to vendor for signature',
            'contract_status' => $contract->workflow_status
        ]);
    }

    /**
     * Update contract workflow status after both parties sign
     */
    public function updateWorkflowStatus($id)
    {
        $contract = Contract::findOrFail($id);
        
        // Check if both vendor and procurement have signed
        if ($contract->vendor_signed_at && $contract->procurement_signed_at) {
            $contract->update([
                'workflow_status' => 'pending_approval',
                'status' => 'Pending Approval'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Contract status updated to pending approval',
                'workflow_status' => $contract->workflow_status
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Both signatures required before approval'
        ]);
    }

    /**
     * Approve a fully signed contract
     */
    public function approve($id, Request $request)
    {
        $request->validate([
            'approval_notes' => 'nullable|string|max:1000'
        ]);

        $contract = Contract::findOrFail($id);
        
        // Verify contract is ready for approval
        if ($contract->workflow_status !== 'pending_approval') {
            return response()->json([
                'success' => false,
                'error' => 'Contract is not ready for approval'
            ], 400);
        }

        if (!$contract->vendor_signed_at || !$contract->procurement_signed_at) {
            return response()->json([
                'success' => false,
                'error' => 'Both vendor and procurement signatures required'
            ], 400);
        }

        // Approve the contract
        $contract->update([
            'workflow_status' => 'approved',
            'status' => 'Active',
            'approved_at' => now(),
            'approved_by' => Auth::id(),
            'approval_notes' => $request->approval_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract approved and activated successfully',
            'contract_number' => $contract->contract_number
        ]);
    }

    /**
     * Reject a contract
     */
    public function reject($id, Request $request)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:1000'
        ]);

        $contract = Contract::findOrFail($id);
        
        $contract->update([
            'workflow_status' => 'rejected',
            'status' => 'Rejected',
            'rejected_at' => now(),
            'rejected_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contract rejected',
            'contract_number' => $contract->contract_number
        ]);
    }
}
