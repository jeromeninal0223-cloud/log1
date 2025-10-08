<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VehicleRequestController extends Controller
{
    private $apiUrl;
    private $apiKey;

    public function __construct()
    {
        // Configure these in your .env file
        $this->apiUrl = env('EXTERNAL_VEHICLE_API_URL', 'https://logistics2.jetlougetravels-ph.com/api/external-approvals.php');
        $this->apiKey = env('EXTERNAL_VEHICLE_API_KEY', '');
    }

    /**
     * Fetch vehicle requests from external department
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending'); // pending, approved, rejected
        
        try {
            $requests = $this->fetchRequests($status);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => $requests,
                    'count' => count($requests)
                ]);
            }
            
            return view('ALMS/vehicle-requests', compact('requests', 'status'));
            
        } catch (\Exception $e) {
            Log::error('Failed to fetch vehicle requests: ' . $e->getMessage());
            // On failure, still render the page so navigation doesn't bounce back to previous URL
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch requests: ' . $e->getMessage()
                ], 500);
            }

            $requests = [];
            $error = 'Failed to fetch vehicle requests: ' . $e->getMessage();
            return view('ALMS/vehicle-requests', compact('requests', 'status'))
                ->with('error', $error);
        }
    }

    /**
     * Get a specific vehicle request
     */
    public function show($id)
    {
        try {
            $request = $this->fetchSingleRequest($id);
            
            return response()->json([
                'success' => true,
                'data' => $request
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve or reject a vehicle request
     */
    public function decide(Request $request)
    {
        $request->validate([
            'request_id' => 'required|integer',
            'decision' => 'required|in:approve,reject',
            'note' => 'nullable|string|max:1000'
        ]);

        try {
            $result = $this->makeDecision(
                $request->request_id,
                $request->decision,
                $request->note ?? ''
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Request ' . $request->decision . 'd successfully',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            Log::error('Failed to update vehicle request: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update request: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Fetch requests from external API
     */
    private function fetchRequests($status = 'pending')
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json'
        ])->get($this->apiUrl, [
            'action' => 'list',
            'status' => $status
        ]);

        if (!$response->successful()) {
            throw new \Exception('API request failed: ' . $response->status() . ' - ' . $response->body());
        }

        $data = $response->json();
        
        if (!$data || $data['success'] === false) {
            throw new \Exception($data['message'] ?? 'Unknown API error');
        }

        return $data['data']['requests'] ?? [];
    }

    /**
     * Fetch single request from external API
     */
    private function fetchSingleRequest($id)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json'
        ])->get($this->apiUrl, [
            'action' => 'get',
            'id' => $id
        ]);

        if (!$response->successful()) {
            throw new \Exception('API request failed: ' . $response->status());
        }

        $data = $response->json();
        
        if (!$data || $data['success'] === false) {
            throw new \Exception($data['message'] ?? 'Request not found');
        }

        return $data['data'];
    }

    /**
     * Make approval/rejection decision via external API
     */
    private function makeDecision($requestId, $decision, $note = '')
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ])->post($this->apiUrl . '?action=decide', [
            'request_id' => $requestId,
            'decision' => $decision,
            'note' => $note
        ]);

        if (!$response->successful()) {
            throw new \Exception('API request failed: ' . $response->status());
        }

        $data = $response->json();
        
        if (!$data || $data['success'] === false) {
            throw new \Exception($data['message'] ?? 'Decision update failed');
        }

        return $data['data'];
    }

    /**
     * Test API connection
     */
    public function testConnection()
    {
        try {
            $requests = $this->fetchRequests('pending');
            
            return response()->json([
                'success' => true,
                'message' => 'API connection successful',
                'request_count' => count($requests)
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'API connection failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
