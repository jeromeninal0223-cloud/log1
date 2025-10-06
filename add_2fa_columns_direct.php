<?php

// Direct database connection to add 2FA columns
$host = 'localhost';
$dbname = 'log1'; // Adjust database name as needed
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Connected to database successfully\n";
    
    // Check if columns exist
    $stmt = $pdo->query("SHOW COLUMNS FROM vendors LIKE 'two_factor_enabled'");
    if ($stmt->rowCount() == 0) {
        // Add the columns
        $sql = "ALTER TABLE vendors 
                ADD COLUMN two_factor_enabled BOOLEAN DEFAULT FALSE,
                ADD COLUMN two_factor_secret VARCHAR(255) NULL,
                ADD COLUMN two_factor_backup_codes TEXT NULL,
                ADD COLUMN two_factor_confirmed_at TIMESTAMP NULL";
        
        $pdo->exec($sql);
        echo "✓ Added 2FA columns to vendors table\n";
    } else {
        echo "✓ 2FA columns already exist\n";
    }
    
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
