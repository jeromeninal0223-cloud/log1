<?php

/**
 * Create Document Version Tables
 * Run this to create the required tables for document versioning
 */

try {
    // Database connection
    $host = '127.0.0.1';
    $port = '3307';
    $dbname = 'logistics1_db';
    $username = 'root';
    $password = '';

    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully!\n";

    // Create documents table
    $documentsTable = "
        CREATE TABLE IF NOT EXISTS `documents` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `title` varchar(255) NOT NULL,
            `document_type` varchar(50) NOT NULL,
            `description` text DEFAULT NULL,
            `current_version` varchar(20) NOT NULL DEFAULT '1.0',
            `created_by_id` bigint(20) unsigned NOT NULL,
            `created_by_name` varchar(255) NOT NULL,
            `status` enum('active','archived','deleted') NOT NULL DEFAULT 'active',
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            KEY `documents_status_index` (`status`),
            KEY `documents_document_type_index` (`document_type`),
            KEY `documents_created_by_id_index` (`created_by_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($documentsTable);
    echo "✓ Created documents table\n";

    // Create document_versions table
    $versionsTable = "
        CREATE TABLE IF NOT EXISTS `document_versions` (
            `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
            `document_id` bigint(20) unsigned NOT NULL,
            `version_number` varchar(20) NOT NULL,
            `modified_by_id` bigint(20) unsigned NOT NULL,
            `modified_by_name` varchar(255) NOT NULL,
            `user_role` varchar(50) NOT NULL,
            `changes_summary` text DEFAULT NULL,
            `file_path` varchar(255) NOT NULL,
            `file_size` bigint(20) DEFAULT NULL,
            `status` enum('active','archived','deleted') NOT NULL DEFAULT 'active',
            `metadata` json DEFAULT NULL,
            `created_at` timestamp NULL DEFAULT NULL,
            `updated_at` timestamp NULL DEFAULT NULL,
            PRIMARY KEY (`id`),
            UNIQUE KEY `document_versions_document_id_version_number_unique` (`document_id`,`version_number`),
            KEY `document_versions_document_id_created_at_index` (`document_id`,`created_at`),
            KEY `document_versions_status_index` (`status`),
            KEY `document_versions_modified_by_id_index` (`modified_by_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

    $pdo->exec($versionsTable);
    echo "✓ Created document_versions table\n";

    echo "\n✅ Database tables created successfully!\n";
    echo "\nTables created:\n";
    echo "- documents\n";
    echo "- document_versions\n";
    echo "\nYou can now add documents and their versions through your application.\n";

} catch (PDOException $e) {
    echo "❌ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
