<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Update all draft contracts to pending_vendor_signature
$updated = \App\Models\Contract::where('workflow_status', 'draft')
    ->update(['workflow_status' => 'pending_vendor_signature']);

echo "Updated {$updated} contracts from 'draft' to 'pending_vendor_signature' status.\n";
echo "Sign buttons should now appear in the vendor portal!\n";

// Show current contract statuses
$contracts = \App\Models\Contract::select('id', 'contract_number', 'workflow_status')->get();
echo "\nCurrent contract statuses:\n";
foreach ($contracts as $contract) {
    echo "Contract {$contract->contract_number}: {$contract->workflow_status}\n";
}
