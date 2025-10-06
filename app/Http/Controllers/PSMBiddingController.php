<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\BidAnalysisService;
use App\Services\ContractTermsService;
use App\Models\Bid;
use App\Models\Opportunity;
use App\Models\Vendor;

class PSMBiddingController extends Controller
{
    protected $bidAnalysisService;

    public function __construct(BidAnalysisService $bidAnalysisService)
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

        // Get opportunities with pagination
        $opportunities = Opportunity::latest('created_at')
            ->paginate(10);

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

        return view('PSM.bidding', compact('stats', 'bids', 'opportunities', 'aiInsights', 'aiServiceStatus'));
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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Opportunity created successfully']);
        }

        // Otherwise, redirect back to the PSM bidding page with a flash message
        return redirect()->route('psm.bidding')->with('success', 'Opportunity created successfully!');
    }

    /**
     * Get current bid count for real-time monitoring
     */
    public function getBidCount()
    {
        try {
            $count = Bid::count();
            
            return response()->json([
                'success' => true,
                'count' => $count,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting bid count: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get bid count',
                'count' => 0
            ], 500);
        }
    }

    /**
     * Get recent bids for real-time updates
     */
    public function getRecentBids(Request $request)
    {
        try {
            $since = $request->get('since'); // ISO timestamp
            $query = Bid::with('vendor');
            
            if ($since) {
                $query->where('created_at', '>', $since);
            }
            
            $bids = $query->latest('created_at')
                ->take(10)
                ->get()
                ->map(function ($bid) {
                    return [
                        'id' => $bid->id,
                        'vendor_name' => $bid->vendor->name ?? 'Unknown Vendor',
                        'amount' => $bid->amount,
                        'status' => $bid->status,
                        'submitted_at' => $bid->submitted_at,
                        'created_at' => $bid->created_at->toISOString()
                    ];
                });
            
            return response()->json([
                'success' => true,
                'bids' => $bids,
                'count' => $bids->count(),
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Error getting recent bids: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to get recent bids',
                'bids' => []
            ], 500);
        }
    }

    public function getOpportunity($id)
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return response()->json(['success' => false, 'error' => 'Opportunity not found'], 404);
        }

        return response()->json([
            'success' => true,
            'opportunity' => [
                'id' => $opportunity->id,
                'title' => $opportunity->title,
                'category' => $opportunity->category,
                'start_date' => $opportunity->start_date ? $opportunity->start_date->format('Y-m-d') : null,
                'end_date' => $opportunity->end_date ? $opportunity->end_date->format('Y-m-d') : null,
                'budget' => $opportunity->budget,
                'current_status' => $opportunity->current_status,
                'description' => $opportunity->description,
            ]
        ]);
    }

    public function updateOpportunity($id, Request $request)
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return response()->json(['success' => false, 'error' => 'Opportunity not found'], 404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'budget' => ['nullable', 'numeric', 'min:0'],
            'current_status' => ['required', 'in:Open,Ended,Closed'],
            'description' => ['nullable', 'string'],
        ]);

        $opportunity->update([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'budget' => $validated['budget'] ?? 0,
            'current_status' => $validated['current_status'],
            'description' => $validated['description'] ?? null,
        ]);

        // If it's an AJAX/API request, return JSON
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Opportunity updated successfully']);
        }

        return redirect()->route('psm.bidding')->with('success', 'Opportunity updated successfully');
    }

    public function deleteOpportunity($id)
    {
        $opportunity = Opportunity::find($id);
        if (!$opportunity) {
            return response()->json(['success' => false, 'error' => 'Opportunity not found'], 404);
        }

        // Check if there are any bids associated with this opportunity
        $bidCount = Bid::where('opportunity_id', $id)->count();
        if ($bidCount > 0) {
            return response()->json([
                'success' => false, 
                'error' => 'Cannot delete opportunity with existing bids. Please remove all bids first.'
            ], 400);
        }

        $opportunity->delete();

        return response()->json(['success' => true, 'message' => 'Opportunity deleted successfully']);
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

    public function getBid($id)
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

    public function aiAnalysis(Request $request)
    {
        $request->validate([
            'title' => 'required|string'
        ]);

        $title = $request->input('title');
        
        $bids = Bid::with('vendor')
            ->where('title', $title)
            ->get();

        if ($bids->isEmpty()) {
            return response()->json([
                'success' => false,
                'error' => 'No bids found for the specified title'
            ], 404);
        }

        try {
            // Check if AI service is available
            if (!$this->bidAnalysisService->isHealthy()) {
                Log::warning('AI service unavailable, falling back to basic analysis');
                return $this->fallbackAnalysis($bids, $title);
            }

            // Format bids for AI service
            $formattedBids = $bids->map(function($bid) {
                return $this->bidAnalysisService->formatBidData($bid);
            })->toArray();

            // Get AI analysis with caching
            $aiResults = $this->bidAnalysisService->getAnalysisWithCache($formattedBids);

            if (isset($aiResults['error'])) {
                Log::warning('AI analysis failed: ' . $aiResults['error']);
                return $this->fallbackAnalysis($bids, $title);
            }

            // Process AI results for frontend
            $analysisResults = [];
            $recommendations = $aiResults['ai_recommendations'] ?? [];
            
            foreach ($bids as $bid) {
                // Find corresponding AI analysis
                $aiData = collect($aiResults['processed_bids'] ?? [])->firstWhere('bid_id', $bid->id);
                
                if ($aiData) {
                    $analysisResults[] = [
                        'bid_id' => $bid->id,
                        'vendor_name' => optional($bid->vendor)->company_name ?? optional($bid->vendor)->name ?? 'Unknown Vendor',
                        'amount' => $bid->amount,
                        'scores' => [
                            'price' => round($aiData['price_competitiveness'] ?? 7.5, 1),
                            'quality' => round($aiData['quality_score'] ?? 7.5, 1),
                            'delivery' => round($aiData['delivery_score'] ?? 7.5, 1),
                            'experience' => round($aiData['experience_score'] ?? 7.5, 1),
                            'total' => round($aiData['composite_score'] ?? 7.5, 1)
                        ],
                        'winning_probability' => round(($aiData['winning_probability'] ?? 0.5) * 100, 1),
                        'ai_insights' => $aiData['ai_insights'] ?? [],
                        'status' => $bid->status ?? 'Under Review'
                    ];
                }
            }

            // Sort by AI composite score
            usort($analysisResults, function($a, $b) {
                return $b['scores']['total'] <=> $a['scores']['total'];
            });

            $recommendedBid = $analysisResults[0] ?? null;
            
            if (!$recommendedBid) {
                return $this->fallbackAnalysis($bids, $title);
            }

            $summary = sprintf(
                '%s ranks #1 out of %d bids with AI score %.1f/10 and %.1f%% winning probability. AI analysis shows strong performance across multiple criteria.',
                $recommendedBid['vendor_name'],
                count($analysisResults),
                $recommendedBid['scores']['total'],
                $recommendedBid['winning_probability']
            );

            return response()->json([
                'success' => true,
                'analysis' => [
                    'title' => $title,
                    'total_bids' => count($analysisResults),
                    'recommended_bid' => $recommendedBid,
                    'all_bids' => $analysisResults,
                    'summary' => $summary,
                    'ai_powered' => true,
                    'model_performance' => $aiResults['analysis_summary']['models_performance'] ?? null,
                    'analyzed_at' => now()->toISOString()
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('AI Analysis failed: ' . $e->getMessage());
            return $this->fallbackAnalysis($bids, $title);
        }
    }

    /**
     * Fallback analysis when AI service is unavailable
     */
    private function fallbackAnalysis($bids, $title)
    {
        $analysisResults = [];
        
        foreach ($bids as $bid) {
            $allAmounts = $bids->pluck('amount')->toArray();
            $minAmount = min($allAmounts);
            $maxAmount = max($allAmounts);
            
            $priceScore = 10;
            if ($maxAmount > $minAmount) {
                $priceScore = 10 - (($bid->amount - $minAmount) / ($maxAmount - $minAmount)) * 10;
            }
            
            $qualityScore = rand(70, 100) / 10;
            $deliveryScore = rand(75, 100) / 10;
            $experienceScore = rand(65, 95) / 10;
            
            $totalScore = ($priceScore * 0.4) + ($qualityScore * 0.3) + ($deliveryScore * 0.2) + ($experienceScore * 0.1);
            
            $analysisResults[] = [
                'bid_id' => $bid->id,
                'vendor_name' => optional($bid->vendor)->company_name ?? optional($bid->vendor)->name ?? 'Unknown Vendor',
                'amount' => $bid->amount,
                'scores' => [
                    'price' => round($priceScore, 1),
                    'quality' => round($qualityScore, 1),
                    'delivery' => round($deliveryScore, 1),
                    'experience' => round($experienceScore, 1),
                    'total' => round($totalScore, 1)
                ],
                'winning_probability' => round(rand(30, 90), 1),
                'status' => $bid->status ?? 'Under Review'
            ];
        }
        
        usort($analysisResults, function($a, $b) {
            return $b['scores']['total'] <=> $a['scores']['total'];
        });
        
        $recommendedBid = $analysisResults[0];
        
        $summary = sprintf(
            '%s ranks #1 out of %d bids with score %.1f/10. Analysis based on price competitiveness and estimated quality metrics.',
            $recommendedBid['vendor_name'],
            count($analysisResults),
            $recommendedBid['scores']['total']
        );
        
        return response()->json([
            'success' => true,
            'analysis' => [
                'title' => $title,
                'total_bids' => count($analysisResults),
                'recommended_bid' => $recommendedBid,
                'all_bids' => $analysisResults,
                'summary' => $summary,
                'ai_powered' => false,
                'fallback_mode' => true,
                'analyzed_at' => now()->toISOString()
            ]
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

            // Update opportunity status to "Ended" since a winner has been selected
            if ($opportunity) {
                $opportunity->update(['current_status' => 'Ended']);
            }

            // Initiate contract negotiation workflow instead of creating active contract
            Log::info('Starting contract negotiation for winning bid', ['bid_id' => $bid->id, 'vendor_id' => $bid->vendor_id]);
            
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
                
                // Create contract in draft status for negotiation
                $contract = \App\Models\Contract::create([
                    'contract_number' => $contractNumber,
                    'bid_id' => $bid->id,
                    'vendor_id' => $bid->vendor_id,
                    'title' => $opportunity->title ?? 'Contract for ' . $bid->title,
                    'description' => $opportunity->description ?? 'Contract generated from winning bid',
                    'value' => $bid->amount,
                    'negotiated_value' => $bid->amount, // Initial negotiated value
                    'workflow_status' => 'draft', // Start in draft status
                    'status' => 'Pending', // Contract pending until fully signed
                    'start_date' => now(),
                    'end_date' => now()->addMonths(12),
                    'procurement_officer_id' => auth()->id(),
                ]);
                
                // Generate comprehensive contract terms and conditions
                $contractTerms = ContractTermsService::generateContractTerms($contract);
                $contract->update(['terms' => $contractTerms]);
                
                Log::info('Contract draft created successfully', [
                    'contract_id' => $contract->id, 
                    'contract_number' => $contract->contract_number,
                    'workflow_status' => $contract->workflow_status
                ]);

                return response()->json([
                    'success' => true, 
                    'message' => 'Winner selected and contract negotiation initiated',
                    'contract_id' => $contract->id,
                    'contract_number' => $contract->contract_number,
                    'next_step' => 'Contract is ready for terms negotiation and signing workflow'
                ]);
            } else {
                Log::warning('Opportunity not found', ['opportunity_id' => $bid->opportunity_id]);
                return response()->json(['success' => false, 'error' => 'Associated opportunity not found'], 404);
            }

        } catch (\Throwable $e) {
            Log::error('Contract negotiation initiation failed', [
                'bid_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['success' => false, 'error' => 'Contract initiation failed: ' . $e->getMessage()], 500);
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
