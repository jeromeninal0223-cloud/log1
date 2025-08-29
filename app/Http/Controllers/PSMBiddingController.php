<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\EnhancedBidAnalysisService;
use App\Models\Bid;
use App\Models\Opportunity;
use App\Models\Vendor;

class PSMBiddingController extends Controller
{
    protected $bidAnalysisService;

    public function __construct(EnhancedBidAnalysisService $bidAnalysisService)
    {
        $this->bidAnalysisService = $bidAnalysisService;
    }

    public function index()
    {
        $stats = [
            'active_rfqs' => Opportunity::where('current_status', 'Open')->count(),
            'pending_evaluation' => Bid::whereIn('status', ['Pending Evaluation', 'Under Review'])->count(),
            'bids_won' => Bid::where('status', 'Won')->count(),
            'total_value' => (int) Bid::sum('amount'),
        ];

        $bids = Bid::with('vendor')
            ->latest('submitted_at')
            ->take(100)
            ->get();

        // Get AI insights if service is available
        $aiInsights = null;
        $aiServiceStatus = $this->bidAnalysisService->isHealthy();
        
        if ($aiServiceStatus) {
            try {
                $aiInsights = $this->bidAnalysisService->getRecentBidsInsights(10);
            } catch (\Exception $e) {
                Log::error('Failed to get AI insights: ' . $e->getMessage());
            }
        }

        return view('PSM.bidding', compact('stats', 'bids', 'aiInsights', 'aiServiceStatus'));
    }

    public function storeOpportunity(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'current_status' => ['required', 'in:Open,Ended'],
            'description' => ['nullable', 'string'],
        ]);

        $opportunity = Opportunity::create([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'current_status' => $validated['current_status'],
            'description' => $validated['description'] ?? null,
            'submission_count' => 0,
        ]);

        // If it's an AJAX/API request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Opportunity created successfully']);
        }

        // Otherwise, redirect back to the PSM bidding page with a flash message
        return redirect()->route('psm.bidding')->with('success', 'Opportunity created successfully');
    }

    public function evaluateOpportunity($id, Request $request)
    {
        $opportunity = Opportunity::findOrFail($id);
        
        // Get AI analysis for the opportunity
        $aiAnalysis = $this->bidAnalysisService->analyzeOpportunityBids($opportunity);
        
        return response()->json([
            'success' => true, 
            'message' => 'Opportunity evaluated',
            'ai_analysis' => $aiAnalysis
        ]);
    }

    public function getBids()
    {
        $bids = Bid::with('vendor')
            ->latest('submitted_at')
            ->take(100)
            ->get()
            ->map(function ($bid) {
                return [
                    'id' => $bid->id,
                    'bid_number' => 'BID-' . str_pad((string) $bid->id, 4, '0', STR_PAD_LEFT),
                    'title' => $bid->title ?? ('Bid for Opportunity #' . ($bid->opportunity_id ?? '')),
                    'vendor_name' => optional($bid->vendor)->company_name ?? optional($bid->vendor)->name ?? '—',
                    'amount' => (float) $bid->amount,
                    'submitted_at' => optional($bid->submitted_at)->toDateTimeString(),
                    'status' => $bid->status ?? 'Under Review',
                    'proposal' => $bid->description ?? '',
                ];
            });

        return response()->json(['success' => true, 'bids' => $bids]);
    }

    public function getBidDetails($id)
    {
        $bid = Bid::with('vendor')->find($id);
        if (!$bid) {
            return response()->json(['success' => false, 'error' => 'Bid not found'], 404);
        }

        $result = [
            'id' => $bid->id,
            'bid_number' => 'BID-' . str_pad((string) $bid->id, 4, '0', STR_PAD_LEFT),
            'title' => $bid->title ?? ('Bid for Opportunity #' . ($bid->opportunity_id ?? '')),
            'vendor_name' => optional($bid->vendor)->company_name ?? optional($bid->vendor)->name ?? '—',
            'amount' => (float) $bid->amount,
            'submitted_at' => optional($bid->submitted_at)->toDateTimeString(),
            'status' => $bid->status ?? 'Under Review',
            'proposal' => $bid->description ?? '',
        ];

        // Get AI analysis for the bid
        $aiAnalysis = null;
        if ($this->bidAnalysisService->isHealthy()) {
            try {
                $aiAnalysis = $this->bidAnalysisService->analyzeBid($bid);
            } catch (\Exception $e) {
                Log::error('Failed to analyze bid with AI: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true, 
            'bid' => $result,
            'ai_analysis' => $aiAnalysis
        ]);
    }

    public function updateBidStatus($id, Request $request)
    {
        $request->validate([
            'status' => ['required', 'in:Under Review,Pending Evaluation,Won,Rejected']
        ]);

        $bid = Bid::find($id);
        if (!$bid) {
            return response()->json(['success' => false, 'error' => 'Bid not found'], 404);
        }

        $bid->status = $request->input('status');
        $bid->save();

        return response()->json(['success' => true, 'message' => 'Bid status updated']);
    }

    public function selectWinner($id, Request $request)
    {
        $bid = Bid::find($id);
        if (!$bid) {
            return response()->json(['success' => false, 'error' => 'Bid not found'], 404);
        }

        // Check if opportunity exists and extend deadline if needed
        $opportunity = Opportunity::find($bid->opportunity_id);
        if ($opportunity && $opportunity->end_date && $opportunity->end_date < now()) {
            // Extend opportunity deadline to allow contract generation
            $opportunity->update(['end_date' => now()->addDays(30)]);
        }

        try {
            // Update the selected bid as winner
            $bid->status = 'Won';
            $bid->save();

            // Reject all other bids for the same opportunity
            Bid::where('opportunity_id', $bid->opportunity_id)
                ->where('id', '!=', $bid->id)
                ->update(['status' => 'Rejected']);

            // Create contract for the winning bid
            Log::info('Starting contract creation for bid', ['bid_id' => $bid->id, 'vendor_id' => $bid->vendor_id]);
            
            $opportunity = Opportunity::find($bid->opportunity_id);
            if ($opportunity) {
                Log::info('Opportunity found', ['opportunity_id' => $opportunity->id]);
                
                // Generate unique contract number to avoid conflicts
                $contractCount = \App\Models\Contract::count();
                do {
                    $contractCount++;
                    $contractNumber = 'CON-' . date('Y') . '-' . str_pad($contractCount, 4, '0', STR_PAD_LEFT);
                } while (\App\Models\Contract::where('contract_number', $contractNumber)->exists());
                
                Log::info('Generated contract number', ['contract_number' => $contractNumber]);
                
                $contract = \App\Models\Contract::create([
                    'contract_number' => $contractNumber,
                    'bid_id' => $bid->id,
                    'vendor_id' => $bid->vendor_id,
                    'title' => $opportunity->title ?? 'Contract for ' . $bid->title,
                    'description' => $opportunity->description ?? 'Contract generated from winning bid',
                    'value' => $bid->amount,
                    'start_date' => now(),
                    'end_date' => now()->addMonths(12),
                    'status' => 'Active',
                ]);
                
                Log::info('Contract created successfully', ['contract_id' => $contract->id, 'contract_number' => $contract->contract_number]);
            } else {
                Log::warning('Opportunity not found', ['opportunity_id' => $bid->opportunity_id]);
            }

            return response()->json(['success' => true, 'message' => 'Winner selected']);
        } catch (\Throwable $e) {
            Log::error('Contract creation failed', [
                'bid_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'error' => 'Contract creation failed: ' . $e->getMessage()], 500);
        }
    }

    public function rejectBid($id, Request $request)
    {
        $bid = Bid::find($id);
        if (!$bid) {
            return response()->json(['success' => false, 'error' => 'Bid not found'], 404);
        }

        $bid->status = 'Rejected';
        $bid->save();

        return response()->json(['success' => true, 'message' => 'Bid rejected']);
    }

    public function startEvaluation($id, Request $request)
    {
        $bid = Bid::find($id);
        if (!$bid) {
            return response()->json(['success' => false, 'error' => 'Bid not found'], 404);
        }

        $bid->status = 'Under Review';
        $bid->save();

        return response()->json(['success' => true, 'message' => 'Evaluation started']);
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'bid_ids' => ['required', 'array'],
            'status' => ['required', 'in:Under Review,Pending Evaluation,Won,Rejected']
        ]);

        $bidIds = $request->input('bid_ids');
        $status = $request->input('status');

        Bid::whereIn('id', $bidIds)->update(['status' => $status]);

        return response()->json(['success' => true, 'message' => 'Bulk status update completed']);
    }

    public function getStatistics()
    {
        $stats = [
            'total_bids' => Bid::count(),
            'total_opportunities' => Opportunity::count(),
            'total_vendors' => Vendor::count(),
            'ai_service_status' => $this->bidAnalysisService->isHealthy(),
        ];

        if ($this->bidAnalysisService->isHealthy()) {
            try {
                $aiStats = $this->bidAnalysisService->getDashboardStats();
                $stats = array_merge($stats, $aiStats);
            } catch (\Exception $e) {
                Log::error('Failed to get AI statistics: ' . $e->getMessage());
            }
        }

        return response()->json(['statistics' => $stats]);
    }

    public function exportBids()
    {
        $bids = Bid::with('vendor')->get();
        
        $csvData = [];
        $csvData[] = ['Bid ID', 'Vendor', 'Amount', 'Status', 'Submitted At', 'Description'];
        
        foreach ($bids as $bid) {
            $csvData[] = [
                $bid->id,
                optional($bid->vendor)->company_name ?? 'Unknown',
                $bid->amount,
                $bid->status,
                $bid->submitted_at,
                $bid->description
            ];
        }

        $filename = 'bids_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // New AI-powered methods

    public function getAiRecommendations(Request $request)
    {
        $opportunityId = $request->input('opportunity_id');
        
        if (!$opportunityId) {
            return response()->json(['error' => 'Opportunity ID required'], 400);
        }

        $opportunity = Opportunity::find($opportunityId);
        if (!$opportunity) {
            return response()->json(['error' => 'Opportunity not found'], 404);
        }

        $recommendations = $this->bidAnalysisService->getOpportunityRecommendations($opportunity, 5);
        
        return response()->json($recommendations);
    }

    public function predictWinner(Request $request)
    {
        $opportunityId = $request->input('opportunity_id');
        
        if (!$opportunityId) {
            return response()->json(['error' => 'Opportunity ID required'], 400);
        }

        $opportunity = Opportunity::find($opportunityId);
        if (!$opportunity) {
            return response()->json(['error' => 'Opportunity not found'], 404);
        }

        $prediction = $this->bidAnalysisService->predictOpportunityWinner($opportunity);
        
        return response()->json($prediction);
    }

    public function compareBids(Request $request)
    {
        $opportunityId = $request->input('opportunity_id');
        
        if (!$opportunityId) {
            return response()->json(['error' => 'Opportunity ID required'], 400);
        }

        $opportunity = Opportunity::find($opportunityId);
        if (!$opportunity) {
            return response()->json(['error' => 'Opportunity not found'], 404);
        }

        $comparison = $this->bidAnalysisService->compareOpportunityBids($opportunity);
        
        return response()->json($comparison);
    }

    public function analyzeBidWithAi($id)
    {
        $bid = Bid::with('vendor')->find($id);
        if (!$bid) {
            return response()->json(['error' => 'Bid not found'], 404);
        }

        $analysis = $this->bidAnalysisService->analyzeBid($bid);
        
        return response()->json($analysis);
    }

    public function getModelPerformance()
    {
        $performance = $this->bidAnalysisService->getModelPerformance();
        
        return response()->json($performance);
    }

    public function retrainModels(Request $request)
    {
        $numSamples = $request->input('num_samples', 500);
        
        $result = $this->bidAnalysisService->retrainModels($numSamples);
        
        return response()->json($result);
    }

    public function generateSampleData(Request $request)
    {
        $numBids = $request->input('num_bids', 50);
        
        $result = $this->bidAnalysisService->generateSampleData($numBids);
        
        return response()->json($result);
    }
}
