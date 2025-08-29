import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split, cross_val_score, GridSearchCV
from sklearn.preprocessing import StandardScaler, LabelEncoder, MinMaxScaler
from sklearn.ensemble import RandomForestRegressor, RandomForestClassifier, GradientBoostingRegressor
from sklearn.linear_model import LinearRegression, LogisticRegression, Ridge
from sklearn.svm import SVR, SVC
from sklearn.metrics import mean_squared_error, r2_score, classification_report, confusion_matrix, accuracy_score
from sklearn.cluster import KMeans
from sklearn.decomposition import PCA
from sklearn.feature_extraction.text import TfidfVectorizer
import joblib
import warnings
import logging
from datetime import datetime
warnings.filterwarnings('ignore')

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class EnhancedBidScoringModel:
    def __init__(self):
        """Initialize the enhanced bid scoring model"""
        self.scaler = StandardScaler()
        self.text_vectorizer = TfidfVectorizer(max_features=500, stop_words='english')
        self.models = {}
        self.feature_importance = {}
        self.label_encoders = {}
        self.is_trained = False
        
    def prepare_features(self, bids_df):
        """Prepare comprehensive features for ML models"""
        logger.info("Preparing features for ML models...")
        
        # Numerical features
        numerical_features = [
            'bid_amount', 'quality_score', 'delivery_time_days', 
            'experience_years', 'customer_rating', 'previous_projects',
            'warranty_months', 'team_size', 'response_time_hours'
        ]
        
        # Categorical features
        categorical_features = [
            'service_type', 'supplier_type', 'payment_terms', 
            'location_coverage', 'certification_level'
        ]
        
        # Boolean features
        boolean_features = [
            'certifications', 'insurance_coverage', 'availability_24_7',
            'sustainability_certified', 'has_portfolio', 'references_provided'
        ]
        
        # Create feature matrix
        X = pd.DataFrame()
        
        # Add numerical features with proper handling
        for feature in numerical_features:
            if feature in bids_df.columns:
                # Fill missing values with median
                median_val = bids_df[feature].median()
                X[feature] = bids_df[feature].fillna(median_val)
                
                # Handle outliers (cap at 95th percentile)
                q95 = X[feature].quantile(0.95)
                X[feature] = X[feature].clip(upper=q95)
        
        # Add boolean features
        for feature in boolean_features:
            if feature in bids_df.columns:
                X[feature] = bids_df[feature].fillna(False).astype(int)
        
        # Encode categorical features
        for feature in categorical_features:
            if feature in bids_df.columns:
                if feature not in self.label_encoders:
                    self.label_encoders[feature] = LabelEncoder()
                    # Fit on all unique values including 'Unknown'
                    unique_values = bids_df[feature].fillna('Unknown').unique()
                    self.label_encoders[feature].fit(unique_values)
                
                X[feature] = self.label_encoders[feature].transform(
                    bids_df[feature].fillna('Unknown')
                )
        
        # Add text-based features if available
        if 'sentiment_score' in bids_df.columns:
            X['sentiment_score'] = bids_df['sentiment_score'].fillna(0)
        
        if 'text_complexity' in bids_df.columns:
            X['text_complexity'] = bids_df['text_complexity'].fillna(0)
        
        if 'key_phrases_count' in bids_df.columns:
            X['key_phrases_count'] = bids_df['key_phrases_count'].fillna(0)
        
        # Add derived features
        if 'bid_amount' in X.columns and 'quality_score' in X.columns:
            X['quality_per_dollar'] = X['quality_score'] / (X['bid_amount'] + 1)
        
        if 'experience_years' in X.columns and 'customer_rating' in X.columns:
            X['experience_rating_score'] = X['experience_years'] * X['customer_rating']
        
        logger.info(f"Prepared {X.shape[1]} features for ML models")
        return X
    
    def create_winning_probability_score(self, bids_df, weights=None):
        """Create a winning probability score for each bid"""
        if weights is None:
            weights = {
                'price_weight': 0.25,
                'quality_weight': 0.20,
                'delivery_weight': 0.15,
                'experience_weight': 0.15,
                'rating_weight': 0.10,
                'certification_weight': 0.05,
                'insurance_weight': 0.05,
                'sentiment_weight': 0.05
            }
        
        # Normalize features to 0-1 scale
        scaler = MinMaxScaler()
        
        # Price score (lower is better, inverted)
        price_normalized = scaler.fit_transform(bids_df[['bid_amount']].values.reshape(-1, 1)).flatten()
        price_score = 1 - price_normalized
        
        # Quality score (higher is better)
        quality_score = bids_df['quality_score'] / 100
        
        # Delivery score (faster is better, inverted)
        delivery_normalized = scaler.fit_transform(bids_df[['delivery_time_days']].values.reshape(-1, 1)).flatten()
        delivery_score = 1 - delivery_normalized
        
        # Experience score
        max_exp = bids_df['experience_years'].max()
        experience_score = bids_df['experience_years'] / max_exp if max_exp > 0 else 0
        
        # Rating score
        rating_score = bids_df['customer_rating'] / 5
        
        # Certification and insurance scores
        cert_score = bids_df['certifications'].astype(int)
        insurance_score = bids_df['insurance_coverage'].astype(int)
        
        # Sentiment score (if available)
        sentiment_score = bids_df.get('sentiment_score', 0) / 2 + 0.5  # Convert -1,1 to 0,1
        
        # Calculate winning probability
        winning_probability = (
            weights['price_weight'] * price_score +
            weights['quality_weight'] * quality_score +
            weights['delivery_weight'] * delivery_score +
            weights['experience_weight'] * experience_score +
            weights['rating_weight'] * rating_score +
            weights['certification_weight'] * cert_score +
            weights['insurance_weight'] * insurance_score +
            weights['sentiment_weight'] * sentiment_score
        )
        
        return winning_probability
    
    def train_winning_predictor(self, bids_df):
        """Train a model to predict winning probability"""
        logger.info("Training winning probability predictor...")
        
        X = self.prepare_features(bids_df)
        
        # Create winning probability target
        winning_probability = self.create_winning_probability_score(bids_df)
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, winning_probability, test_size=0.2, random_state=42
        )
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train Gradient Boosting model
        gb_model = GradientBoostingRegressor(
            n_estimators=100,
            learning_rate=0.1,
            max_depth=6,
            random_state=42
        )
        
        gb_model.fit(X_train_scaled, y_train)
        
        # Evaluate
        y_pred = gb_model.predict(X_test_scaled)
        mse = mean_squared_error(y_test, y_pred)
        r2 = r2_score(y_test, y_pred)
        
        # Store model and feature importance
        self.models['winning_predictor'] = gb_model
        self.feature_importance['winning_predictor'] = dict(
            zip(X.columns, gb_model.feature_importances_)
        )
        
        logger.info(f"Winning predictor trained - R²: {r2:.3f}, MSE: {mse:.4f}")
        
        return {
            'model': gb_model,
            'mse': mse,
            'r2': r2,
            'feature_importance': self.feature_importance['winning_predictor']
        }
    
    def train_quality_predictor(self, bids_df):
        """Train a model to predict bid quality"""
        logger.info("Training quality predictor...")
        
        X = self.prepare_features(bids_df)
        y = bids_df['quality_score']
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, y, test_size=0.2, random_state=42
        )
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train Random Forest model
        rf_model = RandomForestRegressor(
            n_estimators=100,
            max_depth=10,
            random_state=42
        )
        rf_model.fit(X_train_scaled, y_train)
        
        # Evaluate
        y_pred = rf_model.predict(X_test_scaled)
        mse = mean_squared_error(y_test, y_pred)
        r2 = r2_score(y_test, y_pred)
        
        # Store model and feature importance
        self.models['quality_predictor'] = rf_model
        self.feature_importance['quality_predictor'] = dict(
            zip(X.columns, rf_model.feature_importances_)
        )
        
        logger.info(f"Quality predictor trained - R²: {r2:.3f}, MSE: {mse:.4f}")
        
        return {
            'model': rf_model,
            'mse': mse,
            'r2': r2,
            'feature_importance': self.feature_importance['quality_predictor']
        }
    
    def train_supplier_classifier(self, bids_df):
        """Train a model to classify suppliers as good/bad"""
        logger.info("Training supplier classifier...")
        
        X = self.prepare_features(bids_df)
        
        # Create binary classification target based on quality and price
        median_quality = bids_df['quality_score'].median()
        median_price = bids_df['bid_amount'].median()
        
        # Good supplier: above median quality and below median price
        good_supplier = (
            (bids_df['quality_score'] > median_quality) & 
            (bids_df['bid_amount'] < median_price)
        ).astype(int)
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(
            X, good_supplier, test_size=0.2, random_state=42, stratify=good_supplier
        )
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train Random Forest classifier
        rf_classifier = RandomForestClassifier(
            n_estimators=100,
            max_depth=8,
            random_state=42
        )
        rf_classifier.fit(X_train_scaled, y_train)
        
        # Evaluate
        y_pred = rf_classifier.predict(X_test_scaled)
        accuracy = accuracy_score(y_test, y_pred)
        report = classification_report(y_test, y_pred)
        
        self.models['supplier_classifier'] = rf_classifier
        self.feature_importance['supplier_classifier'] = dict(
            zip(X.columns, rf_classifier.feature_importances_)
        )
        
        logger.info(f"Supplier classifier trained - Accuracy: {accuracy:.3f}")
        
        return {
            'model': rf_classifier,
            'accuracy': accuracy,
            'classification_report': report,
            'feature_importance': self.feature_importance['supplier_classifier']
        }
    
    def predict_winning_probability(self, bids_df):
        """Predict winning probability for bids"""
        if 'winning_predictor' not in self.models:
            raise ValueError("Winning predictor model not trained")
        
        X = self.prepare_features(bids_df)
        X_scaled = self.scaler.transform(X)
        
        probabilities = self.models['winning_predictor'].predict(X_scaled)
        return probabilities
    
    def rank_bids(self, bids_df, ranking_method='winning_probability'):
        """Rank bids using different methods"""
        if ranking_method == 'winning_probability':
            # Use winning probability
            winning_probs = self.predict_winning_probability(bids_df)
            bids_df['winning_probability'] = winning_probs
            ranked_bids = bids_df.sort_values('winning_probability', ascending=False)
            
        elif ranking_method == 'composite':
            # Use composite scoring
            composite_score = self.create_winning_probability_score(bids_df)
            bids_df['composite_score'] = composite_score
            ranked_bids = bids_df.sort_values('composite_score', ascending=False)
            
        elif ranking_method == 'cost_effectiveness':
            # Rank by cost-effectiveness
            bids_df['cost_effectiveness'] = bids_df['quality_score'] / bids_df['bid_amount']
            ranked_bids = bids_df.sort_values('cost_effectiveness', ascending=False)
            
        elif ranking_method == 'quality':
            # Rank by quality score
            ranked_bids = bids_df.sort_values('quality_score', ascending=False)
            
        elif ranking_method == 'price':
            # Rank by price (lowest first)
            ranked_bids = bids_df.sort_values('bid_amount', ascending=True)
            
        else:
            raise ValueError(f"Unknown ranking method: {ranking_method}")
        
        return ranked_bids
    
    def get_recommendations(self, bids_df, top_n=5):
        """Get top recommendations with detailed analysis"""
        # Rank bids by winning probability
        ranked_bids = self.rank_bids(bids_df, 'winning_probability')
        
        recommendations = []
        for i, (_, bid) in enumerate(ranked_bids.head(top_n).iterrows()):
            recommendation = {
                'rank': i + 1,
                'bid_id': bid['bid_id'],
                'supplier_name': bid['supplier_name'],
                'service_type': bid['service_type'],
                'bid_amount': float(bid['bid_amount']),
                'quality_score': float(bid['quality_score']),
                'winning_probability': float(bid.get('winning_probability', 0)),
                'delivery_time_days': int(bid['delivery_time_days']),
                'experience_years': int(bid['experience_years']),
                'customer_rating': float(bid['customer_rating']),
                'strengths': [],
                'weaknesses': [],
                'risk_factors': [],
                'recommendation_reason': ''
            }
            
            # Analyze strengths and weaknesses
            if bid['quality_score'] > 85:
                recommendation['strengths'].append('Excellent quality score')
            elif bid['quality_score'] > 70:
                recommendation['strengths'].append('Good quality score')
            elif bid['quality_score'] < 60:
                recommendation['weaknesses'].append('Low quality score')
            
            if bid['certifications']:
                recommendation['strengths'].append('Has certifications')
            else:
                recommendation['weaknesses'].append('No certifications')
            
            if bid['insurance_coverage']:
                recommendation['strengths'].append('Has insurance coverage')
            else:
                recommendation['weaknesses'].append('No insurance coverage')
            
            if bid['experience_years'] > 10:
                recommendation['strengths'].append('High experience level')
            elif bid['experience_years'] < 3:
                recommendation['weaknesses'].append('Low experience level')
            
            if bid['customer_rating'] > 4.5:
                recommendation['strengths'].append('Excellent customer rating')
            elif bid['customer_rating'] < 3.5:
                recommendation['weaknesses'].append('Low customer rating')
            
            # Risk factors
            if bid['delivery_time_days'] > 30:
                recommendation['risk_factors'].append('Long delivery time')
            
            if bid['bid_amount'] > bids_df['bid_amount'].quantile(0.9):
                recommendation['risk_factors'].append('High bid amount')
            
            # Recommendation reason
            if recommendation['winning_probability'] > 0.8:
                recommendation['recommendation_reason'] = 'High winning probability with strong overall profile'
            elif recommendation['winning_probability'] > 0.6:
                recommendation['recommendation_reason'] = 'Good balance of quality and cost'
            else:
                recommendation['recommendation_reason'] = 'Competitive option with some trade-offs'
            
            recommendations.append(recommendation)
        
        return recommendations
    
    def cluster_suppliers(self, bids_df, n_clusters=3):
        """Cluster suppliers based on their characteristics"""
        X = self.prepare_features(bids_df)
        
        # Scale features
        X_scaled = self.scaler.fit_transform(X)
        
        # Apply PCA for dimensionality reduction
        pca = PCA(n_components=min(5, X.shape[1]))
        X_pca = pca.fit_transform(X_scaled)
        
        # K-means clustering
        kmeans = KMeans(n_clusters=n_clusters, random_state=42)
        clusters = kmeans.fit_predict(X_pca)
        
        # Add cluster labels to dataframe
        bids_df['cluster'] = clusters
        
        # Analyze clusters
        cluster_analysis = {}
        for cluster_id in range(n_clusters):
            cluster_bids = bids_df[bids_df['cluster'] == cluster_id]
            cluster_analysis[cluster_id] = {
                'size': len(cluster_bids),
                'avg_quality': float(cluster_bids['quality_score'].mean()),
                'avg_price': float(cluster_bids['bid_amount'].mean()),
                'avg_experience': float(cluster_bids['experience_years'].mean()),
                'characteristics': self._analyze_cluster_characteristics(cluster_bids)
            }
        
        return {
            'clusters': clusters,
            'cluster_centers': kmeans.cluster_centers_,
            'cluster_analysis': cluster_analysis,
            'pca_components': pca.components_
        }
    
    def _analyze_cluster_characteristics(self, cluster_bids):
        """Analyze characteristics of a cluster"""
        characteristics = []
        
        if cluster_bids['quality_score'].mean() > 80:
            characteristics.append('High Quality')
        elif cluster_bids['quality_score'].mean() < 60:
            characteristics.append('Low Quality')
        
        if cluster_bids['bid_amount'].mean() > cluster_bids['bid_amount'].quantile(0.75):
            characteristics.append('Premium Pricing')
        elif cluster_bids['bid_amount'].mean() < cluster_bids['bid_amount'].quantile(0.25):
            characteristics.append('Budget Friendly')
        
        if cluster_bids['experience_years'].mean() > 10:
            characteristics.append('Experienced')
        elif cluster_bids['experience_years'].mean() < 3:
            characteristics.append('New/Startup')
        
        return characteristics
    
    def save_models(self, filepath):
        """Save trained models and metadata"""
        model_data = {
            'models': self.models,
            'scaler': self.scaler,
            'feature_importance': self.feature_importance,
            'label_encoders': self.label_encoders,
            'is_trained': self.is_trained,
            'trained_at': datetime.now().isoformat()
        }
        joblib.dump(model_data, filepath)
        logger.info(f"Models saved to {filepath}")
    
    def load_models(self, filepath):
        """Load trained models and metadata"""
        model_data = joblib.load(filepath)
        self.models = model_data['models']
        self.scaler = model_data['scaler']
        self.feature_importance = model_data['feature_importance']
        self.label_encoders = model_data['label_encoders']
        self.is_trained = model_data.get('is_trained', False)
        logger.info(f"Models loaded from {filepath}")
    
    def train_all_models(self, bids_df):
        """Train all models on the provided data"""
        logger.info("Training all models...")
        
        # Train winning predictor
        winning_results = self.train_winning_predictor(bids_df)
        
        # Train quality predictor
        quality_results = self.train_quality_predictor(bids_df)
        
        # Train supplier classifier
        supplier_results = self.train_supplier_classifier(bids_df)
        
        self.is_trained = True
        
        return {
            'winning_predictor': winning_results,
            'quality_predictor': quality_results,
            'supplier_classifier': supplier_results
        }

# Example usage
if __name__ == "__main__":
    # Load sample data
    from data_generator import TravelBidDataGenerator
    
    generator = TravelBidDataGenerator()
    sample_bids = generator.generate_sample_bids(200)
    bids_df = pd.DataFrame(sample_bids)
    
    # Initialize and train models
    scoring_model = EnhancedBidScoringModel()
    
    # Train all models
    results = scoring_model.train_all_models(bids_df)
    
    print("Model Training Results:")
    print(f"Winning Predictor R²: {results['winning_predictor']['r2']:.3f}")
    print(f"Quality Predictor R²: {results['quality_predictor']['r2']:.3f}")
    print(f"Supplier Classifier Accuracy: {results['supplier_classifier']['accuracy']:.3f}")
    
    # Get recommendations
    recommendations = scoring_model.get_recommendations(bids_df, top_n=3)
    print("\nTop 3 Recommendations:")
    for rec in recommendations:
        print(f"{rec['rank']}. {rec['supplier_name']} - ${rec['bid_amount']:,.2f}")
        print(f"   Winning Probability: {rec['winning_probability']:.2%}")
        print(f"   Strengths: {', '.join(rec['strengths'])}")
        print(f"   Weaknesses: {', '.join(rec['weaknesses'])}")
        print()
