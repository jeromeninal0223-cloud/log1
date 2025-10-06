<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_versions', function (Blueprint $table) {
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
            $table->json('metadata')->nullable(); // Store additional file metadata
            $table->timestamps();
            
            // Indexes
            $table->index(['document_id', 'created_at']);
            $table->index(['status']);
            $table->index(['modified_by_id']);
            $table->unique(['document_id', 'version_number']);
        });
        
        // Create documents table if it doesn't exist
        Schema::create('documents', function (Blueprint $table) {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_versions');
        Schema::dropIfExists('documents');
    }
};
