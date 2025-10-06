<?php

require_once 'bootstrap/app.php';

// Test 2FA database setup with correct connection details
$host = '127.0.0.1';
$port = '3307';
$dbname = 'logistics1_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✓ Connected to database: $dbname on port $port\n";
    
    // Check if vendors table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'vendors'");
    if ($stmt->rowCount() == 0) {
        echo "✗ Vendors table does not exist\n";
        exit;
    }
    echo "✓ Vendors table exists\n";
    
    // Check for 2FA columns
    $stmt = $pdo->query("SHOW COLUMNS FROM vendors");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $required = ['two_factor_enabled', 'two_factor_secret', 'two_factor_backup_codes', 'two_factor_confirmed_at'];
    $missing = [];
    
    foreach ($required as $col) {
        if (in_array($col, $columns)) {
            echo "✓ $col exists\n";
        } else {
            echo "✗ $col missing\n";
            $missing[] = $col;
        }
    }
    
    // Add missing columns
    if (!empty($missing)) {
        echo "\nAdding missing columns...\n";
        
        $alterSql = "ALTER TABLE vendors ";
        $alterParts = [];
        
        if (in_array('two_factor_enabled', $missing)) {
            $alterParts[] = "ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE";
        }
        if (in_array('two_factor_secret', $missing)) {
            $alterParts[] = "ADD COLUMN two_factor_secret VARCHAR(255) NULL";
        }
        if (in_array('two_factor_backup_codes', $missing)) {
            $alterParts[] = "ADD COLUMN two_factor_backup_codes TEXT NULL";
        }
        if (in_array('two_factor_confirmed_at', $missing)) {
            $alterParts[] = "ADD COLUMN two_factor_confirmed_at TIMESTAMP NULL";
        }
        
        $alterSql .= implode(', ', $alterParts);
        
        echo "Executing: $alterSql\n";
        $pdo->exec($alterSql);
        echo "✓ Columns added successfully\n";
        
        // Verify again
        $stmt = $pdo->query("SHOW COLUMNS FROM vendors");
        $newColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        foreach ($required as $col) {
            if (in_array($col, $newColumns)) {
                echo "✓ Verified: $col exists\n";
            } else {
                echo "✗ Still missing: $col\n";
            }
        }
        
    } else {
        echo "✗ Vendors table does not exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
