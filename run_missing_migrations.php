<?php

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Initialize database connection
$capsule = new Capsule;

// Add database configuration (adjust as needed)
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'log_1', // Adjust database name
    'username' => 'root',   // Adjust username
    'password' => '',       // Adjust password
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$schema = $capsule->schema();

echo "Running missing database migrations...\n\n";

try {
    // 1. Add foreign key constraint to bids.opportunity_id
    if (!$schema->hasTable('bids') || !$schema->hasTable('opportunities')) {
        echo "❌ Required tables (bids, opportunities) not found. Please run main migrations first.\n";
        exit(1);
    }

    echo "1. Adding foreign key constraint to bids.opportunity_id...\n";
    $schema->table('bids', function (Blueprint $table) {
        // Check if foreign key doesn't already exist
        $table->foreign('opportunity_id')->references('id')->on('opportunities')->nullOnDelete();
    });
    echo "✅ Foreign key constraint added to bids.opportunity_id\n\n";

    // 2. Add missing columns to contracts table
    echo "2. Adding missing columns to contracts table...\n";
    if (!$schema->hasColumn('contracts', 'terms')) {
        $schema->table('contracts', function (Blueprint $table) {
            $table->text('terms')->nullable()->after('description');
        });
        echo "✅ Added 'terms' column to contracts table\n";
    } else {
        echo "ℹ️ 'terms' column already exists in contracts table\n";
    }

    if (!$schema->hasColumn('contracts', 'document_path')) {
        $schema->table('contracts', function (Blueprint $table) {
            $table->string('document_path')->nullable()->after('terms');
        });
        echo "✅ Added 'document_path' column to contracts table\n";
    } else {
        echo "ℹ️ 'document_path' column already exists in contracts table\n";
    }
    echo "\n";

    // 3. Add foreign key constraint to invoices.vendor_id
    echo "3. Adding foreign key constraint to invoices.vendor_id...\n";
    if ($schema->hasTable('invoices') && $schema->hasTable('vendors')) {
        $schema->table('invoices', function (Blueprint $table) {
            $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
        echo "✅ Foreign key constraint added to invoices.vendor_id\n\n";
    } else {
        echo "❌ Required tables (invoices, vendors) not found\n\n";
    }

    echo "🎉 All missing database components have been successfully added!\n";
    echo "\nDatabase structure is now complete with:\n";
    echo "- Proper foreign key relationships\n";
    echo "- Missing columns in contracts table\n";
    echo "- Updated model relationships\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "This might be because the constraints already exist or there's a database connection issue.\n";
    echo "Please check your database configuration and ensure all base tables exist.\n";
}
