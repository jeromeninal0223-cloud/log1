<?php

// Simple script to create vendors table directly
try {
    $pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create vendors table
    $sql = "CREATE TABLE IF NOT EXISTS vendors (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        email_verified_at DATETIME NULL,
        password VARCHAR(255) NOT NULL,
        company_name VARCHAR(255) NOT NULL,
        phone VARCHAR(20) NULL,
        address TEXT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'pending',
        remember_token VARCHAR(100) NULL,
        created_at DATETIME NULL,
        updated_at DATETIME NULL
    )";
    
    $pdo->exec($sql);
    
    // Check if table was created
    $result = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='vendors'");
    if ($result->fetch()) {
        echo "SUCCESS: vendors table created successfully\n";
        
        // Also create migrations table entry to track this migration
        $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            migration VARCHAR(255) NOT NULL,
            batch INTEGER NOT NULL
        )");
        
        // Insert migration record
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO migrations (migration, batch) VALUES (?, 1)");
        $stmt->execute(['2024_01_01_000004_create_vendors_table']);
        
        echo "SUCCESS: Migration record added\n";
    } else {
        echo "ERROR: Failed to create vendors table\n";
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
