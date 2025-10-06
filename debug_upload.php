<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check database records
echo "=== Database Records ===\n";
$assets = App\Models\Asset::select('id', 'plate_number', 'image_path')->get();
foreach($assets as $asset) {
    echo "ID: {$asset->id}, Plate: {$asset->plate_number}, Image Path: " . ($asset->image_path ?: 'NULL') . "\n";
}

// Check storage directories
echo "\n=== Storage Directory Check ===\n";
$storagePath = storage_path('app/public/vehicles');
echo "Storage path: $storagePath\n";
echo "Directory exists: " . (is_dir($storagePath) ? 'YES' : 'NO') . "\n";
echo "Directory writable: " . (is_writable($storagePath) ? 'YES' : 'NO') . "\n";

if (is_dir($storagePath)) {
    $files = scandir($storagePath);
    $files = array_filter($files, function($file) { return $file !== '.' && $file !== '..'; });
    echo "Files in directory: " . count($files) . "\n";
    foreach($files as $file) {
        echo "  - $file\n";
    }
}

// Check public storage symlink
echo "\n=== Public Storage Symlink ===\n";
$publicStoragePath = public_path('storage');
echo "Public storage path: $publicStoragePath\n";
echo "Symlink exists: " . (file_exists($publicStoragePath) ? 'YES' : 'NO') . "\n";
echo "Is link: " . (is_link($publicStoragePath) ? 'YES' : 'NO') . "\n";

if (file_exists($publicStoragePath)) {
    $vehiclesPath = $publicStoragePath . '/vehicles';
    echo "Vehicles path accessible: " . (is_dir($vehiclesPath) ? 'YES' : 'NO') . "\n";
}
