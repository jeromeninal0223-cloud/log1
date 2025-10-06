<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Contract Status Check ===\n";

$contracts = \App\Models\Contract::select('id', 'workflow_status', 'vendor_id', 'contract_number', 'title')
    ->with('vendor:id,name')
    ->get();

if ($contracts->count() > 0) {
    foreach ($contracts as $contract) {
        echo "ID: {$contract->id}\n";
        echo "Number: {$contract->contract_number}\n";
        echo "Title: {$contract->title}\n";
        echo "Status: {$contract->workflow_status}\n";
        echo "Vendor: " . ($contract->vendor ? $contract->vendor->name : 'N/A') . "\n";
        echo "---\n";
    }
    
    // Update first contract to pending_vendor_signature for testing
    $firstContract = $contracts->first();
    if ($firstContract && $firstContract->workflow_status !== 'pending_vendor_signature') {
        $firstContract->update(['workflow_status' => 'pending_vendor_signature']);
        echo "\nUpdated Contract {$firstContract->id} to 'pending_vendor_signature' status for testing.\n";
        echo "Now the sign button should appear in the vendor portal!\n";
    }
} else {
    echo "No contracts found in database.\n";
}
