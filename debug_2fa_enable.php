<?php

require_once 'bootstrap/app.php';

use App\Models\Vendor;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "=== 2FA Debug Script ===\n";
    
    // Test database connection
    DB::connection()->getPdo();
    echo "✓ Database connected\n";
    
    // Check if vendors table exists
    if (!Schema::hasTable('vendors')) {
        echo "✗ Vendors table does not exist\n";
        exit;
    }
    echo "✓ Vendors table exists\n";
    
    // Check for 2FA columns
    $columns = ['two_factor_enabled', 'two_factor_secret', 'two_factor_backup_codes', 'two_factor_confirmed_at'];
    $missingColumns = [];
    
    foreach ($columns as $column) {
        if (Schema::hasColumn('vendors', $column)) {
            echo "✓ Column '{$column}' exists\n";
        } else {
            echo "✗ Column '{$column}' missing\n";
            $missingColumns[] = $column;
        }
    }
    
    // Add missing columns
    if (!empty($missingColumns)) {
        echo "\nAdding missing columns...\n";
        
        Schema::table('vendors', function ($table) use ($missingColumns) {
            if (in_array('two_factor_enabled', $missingColumns)) {
                $table->boolean('two_factor_enabled')->default(false);
                echo "✓ Added two_factor_enabled\n";
            }
            if (in_array('two_factor_secret', $missingColumns)) {
                $table->string('two_factor_secret')->nullable();
                echo "✓ Added two_factor_secret\n";
            }
            if (in_array('two_factor_backup_codes', $missingColumns)) {
                $table->text('two_factor_backup_codes')->nullable();
                echo "✓ Added two_factor_backup_codes\n";
            }
            if (in_array('two_factor_confirmed_at', $missingColumns)) {
                $table->timestamp('two_factor_confirmed_at')->nullable();
                echo "✓ Added two_factor_confirmed_at\n";
            }
        });
    }
    
    // Test vendor model
    $vendor = Vendor::first();
    if ($vendor) {
        echo "✓ Found vendor: " . $vendor->email . "\n";
        
        // Test setting 2FA properties
        $vendor->two_factor_enabled = true;
        $vendor->two_factor_secret = 'test_secret';
        $vendor->two_factor_backup_codes = json_encode(['code1', 'code2']);
        $vendor->two_factor_confirmed_at = now();
        
        if ($vendor->save()) {
            echo "✓ Successfully saved 2FA data to vendor\n";
            
            // Reset the values
            $vendor->two_factor_enabled = false;
            $vendor->two_factor_secret = null;
            $vendor->two_factor_backup_codes = null;
            $vendor->two_factor_confirmed_at = null;
            $vendor->save();
            echo "✓ Reset 2FA data\n";
        } else {
            echo "✗ Failed to save 2FA data\n";
        }
    } else {
        echo "✗ No vendors found in database\n";
    }
    
    echo "\n=== Debug Complete ===\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}
