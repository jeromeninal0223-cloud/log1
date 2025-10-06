<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find contract CON-2025-0004
$contract = \App\Models\Contract::where('contract_number', 'CON-2025-0004')->first();

if (!$contract) {
    echo "Contract CON-2025-0004 not found.\n";
    exit(1);
}

echo "Found contract: {$contract->contract_number}\n";
echo "Vendor signed: " . ($contract->vendor_signed_at ? 'Yes (' . $contract->vendor_signed_at . ')' : 'No') . "\n";
echo "Procurement signed: " . ($contract->procurement_signed_at ? 'Yes (' . $contract->procurement_signed_at . ')' : 'No') . "\n";

// Regenerate contract terms with current signature data
$contract->generateTerms();

echo "Contract terms regenerated successfully!\n";
echo "The signature sections should now display properly in the contract document.\n";
