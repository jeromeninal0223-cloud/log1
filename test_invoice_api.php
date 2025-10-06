<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Invoice API ===\n";

// Test 1: Check if invoices exist
$invoiceCount = App\Models\Invoice::count();
echo "Total invoices in database: {$invoiceCount}\n";

if ($invoiceCount > 0) {
    echo "\nInvoices in database:\n";
    App\Models\Invoice::all()->each(function($invoice) {
        echo "- {$invoice->invoice_no} | Vendor: {$invoice->vendor_name} | PO: " . ($invoice->po_number ?? 'None') . " | Amount: \${$invoice->amount}\n";
    });
}

// Test 2: Check vendors
$vendorCount = App\Models\Vendor::count();
echo "\nTotal vendors in database: {$vendorCount}\n";

if ($vendorCount > 0) {
    echo "\nVendors in database:\n";
    App\Models\Vendor::all()->each(function($vendor) {
        echo "- ID: {$vendor->id} | Company: {$vendor->company_name} | Name: {$vendor->name}\n";
    });
}

// Test 3: Test the API logic directly
echo "\n=== Testing API Logic ===\n";
$supplierName = 'Smart Supplier Inc.';
echo "Testing with supplier: {$supplierName}\n";

// Find vendor
$vendor = App\Models\Vendor::where('company_name', $supplierName)->first();
if (!$vendor) {
    $vendor = App\Models\Vendor::where('company_name', 'LIKE', "%{$supplierName}%")
                              ->orWhere('name', 'LIKE', "%{$supplierName}%")
                              ->first();
}

if ($vendor) {
    echo "Vendor found: ID {$vendor->id}, Company: {$vendor->company_name}\n";
    
    // Find invoices
    $invoices = App\Models\Invoice::where(function($query) use ($supplierName, $vendor) {
        $query->where('vendor_name', $supplierName)
              ->orWhere('vendor_name', 'LIKE', "%{$supplierName}%")
              ->orWhere('vendor_id', $vendor->id);
    })->get();
    
    echo "Invoices found: {$invoices->count()}\n";
    $invoices->each(function($invoice) {
        echo "- {$invoice->invoice_no} | Vendor: {$invoice->vendor_name} | PO: " . ($invoice->po_number ?? 'None') . "\n";
    });
    
    // Find purchase orders
    $purchaseOrders = App\Models\PurchaseOrder::where('vendor_id', $vendor->id)->get();
    echo "Purchase orders for vendor: {$purchaseOrders->count()}\n";
    $purchaseOrders->each(function($po) {
        echo "- {$po->po_number} | Status: {$po->status} | Title: {$po->title}\n";
    });
    
} else {
    echo "Vendor not found for: {$supplierName}\n";
}

echo "\n=== Test Complete ===\n";
