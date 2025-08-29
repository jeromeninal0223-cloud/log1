<?php

try {
    // Connect to SQLite database
    $pdo = new PDO('sqlite:' . __DIR__ . '/database/database.sqlite');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if role column exists
    $result = $pdo->query("PRAGMA table_info(users)");
    $columns = $result->fetchAll(PDO::FETCH_ASSOC);
    $hasRole = false;
    
    foreach ($columns as $column) {
        if ($column['name'] === 'role') {
            $hasRole = true;
            break;
        }
    }
    
    if (!$hasRole) {
        echo "Adding role column to users table...\n";
        
        // Add role column
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT 'admin'");
        
        // Add other columns
        $pdo->exec("ALTER TABLE users ADD COLUMN first_name VARCHAR(255) NULL");
        $pdo->exec("ALTER TABLE users ADD COLUMN last_name VARCHAR(255) NULL");
        $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL");
        
        echo "Columns added successfully!\n";
    } else {
        echo "Role column already exists.\n";
    }
    
    // Update existing users to have procurement_officer role
    $stmt = $pdo->prepare("UPDATE users SET role = 'procurement_officer' WHERE role IS NULL OR role = ''");
    $stmt->execute();
    
    echo "Updated existing users with procurement_officer role.\n";
    
    // Show current users
    $result = $pdo->query("SELECT id, name, email, role FROM users");
    $users = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nCurrent users:\n";
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
