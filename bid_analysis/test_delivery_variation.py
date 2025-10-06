#!/usr/bin/env python3
"""
Quick test to check if delivery days are now showing variation
"""

import sys
import os
sys.path.append(os.path.dirname(os.path.abspath(__file__)))

from database_connector import DatabaseConnector
from api_server import calculate_delivery_score_from_completion_date
import logging

logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

def test_delivery_variation():
    """Test if delivery days are now varied instead of all being 7"""
    
    print("🧪 Testing Delivery Day Variation")
    print("=" * 50)
    
    # Test with database connection
    db = DatabaseConnector()
    if db.connect():
        submissions = db.get_vendor_submissions()
        print(f"📊 Found {len(submissions)} submissions")
        
        delivery_days_found = []
        for submission in submissions[:10]:  # Check first 10
            vendor_name = submission.get('supplier_name', 'Unknown')
            proposed_days = submission.get('proposed_delivery_days')
            delivery_days_found.append(proposed_days)
            print(f"   {vendor_name}: {proposed_days} days")
        
        unique_days = set(delivery_days_found)
        print(f"\n📈 Unique delivery day values: {sorted(unique_days)}")
        
        if len(unique_days) > 1:
            print("✅ SUCCESS: Delivery days are now varied!")
        else:
            print("❌ ISSUE: All delivery days are still the same")
    else:
        print("❌ Could not connect to database")
    
    # Test calculation function directly
    print("\n🔧 Testing Calculation Function:")
    test_vendors = ['Test Vendor A', 'Test Vendor B', 'Test Vendor C']
    
    for vendor in test_vendors:
        result = calculate_delivery_score_from_completion_date(
            vendor_name=vendor,
            service_type='Travel Services'
        )
        print(f"   {vendor}: {result['delivery_days']} days, Score: {result['score']:.1f}")

if __name__ == "__main__":
    test_delivery_variation()
