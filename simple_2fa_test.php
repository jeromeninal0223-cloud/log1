<?php

// Simple test to add 2FA columns directly
$host = 'localhost';
$dbname = 'log1';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database\n";
    
    // Add columns directly
    $sql = "ALTER TABLE vendors 
            ADD COLUMN IF NOT EXISTS two_factor_enabled BOOLEAN DEFAULT FALSE,
            ADD COLUMN IF NOT EXISTS two_factor_secret VARCHAR(255) NULL,
            ADD COLUMN IF NOT EXISTS two_factor_backup_codes TEXT NULL,
            ADD COLUMN IF NOT EXISTS two_factor_confirmed_at TIMESTAMP NULL";
    
    $pdo->exec($sql);
    echo "Columns added successfully\n";
    
    // Verify columns exist
    $stmt = $pdo->query("DESCRIBE vendors");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $required = ['two_factor_enabled', 'two_factor_secret', 'two_factor_backup_codes', 'two_factor_confirmed_at'];
    foreach ($required as $col) {
        if (in_array($col, $columns)) {
            echo "✓ $col exists\n";
        } else {
            echo "✗ $col missing\n";
        }
    }
    
} catch(PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
