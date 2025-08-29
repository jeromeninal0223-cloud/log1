#!/usr/bin/env python3
"""
Test script for the AI-powered bid analysis system
"""

import requests
import json
import time
from datetime import datetime

# Configuration
API_BASE_URL = "http://localhost:5000"
TEST_BIDS = [
    {
        "bid_id": "TEST-001",
        "supplier_name": "Premium Transport Co.",
        "service_type": "Transportation",
        "bid_amount": 2500.00,
        "quality_score": 85,
        "delivery_time_days": 7,
        "experience_years": 10,
        "customer_rating": 4.5,
        "certifications": True,
        "insurance_coverage": True,
        "bid_text": "We offer premium transportation services with 10 years of experience. Our fleet includes luxury vehicles with full insurance coverage. We guarantee on-time delivery and professional service."
    },
    {
        "bid_id": "TEST-002",
        "supplier_name": "Budget Travel Solutions",
        "service_type": "Transportation",
        "bid_amount": 1800.00,
        "quality_score": 70,
        "delivery_time_days": 10,
        "experience_years": 5,
        "customer_rating": 3.8,
        "certifications": False,
        "insurance_coverage": True,
        "bid_text": "Affordable transportation services with basic insurance. We have 5 years of experience in the industry."
    },
    {
        "bid_id": "TEST-003",
        "supplier_name": "Elite Travel Services",
        "service_type": "Transportation",
        "bid_amount": 3200.00,
        "quality_score": 95,
        "delivery_time_days": 5,
        "experience_years": 15,
        "customer_rating": 4.9,
        "certifications": True,
        "insurance_coverage": True,
        "bid_text": "Elite transportation services with 15 years of experience. We provide luxury vehicles, certified drivers, comprehensive insurance, and guaranteed satisfaction. Our premium service includes 24/7 support."
    }
]

def test_health_check():
    """Test the health check endpoint"""
    print("🔍 Testing health check...")
    try:
        response = requests.get(f"{API_BASE_URL}/health", timeout=10)
        if response.status_code == 200:
            data = response.json()
            print(f"✅ Health check passed: {data['status']}")
            print(f"   Models loaded: {data.get('models_loaded', False)}")
            return True
        else:
            print(f"❌ Health check failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Health check error: {str(e)}")
        return False

def test_analyze_bids():
    """Test bid analysis endpoint"""
    print("\n🔍 Testing bid analysis...")
    try:
        response = requests.post(
            f"{API_BASE_URL}/analyze_bids",
            json={"bids": TEST_BIDS},
            timeout=30
        )
        
        if response.status_code == 200:
            data = response.json()
            print("✅ Bid analysis completed successfully")
            print(f"   Total bids analyzed: {data['analysis_summary']['total_bids']}")
            print(f"   Average bid amount: ${data['analysis_summary']['avg_bid_amount']:,.2f}")
            print(f"   Average quality score: {data['analysis_summary']['avg_quality_score']:.1f}")
            
            # Show top recommendations
            if 'ai_recommendations' in data:
                print("\n🏆 Top AI Recommendations:")
                for rec in data['ai_recommendations'][:3]:
                    print(f"   {rec['rank']}. {rec['supplier_name']} - ${rec['bid_amount']:,.2f}")
                    print(f"      Winning Probability: {rec['winning_probability']:.1%}")
                    print(f"      Strengths: {', '.join(rec['strengths'][:2])}")
            
            return True
        else:
            print(f"❌ Bid analysis failed: {response.status_code}")
            print(f"   Response: {response.text}")
            return False
    except Exception as e:
        print(f"❌ Bid analysis error: {str(e)}")
        return False

def test_predict_winning_probability():
    """Test winning probability prediction"""
    print("\n🔍 Testing winning probability prediction...")
    try:
        response = requests.post(
            f"{API_BASE_URL}/predict_winning_probability",
            json={"bids": TEST_BIDS},
            timeout=30
        )
        
        if response.status_code == 200:
            data = response.json()
            print("✅ Winning probability prediction completed")
            
            if 'predictions' in data:
                print("\n🎯 Winning Probability Rankings:")
                for pred in data['predictions']:
                    print(f"   {pred['rank']}. {pred['supplier_name']}: {pred['winning_probability']:.1%} ({pred['confidence_level']})")
            
            if 'top_contender' in data:
                top = data['top_contender']
                print(f"\n🥇 Top Contender: {top['supplier_name']} ({top['winning_probability']:.1%})")
            
            return True
        else:
            print(f"❌ Winning probability prediction failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Winning probability prediction error: {str(e)}")
        return False

def test_compare_bids():
    """Test bid comparison"""
    print("\n🔍 Testing bid comparison...")
    try:
        response = requests.post(
            f"{API_BASE_URL}/compare_bids",
            json={"bids": TEST_BIDS},
            timeout=30
        )
        
        if response.status_code == 200:
            data = response.json()
            print("✅ Bid comparison completed")
            
            if 'summary' in data:
                summary = data['summary']
                print(f"\n📊 Comparison Summary:")
                print(f"   Price range: ${summary['price_range']['min']:,.2f} - ${summary['price_range']['max']:,.2f}")
                print(f"   Quality range: {summary['quality_range']['min']} - {summary['quality_range']['max']}")
                print(f"   Winning probability range: {summary['winning_probability_range']['min']:.1%} - {summary['winning_probability_range']['max']:.1%}")
            
            return True
        else:
            print(f"❌ Bid comparison failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Bid comparison error: {str(e)}")
        return False

def test_single_bid_analysis():
    """Test single bid analysis"""
    print("\n🔍 Testing single bid analysis...")
    try:
        test_bid = TEST_BIDS[0]
        response = requests.post(
            f"{API_BASE_URL}/analyze_single_bid",
            json={
                "bid_text": test_bid["bid_text"],
                "supplier_name": test_bid["supplier_name"],
                "service_type": test_bid["service_type"],
                "bid_amount": test_bid["bid_amount"]
            },
            timeout=30
        )
        
        if response.status_code == 200:
            data = response.json()
            print("✅ Single bid analysis completed")
            
            if 'ai_predictions' in data:
                predictions = data['ai_predictions']
                print(f"\n🤖 AI Predictions for {test_bid['supplier_name']}:")
                print(f"   Winning Probability: {predictions['winning_probability']:.1%}")
                print(f"   Predicted Quality Score: {predictions['predicted_quality_score']:.1f}")
                print(f"   Supplier Classification: {predictions['supplier_classification']}")
                print(f"   Confidence Level: {predictions['confidence_level']}")
            
            return True
        else:
            print(f"❌ Single bid analysis failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Single bid analysis error: {str(e)}")
        return False

def test_model_performance():
    """Test model performance endpoint"""
    print("\n🔍 Testing model performance...")
    try:
        response = requests.get(f"{API_BASE_URL}/model_performance", timeout=10)
        
        if response.status_code == 200:
            data = response.json()
            print("✅ Model performance retrieved")
            
            if 'models_performance' in data:
                perf = data['models_performance']
                print(f"\n📈 Model Performance:")
                print(f"   Winning Predictor R²: {perf.get('winning_predictor_r2', 'N/A')}")
                print(f"   Quality Predictor R²: {perf.get('quality_predictor_r2', 'N/A')}")
                print(f"   Supplier Classifier Accuracy: {perf.get('supplier_classifier_accuracy', 'N/A')}")
            
            return True
        else:
            print(f"❌ Model performance check failed: {response.status_code}")
            return False
    except Exception as e:
        print(f"❌ Model performance check error: {str(e)}")
        return False

def main():
    """Run all tests"""
    print("🚀 Starting AI Bid Analysis System Tests")
    print("=" * 50)
    print(f"API Base URL: {API_BASE_URL}")
    print(f"Test Time: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    print("=" * 50)
    
    tests = [
        ("Health Check", test_health_check),
        ("Bid Analysis", test_analyze_bids),
        ("Winning Probability", test_predict_winning_probability),
        ("Bid Comparison", test_compare_bids),
        ("Single Bid Analysis", test_single_bid_analysis),
        ("Model Performance", test_model_performance),
    ]
    
    passed = 0
    total = len(tests)
    
    for test_name, test_func in tests:
        try:
            if test_func():
                passed += 1
            else:
                print(f"❌ {test_name} failed")
        except Exception as e:
            print(f"❌ {test_name} error: {str(e)}")
        
        time.sleep(1)  # Brief pause between tests
    
    print("\n" + "=" * 50)
    print(f"🎯 Test Results: {passed}/{total} tests passed")
    
    if passed == total:
        print("✅ All tests passed! The AI system is working correctly.")
    else:
        print("⚠️  Some tests failed. Please check the API server and try again.")
    
    print("=" * 50)

if __name__ == "__main__":
    main()
