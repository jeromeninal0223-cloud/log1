# Jetlouge Travels: AI Procurement Bid Analysis System

An AI-powered system for analyzing supplier bids in travel and tourism procurement using Scikit-Learn and NLP.

## Features

- **Bid Text Analysis**: Extract key information from bid texts using NLP
- **Sentiment Analysis**: Analyze bid sentiment and professionalism
- **Quality Prediction**: ML models to predict bid quality scores
- **Cost-Effectiveness Analysis**: Evaluate value for money
- **Supplier Classification**: Categorize suppliers as good/bad
- **Bid Ranking**: Multiple ranking methods (composite, cost-effectiveness, quality)
- **Supplier Clustering**: Group similar suppliers
- **Real-time API**: RESTful API for Laravel integration
- **Caching**: Performance optimization with result caching

## Architecture

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Laravel App   │    │   Flask API     │    │   ML Models     │
│                 │◄──►│                 │◄──►│                 │
│ - Bid Management│    │ - Bid Analysis  │    │ - Random Forest │
│ - UI/UX         │    │ - Text Processing│   │ - SVR           │
│ - Database      │    │ - Model Serving │    │ - K-means       │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

## Quick Start

### 1. Setup Python Environment

```bash
# Create virtual environment
python -m venv venv
source venv/bin/activate  # On Windows: venv\Scripts\activate

# Install dependencies
pip install -r requirements.txt

# Download NLTK data
python -c "import nltk; nltk.download('punkt'); nltk.download('stopwords')"
```

### 2. Generate Sample Data

```python
from data_generator import TravelBidDataGenerator

# Generate 200 sample bids
generator = TravelBidDataGenerator()
bids = generator.generate_sample_bids(200)

# Save to CSV
generator.save_to_csv(bids, 'sample_bids.csv')
```

### 3. Run AI Analysis

```python
from nlp_processor import BidAnalyzer
from ml_models import BidScoringModel

# Initialize analyzer
analyzer = BidAnalyzer()
scoring_model = BidScoringModel()

# Load and analyze bids
import pandas as pd
bids_df = pd.read_csv('sample_bids.csv')
processed_bids = analyzer.analyze_bids(bids_df)

# Train models
quality_results = scoring_model.train_quality_predictor(processed_bids)
recommendations = scoring_model.get_recommendations(processed_bids, top_n=5)

print("Top Recommendations:")
for rec in recommendations:
    print(f"{rec['rank']}. {rec['supplier_name']} - ${rec['bid_amount']:,.2f}")
```

### 4. Start API Server

```bash
python api_server.py
```

The API will be available at `http://localhost:5000`

## API Endpoints

### Health Check
```bash
GET /health
```

### Analyze Multiple Bids
```bash
POST /analyze_bids
Content-Type: application/json

{
  "bids": [
    {
      "bid_id": "BID-001",
      "supplier_name": "ABC Transport",
      "service_type": "Transportation",
      "bid_amount": 2500.00,
      "bid_text": "Professional transportation services...",
      "quality_score": 85,
      "delivery_time_days": 7,
      "experience_years": 10,
      "certifications": true,
      "insurance_coverage": true
    }
  ]
}
```

### Analyze Single Bid
```bash
POST /analyze_single_bid
Content-Type: application/json

{
  "bid_text": "We offer premium transportation services...",
  "supplier_name": "XYZ Company",
  "service_type": "Transportation",
  "bid_amount": 3000.00
}
```

### Compare Bids
```bash
POST /compare_bids
Content-Type: application/json

{
  "bids": [/* array of bid objects */]
}
```

### Generate Sample Data
```bash
POST /generate_sample_data
Content-Type: application/json

{
  "num_bids": 50
}
```

## Laravel Integration

### 1. Add Service Configuration

Add to `config/services.php`:

```php
'bid_analysis' => [
    'url' => env('BID_ANALYSIS_URL', 'http://localhost:5000'),
    'timeout' => env('BID_ANALYSIS_TIMEOUT', 30),
],
```

### 2. Add Environment Variables

```env
BID_ANALYSIS_URL=http://localhost:5000
BID_ANALYSIS_TIMEOUT=30
```

### 3. Use the Service

```php
use App\Services\BidAnalysisService;

class BidController extends Controller
{
    protected $bidAnalysisService;

    public function __construct(BidAnalysisService $bidAnalysisService)
    {
        $this->bidAnalysisService = $bidAnalysisService;
    }

    public function analyzeBids(Request $request)
    {
        // Get bids from request
        $bids = $request->input('bids');

        // Format bids for AI service
        $formattedBids = array_map(function($bid) {
            return $this->bidAnalysisService->formatBidData($bid);
        }, $bids);

        // Analyze with caching
        $analysis = $this->bidAnalysisService->getAnalysisWithCache($formattedBids);

        return response()->json($analysis);
    }

    public function getRecommendations()
    {
        // Get all bids from database
        $bids = Bid::with('vendor')->get();

        // Format and analyze
        $formattedBids = $bids->map(function($bid) {
            return $this->bidAnalysisService->formatBidData($bid);
        })->toArray();

        $analysis = $this->bidAnalysisService->analyzeBids($formattedBids);

        return response()->json([
            'recommendations' => $analysis['recommendations'] ?? [],
            'rankings' => $analysis['rankings'] ?? []
        ]);
    }
}
```

### 4. Add Routes

```php
// routes/web.php or routes/api.php
Route::post('/bids/analyze', [BidController::class, 'analyzeBids']);
Route::get('/bids/recommendations', [BidController::class, 'getRecommendations']);
```

## Sample Datasets

The system includes a data generator that creates realistic bid data for:

- **Transportation Services**: Buses, cars, boats
- **Accommodation**: Hotels, resorts, guesthouses
- **Tour Guides**: Professional guides, local experts
- **Equipment Rental**: Vehicles, audio equipment, safety gear
- **Catering**: Food services, meal planning
- **Insurance**: Travel insurance, liability coverage

Each bid includes:
- Supplier information (name, type, experience)
- Service details (type, description, specifications)
- Pricing information
- Quality metrics
- Delivery timelines
- Certifications and insurance
- Customer ratings

## Model Performance

The system includes multiple ML models:

1. **Quality Predictor** (Random Forest)
   - Predicts bid quality scores
   - R² typically > 0.8

2. **Cost-Effectiveness Model** (SVR)
   - Evaluates value for money
   - Considers quality per dollar

3. **Supplier Classifier** (Random Forest)
   - Classifies suppliers as good/bad
   - Accuracy typically > 85%

4. **Supplier Clustering** (K-means)
   - Groups similar suppliers
   - Helps identify supplier segments

## Customization

### Adjust Scoring Weights

```python
# In ml_models.py, modify the weights in create_composite_score()
weights = {
    'price_weight': 0.3,        # Price importance
    'quality_weight': 0.25,     # Quality importance
    'delivery_weight': 0.15,    # Delivery speed importance
    'experience_weight': 0.1,   # Experience importance
    'rating_weight': 0.1,       # Customer rating importance
    'certification_weight': 0.05, # Certification importance
    'insurance_weight': 0.05    # Insurance importance
}
```

### Add New Service Types

```python
# In data_generator.py, add to service_categories
self.service_categories = [
    'Transportation', 'Accommodation', 'Tour Guide',
    'Equipment Rental', 'Catering', 'Insurance',
    'Your New Service'  # Add here
]
```

### Custom Text Analysis

```python
# In nlp_processor.py, extend BidTextProcessor
def extract_custom_features(self, text):
    # Add your custom feature extraction logic
    pass
```

## Deployment

### Production Setup

1. **Python Environment**:
   ```bash
   # Use production WSGI server
   pip install gunicorn
   gunicorn -w 4 -b 0.0.0.0:5000 api_server:app
   ```

2. **Docker** (optional):
   ```dockerfile
   FROM python:3.9-slim
   WORKDIR /app
   COPY requirements.txt .
   RUN pip install -r requirements.txt
   COPY . .
   EXPOSE 5000
   CMD ["gunicorn", "-w", "4", "-b", "0.0.0.0:5000", "api_server:app"]
   ```

3. **Laravel Production**:
   - Set proper environment variables
   - Use queue workers for async processing
   - Implement proper error handling

### Monitoring

- Health check endpoint: `GET /health`
- Model performance: `GET /model_performance`
- Log analysis requests and errors
- Monitor API response times

## Troubleshooting

### Common Issues

1. **NLTK Data Missing**:
   ```bash
   python -c "import nltk; nltk.download('punkt'); nltk.download('stopwords')"
   ```

2. **Model Training Fails**:
   - Ensure sufficient data (minimum 50 bids)
   - Check data quality and completeness
   - Verify feature columns exist

3. **API Connection Issues**:
   - Check if Flask server is running
   - Verify CORS settings
   - Check network connectivity

4. **Memory Issues**:
   - Reduce batch size for large datasets
   - Use data streaming for very large files
   - Implement result caching

## Contributing

1. Fork the repository
2. Create a feature branch
3. Add tests for new functionality
4. Ensure code passes linting
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support and questions:
- Create an issue in the repository
- Check the troubleshooting section
- Review the API documentation

