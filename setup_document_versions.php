<?php

/**
 * Setup script for Document Version History
 * Run this file to create tables and seed sample data
 */

require_once __DIR__ . '/vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Database configuration
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'sqlite',
    'database' => __DIR__ . '/database/database.sqlite',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

echo "Setting up Document Version History...\n";

try {
    // Create documents table
    if (!Capsule::schema()->hasTable('documents')) {
        Capsule::schema()->create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('document_type', 50);
            $table->text('description')->nullable();
            $table->string('current_version', 20)->default('1.0');
            $table->unsignedBigInteger('created_by_id');
            $table->string('created_by_name');
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->timestamps();
            
            // Indexes
            $table->index(['status']);
            $table->index(['document_type']);
            $table->index(['created_by_id']);
        });
        echo "✓ Created documents table\n";
    } else {
        echo "✓ Documents table already exists\n";
    }

    // Create document_versions table
    if (!Capsule::schema()->hasTable('document_versions')) {
        Capsule::schema()->create('document_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('document_id');
            $table->string('version_number', 20);
            $table->unsignedBigInteger('modified_by_id');
            $table->string('modified_by_name');
            $table->string('user_role', 50);
            $table->text('changes_summary')->nullable();
            $table->string('file_path');
            $table->bigInteger('file_size')->nullable();
            $table->enum('status', ['active', 'archived', 'deleted'])->default('active');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['document_id', 'created_at']);
            $table->index(['status']);
            $table->index(['modified_by_id']);
            $table->unique(['document_id', 'version_number']);
        });
        echo "✓ Created document_versions table\n";
    } else {
        echo "✓ Document_versions table already exists\n";
    }

    echo "\n✅ Database setup completed successfully!\n";
    echo "\nNext steps:\n";
    echo "1. Run: php artisan migrate (if using Laravel migrations)\n";
    echo "2. Run: php artisan db:seed --class=DocumentVersionSeeder\n";
    echo "3. Access /dtrs/version to view the document version history\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
