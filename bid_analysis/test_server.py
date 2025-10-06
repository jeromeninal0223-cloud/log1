#!/usr/bin/env python3
"""
Simple test script to verify API server functionality
"""

import requests
import json
import time

def test_endpoints():
    base_url = "http://localhost:5000"
    
    endpoints = [
        ("/health", "GET"),
        ("/confidence_level", "GET"),
        ("/vendor_submissions", "GET"),
        ("/model_performance", "GET")
    ]
    
    print("Testing API endpoints...")
    
    for endpoint, method in endpoints:
        try:
            if method == "GET":
                response = requests.get(f"{base_url}{endpoint}", timeout=5)
            
            print(f"✅ {endpoint}: {response.status_code}")
            
            if endpoint == "/confidence_level":
                data = response.json()
                print(f"   Confidence: {data.get('confidence_percentage', 'N/A')}%")
                
        except requests.exceptions.ConnectionError:
            print(f"❌ {endpoint}: Connection refused (server not running)")
        except Exception as e:
            print(f"❌ {endpoint}: {str(e)}")
    
    # Test POST endpoint
    try:
        sample_bids = [
            {
                "supplier_name": "Test Supplier",
                "bid_amount": 25000,
                "quality_score": 85,
                "delivery_time_days": 7,
                "experience_years": 10
            }
        ]
        
        response = requests.post(f"{base_url}/explain_scoring", 
                               json={"bids": sample_bids}, 
                               timeout=5)
        print(f"✅ /explain_scoring (POST): {response.status_code}")
        
    except requests.exceptions.ConnectionError:
        print(f"❌ /explain_scoring: Connection refused (server not running)")
    except Exception as e:
        print(f"❌ /explain_scoring: {str(e)}")

if __name__ == "__main__":
    test_endpoints()
