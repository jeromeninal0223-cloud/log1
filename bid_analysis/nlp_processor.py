import re
import pandas as pd
import numpy as np
from sklearn.feature_extraction.text import TfidfVectorizer, CountVectorizer
from sklearn.decomposition import LatentDirichletAllocation, NMF
from textblob import TextBlob
import spacy
import nltk
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize, sent_tokenize
import warnings
warnings.filterwarnings('ignore')

# Download required NLTK data
try:
    nltk.data.find('tokenizers/punkt')
except LookupError:
    nltk.download('punkt')

try:
    nltk.data.find('corpora/stopwords')
except LookupError:
    nltk.download('stopwords')

class BidTextProcessor:
    def __init__(self):
        """Initialize the bid text processor"""
        self.stop_words = set(stopwords.words('english'))
        self.tfidf_vectorizer = TfidfVectorizer(
            max_features=1000,
            stop_words='english',
            ngram_range=(1, 2),
            min_df=2
        )
        
        # Keywords for different aspects
        self.price_keywords = [
            'price', 'cost', 'amount', 'total', 'fee', 'rate', 'charge',
            'dollars', 'dollar', '$', 'usd', 'price quote', 'estimate'
        ]
        
        self.quality_keywords = [
            'quality', 'excellent', 'premium', 'professional', 'certified',
            'licensed', 'insured', 'guaranteed', 'warranty', 'experience',
            'years', 'reputation', 'reliable', 'trusted'
        ]
        
        self.service_keywords = [
            'service', 'support', 'available', 'flexible', 'custom',
            'tailored', 'comprehensive', 'complete', 'full', 'included'
        ]
        
        self.time_keywords = [
            'delivery', 'time', 'schedule', 'punctual', 'on-time',
            'deadline', 'duration', 'period', 'days', 'hours'
        ]
    
    def extract_price_information(self, text):
        """Extract price information from bid text"""
        price_patterns = [
            r'\$\s*([\d,]+\.?\d*)',
            r'(\d+(?:,\d{3})*(?:\.\d{2})?)\s*(?:dollars?|usd)',
            r'price[:\s]*\$?\s*([\d,]+\.?\d*)',
            r'cost[:\s]*\$?\s*([\d,]+\.?\d*)',
            r'amount[:\s]*\$?\s*([\d,]+\.?\d*)'
        ]
        
        prices = []
        for pattern in price_patterns:
            matches = re.findall(pattern, text.lower())
            for match in matches:
                try:
                    price = float(match.replace(',', ''))
                    prices.append(price)
                except ValueError:
                    continue
        
        return {
            'extracted_prices': prices,
            'min_price': min(prices) if prices else None,
            'max_price': max(prices) if prices else None,
            'price_mentioned': len(prices) > 0
        }
    
    def extract_service_details(self, text):
        """Extract service-specific details from bid text"""
        service_details = {
            'experience_years': None,
            'certifications': False,
            'insurance': False,
            'warranty': False,
            'availability_24_7': False,
            'customizable': False
        }
        
        # Extract experience years
        exp_patterns = [
            r'(\d+)\s*years?\s*experience',
            r'experience[:\s]*(\d+)\s*years?',
            r'(\d+)\s*years?\s*in\s*business'
        ]
        
        for pattern in exp_patterns:
            match = re.search(pattern, text.lower())
            if match:
                service_details['experience_years'] = int(match.group(1))
                break
        
        # Check for certifications
        cert_keywords = ['certified', 'certification', 'licensed', 'accredited']
        service_details['certifications'] = any(keyword in text.lower() for keyword in cert_keywords)
        
        # Check for insurance
        insurance_keywords = ['insurance', 'insured', 'coverage']
        service_details['insurance'] = any(keyword in text.lower() for keyword in insurance_keywords)
        
        # Check for warranty
        warranty_keywords = ['warranty', 'guarantee', 'guaranteed']
        service_details['warranty'] = any(keyword in text.lower() for keyword in warranty_keywords)
        
        # Check for 24/7 availability
        availability_keywords = ['24/7', '24 hours', 'round the clock', 'always available']
        service_details['availability_24_7'] = any(keyword in text.lower() for keyword in availability_keywords)
        
        # Check for customization
        custom_keywords = ['custom', 'tailored', 'flexible', 'adjustable']
        service_details['customizable'] = any(keyword in text.lower() for keyword in custom_keywords)
        
        return service_details
    
    def analyze_sentiment(self, text):
        """Analyze sentiment of bid text"""
        blob = TextBlob(text)
        
        return {
            'polarity': blob.sentiment.polarity,  # -1 to 1
            'subjectivity': blob.sentiment.subjectivity,  # 0 to 1
            'sentiment_label': self._get_sentiment_label(blob.sentiment.polarity)
        }
    
    def _get_sentiment_label(self, polarity):
        """Convert polarity score to label"""
        if polarity > 0.1:
            return 'positive'
        elif polarity < -0.1:
            return 'negative'
        else:
            return 'neutral'
    
    def extract_key_phrases(self, text, top_n=10):
        """Extract key phrases from bid text using TF-IDF"""
        # Clean text
        cleaned_text = re.sub(r'[^\w\s]', '', text.lower())
        
        # Create TF-IDF vectorizer for single document
        tfidf = TfidfVectorizer(
            stop_words='english',
            ngram_range=(1, 3),
            max_features=top_n
        )
        
        try:
            tfidf_matrix = tfidf.fit_transform([cleaned_text])
            feature_names = tfidf.get_feature_names_out()
            
            # Get top phrases
            scores = tfidf_matrix.toarray()[0]
            phrase_scores = list(zip(feature_names, scores))
            phrase_scores.sort(key=lambda x: x[1], reverse=True)
            
            return [phrase for phrase, score in phrase_scores if score > 0]
        except:
            return []
    
    def calculate_text_complexity(self, text):
        """Calculate text complexity metrics"""
        sentences = sent_tokenize(text)
        words = word_tokenize(text.lower())
        
        # Remove punctuation and stop words
        words = [word for word in words if word.isalpha() and word not in self.stop_words]
        
        return {
            'word_count': len(words),
            'sentence_count': len(sentences),
            'avg_sentence_length': len(words) / len(sentences) if sentences else 0,
            'unique_words': len(set(words)),
            'lexical_diversity': len(set(words)) / len(words) if words else 0
        }
    
    def process_bid_text(self, text):
        """Complete text processing pipeline"""
        results = {
            'price_info': self.extract_price_information(text),
            'service_details': self.extract_service_details(text),
            'sentiment': self.analyze_sentiment(text),
            'key_phrases': self.extract_key_phrases(text),
            'complexity': self.calculate_text_complexity(text)
        }
        
        return results

class BidAnalyzer:
    def __init__(self):
        """Initialize the bid analyzer"""
        self.text_processor = BidTextProcessor()
        self.tfidf_vectorizer = TfidfVectorizer(
            max_features=500,
            stop_words='english',
            ngram_range=(1, 2)
        )
    
    def analyze_bids(self, bids_data):
        """Analyze a collection of bids"""
        if isinstance(bids_data, str):
            # Load from CSV
            df = pd.read_csv(bids_data)
        elif isinstance(bids_data, pd.DataFrame):
            df = bids_data
        else:
            df = pd.DataFrame(bids_data)
        
        # Process each bid
        processed_bids = []
        for _, bid in df.iterrows():
            text_analysis = self.text_processor.process_bid_text(bid['bid_text'])
            
            processed_bid = {
                'bid_id': bid['bid_id'],
                'supplier_name': bid['supplier_name'],
                'service_type': bid['service_type'],
                'bid_amount': bid['bid_amount'],
                'text_analysis': text_analysis,
                'extracted_price': text_analysis['price_info']['min_price'],
                'sentiment_score': text_analysis['sentiment']['polarity'],
                'experience_years': text_analysis['service_details']['experience_years'],
                'has_certifications': text_analysis['service_details']['certifications'],
                'has_insurance': text_analysis['service_details']['insurance'],
                'text_complexity': text_analysis['complexity']['word_count']
            }
            
            processed_bids.append(processed_bid)
        
        return pd.DataFrame(processed_bids)
    
    def create_text_features(self, bids_df):
        """Create text-based features for ML models"""
        texts = bids_df['bid_text'].tolist()
        
        # TF-IDF features
        tfidf_matrix = self.tfidf_vectorizer.fit_transform(texts)
        tfidf_features = pd.DataFrame(
            tfidf_matrix.toarray(),
            columns=[f'tfidf_{i}' for i in range(tfidf_matrix.shape[1])]
        )
        
        # Add text features
        text_features = []
        for text in texts:
            analysis = self.text_processor.process_bid_text(text)
            text_features.append({
                'sentiment_polarity': analysis['sentiment']['polarity'],
                'sentiment_subjectivity': analysis['sentiment']['subjectivity'],
                'word_count': analysis['complexity']['word_count'],
                'sentence_count': analysis['complexity']['sentence_count'],
                'lexical_diversity': analysis['complexity']['lexical_diversity'],
                'price_mentioned': analysis['price_info']['price_mentioned'],
                'has_certifications': analysis['service_details']['certifications'],
                'has_insurance': analysis['service_details']['insurance'],
                'has_warranty': analysis['service_details']['warranty'],
                'availability_24_7': analysis['service_details']['availability_24_7']
            })
        
        text_features_df = pd.DataFrame(text_features)
        
        return pd.concat([tfidf_features, text_features_df], axis=1)

# Example usage
if __name__ == "__main__":
    # Load sample data
    from data_generator import TravelBidDataGenerator
    
    generator = TravelBidDataGenerator()
    sample_bids = generator.generate_sample_bids(50)
    
    # Analyze bids
    analyzer = BidAnalyzer()
    processed_bids = analyzer.analyze_bids(sample_bids)
    
    print("Processed bids analysis:")
    print(processed_bids[['bid_id', 'supplier_name', 'sentiment_score', 'extracted_price']].head())
    
    # Create text features
    text_features = analyzer.create_text_features(pd.DataFrame(sample_bids))
    print(f"\nText features shape: {text_features.shape}")
    print("Text features columns:", list(text_features.columns[:10]))

