import pandas as pd
import numpy as np
from sklearn.model_selection import train_test_split, cross_val_score, GridSearchCV
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.ensemble import RandomForestRegressor, RandomForestClassifier
from sklearn.linear_model import LinearRegression, LogisticRegression
from sklearn.svm import SVR, SVC
from sklearn.metrics import mean_squared_error, r2_score, classification_report, confusion_matrix
from sklearn.cluster import KMeans
from sklearn.decomposition import PCA
import joblib
import warnings
warnings.filterwarnings('ignore')

class BidScoringModel:
    def __init__(self):
        """Initialize the bid scoring model"""
        self.scaler = StandardScaler()
        self.models = {}
        self.feature_importance = {}
        
    def prepare_features(self, bids_df):
        """Prepare features for ML models"""
        # Numerical features
        numerical_features = [
            'bid_amount', 'quality_score', 'delivery_time_days', 
            'experience_years', 'customer_rating', 'previous_projects',
            'warranty_months'
        ]
        
        # Categorical features
        categorical_features = [
            'service_type', 'supplier_type', 'payment_terms', 
            'location_coverage'
        ]
        
        # Boolean features
        boolean_features = [
            'certifications', 'insurance_coverage', 'availability_24_7',
            'sustainability_certified'
        ]
        
        # Create feature matrix
        X = pd.DataFrame()
        
        # Add numerical features
        for feature in numerical_features:
            if feature in bids_df.columns:
                X[feature] = bids_df[feature].fillna(bids_df[feature].median())
        
        # Add boolean features
        for feature in boolean_features:
            if feature in bids_df.columns:
                X[feature] = bids_df[feature].astype(int)
        
        # Encode categorical features
        label_encoders = {}
        for feature in categorical_features:
            if feature in bids_df.columns:
                le = LabelEncoder()
                X[feature] = le.fit_transform(bids_df[feature].fillna('Unknown'))
                label_encoders[feature] = le
        
        # Add text-based features if available
        if 'sentiment_score' in bids_df.columns:
            X['sentiment_score'] = bids_df['sentiment_score'].fillna(0)
        
        if 'text_complexity' in bids_df.columns:
            X['text_complexity'] = bids_df['text_complexity'].fillna(0)
        
        return X, label_encoders
    
    def create_composite_score(self, bids_df, weights=None):
        """Create a composite score for bid ranking"""
        if weights is None:
            weights = {
                'price_weight': 0.3,
                'quality_weight': 0.25,
                'delivery_weight': 0.15,
                'experience_weight': 0.1,
                'rating_weight': 0.1,
                'certification_weight': 0.05,
                'insurance_weight': 0.05
            }
        
        # Normalize features to 0-1 scale
        max_price = bids_df['bid_amount'].max()
        min_price = bids_df['bid_amount'].min()
        
        # Price score (lower is better)
        price_score = 1 - ((bids_df['bid_amount'] - min_price) / (max_price - min_price))
        
        # Quality score
        quality_score = bids_df['quality_score'] / 100
        
        # Delivery score (faster is better)
        max_delivery = bids_df['delivery_time_days'].max()
        min_delivery = bids_df['delivery_time_days'].min()
        delivery_score = 1 - ((bids_df['delivery_time_days'] - min_delivery) / (max_delivery - min_delivery))
        
        # Experience score
        max_exp = bids_df['experience_years'].max()
        experience_score = bids_df['experience_years'] / max_exp
        
        # Rating score
        rating_score = bids_df['customer_rating'] / 5
        
        # Certification and insurance scores
        cert_score = bids_df['certifications'].astype(int)
        insurance_score = bids_df['insurance_coverage'].astype(int)
        
        # Calculate composite score
        composite_score = (
            weights['price_weight'] * price_score +
            weights['quality_weight'] * quality_score +
            weights['delivery_weight'] * delivery_score +
            weights['experience_weight'] * experience_score +
            weights['rating_weight'] * rating_score +
            weights['certification_weight'] * cert_score +
            weights['insurance_weight'] * insurance_score
        )
        
        return composite_score
    
    def train_quality_predictor(self, bids_df):
        """Train a model to predict bid quality"""
        X, label_encoders = self.prepare_features(bids_df)
        y = bids_df['quality_score']
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train Random Forest model
        rf_model = RandomForestRegressor(n_estimators=100, random_state=42)
        rf_model.fit(X_train_scaled, y_train)
        
        # Evaluate
        y_pred = rf_model.predict(X_test_scaled)
        mse = mean_squared_error(y_test, y_pred)
        r2 = r2_score(y_test, y_pred)
        
        # Store model and feature importance
        self.models['quality_predictor'] = rf_model
        self.feature_importance['quality_predictor'] = dict(zip(X.columns, rf_model.feature_importances_))
        
        return {
            'model': rf_model,
            'mse': mse,
            'r2': r2,
            'feature_importance': self.feature_importance['quality_predictor']
        }
    
    def train_cost_effectiveness_model(self, bids_df):
        """Train a model to predict cost-effectiveness"""
        # Create cost-effectiveness metric (quality per dollar)
        bids_df['cost_effectiveness'] = bids_df['quality_score'] / bids_df['bid_amount']
        
        X, label_encoders = self.prepare_features(bids_df)
        y = bids_df['cost_effectiveness']
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train SVR model
        svr_model = SVR(kernel='rbf', C=1.0, gamma='scale')
        svr_model.fit(X_train_scaled, y_train)
        
        # Evaluate
        y_pred = svr_model.predict(X_test_scaled)
        mse = mean_squared_error(y_test, y_pred)
        r2 = r2_score(y_test, y_pred)
        
        self.models['cost_effectiveness'] = svr_model
        
        return {
            'model': svr_model,
            'mse': mse,
            'r2': r2
        }
    
    def train_supplier_classifier(self, bids_df):
        """Train a model to classify suppliers as good/bad"""
        # Create binary classification target
        median_quality = bids_df['quality_score'].median()
        bids_df['supplier_class'] = (bids_df['quality_score'] > median_quality).astype(int)
        
        X, label_encoders = self.prepare_features(bids_df)
        y = bids_df['supplier_class']
        
        # Split data
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42, stratify=y)
        
        # Scale features
        X_train_scaled = self.scaler.fit_transform(X_train)
        X_test_scaled = self.scaler.transform(X_test)
        
        # Train Random Forest classifier
        rf_classifier = RandomForestClassifier(n_estimators=100, random_state=42)
        rf_classifier.fit(X_train_scaled, y_train)
        
        # Evaluate
        y_pred = rf_classifier.predict(X_test_scaled)
        report = classification_report(y_test, y_pred)
        
        self.models['supplier_classifier'] = rf_classifier
        self.feature_importance['supplier_classifier'] = dict(zip(X.columns, rf_classifier.feature_importances_))
        
        return {
            'model': rf_classifier,
            'classification_report': report,
            'feature_importance': self.feature_importance['supplier_classifier']
        }
    
    def cluster_suppliers(self, bids_df, n_clusters=3):
        """Cluster suppliers based on their characteristics"""
        X, _ = self.prepare_features(bids_df)
        
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
        
        return {
            'clusters': clusters,
            'cluster_centers': kmeans.cluster_centers_,
            'pca_components': pca.components_
        }
    
    def rank_bids(self, bids_df, ranking_method='composite'):
        """Rank bids using different methods"""
        if ranking_method == 'composite':
            # Use composite scoring
            bids_df['composite_score'] = self.create_composite_score(bids_df)
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
        """Get top recommendations with explanations"""
        # Rank bids
        ranked_bids = self.rank_bids(bids_df, 'composite')
        
        recommendations = []
        for i, (_, bid) in enumerate(ranked_bids.head(top_n).iterrows()):
            recommendation = {
                'rank': i + 1,
                'supplier_name': bid['supplier_name'],
                'service_type': bid['service_type'],
                'bid_amount': bid['bid_amount'],
                'quality_score': bid['quality_score'],
                'composite_score': bid.get('composite_score', 0),
                'strengths': [],
                'weaknesses': []
            }
            
            # Analyze strengths and weaknesses
            if bid['quality_score'] > 80:
                recommendation['strengths'].append('High quality score')
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
                recommendation['strengths'].append('High experience')
            elif bid['experience_years'] < 3:
                recommendation['weaknesses'].append('Low experience')
            
            recommendations.append(recommendation)
        
        return recommendations
    
    def save_models(self, filepath):
        """Save trained models"""
        model_data = {
            'models': self.models,
            'scaler': self.scaler,
            'feature_importance': self.feature_importance
        }
        joblib.dump(model_data, filepath)
    
    def load_models(self, filepath):
        """Load trained models"""
        model_data = joblib.load(filepath)
        self.models = model_data['models']
        self.scaler = model_data['scaler']
        self.feature_importance = model_data['feature_importance']

# Example usage
if __name__ == "__main__":
    # Load sample data
    from data_generator import TravelBidDataGenerator
    
    generator = TravelBidDataGenerator()
    sample_bids = generator.generate_sample_bids(200)
    bids_df = pd.DataFrame(sample_bids)
    
    # Initialize and train models
    scoring_model = BidScoringModel()
    
    # Train quality predictor
    quality_results = scoring_model.train_quality_predictor(bids_df)
    print(f"Quality Predictor R²: {quality_results['r2']:.3f}")
    
    # Train cost-effectiveness model
    cost_results = scoring_model.train_cost_effectiveness_model(bids_df)
    print(f"Cost-Effectiveness R²: {cost_results['r2']:.3f}")
    
    # Train supplier classifier
    supplier_results = scoring_model.train_supplier_classifier(bids_df)
    print("Supplier Classification Report:")
    print(supplier_results['classification_report'])
    
    # Get recommendations
    recommendations = scoring_model.get_recommendations(bids_df, top_n=3)
    print("\nTop 3 Recommendations:")
    for rec in recommendations:
        print(f"{rec['rank']}. {rec['supplier_name']} - ${rec['bid_amount']:,.2f}")
        print(f"   Strengths: {', '.join(rec['strengths'])}")
        print(f"   Weaknesses: {', '.join(rec['weaknesses'])}")
        print()

