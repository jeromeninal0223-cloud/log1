<?php

namespace App\Http\Controllers\ALMS;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\DisposalRequest;
use Illuminate\Http\Request;

class DisposalController extends Controller
{
    public function index()
    {
        try {
            $assets = Asset::orderBy('asset_id')->get();
            
            // Check if disposal_requests table exists
            $disposalRequests = collect();
            try {
                $disposalRequests = DisposalRequest::with('asset')->latest()->take(50)->get();
            } catch (\Exception $e) {
                \Log::warning('Disposal requests table not found or empty', ['error' => $e->getMessage()]);
                $disposalRequests = collect();
            }
            
            return view('ALMS.disposalretirement', compact('assets', 'disposalRequests'));
        } catch (\Exception $e) {
            \Log::error('Error loading disposal page', ['error' => $e->getMessage()]);
            return view('ALMS.disposalretirement', [
                'assets' => collect(),
                'disposalRequests' => collect()
            ]);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'disposal_reason' => ['required', 'string', 'max:255'],
            'disposal_method' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'estimated_value' => ['nullable', 'numeric', 'min:0'],
            'urgency' => ['required', 'string', 'in:low,medium,high,critical'],
            'justification' => ['required', 'string'],
            'additional_notes' => ['nullable', 'string'],
            'requested_by' => ['required', 'string', 'max:255'],
        ]);

        try {
            // Generate request ID
            $requestId = 'DR-' . date('Y') . '-' . str_pad(DisposalRequest::count() + 1, 3, '0', STR_PAD_LEFT);

            $disposalRequest = DisposalRequest::create([
                'request_id' => $requestId,
                'asset_id' => $validated['asset_id'],
                'disposal_reason' => $validated['disposal_reason'],
                'disposal_method' => $validated['disposal_method'],
                'department' => $validated['department'],
                'estimated_value' => $validated['estimated_value'] ?? 0,
                'urgency' => $validated['urgency'],
                'justification' => $validated['justification'],
                'additional_notes' => $validated['additional_notes'],
                'requested_by' => $validated['requested_by'],
                'status' => 'pending',
                'created_by' => auth()->id(),
            ]);

            \Log::info('Disposal request created', [
                'request_id' => $requestId,
                'asset_id' => $validated['asset_id'],
                'user_id' => auth()->id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Disposal request submitted successfully!',
                    'request_id' => $requestId,
                    'data' => $disposalRequest->load('asset')
                ], 201);
            }

            return redirect()->back()->with('success', "Disposal request submitted successfully! Request ID: {$requestId}");

        } catch (\Exception $e) {
            \Log::error('Failed to create disposal request', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'asset_id' => $validated['asset_id'] ?? null
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to submit disposal request. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to submit disposal request. Please try again.');
        }
    }
}
