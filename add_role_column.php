<?php

try {
    // Database connection using your .env settings
    $pdo = new PDO(
        'mysql:host=127.0.0.1;port=3307;dbname=logistics1_db',
        'root',
        '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "Connected to MySQL database successfully.\n";
    
    // Check if role column already exists
    $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE 'role'");
    if ($stmt->rowCount() > 0) {
        echo "Role column already exists.\n";
    } else {
        echo "Adding role column...\n";
        $pdo->exec("ALTER TABLE users ADD COLUMN role VARCHAR(255) DEFAULT 'admin' AFTER email");
        echo "Role column added.\n";
    }
    
    // Check and add other columns
    $columns = ['first_name', 'last_name', 'phone'];
    foreach ($columns as $column) {
        $stmt = $pdo->query("SHOW COLUMNS FROM users LIKE '$column'");
        if ($stmt->rowCount() == 0) {
            if ($column == 'first_name') {
                $pdo->exec("ALTER TABLE users ADD COLUMN first_name VARCHAR(255) NULL AFTER name");
                echo "Added first_name column.\n";
            } elseif ($column == 'last_name') {
                $pdo->exec("ALTER TABLE users ADD COLUMN last_name VARCHAR(255) NULL AFTER first_name");
                echo "Added last_name column.\n";
            } elseif ($column == 'phone') {
                $pdo->exec("ALTER TABLE users ADD COLUMN phone VARCHAR(20) NULL AFTER email");
                echo "Added phone column.\n";
            }
        }
    }
    
    // Update existing users to have procurement_officer role if they don't have one
    $stmt = $pdo->prepare("UPDATE users SET role = 'procurement_officer' WHERE role = 'admin' OR role IS NULL");
    $affected = $stmt->execute();
    echo "Updated existing users with procurement_officer role.\n";
    
    // Show current users
    $result = $pdo->query("SELECT id, name, email, role FROM users LIMIT 5");
    $users = $result->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nCurrent users:\n";
    foreach ($users as $user) {
        echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}, Role: {$user['role']}\n";
    }
    
    echo "\nDatabase update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
