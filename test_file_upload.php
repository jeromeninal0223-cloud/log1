<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test file upload simulation
echo "=== File Upload Debug Test ===\n";

// Check storage configuration
echo "Storage disk 'public' path: " . storage_path('app/public') . "\n";
echo "Vehicles directory: " . storage_path('app/public/vehicles') . "\n";

// Check if directories exist
$publicPath = storage_path('app/public');
$vehiclesPath = storage_path('app/public/vehicles');

echo "Public storage exists: " . (is_dir($publicPath) ? 'YES' : 'NO') . "\n";
echo "Vehicles directory exists: " . (is_dir($vehiclesPath) ? 'YES' : 'NO') . "\n";

// Create vehicles directory if it doesn't exist
if (!is_dir($vehiclesPath)) {
    echo "Creating vehicles directory...\n";
    mkdir($vehiclesPath, 0755, true);
    echo "Vehicles directory created: " . (is_dir($vehiclesPath) ? 'YES' : 'NO') . "\n";
}

echo "Vehicles directory writable: " . (is_writable($vehiclesPath) ? 'YES' : 'NO') . "\n";

// Test file creation
$testFile = $vehiclesPath . '/test_' . time() . '.txt';
echo "Testing file creation at: $testFile\n";

if (file_put_contents($testFile, 'Test content')) {
    echo "✓ File creation successful\n";
    unlink($testFile); // Clean up
} else {
    echo "✗ File creation failed\n";
}

// Check Laravel Storage facade
echo "\n=== Laravel Storage Test ===\n";
try {
    $disk = \Illuminate\Support\Facades\Storage::disk('public');
    echo "Storage disk accessible: YES\n";
    echo "Storage root path: " . $disk->path('') . "\n";
    
    // Test storage write
    $testContent = 'Test storage content';
    $testPath = 'vehicles/test_storage_' . time() . '.txt';
    
    if ($disk->put($testPath, $testContent)) {
        echo "✓ Storage write successful\n";
        echo "File exists: " . ($disk->exists($testPath) ? 'YES' : 'NO') . "\n";
        $disk->delete($testPath); // Clean up
    } else {
        echo "✗ Storage write failed\n";
    }
} catch (Exception $e) {
    echo "Storage error: " . $e->getMessage() . "\n";
}

echo "\n=== Current Assets with Images ===\n";
$assets = App\Models\Asset::whereNotNull('image_path')->get(['id', 'plate_number', 'image_path']);
foreach($assets as $asset) {
    echo "Asset {$asset->id} ({$asset->plate_number}): {$asset->image_path}\n";
    $fullPath = storage_path('app/public/' . $asset->image_path);
    echo "  File exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
}
