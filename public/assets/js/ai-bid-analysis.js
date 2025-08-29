/**
 * AI-Powered Bid Analysis JavaScript
 * Enhances the bidding view with AI insights and recommendations
 */

class AIBidAnalysis {
    constructor() {
        this.apiBaseUrl = '/api/psm/bidding';
        this.aiEnabled = false;
        this.currentBids = [];
        this.analysisResults = null;
        
        this.init();
    }

    init() {
        // Check if AI service is available
        this.checkAIService();
        
        // Bind event listeners
        this.bindEvents();
        
        // Initialize AI features if available
        if (this.aiEnabled) {
            this.initializeAIFeatures();
        }
    }

    async checkAIService() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/ai/model-performance`);
            if (response.ok) {
                this.aiEnabled = true;
                console.log('✅ AI service is available');
                this.showAIStatus(true);
            } else {
                console.log('⚠️ AI service is not available');
                this.showAIStatus(false);
            }
        } catch (error) {
            console.log('❌ AI service check failed:', error);
            this.showAIStatus(false);
        }
    }

    showAIStatus(enabled) {
        const statusElement = document.getElementById('ai-status');
        if (statusElement) {
            statusElement.innerHTML = enabled 
                ? '<span class="badge bg-success"><i class="bi bi-robot"></i> AI Enabled</span>'
                : '<span class="badge bg-secondary"><i class="bi bi-robot"></i> AI Disabled</span>';
        }
    }

    bindEvents() {
        // AI analysis button
        const analyzeBtn = document.getElementById('analyze-bids-ai');
        if (analyzeBtn) {
            analyzeBtn.addEventListener('click', () => this.analyzeCurrentBids());
        }

        // AI recommendations button
        const recommendBtn = document.getElementById('get-ai-recommendations');
        if (recommendBtn) {
            recommendBtn.addEventListener('click', () => this.getRecommendations());
        }

        // Predict winner button
        const predictBtn = document.getElementById('predict-winner');
        if (predictBtn) {
            predictBtn.addEventListener('click', () => this.predictWinner());
        }

        // Compare bids button
        const compareBtn = document.getElementById('compare-bids-ai');
        if (compareBtn) {
            compareBtn.addEventListener('click', () => this.compareBids());
        }

        // Individual bid analysis
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('analyze-bid-ai')) {
                const bidId = e.target.dataset.bidId;
                this.analyzeSingleBid(bidId);
            }
        });
    }

    initializeAIFeatures() {
        // Add AI status indicator to the page
        this.addAIStatusIndicator();
        
        // Add AI action buttons
        this.addAIActionButtons();
        
        // Load current bids data
        this.loadCurrentBids();
    }

    addAIStatusIndicator() {
        const header = document.querySelector('.page-header');
        if (header && !document.getElementById('ai-status')) {
            const statusDiv = document.createElement('div');
            statusDiv.id = 'ai-status';
            statusDiv.className = 'ms-auto';
            header.appendChild(statusDiv);
            this.showAIStatus(this.aiEnabled);
        }
    }

    addAIActionButtons() {
        const quickActions = document.querySelector('.card-body .d-grid');
        if (quickActions && !document.getElementById('analyze-bids-ai')) {
            const aiButtons = `
                <button class="btn btn-outline-primary" id="analyze-bids-ai">
                    <i class="bi bi-robot me-2"></i>AI Analysis
                </button>
                <button class="btn btn-outline-success" id="get-ai-recommendations">
                    <i class="bi bi-lightbulb me-2"></i>AI Recommendations
                </button>
                <button class="btn btn-outline-warning" id="predict-winner">
                    <i class="bi bi-trophy me-2"></i>Predict Winner
                </button>
                <button class="btn btn-outline-info" id="compare-bids-ai">
                    <i class="bi bi-bar-chart me-2"></i>Compare Bids
                </button>
            `;
            quickActions.insertAdjacentHTML('beforeend', aiButtons);
        }
    }

    async loadCurrentBids() {
        try {
            const response = await fetch(`${this.apiBaseUrl}/bids`);
            if (response.ok) {
                const data = await response.json();
                this.currentBids = data.bids || [];
                console.log(`Loaded ${this.currentBids.length} bids`);
            }
        } catch (error) {
            console.error('Failed to load bids:', error);
        }
    }

    async analyzeCurrentBids() {
        if (!this.aiEnabled) {
            this.showNotification('AI service is not available', 'warning');
            return;
        }

        if (this.currentBids.length === 0) {
            this.showNotification('No bids available for analysis', 'warning');
            return;
        }

        this.showLoading('Analyzing bids with AI...');

        try {
            const response = await fetch(`${this.apiBaseUrl}/ai/analyze-bids`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ bids: this.currentBids })
            });

            if (response.ok) {
                const data = await response.json();
                this.analysisResults = data;
                this.displayAnalysisResults(data);
                this.showNotification('AI analysis completed successfully', 'success');
            } else {
                throw new Error('Analysis failed');
            }
        } catch (error) {
            console.error('AI analysis error:', error);
            this.showNotification('AI analysis failed', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async getRecommendations() {
        if (!this.aiEnabled) {
            this.showNotification('AI service is not available', 'warning');
            return;
        }

        this.showLoading('Getting AI recommendations...');

        try {
            const response = await fetch(`${this.apiBaseUrl}/ai/recommendations`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ opportunity_id: 1 }) // You might want to get this dynamically
            });

            if (response.ok) {
                const data = await response.json();
                this.displayRecommendations(data);
                this.showNotification('AI recommendations loaded', 'success');
            } else {
                throw new Error('Failed to get recommendations');
            }
        } catch (error) {
            console.error('Recommendations error:', error);
            this.showNotification('Failed to get recommendations', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async predictWinner() {
        if (!this.aiEnabled) {
            this.showNotification('AI service is not available', 'warning');
            return;
        }

        this.showLoading('Predicting winner...');

        try {
            const response = await fetch(`${this.apiBaseUrl}/ai/predict-winner`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ opportunity_id: 1 })
            });

            if (response.ok) {
                const data = await response.json();
                this.displayWinnerPrediction(data);
                this.showNotification('Winner prediction completed', 'success');
            } else {
                throw new Error('Prediction failed');
            }
        } catch (error) {
            console.error('Winner prediction error:', error);
            this.showNotification('Winner prediction failed', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async compareBids() {
        if (!this.aiEnabled) {
            this.showNotification('AI service is not available', 'warning');
            return;
        }

        this.showLoading('Comparing bids...');

        try {
            const response = await fetch(`${this.apiBaseUrl}/ai/compare-bids`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ opportunity_id: 1 })
            });

            if (response.ok) {
                const data = await response.json();
                this.displayBidComparison(data);
                this.showNotification('Bid comparison completed', 'success');
            } else {
                throw new Error('Comparison failed');
            }
        } catch (error) {
            console.error('Bid comparison error:', error);
            this.showNotification('Bid comparison failed', 'error');
        } finally {
            this.hideLoading();
        }
    }

    async analyzeSingleBid(bidId) {
        if (!this.aiEnabled) {
            this.showNotification('AI service is not available', 'warning');
            return;
        }

        this.showLoading('Analyzing bid...');

        try {
            const response = await fetch(`${this.apiBaseUrl}/ai/analyze-bid/${bidId}`);
            if (response.ok) {
                const data = await response.json();
                this.displaySingleBidAnalysis(data, bidId);
                this.showNotification('Bid analysis completed', 'success');
            } else {
                throw new Error('Single bid analysis failed');
            }
        } catch (error) {
            console.error('Single bid analysis error:', error);
            this.showNotification('Bid analysis failed', 'error');
        } finally {
            this.hideLoading();
        }
    }

    displayAnalysisResults(data) {
        // Create or update analysis results modal
        let modal = document.getElementById('ai-analysis-modal');
        if (!modal) {
            modal = this.createModal('ai-analysis-modal', 'AI Analysis Results');
        }

        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Analysis Summary</h6>
                    <ul class="list-unstyled">
                        <li><strong>Total Bids:</strong> ${data.analysis_summary?.total_bids || 0}</li>
                        <li><strong>Average Amount:</strong> $${(data.analysis_summary?.avg_bid_amount || 0).toLocaleString()}</li>
                        <li><strong>Average Quality:</strong> ${(data.analysis_summary?.avg_quality_score || 0).toFixed(1)}</li>
                        <li><strong>Avg Winning Probability:</strong> ${((data.analysis_summary?.avg_winning_probability || 0) * 100).toFixed(1)}%</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Top Recommendations</h6>
                    ${this.renderRecommendationsList(data.ai_recommendations || [])}
                </div>
            </div>
        `;

        modal.querySelector('.modal-body').innerHTML = content;
        this.showModal(modal);
    }

    displayRecommendations(data) {
        let modal = document.getElementById('ai-recommendations-modal');
        if (!modal) {
            modal = this.createModal('ai-recommendations-modal', 'AI Recommendations');
        }

        const content = `
            <div class="recommendations-list">
                ${this.renderRecommendationsList(data.recommendations || [])}
            </div>
        `;

        modal.querySelector('.modal-body').innerHTML = content;
        this.showModal(modal);
    }

    displayWinnerPrediction(data) {
        let modal = document.getElementById('winner-prediction-modal');
        if (!modal) {
            modal = this.createModal('winner-prediction-modal', 'Winner Prediction');
        }

        const topContender = data.top_contender;
        const content = `
            <div class="text-center mb-4">
                <h4>🥇 Predicted Winner</h4>
                <div class="card border-success">
                    <div class="card-body">
                        <h5 class="card-title">${topContender?.supplier_name || 'N/A'}</h5>
                        <p class="card-text">
                            <strong>Winning Probability:</strong> ${((topContender?.winning_probability || 0) * 100).toFixed(1)}%<br>
                            <strong>Confidence Level:</strong> ${topContender?.confidence_level || 'N/A'}
                        </p>
                    </div>
                </div>
            </div>
            <h6>All Predictions</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Supplier</th>
                            <th>Probability</th>
                            <th>Confidence</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${(data.predictions || []).map(pred => `
                            <tr>
                                <td>${pred.rank}</td>
                                <td>${pred.supplier_name}</td>
                                <td>${(pred.winning_probability * 100).toFixed(1)}%</td>
                                <td><span class="badge bg-${pred.confidence_level === 'High' ? 'success' : pred.confidence_level === 'Medium' ? 'warning' : 'secondary'}">${pred.confidence_level}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        modal.querySelector('.modal-body').innerHTML = content;
        this.showModal(modal);
    }

    displayBidComparison(data) {
        let modal = document.getElementById('bid-comparison-modal');
        if (!modal) {
            modal = this.createModal('bid-comparison-modal', 'Bid Comparison');
        }

        const summary = data.summary;
        const content = `
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Price Range</h6>
                            <p class="mb-0">$${summary?.price_range?.min?.toLocaleString()} - $${summary?.price_range?.max?.toLocaleString()}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Quality Range</h6>
                            <p class="mb-0">${summary?.quality_range?.min} - ${summary?.quality_range?.max}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <h6>Winning Probability</h6>
                            <p class="mb-0">${((summary?.winning_probability_range?.min || 0) * 100).toFixed(1)}% - ${((summary?.winning_probability_range?.max || 0) * 100).toFixed(1)}%</p>
                        </div>
                    </div>
                </div>
            </div>
            <h6>Comparison Details</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Supplier</th>
                            <th>Amount</th>
                            <th>Quality</th>
                            <th>Probability</th>
                            <th>Cost/Quality</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${(data.comparison_data || []).map(bid => `
                            <tr>
                                <td>${bid.supplier_name}</td>
                                <td>$${bid.bid_amount?.toLocaleString()}</td>
                                <td>${bid.quality_score}</td>
                                <td>${(bid.winning_probability * 100).toFixed(1)}%</td>
                                <td>${(bid.cost_effectiveness || 0).toFixed(2)}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;

        modal.querySelector('.modal-body').innerHTML = content;
        this.showModal(modal);
    }

    displaySingleBidAnalysis(data, bidId) {
        let modal = document.getElementById('single-bid-analysis-modal');
        if (!modal) {
            modal = this.createModal('single-bid-analysis-modal', 'Single Bid AI Analysis');
        }

        const predictions = data.ai_predictions;
        const content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>AI Predictions</h6>
                    <ul class="list-unstyled">
                        <li><strong>Winning Probability:</strong> ${((predictions?.winning_probability || 0) * 100).toFixed(1)}%</li>
                        <li><strong>Predicted Quality:</strong> ${predictions?.predicted_quality_score?.toFixed(1) || 'N/A'}</li>
                        <li><strong>Supplier Classification:</strong> ${predictions?.supplier_classification || 'N/A'}</li>
                        <li><strong>Confidence Level:</strong> ${predictions?.confidence_level || 'N/A'}</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Text Analysis</h6>
                    <ul class="list-unstyled">
                        <li><strong>Sentiment:</strong> ${data.sentiment?.sentiment_label || 'N/A'}</li>
                        <li><strong>Key Phrases:</strong> ${(data.extracted_features?.key_phrases || []).slice(0, 3).join(', ')}</li>
                        <li><strong>Has Certifications:</strong> ${data.extracted_features?.has_certifications ? 'Yes' : 'No'}</li>
                        <li><strong>Has Insurance:</strong> ${data.extracted_features?.has_insurance ? 'Yes' : 'No'}</li>
                    </ul>
                </div>
            </div>
        `;

        modal.querySelector('.modal-body').innerHTML = content;
        this.showModal(modal);
    }

    renderRecommendationsList(recommendations) {
        if (!recommendations.length) {
            return '<p class="text-muted">No recommendations available</p>';
        }

        return recommendations.map(rec => `
            <div class="card mb-2">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">${rec.rank}. ${rec.supplier_name}</h6>
                            <p class="mb-1 text-muted">$${rec.bid_amount?.toLocaleString()} • ${rec.service_type}</p>
                            <small class="text-success">
                                <strong>Winning Probability:</strong> ${(rec.winning_probability * 100).toFixed(1)}%
                            </small>
                        </div>
                        <span class="badge bg-primary">${rec.rank}</span>
                    </div>
                    ${rec.strengths?.length ? `
                        <div class="mt-2">
                            <small class="text-success"><strong>Strengths:</strong> ${rec.strengths.join(', ')}</small>
                        </div>
                    ` : ''}
                    ${rec.weaknesses?.length ? `
                        <div class="mt-1">
                            <small class="text-danger"><strong>Weaknesses:</strong> ${rec.weaknesses.join(', ')}</small>
                        </div>
                    ` : ''}
                </div>
            </div>
        `).join('');
    }

    createModal(id, title) {
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.id = id;
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">${title}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Content will be inserted here -->
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        return modal;
    }

    showModal(modal) {
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }

    showLoading(message) {
        // Create loading overlay
        let overlay = document.getElementById('ai-loading-overlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'ai-loading-overlay';
            overlay.className = 'position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex align-items-center justify-content-center';
            overlay.style.zIndex = '9999';
            overlay.innerHTML = `
                <div class="text-center text-white">
                    <div class="spinner-border mb-3" role="status"></div>
                    <div>${message}</div>
                </div>
            `;
            document.body.appendChild(overlay);
        } else {
            overlay.querySelector('div:last-child').textContent = message;
            overlay.style.display = 'flex';
        }
    }

    hideLoading() {
        const overlay = document.getElementById('ai-loading-overlay');
        if (overlay) {
            overlay.style.display = 'none';
        }
    }

    showNotification(message, type = 'info') {
        const alertClass = {
            'success': 'alert-success',
            'error': 'alert-danger',
            'warning': 'alert-warning',
            'info': 'alert-info'
        }[type] || 'alert-info';

        const notification = document.createElement('div');
        notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }
}

// Initialize AI Bid Analysis when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.aiBidAnalysis = new AIBidAnalysis();
});
