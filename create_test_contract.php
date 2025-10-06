<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Contract;
use App\Models\Vendor;
use App\Models\Bid;

// Find or create a test vendor
$vendor = Vendor::first();
if (!$vendor) {
    $vendor = Vendor::create([
        'name' => 'Test Vendor',
        'email' => 'test@vendor.com',
        'password' => bcrypt('password'),
        'company_name' => 'Test Company Ltd',
        'business_type' => 'Technology',
        'phone' => '+1234567890',
        'address' => '123 Test Street, Test City',
        'status' => 'Active',
        'documents_verified' => true,
    ]);
    echo "Created test vendor with ID: {$vendor->id}\n";
} else {
    echo "Using existing vendor with ID: {$vendor->id}\n";
}

// Create a test bid
$bid = Bid::create([
    'vendor_id' => $vendor->id,
    'opportunity_id' => 1,
    'title' => 'Test Logistics Service',
    'description' => 'Providing comprehensive logistics solutions',
    'category' => 'Logistics & Transportation',
    'amount' => 50000.00,
    'status' => 'Won',
    'completion_date' => now()->addMonths(6),
    'submitted_at' => now()->subDays(10),
]);

echo "Created test bid with ID: {$bid->id}\n";

// Create a test contract
$contract = Contract::create([
    'contract_number' => 'CON-2024-TEST-001',
    'bid_id' => $bid->id,
    'vendor_id' => $vendor->id,
    'title' => 'Logistics Service Agreement',
    'description' => 'Contract for comprehensive logistics and transportation services',
    'terms' => '<h4>Terms and Conditions</h4>
    <p><strong>1. Service Scope:</strong> The vendor shall provide comprehensive logistics and transportation services as outlined in the bid proposal.</p>
    <p><strong>2. Payment Terms:</strong> Payment shall be made within 30 days of invoice receipt.</p>
    <p><strong>3. Duration:</strong> This contract is valid for 12 months from the start date.</p>
    <p><strong>4. Performance Standards:</strong> All services must meet the quality standards specified in the original RFQ.</p>
    <p><strong>5. Termination:</strong> Either party may terminate this contract with 30 days written notice.</p>',
    'value' => 50000.00,
    'negotiated_value' => 47500.00,
    'status' => 'Active',
    'workflow_status' => 'pending_vendor_signature',
    'start_date' => now(),
    'end_date' => now()->addYear(),
    'procurement_officer_id' => 1,
]);

echo "Created test contract with ID: {$contract->id}\n";
echo "Contract Number: {$contract->contract_number}\n";
echo "Vendor ID: {$contract->vendor_id}\n";
echo "Start Date: {$contract->start_date}\n";
echo "End Date: {$contract->end_date}\n";
echo "Terms: " . (strlen($contract->terms) > 50 ? substr($contract->terms, 0, 50) . '...' : $contract->terms) . "\n";

echo "\nTest contract created successfully!\n";
echo "You can now test the contract details view in the vendor portal.\n";
