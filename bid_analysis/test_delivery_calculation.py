#!/usr/bin/env python3
"""
Test script to verify delivery score calculation with real completion dates
"""

import requests
import json
from datetime import datetime

def test_delivery_calculation():
    """Test the delivery score calculation endpoint"""
    
    # Test data - replace with actual vendor names from your database
    test_vendors = [
        'TechSolutions Corp',
        'Global Services Ltd', 
        'Premier Contractors Inc',
        'Excellence Partners'
    ]
    
    api_url = 'http://localhost:5000/calculate_delivery_score'
    
    print("Testing Delivery Score Calculation")
    print("=" * 50)
    
    for vendor in test_vendors:
        print(f"\nTesting vendor: {vendor}")
        
        # Test data
        test_data = {
            'vendor_name': vendor,
            'service_type': 'Travel Services',
            'bid_amount': 500000,
            'start_date': datetime.now().isoformat()
        }
        
        try:
            response = requests.post(api_url, json=test_data, timeout=10)
            
            if response.status_code == 200:
                result = response.json()
                
                if result['success']:
                    delivery_info = result['delivery_info']
                    print(f"  ✅ Success!")
                    print(f"  📅 Completion Date: {delivery_info['completion_date']}")
                    print(f"  📊 Delivery Days: {delivery_info['delivery_days']}")
                    print(f"  🎯 Score: {delivery_info['score']}/10")
                    print(f"  📈 Performance: {delivery_info['performance_rating']}")
                    print(f"  📋 Data Source: {delivery_info['data_source']}")
                    
                    if delivery_info['data_source'] == 'database':
                        print(f"  ⚡ Using REAL completion date from database!")
                    else:
                        print(f"  🔄 Using calculated completion date (fallback)")
                        
                else:
                    print(f"  ❌ API Error: {result.get('error', 'Unknown error')}")
            else:
                print(f"  ❌ HTTP Error: {response.status_code}")
                print(f"  Response: {response.text}")
                
        except requests.exceptions.RequestException as e:
            print(f"  ❌ Connection Error: {e}")
            print(f"  💡 Make sure the Python API server is running on localhost:5000")

def test_database_connection():
    """Test if we can connect to the database and get real completion dates"""
    try:
        from database_connector import DatabaseConnector
        
        print("\nTesting Database Connection")
        print("=" * 30)
        
        db = DatabaseConnector()
        if db.connect():
            print("✅ Database connection successful!")
            
            # Get vendor submissions
            submissions = db.get_vendor_submissions()
            print(f"📊 Found {len(submissions)} vendor submissions")
            
            # Show some examples with completion dates
            completion_count = 0
            for submission in submissions[:5]:  # Show first 5
                if submission.get('real_completion_date'):
                    completion_count += 1
                    print(f"  📅 {submission['supplier_name']}: {submission['real_completion_date']} ({submission.get('actual_delivery_days', 'N/A')} days)")
            
            print(f"✅ Found {completion_count} submissions with real completion dates")
            
            if completion_count == 0:
                print("⚠️  No completion dates found in database!")
                print("💡 Make sure your bids table has completion_date values")
                
        else:
            print("❌ Database connection failed!")
            print("💡 Check your database configuration in .env file")
            
    except ImportError as e:
        print(f"❌ Import Error: {e}")
    except Exception as e:
        print(f"❌ Database Error: {e}")

if __name__ == '__main__':
    print("🚀 Delivery Score Calculation Test")
    print("=" * 60)
    
    # Test database connection first
    test_database_connection()
    
    # Test API endpoints
    test_delivery_calculation()
    
    print("\n" + "=" * 60)
    print("✅ Test completed!")
    print("💡 Check the console output above for any issues")
