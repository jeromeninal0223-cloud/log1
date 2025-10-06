<?php

use Illuminate\Support\Facades\Artisan;

Artisan::call('tinker', [], [
    '--execute' => '
        use App\Models\Asset;
        
        echo "Checking for assets with missing image files...\n";
        
        $assets = Asset::whereNotNull("image_path")->get();
        $fixed = 0;
        
        foreach($assets as $asset) {
            $fullPath = storage_path("app/public/" . $asset->image_path);
            if (!file_exists($fullPath)) {
                echo "Fixing asset ID {$asset->id} - removing orphaned image path: {$asset->image_path}\n";
                $asset->update(["image_path" => null]);
                $fixed++;
            } else {
                echo "Asset ID {$asset->id} - image file exists: {$asset->image_path}\n";
            }
        }
        
        echo "Fixed {$fixed} assets with missing image files.\n";
    '
]);
