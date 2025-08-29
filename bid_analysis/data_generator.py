import pandas as pd
import numpy as np
import json
import random
from datetime import datetime, timedelta
from faker import Faker

class TravelBidDataGenerator:
    def __init__(self):
        self.fake = Faker()
        
        # Service categories
        self.service_categories = [
            'Transportation', 'Accommodation', 'Tour Guide', 
            'Equipment Rental', 'Catering', 'Insurance'
        ]
        
        # Supplier types
        self.supplier_types = [
            'Hotel Chain', 'Local Hotel', 'Transport Company', 
            'Tour Operator', 'Equipment Supplier', 'Catering Service'
        ]
        
        # Quality indicators
        self.quality_indicators = [
            'Excellent', 'Good', 'Average', 'Below Average'
        ]
        
    def generate_bid_text(self, service_type, price, supplier_name):
        """Generate realistic bid text based on service type and parameters"""
        
        templates = {
            'Transportation': [
                f"We, {supplier_name}, are pleased to submit our bid for transportation services. "
                f"Our fleet includes modern, well-maintained vehicles with professional drivers. "
                f"Price: ${price:,.2f} for the complete service package. "
                f"We guarantee punctuality and safety standards compliance.",
                
                f"{supplier_name} offers premium transportation solutions with {random.randint(5, 20)} years of experience. "
                f"Comprehensive insurance coverage included. Total cost: ${price:,.2f}. "
                f"Flexible scheduling and 24/7 support available."
            ],
            
            'Accommodation': [
                f"{supplier_name} is delighted to offer accommodation services at ${price:,.2f}. "
                f"Our facilities feature modern amenities, free WiFi, and excellent location. "
                f"Group discounts available for bookings over 10 rooms.",
                
                f"Premium accommodation package from {supplier_name} - ${price:,.2f}. "
                f"Includes breakfast, airport transfers, and concierge services. "
                f"4-star quality with competitive pricing."
            ],
            
            'Tour Guide': [
                f"Professional tour guide services by {supplier_name} - ${price:,.2f}. "
                f"Certified guides with {random.randint(3, 15)} years experience. "
                f"Multilingual support and custom itinerary planning included.",
                
                f"{supplier_name} provides expert tour guidance for ${price:,.2f}. "
                f"Local knowledge, cultural insights, and flexible scheduling. "
                f"All guides are licensed and insured."
            ]
        }
        
        return random.choice(templates.get(service_type, [
            f"{supplier_name} offers {service_type} services for ${price:,.2f}. "
            f"Quality service with competitive pricing."
        ]))
    
    def generate_sample_bids(self, num_bids=100):
        """Generate sample bid data"""
        
        bids = []
        
        for i in range(num_bids):
            service_type = random.choice(self.service_categories)
            supplier_type = random.choice(self.supplier_types)
            supplier_name = self.fake.company()
            
            # Generate realistic pricing based on service type
            base_prices = {
                'Transportation': (500, 5000),
                'Accommodation': (1000, 15000),
                'Tour Guide': (200, 2000),
                'Equipment Rental': (300, 3000),
                'Catering': (800, 8000),
                'Insurance': (100, 1000)
            }
            
            min_price, max_price = base_prices.get(service_type, (500, 5000))
            price = random.uniform(min_price, max_price)
            
            # Generate quality score (0-100)
            quality_score = random.normal(75, 15)
            quality_score = max(0, min(100, quality_score))
            
            # Generate delivery time (days)
            delivery_time = random.randint(1, 30)
            
            # Generate experience years
            experience_years = random.randint(1, 25)
            
            # Generate bid text
            bid_text = self.generate_bid_text(service_type, price, supplier_name)
            
            bid = {
                'bid_id': f"BID-{str(i+1).zfill(4)}",
                'supplier_name': supplier_name,
                'supplier_type': supplier_type,
                'service_type': service_type,
                'bid_amount': round(price, 2),
                'quality_score': round(quality_score, 2),
                'delivery_time_days': delivery_time,
                'experience_years': experience_years,
                'bid_text': bid_text,
                'submission_date': self.fake.date_between(start_date='-30d', end_date='today').isoformat(),
                'certifications': random.choice([True, False]),
                'insurance_coverage': random.choice([True, False]),
                'payment_terms': random.choice(['Net 30', 'Net 15', 'Immediate', 'Net 45']),
                'warranty_months': random.randint(0, 24),
                'customer_rating': round(random.uniform(3.0, 5.0), 1),
                'previous_projects': random.randint(0, 50),
                'location_coverage': random.choice(['Local', 'Regional', 'National', 'International']),
                'availability_24_7': random.choice([True, False]),
                'sustainability_certified': random.choice([True, False])
            }
            
            bids.append(bid)
        
        return bids
    
    def save_to_csv(self, bids, filename='travel_bids_dataset.csv'):
        """Save bids to CSV file"""
        df = pd.DataFrame(bids)
        df.to_csv(filename, index=False)
        print(f"Dataset saved to {filename}")
        return df
    
    def save_to_json(self, bids, filename='travel_bids_dataset.json'):
        """Save bids to JSON file"""
        with open(filename, 'w') as f:
            json.dump(bids, f, indent=2)
        print(f"Dataset saved to {filename}")

# Usage example
if __name__ == "__main__":
    generator = TravelBidDataGenerator()
    bids = generator.generate_sample_bids(200)
    
    # Save to different formats
    df = generator.save_to_csv(bids)
    generator.save_to_json(bids)
    
    print(f"Generated {len(bids)} sample bids")
    print("\nSample bid:")
    print(json.dumps(bids[0], indent=2))

