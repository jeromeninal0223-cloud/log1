<?php

// Quick database check for 2FA columns
$host = '127.0.0.1';
$port = '3307';
$dbname = 'logistics1_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to: $dbname\n";
    
    // Check vendors table structure
    $stmt = $pdo->query("DESCRIBE vendors");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $has2FA = false;
    foreach ($columns as $column) {
        if (strpos($column['Field'], 'two_factor') !== false) {
            echo "✓ {$column['Field']} - {$column['Type']}\n";
            $has2FA = true;
        }
    }
    
    if (!$has2FA) {
        echo "✗ No 2FA columns found. Adding them now...\n";
        
        $sql = "ALTER TABLE vendors 
                ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE,
                ADD COLUMN two_factor_secret VARCHAR(255) NULL,
                ADD COLUMN two_factor_backup_codes TEXT NULL,
                ADD COLUMN two_factor_confirmed_at TIMESTAMP NULL";
        
        $pdo->exec($sql);
        echo "✓ 2FA columns added!\n";
    } else {
        echo "✓ 2FA columns exist\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
