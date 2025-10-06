<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Asset;

echo "=== Asset Image Debug Report ===\n\n";

$assets = Asset::all();

if ($assets->isEmpty()) {
    echo "No assets found in database.\n";
    exit;
}

foreach ($assets as $asset) {
    echo "Asset ID: {$asset->id}\n";
    echo "Plate Number: {$asset->plate_number}\n";
    echo "Image Path: " . ($asset->image_path ?? 'NULL') . "\n";
    
    if ($asset->image_path) {
        $fullPath = storage_path('app/public/' . $asset->image_path);
        $exists = file_exists($fullPath);
        echo "Full Path: {$fullPath}\n";
        echo "File Exists: " . ($exists ? 'YES' : 'NO') . "\n";
        
        if (!$exists) {
            echo "*** ISSUE: Image path set but file missing! ***\n";
        }
    } else {
        echo "No image path set (this is normal)\n";
    }
    
    echo "---\n";
}

echo "\n=== Storage Directory Contents ===\n";
$vehiclesDir = storage_path('app/public/vehicles');
if (is_dir($vehiclesDir)) {
    $files = scandir($vehiclesDir);
    $files = array_diff($files, ['.', '..']);
    
    if (empty($files)) {
        echo "Vehicles directory is empty\n";
    } else {
        foreach ($files as $file) {
            echo "Found file: {$file}\n";
        }
    }
} else {
    echo "Vehicles directory does not exist\n";
}
