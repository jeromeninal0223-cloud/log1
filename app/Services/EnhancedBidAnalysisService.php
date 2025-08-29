<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Bid;
use App\Models\Vendor;
use App\Models\Opportunity;

class EnhancedBidAnalysisService
{
    protected $apiBaseUrl;
    protected $timeout;
    protected $cacheTtl;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.bid_analysis.url', 'http://localhost:5000');
        $this->timeout = config('services.bid_analysis.timeout', 30);
        $this->cacheTtl = config('services.bid_analysis.cache_ttl', 3600);
    }

    /**
     * Check if the AI service is healthy
     */
    public function isHealthy(): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->apiBaseUrl . '/health');

            return $response->successful() && $response->json('status') === 'healthy';
        } catch (\Exception $e) {
            Log::error('Bid analysis service health check failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get model performance metrics
     */
    public function getModelPerformance(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get($this->apiBaseUrl . '/model_performance');

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Model performance fetch failed: ' . $response->body());
                return ['error' => 'Fetch failed'];
            }
        } catch (\Exception $e) {
            Log::error('Model performance request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Analyze multiple bids with AI
     */
    public function analyzeBids(array $bids): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/analyze_bids', [
                    'bids' => $bids
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Bid analysis failed: ' . $response->body());
                return ['error' => 'Analysis failed'];
            }
        } catch (\Exception $e) {
            Log::error('Bid analysis request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Analyze a single bid with detailed AI insights
     */
    public function analyzeSingleBid(string $bidText, array $metadata = []): array
    {
        try {
            $data = array_merge([
                'bid_text' => $bidText
            ], $metadata);

            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/analyze_single_bid', $data);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Single bid analysis failed: ' . $response->body());
                return ['error' => 'Analysis failed'];
            }
        } catch (\Exception $e) {
            Log::error('Single bid analysis request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Predict winning probability for bids
     */
    public function predictWinningProbability(array $bids): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/predict_winning_probability', [
                    'bids' => $bids
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Winning probability prediction failed: ' . $response->body());
                return ['error' => 'Prediction failed'];
            }
        } catch (\Exception $e) {
            Log::error('Winning probability prediction request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Compare multiple bids with AI insights
     */
    public function compareBids(array $bids): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/compare_bids', [
                    'bids' => $bids
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Bid comparison failed: ' . $response->body());
                return ['error' => 'Comparison failed'];
            }
        } catch (\Exception $e) {
            Log::error('Bid comparison request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Get AI-powered recommendations
     */
    public function getRecommendations(array $bids, int $topN = 5): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/get_recommendations', [
                    'bids' => $bids,
                    'top_n' => $topN
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Recommendations fetch failed: ' . $response->body());
                return ['error' => 'Fetch failed'];
            }
        } catch (\Exception $e) {
            Log::error('Recommendations request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Generate sample data for testing
     */
    public function generateSampleData(int $numBids = 50): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/generate_sample_data', [
                    'num_bids' => $numBids
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Sample data generation failed: ' . $response->body());
                return ['error' => 'Generation failed'];
            }
        } catch (\Exception $e) {
            Log::error('Sample data generation request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Retrain AI models
     */
    public function retrainModels(int $numSamples = 500): array
    {
        try {
            $response = Http::timeout($this->timeout * 2) // Longer timeout for training
                ->post($this->apiBaseUrl . '/retrain_models', [
                    'num_samples' => $numSamples
                ]);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Model retraining failed: ' . $response->body());
                return ['error' => 'Retraining failed'];
            }
        } catch (\Exception $e) {
            Log::error('Model retraining request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Convert Laravel bid model to AI service format
     */
    public function formatBidData($bid): array
    {
        $vendor = $bid->vendor;
        
        return [
            'bid_id' => $bid->id ?? 'BID-' . uniqid(),
            'supplier_name' => $vendor->company_name ?? $vendor->name ?? 'Unknown',
            'supplier_type' => $vendor->business_type ?? 'General',
            'service_type' => $bid->service_type ?? $bid->opportunity->category ?? 'General',
            'bid_amount' => (float) ($bid->amount ?? $bid->total_amount ?? 0),
            'quality_score' => (float) ($bid->quality_score ?? 75),
            'delivery_time_days' => (int) ($bid->delivery_time ?? 7),
            'experience_years' => (int) ($vendor->experience_years ?? 5),
            'customer_rating' => (float) ($vendor->rating ?? 4.0),
            'previous_projects' => (int) ($vendor->previous_projects ?? 10),
            'warranty_months' => (int) ($bid->warranty_months ?? 12),
            'certifications' => (bool) ($vendor->certifications ?? false),
            'insurance_coverage' => (bool) ($vendor->insurance_coverage ?? false),
            'availability_24_7' => (bool) ($vendor->availability_24_7 ?? false),
            'sustainability_certified' => (bool) ($vendor->sustainability_certified ?? false),
            'payment_terms' => $bid->payment_terms ?? 'Net 30',
            'location_coverage' => $bid->location_coverage ?? 'Local',
            'team_size' => (int) ($vendor->team_size ?? 5),
            'response_time_hours' => (int) ($bid->response_time_hours ?? 24),
            'has_portfolio' => (bool) ($vendor->has_portfolio ?? false),
            'references_provided' => (bool) ($bid->references_provided ?? false),
            'certification_level' => $vendor->certification_level ?? 'Basic',
            'bid_text' => $bid->description ?? $bid->proposal ?? 'No description provided',
            'submission_date' => $bid->created_at?->toDateString() ?? now()->toDateString()
        ];
    }

    /**
     * Get cached analysis results
     */
    public function getCachedAnalysis(string $cacheKey): ?array
    {
        return Cache::get($cacheKey);
    }

    /**
     * Cache analysis results
     */
    public function cacheAnalysis(string $cacheKey, array $results, int $ttl = null): void
    {
        $ttl = $ttl ?? $this->cacheTtl;
        Cache::put($cacheKey, $results, $ttl);
    }

    /**
     * Get analysis with caching
     */
    public function getAnalysisWithCache(array $bids, string $cacheKey = null): array
    {
        if ($cacheKey === null) {
            $cacheKey = 'bid_analysis_' . md5(serialize($bids));
        }

        // Try to get cached results
        $cached = $this->getCachedAnalysis($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // Perform analysis
        $results = $this->analyzeBids($bids);

        // Cache results if successful
        if (!isset($results['error'])) {
            $this->cacheAnalysis($cacheKey, $results);
        }

        return $results;
    }

    /**
     * Analyze all bids for an opportunity
     */
    public function analyzeOpportunityBids(Opportunity $opportunity): array
    {
        $bids = $opportunity->bids()->with('vendor')->get();
        
        if ($bids->isEmpty()) {
            return ['error' => 'No bids found for this opportunity'];
        }

        $formattedBids = $bids->map(function ($bid) {
            return $this->formatBidData($bid);
        })->toArray();

        return $this->getAnalysisWithCache($formattedBids, "opportunity_analysis_{$opportunity->id}");
    }

    /**
     * Get AI recommendations for an opportunity
     */
    public function getOpportunityRecommendations(Opportunity $opportunity, int $topN = 5): array
    {
        $bids = $opportunity->bids()->with('vendor')->get();
        
        if ($bids->isEmpty()) {
            return ['error' => 'No bids found for this opportunity'];
        }

        $formattedBids = $bids->map(function ($bid) {
            return $this->formatBidData($bid);
        })->toArray();

        return $this->getRecommendations($formattedBids, $topN);
    }

    /**
     * Predict winning probability for an opportunity
     */
    public function predictOpportunityWinner(Opportunity $opportunity): array
    {
        $bids = $opportunity->bids()->with('vendor')->get();
        
        if ($bids->isEmpty()) {
            return ['error' => 'No bids found for this opportunity'];
        }

        $formattedBids = $bids->map(function ($bid) {
            return $this->formatBidData($bid);
        })->toArray();

        return $this->predictWinningProbability($formattedBids);
    }

    /**
     * Compare bids for an opportunity
     */
    public function compareOpportunityBids(Opportunity $opportunity): array
    {
        $bids = $opportunity->bids()->with('vendor')->get();
        
        if ($bids->count() < 2) {
            return ['error' => 'At least 2 bids required for comparison'];
        }

        $formattedBids = $bids->map(function ($bid) {
            return $this->formatBidData($bid);
        })->toArray();

        return $this->compareBids($formattedBids);
    }

    /**
     * Analyze a single bid with AI
     */
    public function analyzeBid(Bid $bid): array
    {
        $formattedBid = $this->formatBidData($bid);
        
        return $this->analyzeSingleBid(
            $formattedBid['bid_text'],
            $formattedBid
        );
    }

    /**
     * Get dashboard statistics with AI insights
     */
    public function getDashboardStats(): array
    {
        $stats = [
            'total_bids' => Bid::count(),
            'total_opportunities' => Opportunity::count(),
            'total_vendors' => Vendor::count(),
            'ai_service_status' => $this->isHealthy(),
        ];

        if ($this->isHealthy()) {
            $modelPerformance = $this->getModelPerformance();
            if (!isset($modelPerformance['error'])) {
                $stats['ai_model_performance'] = $modelPerformance['models_performance'] ?? null;
            }
        }

        return $stats;
    }

    /**
     * Get AI insights for recent bids
     */
    public function getRecentBidsInsights(int $limit = 10): array
    {
        $recentBids = Bid::with('vendor', 'opportunity')
            ->latest('created_at')
            ->limit($limit)
            ->get();

        if ($recentBids->isEmpty()) {
            return ['error' => 'No recent bids found'];
        }

        $formattedBids = $recentBids->map(function ($bid) {
            return $this->formatBidData($bid);
        })->toArray();

        return $this->getAnalysisWithCache($formattedBids, "recent_bids_insights_{$limit}");
    }
}
