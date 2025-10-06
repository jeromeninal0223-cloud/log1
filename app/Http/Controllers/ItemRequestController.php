<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ItemRequestController extends Controller
{
    public function index()
    {
        $requests = ItemRequest::with('requestedBy')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('SWS.item-requests', compact('requests'));
    }

    public function create()
    {
        return view('SWS.create-item-request');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name' => 'required|string|max:255',
            'requested_quantity' => 'required|integer|min:1',
            'available_quantity' => 'nullable|integer|min:0'
        ]);

        // Set default values for optional fields
        $validated['requested_by'] = Auth::id();
        $validated['status'] = 'pending';
        $validated['priority'] = 'MEDIUM';
        $validated['asset_code'] = null;
        $validated['category'] = null;
        $validated['storage_location'] = null;
        $validated['notes'] = null;

        ItemRequest::create($validated);

        return redirect()->route('picking-dispatch')
            ->with('success', 'Item request created successfully!');
    }

    public function updatePicked(Request $request, ItemRequest $itemRequest)
    {
        $validated = $request->validate([
            'picked_quantity' => 'required|integer|min:0|max:' . $itemRequest->requested_quantity
        ]);

        $itemRequest->update([
            'picked_quantity' => $validated['picked_quantity'],
            'status' => $validated['picked_quantity'] >= $itemRequest->requested_quantity ? 'COMPLETED' : 'IN_PROGRESS'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Picked quantity updated successfully',
            'is_complete' => $itemRequest->is_complete
        ]);
    }

    public function getActiveRequests()
    {
        $requests = ItemRequest::where('status', '!=', 'COMPLETED')
            ->where('status', '!=', 'CANCELLED')
            ->with('requestedBy')
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($requests);
    }
}
