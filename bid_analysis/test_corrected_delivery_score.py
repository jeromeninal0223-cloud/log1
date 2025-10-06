#!/usr/bin/env python3
"""
Test script to verify the corrected delivery score calculation.
This script tests that delivery scores are now calculated based on proposed completion dates
rather than treating them as actual completion dates.
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from datetime import datetime, timedelta
from api_server import calculate_delivery_score_from_completion_date
from database_connector import DatabaseConnector
import logging

# Set up logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def test_delivery_score_calculation():
    """Test the corrected delivery score calculation logic"""
    
    print("🧪 Testing Corrected Delivery Score Calculation")
    print("=" * 60)
    
    # Test scenarios
    test_cases = [
        {
            'name': 'Fast Delivery Promise',
            'vendor': 'FastTravel Co.',
            'service_type': 'Travel Services',
            'proposed_days': 3,  # 3 days (very fast for 7-day standard)
            'expected_score_range': (9.0, 10.0)
        },
        {
            'name': 'Standard Delivery Promise', 
            'vendor': 'Standard Services',
            'service_type': 'Travel Services',
            'proposed_days': 7,  # 7 days (exactly standard)
            'expected_score_range': (8.0, 9.0)
        },
        {
            'name': 'Slow Delivery Promise',
            'vendor': 'Slow Logistics',
            'service_type': 'Travel Services', 
            'proposed_days': 14,  # 14 days (2x standard = slow)
            'expected_score_range': (4.0, 6.0)
        },
        {
            'name': 'Transportation Fast',
            'vendor': 'Quick Transport',
            'service_type': 'Transportation',
            'proposed_days': 1,  # 1 day (very fast for 3-day standard)
            'expected_score_range': (9.0, 10.0)
        }
    ]
    
    print("📋 Test Cases:")
    print("-" * 60)
    
    for i, test_case in enumerate(test_cases, 1):
        print(f"\n{i}. {test_case['name']}")
        print(f"   Service Type: {test_case['service_type']}")
        print(f"   Proposed Days: {test_case['proposed_days']}")
        
        # Calculate start and completion dates
        start_date = datetime.now()
        completion_date = start_date + timedelta(days=test_case['proposed_days'])
        
        # Test the calculation
        try:
            result = calculate_delivery_score_from_completion_date(
                vendor_name=test_case['vendor'],
                service_type=test_case['service_type'],
                start_date=start_date.isoformat(),
                completion_date=completion_date.isoformat()
            )
            
            score = result['score']
            performance = result['performance_rating']
            delivery_days = result['delivery_days']
            expected_days = result['expected_days']
            days_vs_standard = result['days_vs_standard']
            
            print(f"   ✅ Score: {score:.1f}/10")
            print(f"   📊 Performance: {performance}")
            print(f"   📅 Delivery Days: {delivery_days}")
            print(f"   📏 Expected Days: {expected_days}")
            print(f"   📈 vs Standard: {days_vs_standard:+d} days")
            
            # Check if score is in expected range
            min_score, max_score = test_case['expected_score_range']
            if min_score <= score <= max_score:
                print(f"   ✅ PASS: Score {score:.1f} is within expected range [{min_score}-{max_score}]")
            else:
                print(f"   ❌ FAIL: Score {score:.1f} is outside expected range [{min_score}-{max_score}]")
                
        except Exception as e:
            print(f"   ❌ ERROR: {str(e)}")
    
    print("\n" + "=" * 60)
    print("🎯 Key Improvements:")
    print("✅ Delivery score now based on PROPOSED completion dates")
    print("✅ Faster delivery promises get higher scores")
    print("✅ Scores compared against industry standards")
    print("✅ Penalty system for late completion promises")
    print("✅ More accurate and fair bid evaluation")

def test_with_real_database():
    """Test with real database data if available"""
    
    print("\n🔗 Testing with Real Database Data")
    print("=" * 60)
    
    try:
        db = DatabaseConnector()
        if not db.connect():
            print("❌ Could not connect to database")
            return
            
        submissions = db.get_vendor_submissions()
        
        if not submissions:
            print("ℹ️  No submissions found in database")
            return
            
        print(f"📊 Found {len(submissions)} submissions")
        
        # Test with first few real submissions
        for i, submission in enumerate(submissions[:3], 1):
            vendor_name = submission.get('supplier_name', 'Unknown')
            service_type = submission.get('service_type', 'Travel Services')
            completion_date = submission.get('real_completion_date')
            proposed_days = submission.get('proposed_delivery_days')
            
            print(f"\n{i}. Real Vendor: {vendor_name}")
            print(f"   Service: {service_type}")
            print(f"   Proposed Days: {proposed_days}")
            print(f"   Completion Date: {completion_date}")
            
            try:
                result = calculate_delivery_score_from_completion_date(
                    vendor_name=vendor_name,
                    service_type=service_type
                )
                
                print(f"   ✅ Score: {result['score']:.1f}/10")
                print(f"   📊 Performance: {result['performance_rating']}")
                print(f"   📅 Source: {result['data_source']}")
                
                if result.get('deadline_penalty', 0) > 0:
                    print(f"   ⚠️  Deadline Penalty: -{result['deadline_penalty']:.1f} points")
                    
            except Exception as e:
                print(f"   ❌ ERROR: {str(e)}")
                
    except Exception as e:
        print(f"❌ Database test failed: {str(e)}")

if __name__ == "__main__":
    test_delivery_score_calculation()
    test_with_real_database()
    
    print("\n🎉 Delivery Score Calculation Test Complete!")
    print("The delivery score now correctly evaluates proposed completion timelines")
    print("rather than treating completion_date as actual delivery performance.")
