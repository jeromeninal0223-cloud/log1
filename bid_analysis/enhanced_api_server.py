from flask import Flask, request, jsonify
from flask_cors import CORS
import pandas as pd
import numpy as np
import json
import os
from datetime import datetime
import logging
import traceback

# Import our custom modules
from data_generator import TravelBidDataGenerator
from nlp_processor import BidAnalyzer
from enhanced_ml_models import EnhancedBidScoringModel

app = Flask(__name__)
CORS(app)  # Enable CORS for Laravel integration

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Global variables for models
bid_analyzer = None
scoring_model = None
trained_models = None
models_loaded = False

def initialize_models():
    """Initialize and train models on startup"""
    global bid_analyzer, scoring_model, trained_models, models_loaded
    
    logger.info("Initializing AI models...")
    
    try:
        # Generate sample data for training
        generator = TravelBidDataGenerator()
        training_bids = generator.generate_sample_bids(500)
        training_df = pd.DataFrame(training_bids)
        
        # Initialize analyzer
        bid_analyzer = BidAnalyzer()
        
        # Process training data
        processed_bids = bid_analyzer.analyze_bids(training_df)
        
        # Initialize and train scoring model
        scoring_model = EnhancedBidScoringModel()
        
        # Train all models
        results = scoring_model.train_all_models(processed_bids)
        
        # Save models
        os.makedirs('models', exist_ok=True)
        scoring_model.save_models('models/enhanced_bid_analysis_models.pkl')
        
        trained_models = {
            'winning_predictor_r2': results['winning_predictor']['r2'],
            'quality_predictor_r2': results['quality_predictor']['r2'],
            'supplier_classifier_accuracy': results['supplier_classifier']['accuracy'],
            'models_trained': True,
            'training_date': datetime.now().isoformat()
        }
        
        models_loaded = True
        logger.info("Models initialized successfully!")
        
    except Exception as e:
        logger.error(f"Error initializing models: {str(e)}")
        logger.error(traceback.format_exc())
        models_loaded = False

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'timestamp': datetime.now().isoformat(),
        'models_loaded': models_loaded,
        'models_performance': trained_models if trained_models else None
    })

@app.route('/analyze_bids', methods=['POST'])
def analyze_bids():
    """Analyze a collection of bids with enhanced AI"""
    try:
        data = request.get_json()
        
        if not data or 'bids' not in data:
            return jsonify({'error': 'No bids data provided'}), 400
        
        if not models_loaded:
            return jsonify({'error': 'AI models not loaded'}), 503
        
        bids_data = data['bids']
        
        # Convert to DataFrame
        bids_df = pd.DataFrame(bids_data)
        
        # Analyze bids with NLP
        processed_bids = bid_analyzer.analyze_bids(bids_df)
        
        # Get AI recommendations
        recommendations = scoring_model.get_recommendations(processed_bids, top_n=5)
        
        # Rank bids by different methods
        winning_ranked = scoring_model.rank_bids(processed_bids, 'winning_probability')
        cost_effective_ranked = scoring_model.rank_bids(processed_bids, 'cost_effectiveness')
        quality_ranked = scoring_model.rank_bids(processed_bids, 'quality')
        
        # Cluster suppliers
        clustering_results = scoring_model.cluster_suppliers(processed_bids)
        
        # Calculate winning probabilities
        winning_probabilities = scoring_model.predict_winning_probability(processed_bids)
        processed_bids['winning_probability'] = winning_probabilities
        
        # Prepare response
        response = {
            'analysis_summary': {
                'total_bids': len(bids_data),
                'service_types': processed_bids['service_type'].value_counts().to_dict(),
                'avg_bid_amount': float(processed_bids['bid_amount'].mean()),
                'avg_quality_score': float(processed_bids['quality_score'].mean()),
                'avg_winning_probability': float(processed_bids['winning_probability'].mean()),
                'models_performance': trained_models
            },
            'ai_recommendations': recommendations,
            'rankings': {
                'winning_probability': winning_ranked[['bid_id', 'supplier_name', 'winning_probability']].head(10).to_dict('records'),
                'cost_effectiveness': cost_effective_ranked[['bid_id', 'supplier_name', 'cost_effectiveness']].head(10).to_dict('records'),
                'quality': quality_ranked[['bid_id', 'supplier_name', 'quality_score']].head(10).to_dict('records')
            },
            'clustering': {
                'cluster_labels': clustering_results['clusters'].tolist(),
                'cluster_analysis': clustering_results['cluster_analysis']
            },
            'processed_bids': processed_bids.to_dict('records')
        }
        
        return jsonify(response)
        
    except Exception as e:
        logger.error(f"Error analyzing bids: {str(e)}")
        logger.error(traceback.format_exc())
        return jsonify({'error': str(e)}), 500

@app.route('/analyze_single_bid', methods=['POST'])
def analyze_single_bid():
    """Analyze a single bid with detailed AI insights"""
    try:
        data = request.get_json()
        
        if not data or 'bid_text' not in data:
            return jsonify({'error': 'No bid text provided'}), 400
        
        if not models_loaded:
            return jsonify({'error': 'AI models not loaded'}), 503
        
        bid_text = data['bid_text']
        
        # Process single bid text
        text_analysis = bid_analyzer.text_processor.process_bid_text(bid_text)
        
        # Create a mock bid for scoring
        mock_bid = {
            'bid_id': data.get('bid_id', 'SINGLE-001'),
            'supplier_name': data.get('supplier_name', 'Unknown'),
            'service_type': data.get('service_type', 'General'),
            'bid_amount': data.get('bid_amount', 0),
            'quality_score': data.get('quality_score', 75),
            'delivery_time_days': data.get('delivery_time_days', 7),
            'experience_years': data.get('experience_years', 5),
            'customer_rating': data.get('customer_rating', 4.0),
            'certifications': data.get('certifications', False),
            'insurance_coverage': data.get('insurance_coverage', False),
            'bid_text': bid_text
        }
        
        # Analyze the single bid
        single_bid_df = pd.DataFrame([mock_bid])
        processed_bid = bid_analyzer.analyze_bids(single_bid_df)
        
        # Get winning probability
        winning_probability = scoring_model.predict_winning_probability(processed_bid)[0]
        
        # Get quality prediction
        quality_prediction = scoring_model.models['quality_predictor'].predict(
            scoring_model.scaler.transform(scoring_model.prepare_features(processed_bid))
        )[0]
        
        # Get supplier classification
        supplier_class = scoring_model.models['supplier_classifier'].predict(
            scoring_model.scaler.transform(scoring_model.prepare_features(processed_bid))
        )[0]
        
        response = {
            'text_analysis': text_analysis,
            'processed_bid': processed_bid.to_dict('records')[0] if len(processed_bid) > 0 else None,
            'ai_predictions': {
                'winning_probability': float(winning_probability),
                'predicted_quality_score': float(quality_prediction),
                'supplier_classification': 'Good Supplier' if supplier_class == 1 else 'Average Supplier',
                'confidence_level': 'High' if winning_probability > 0.7 else 'Medium' if winning_probability > 0.4 else 'Low'
            },
            'sentiment': text_analysis['sentiment'],
            'extracted_features': {
                'price_mentioned': text_analysis['price_info']['price_mentioned'],
                'has_certifications': text_analysis['service_details']['certifications'],
                'has_insurance': text_analysis['service_details']['insurance'],
                'experience_years': text_analysis['service_details']['experience_years'],
                'key_phrases': text_analysis['key_phrases'][:5]
            },
            'recommendations': {
                'strengths': [],
                'weaknesses': [],
                'suggestions': []
            }
        }
        
        # Add recommendations based on analysis
        if winning_probability > 0.8:
            response['recommendations']['strengths'].append('High winning probability')
        elif winning_probability < 0.3:
            response['recommendations']['weaknesses'].append('Low winning probability')
        
        if quality_prediction > 80:
            response['recommendations']['strengths'].append('Predicted high quality')
        elif quality_prediction < 60:
            response['recommendations']['weaknesses'].append('Predicted low quality')
        
        if supplier_class == 1:
            response['recommendations']['strengths'].append('Classified as good supplier')
        else:
            response['recommendations']['weaknesses'].append('Classified as average supplier')
        
        return jsonify(response)
        
    except Exception as e:
        logger.error(f"Error analyzing single bid: {str(e)}")
        logger.error(traceback.format_exc())
        return jsonify({'error': str(e)}), 500

@app.route('/predict_winning_probability', methods=['POST'])
def predict_winning_probability():
    """Predict winning probability for bids"""
    try:
        data = request.get_json()
        
        if not data or 'bids' not in data:
            return jsonify({'error': 'No bids data provided'}), 400
        
        if not models_loaded:
            return jsonify({'error': 'AI models not loaded'}), 503
        
        bids_data = data['bids']
        bids_df = pd.DataFrame(bids_data)
        
        # Process bids
        processed_bids = bid_analyzer.analyze_bids(bids_df)
        
        # Predict winning probabilities
        winning_probabilities = scoring_model.predict_winning_probability(processed_bids)
        
        # Prepare response
        predictions = []
        for i, (_, bid) in enumerate(processed_bids.iterrows()):
            predictions.append({
                'bid_id': bid['bid_id'],
                'supplier_name': bid['supplier_name'],
                'winning_probability': float(winning_probabilities[i]),
                'confidence_level': 'High' if winning_probabilities[i] > 0.7 else 'Medium' if winning_probabilities[i] > 0.4 else 'Low',
                'rank': i + 1
            })
        
        # Sort by winning probability
        predictions.sort(key=lambda x: x['winning_probability'], reverse=True)
        
        return jsonify({
            'predictions': predictions,
            'top_contender': predictions[0] if predictions else None,
            'analysis_date': datetime.now().isoformat()
        })
        
    except Exception as e:
        logger.error(f"Error predicting winning probability: {str(e)}")
        logger.error(traceback.format_exc())
        return jsonify({'error': str(e)}), 500

@app.route('/compare_bids', methods=['POST'])
def compare_bids():
    """Compare multiple bids side by side with AI insights"""
    try:
        data = request.get_json()
        
        if not data or 'bids' not in data or len(data['bids']) < 2:
            return jsonify({'error': 'At least 2 bids required for comparison'}), 400
        
        if not models_loaded:
            return jsonify({'error': 'AI models not loaded'}), 503
        
        bids_data = data['bids']
        bids_df = pd.DataFrame(bids_data)
        
        # Analyze bids
        processed_bids = bid_analyzer.analyze_bids(bids_df)
        
        # Get winning probabilities
        winning_probabilities = scoring_model.predict_winning_probability(processed_bids)
        processed_bids['winning_probability'] = winning_probabilities
        
        # Create comparison matrix
        comparison_data = []
        for _, bid in processed_bids.iterrows():
            comparison_data.append({
                'bid_id': bid['bid_id'],
                'supplier_name': bid['supplier_name'],
                'service_type': bid['service_type'],
                'bid_amount': float(bid['bid_amount']),
                'quality_score': float(bid['quality_score']),
                'winning_probability': float(bid['winning_probability']),
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
        winning_probs = [bid['winning_probability'] for bid in comparison_data]
        
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
            'winning_probability_range': {
                'min': min(winning_probs),
                'max': max(winning_probs),
                'avg': sum(winning_probs) / len(winning_probs)
            },
            'ai_recommendations': {
                'best_value': min(comparison_data, key=lambda x: x['bid_amount'] / x['quality_score']) if any(x['quality_score'] > 0 for x in comparison_data) else None,
                'highest_quality': max(comparison_data, key=lambda x: x['quality_score']),
                'lowest_price': min(comparison_data, key=lambda x: x['bid_amount']),
                'most_likely_winner': max(comparison_data, key=lambda x: x['winning_probability'])
            }
        }
        
        response = {
            'comparison_data': comparison_data,
            'summary': comparison_summary,
            'ai_recommendations': scoring_model.get_recommendations(processed_bids, top_n=3)
        }
        
        return jsonify(response)
        
    except Exception as e:
        logger.error(f"Error comparing bids: {str(e)}")
        logger.error(traceback.format_exc())
        return jsonify({'error': str(e)}), 500

@app.route('/get_recommendations', methods=['POST'])
def get_recommendations():
    """Get AI-powered recommendations for bids"""
    try:
        data = request.get_json()
        
        if not data or 'bids' not in data:
            return jsonify({'error': 'No bids data provided'}), 400
        
        if not models_loaded:
            return jsonify({'error': 'AI models not loaded'}), 503
        
        bids_data = data['bids']
        top_n = data.get('top_n', 5)
        
        bids_df = pd.DataFrame(bids_data)
        processed_bids = bid_analyzer.analyze_bids(bids_df)
        
        recommendations = scoring_model.get_recommendations(processed_bids, top_n=top_n)
        
        return jsonify({
            'recommendations': recommendations,
            'total_bids_analyzed': len(bids_data),
            'analysis_date': datetime.now().isoformat()
        })
        
    except Exception as e:
        logger.error(f"Error getting recommendations: {str(e)}")
        logger.error(traceback.format_exc())
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
        logger.error(traceback.format_exc())
        return jsonify({'error': str(e)}), 500

@app.route('/model_performance', methods=['GET'])
def get_model_performance():
    """Get model performance metrics"""
    if not models_loaded:
        return jsonify({'error': 'Models not loaded'}), 503
    
    return jsonify({
        'models_performance': trained_models,
        'models_available': list(scoring_model.models.keys()) if scoring_model else [],
        'feature_importance': scoring_model.feature_importance if scoring_model else {}
    })

@app.route('/retrain_models', methods=['POST'])
def retrain_models():
    """Retrain models with new data"""
    try:
        data = request.get_json() or {}
        num_samples = data.get('num_samples', 500)
        
        logger.info(f"Retraining models with {num_samples} samples...")
        
        # Generate new training data
        generator = TravelBidDataGenerator()
        training_bids = generator.generate_sample_bids(num_samples)
        training_df = pd.DataFrame(training_bids)
        
        # Process training data
        processed_bids = bid_analyzer.analyze_bids(training_df)
        
        # Retrain models
        results = scoring_model.train_all_models(processed_bids)
        
        # Save updated models
        scoring_model.save_models('models/enhanced_bid_analysis_models.pkl')
        
        # Update global variables
        global trained_models
        trained_models = {
            'winning_predictor_r2': results['winning_predictor']['r2'],
            'quality_predictor_r2': results['quality_predictor']['r2'],
            'supplier_classifier_accuracy': results['supplier_classifier']['accuracy'],
            'models_trained': True,
            'training_date': datetime.now().isoformat(),
            'retrained': True
        }
        
        return jsonify({
            'success': True,
            'message': 'Models retrained successfully',
            'new_performance': trained_models
        })
        
    except Exception as e:
        logger.error(f"Error retraining models: {str(e)}")
        logger.error(traceback.format_exc())
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    # Create models directory if it doesn't exist
    os.makedirs('models', exist_ok=True)
    
    # Initialize models
    initialize_models()
    
    # Run the Flask app
    app.run(host='0.0.0.0', port=5000, debug=True)
