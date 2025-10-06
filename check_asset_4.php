<?php

require_once 'vendor/autoload.php';

use App\Models\Asset;

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    echo "Checking for asset ID 4...\n";
    
    $asset = Asset::find(4);
    
    if ($asset) {
        echo "Asset ID 4 EXISTS:\n";
        echo "- ID: " . $asset->id . "\n";
        echo "- Plate Number: " . $asset->plate_number . "\n";
        echo "- Vehicle Type: " . $asset->vehicle_type . "\n";
        echo "- Status: " . $asset->status . "\n";
        echo "- Created At: " . $asset->created_at . "\n";
        echo "- Updated At: " . $asset->updated_at . "\n";
    } else {
        echo "Asset ID 4 does NOT exist in the database.\n";
    }
    
    // Also check all assets
    $allAssets = Asset::all();
    echo "\nAll assets in database:\n";
    foreach ($allAssets as $a) {
        echo "- ID: {$a->id}, Plate: {$a->plate_number}, Type: {$a->vehicle_type}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
