<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class BidAnalysisService
{
    protected $apiBaseUrl;
    protected $timeout;

    public function __construct()
    {
        $this->apiBaseUrl = config('services.bid_analysis.url', 'http://localhost:5000');
        $this->timeout = config('services.bid_analysis.timeout', 30);
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
     * Analyze multiple bids
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
     * Analyze a single bid
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
     * Predict quality score for a bid
     */
    public function predictQuality(array $bidData): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->post($this->apiBaseUrl . '/predict_quality', $bidData);

            if ($response->successful()) {
                return $response->json();
            } else {
                Log::error('Quality prediction failed: ' . $response->body());
                return ['error' => 'Prediction failed'];
            }
        } catch (\Exception $e) {
            Log::error('Quality prediction request failed: ' . $e->getMessage());
            return ['error' => 'Service unavailable'];
        }
    }

    /**
     * Compare multiple bids
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
     * Convert Laravel bid data to AI service format
     */
    public function formatBidData($bid): array
    {
        return [
            'bid_id' => $bid->id ?? 'BID-' . uniqid(),
            'supplier_name' => $bid->vendor->company_name ?? $bid->vendor->name ?? 'Unknown',
            'supplier_type' => $bid->vendor->business_type ?? 'General',
            'service_type' => $bid->service_type ?? 'General',
            'bid_amount' => (float) ($bid->amount ?? $bid->total_amount ?? 0),
            'quality_score' => (float) ($bid->quality_score ?? 75),
            'delivery_time_days' => (int) ($bid->delivery_time ?? 7),
            'experience_years' => (int) ($bid->vendor->experience_years ?? 5),
            'customer_rating' => (float) ($bid->vendor->rating ?? 4.0),
            'previous_projects' => (int) ($bid->vendor->previous_projects ?? 10),
            'warranty_months' => (int) ($bid->warranty_months ?? 12),
            'certifications' => (bool) ($bid->vendor->certifications ?? false),
            'insurance_coverage' => (bool) ($bid->vendor->insurance_coverage ?? false),
            'availability_24_7' => (bool) ($bid->vendor->availability_24_7 ?? false),
            'sustainability_certified' => (bool) ($bid->vendor->sustainability_certified ?? false),
            'payment_terms' => $bid->payment_terms ?? 'Net 30',
            'location_coverage' => $bid->location_coverage ?? 'Local',
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
    public function cacheAnalysis(string $cacheKey, array $results, int $ttl = 3600): void
    {
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
}

