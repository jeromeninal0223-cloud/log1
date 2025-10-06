#!/usr/bin/env python3
"""
Test script to verify database connection and retrieve real vendor data
"""

from database_connector import db_connector
import json

def test_database_connection():
    """Test the database connection and retrieve real data"""
    print("Testing database connection...")
    
    # Test connection
    if db_connector.connect():
        print("✅ Database connection successful!")
        
        # Test getting real vendors
        print("\nRetrieving real vendors...")
        vendors = db_connector.get_real_vendors()
        
        if vendors:
            print(f"✅ Found {len(vendors)} real vendors:")
            for vendor in vendors[:5]:  # Show first 5
                exp_years = vendor.get('experience_years', 'N/A')
                print(f"  - {vendor['supplier_name']}: {exp_years} years experience")
        else:
            print("⚠️ No vendors found in database")
        
        # Test getting vendor submissions
        print("\nRetrieving vendor submissions...")
        submissions = db_connector.get_vendor_submissions()
        
        if submissions:
            print(f"✅ Found {len(submissions)} submissions:")
            for sub in submissions[:3]:  # Show first 3
                print(f"  - {sub['supplier_name']}: {sub['experience_years']} years, ₱{sub['bid_amount']:,.2f}")
        else:
            print("⚠️ No submissions found")
        
        # Test specific vendor experience
        if vendors:
            test_vendor = vendors[0]['supplier_name']
            print(f"\nTesting specific vendor experience for '{test_vendor}'...")
            exp = db_connector.get_vendor_experience_years(test_vendor)
            print(f"✅ Experience: {exp} years" if exp else "⚠️ No experience data found")
        
        db_connector.disconnect()
        
    else:
        print("❌ Database connection failed!")
        print("Please check your database configuration:")
        print(f"  Host: {db_connector.db_config['host']}")
        print(f"  Port: {db_connector.db_config['port']}")
        print(f"  Database: {db_connector.db_config['database']}")
        print(f"  User: {db_connector.db_config['user']}")

if __name__ == "__main__":
    test_database_connection()
