<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\User;

class PurchaseRequestController extends Controller
{
    public function index()
    {
        // Get current user's purchase requests only
        $pendingRequests = PurchaseRequest::where('status', 'Pending')
            ->where('requested_by', auth()->id())
            ->with('requestedBy')->get();
        $approvedRequests = PurchaseRequest::where('status', 'Approved')
            ->where('requested_by', auth()->id())
            ->with('requestedBy')->get();
        $purchaseRequests = PurchaseRequest::where('requested_by', auth()->id())
            ->with('requestedBy')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('PSM.request', compact('pendingRequests', 'approvedRequests', 'purchaseRequests'));
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_description' => 'required|string|max:255',
            'category' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'unit' => 'required|string',
            'estimated_cost' => 'required|numeric|min:0',
            'required_date' => 'required|date',
            'priority' => 'required|in:Low,Medium,High',
            'justification' => 'required|string',
        ]);

        PurchaseRequest::create([
            'request_number' => PurchaseRequest::generateRequestNumber(),
            'item_description' => $validated['item_description'],
            'category' => $validated['category'],
            'quantity' => $validated['quantity'],
            'unit' => $validated['unit'],
            'estimated_cost' => $validated['estimated_cost'],
            'required_date' => $validated['required_date'],
            'priority' => $validated['priority'],
            'status' => 'Pending',
            'justification' => $validated['justification'],
            'requested_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Purchase request submitted successfully!');
    }

    public function showBidForm($id)
    {
        // Logic for showing bid form
        return view('PSM.bid_form', compact('id'));
    }

    public function submitBidForm(Request $request)
    {
        // Logic for submitting bid form
        return response()->json(['success' => true, 'message' => 'Bid submitted successfully']);
    }

    public function approvalIndex()
    {
        // Show all purchase requests for approval
        $requests = PurchaseRequest::with('requestedBy')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'pending' => PurchaseRequest::where('status', 'Pending')->count(),
            'approved' => PurchaseRequest::where('status', 'Approved')->count(),
            'rejected' => PurchaseRequest::where('status', 'Rejected')->count(),
            'total_value' => PurchaseRequest::sum('estimated_cost')
        ];
        
        return view('PSM.purchaserequest-approval', compact('requests', 'stats'));
    }

    public function approve(Request $request)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($request->request_id);
        $purchaseRequest->update([
            'status' => 'Approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Purchase request approved successfully']);
    }
    
    public function reject(Request $request)
    {
        $purchaseRequest = PurchaseRequest::findOrFail($request->request_id);
        $purchaseRequest->update([
            'status' => 'Rejected',
            'rejection_reason' => $request->reason,
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);
        
        return response()->json(['success' => true, 'message' => 'Purchase request rejected successfully']);
    }

    public function getBidFormData($id)
    {
        // Logic for getting bid form data
        return response()->json(['data' => []]);
    }
}
