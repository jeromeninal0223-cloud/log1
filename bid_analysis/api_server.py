from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import numpy as np
import json
import os
from datetime import datetime
import logging

# Import our custom modules
from data_generator import TravelBidDataGenerator
from nlp_processor import BidAnalyzer
from ml_models import BidScoringModel

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel integration

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Global variables for models
bid_analyzer = None
scoring_model = None
trained_models = None

def initialize_models():
    """Initialize and train models on startup"""
    global bid_analyzer, scoring_model, trained_models
    
    logger.info("Initializing AI models...")
    
    # Generate sample data for training
    generator = TravelBidDataGenerator()
    training_bids = generator.generate_sample_bids(500)
    training_df = pd.DataFrame(training_bids)
    
    # Initialize analyzer
    bid_analyzer = BidAnalyzer()
    
    # Process training data
    processed_bids = bid_analyzer.analyze_bids(training_df)
    
    # Initialize and train scoring model
    scoring_model = BidScoringModel()
    
    # Train models
    quality_results = scoring_model.train_quality_predictor(processed_bids)
    cost_results = scoring_model.train_cost_effectiveness_model(processed_bids)
    supplier_results = scoring_model.train_supplier_classifier(processed_bids)
    
    # Save models
    scoring_model.save_models('models/bid_analysis_models.pkl')
    
    trained_models = {
        'quality_r2': quality_results['r2'],
        'cost_effectiveness_r2': cost_results['r2'],
        'supplier_classification_report': supplier_results['classification_report']
    }
    
    logger.info("Models initialized successfully!")

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'models_loaded': trained_models is not None
    })

@app.route('/analyze_bids', methods=['POST'])
def analyze_bids():
    """Analyze a collection of bids"""
    try:
        data = request.get_json()
        
        if not data or 'bids' not in data:
            return jsonify({'error': 'No bids data provided'}), 400
        
        bids_data = data['bids']
        
        # Convert to DataFrame
        bids_df = pd.DataFrame(bids_data)
        
        # Analyze bids
        processed_bids = bid_analyzer.analyze_bids(bids_df)
        
        # Get recommendations
        recommendations = scoring_model.get_recommendations(processed_bids, top_n=5)
        
        # Rank bids by different methods
        composite_ranked = scoring_model.rank_bids(processed_bids, 'composite')
        cost_effective_ranked = scoring_model.rank_bids(processed_bids, 'cost_effectiveness')
        quality_ranked = scoring_model.rank_bids(processed_bids, 'quality')
        
        # Cluster suppliers
        clustering_results = scoring_model.cluster_suppliers(processed_bids)
        
        # Prepare response
        response = {
            'analysis_summary': {
                'total_bids': len(bids_data),
                'service_types': processed_bids['service_type'].value_counts().to_dict(),
                'avg_bid_amount': float(processed_bids['bid_amount'].mean()),
                'avg_quality_score': float(processed_bids['quality_score'].mean()),
                'models_performance': trained_models
            },
            'recommendations': recommendations,
            'rankings': {
                'composite': composite_ranked[['bid_id', 'supplier_name', 'composite_score']].head(10).to_dict('records'),
                'cost_effectiveness': cost_effective_ranked[['bid_id', 'supplier_name', 'cost_effectiveness']].head(10).to_dict('records'),
                'quality': quality_ranked[['bid_id', 'supplier_name', 'quality_score']].head(10).to_dict('records')
            },
            'clustering': {
                'cluster_labels': clustering_results['clusters'].tolist(),
                'cluster_centers': clustering_results['cluster_centers'].tolist()
            },
            'processed_bids': processed_bids.to_dict('records')
        }
        
        return jsonify(response)
        
    except Exception as e:
        logger.error(f"Error analyzing bids: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/analyze_single_bid', methods=['POST'])
def analyze_single_bid():
    """Analyze a single bid"""
    try:
        data = request.get_json()
        
        if not data or 'bid_text' not in data:
            return jsonify({'error': 'No bid text provided'}), 400
        
        bid_text = data['bid_text']
        
        # Process single bid text
        text_analysis = bid_analyzer.text_processor.process_bid_text(bid_text)
        
        # Create a mock bid for scoring
        mock_bid = {
            'bid_id': 'SINGLE-001',
            'supplier_name': data.get('supplier_name', 'Unknown'),
            'service_type': data.get('service_type', 'General'),
            'bid_amount': data.get('bid_amount', 0),
            'bid_text': bid_text
        }
        
        # Analyze the single bid
        single_bid_df = pd.DataFrame([mock_bid])
        processed_bid = bid_analyzer.analyze_bids(single_bid_df)
        
        response = {
            'text_analysis': text_analysis,
            'processed_bid': processed_bid.to_dict('records')[0] if len(processed_bid) > 0 else None,
            'sentiment': text_analysis['sentiment'],
            'extracted_features': {
                'price_mentioned': text_analysis['price_info']['price_mentioned'],
                'has_certifications': text_analysis['service_details']['certifications'],
                'has_insurance': text_analysis['service_details']['insurance'],
                'experience_years': text_analysis['service_details']['experience_years'],
                'key_phrases': text_analysis['key_phrases'][:5]
            }
        }
        
        return jsonify(response)
        
    except Exception as e:
        logger.error(f"Error analyzing single bid: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/predict_quality', methods=['POST'])
def predict_quality():
    """Predict quality score for a bid"""
    try:
        data = request.get_json()
        
        if not data:
            return jsonify({'error': 'No data provided'}), 400
        
        # Create bid DataFrame
        bid_df = pd.DataFrame([data])
        
        # Prepare features
        X, _ = scoring_model.prepare_features(bid_df)
        
        # Scale features
        X_scaled = scoring_model.scaler.transform(X)
        
        # Predict quality
        if 'quality_predictor' in scoring_model.models:
            predicted_quality = scoring_model.models['quality_predictor'].predict(X_scaled)[0]
            
            response = {
                'predicted_quality_score': float(predicted_quality),
                'confidence': 'high' if predicted_quality > 70 else 'medium' if predicted_quality > 50 else 'low',
                'features_used': list(X.columns)
            }
            
            return jsonify(response)
        else:
            return jsonify({'error': 'Quality predictor model not available'}), 500
            
    except Exception as e:
        logger.error(f"Error predicting quality: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/compare_bids', methods=['POST'])
def compare_bids():
    """Compare multiple bids side by side"""
    try:
        data = request.get_json()
        
        if not data or 'bids' not in data or len(data['bids']) < 2:
            return jsonify({'error': 'At least 2 bids required for comparison'}), 400
        
        bids_data = data['bids']
        bids_df = pd.DataFrame(bids_data)
        
        # Analyze bids
        processed_bids = bid_analyzer.analyze_bids(bids_df)
        
        # Create comparison matrix
        comparison_data = []
        for _, bid in processed_bids.iterrows():
            comparison_data.append({
                'bid_id': bid['bid_id'],
                'supplier_name': bid['supplier_name'],
                'service_type': bid['service_type'],
                'bid_amount': float(bid['bid_amount']),
                'quality_score': float(bid['quality_score']),
                'sentiment_score': float(bid.get('sentiment_score', 0)),
                'delivery_time_days': int(bid['delivery_time_days']),
                'experience_years': int(bid['experience_years']),
                'customer_rating': float(bid['customer_rating']),
                'certifications': bool(bid['certifications']),
                'insurance_coverage': bool(bid['insurance_coverage']),
                'cost_effectiveness': float(bid['quality_score'] / bid['bid_amount']) if bid['bid_amount'] > 0 else 0
            })
        
        # Calculate statistics
        amounts = [bid['bid_amount'] for bid in comparison_data]
        quality_scores = [bid['quality_score'] for bid in comparison_data]
        
        comparison_summary = {
            'price_range': {
                'min': min(amounts),
                'max': max(amounts),
                'avg': sum(amounts) / len(amounts)
            },
            'quality_range': {
                'min': min(quality_scores),
                'max': max(quality_scores),
                'avg': sum(quality_scores) / len(quality_scores)
            },
            'best_value': min(comparison_data, key=lambda x: x['bid_amount'] / x['quality_score']) if any(x['quality_score'] > 0 for x in comparison_data) else None,
            'highest_quality': max(comparison_data, key=lambda x: x['quality_score']),
            'lowest_price': min(comparison_data, key=lambda x: x['bid_amount'])
        }
        
        response = {
            'comparison_data': comparison_data,
            'summary': comparison_summary,
            'recommendations': scoring_model.get_recommendations(processed_bids, top_n=3)
        }
        
        return jsonify(response)
        
    except Exception as e:
        logger.error(f"Error comparing bids: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/generate_sample_data', methods=['POST'])
def generate_sample_data():
    """Generate sample bid data for testing"""
    try:
        data = request.get_json() or {}
        num_bids = data.get('num_bids', 50)
        
        generator = TravelBidDataGenerator()
        sample_bids = generator.generate_sample_bids(num_bids)
        
        return jsonify({
            'bids': sample_bids,
            'count': len(sample_bids),
            'generated_at': datetime.now().isoformat()
        })
        
    except Exception as e:
        logger.error(f"Error generating sample data: {str(e)}")
        return jsonify({'error': str(e)}), 500

@app.route('/model_performance', methods=['GET'])
def get_model_performance():
    """Get model performance metrics"""
    if trained_models is None:
        return jsonify({'error': 'Models not initialized'}), 500
    
    return jsonify({
        'models_performance': trained_models,
        'models_available': list(scoring_model.models.keys()) if scoring_model else []
    })

if __name__ == '__main__':
    # Create models directory if it doesn't exist
    os.makedirs('models', exist_ok=True)
    
    # Initialize models
    initialize_models()
    
    # Run the Flask app
    app.run(host='0.0.0.0', port=5000, debug=True)

