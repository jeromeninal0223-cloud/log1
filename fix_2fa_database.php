<?php

// Direct PDO approach to add 2FA columns
$host = '127.0.0.1';
$port = '3307';
$dbname = 'logistics1_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database: $dbname\n";
    
    // Check if 2FA columns exist
    $stmt = $pdo->query("SHOW COLUMNS FROM vendors");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $requiredColumns = ['two_factor_enabled', 'two_factor_secret', 'two_factor_backup_codes', 'two_factor_confirmed_at'];
    $missingColumns = array_diff($requiredColumns, $existingColumns);
    
    if (!empty($missingColumns)) {
        echo "Missing columns: " . implode(', ', $missingColumns) . "\n";
        echo "Adding 2FA columns...\n";
        
        $sql = "ALTER TABLE vendors 
                ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE,
                ADD COLUMN two_factor_secret VARCHAR(255) NULL,
                ADD COLUMN two_factor_backup_codes TEXT NULL,
                ADD COLUMN two_factor_confirmed_at TIMESTAMP NULL";
        
        $pdo->exec($sql);
        echo " 2FA columns added successfully\n";
    } else {
        echo " All 2FA columns already exist\n";
    }
    
    // Verify columns exist now
    $stmt = $pdo->query("DESCRIBE vendors");
    $allColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nCurrent vendors table structure:\n";
    foreach ($allColumns as $column) {
        if (strpos($column['Field'], 'two_factor') !== false) {
            echo "  {$column['Field']} ({$column['Type']})\n";
        }
    }
    
    echo "\n2FA database setup complete!\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
