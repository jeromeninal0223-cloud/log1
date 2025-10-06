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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable(); // Store name for deleted users
            $table->string('user_role')->nullable();
            $table->string('action'); // login, logout, create, update, delete, view, download
            $table->string('module'); // DTRS, PSM, PLT, SWS, ALMS, AUTH
            $table->string('resource_type')->nullable(); // Model name (User, Document, Contract, etc.)
            $table->unsignedBigInteger('resource_id')->nullable(); // ID of the affected resource
            $table->text('description'); // Human readable description
            $table->json('old_values')->nullable(); // Before values for updates
            $table->json('new_values')->nullable(); // After values for updates
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('session_id')->nullable();
            $table->enum('status', ['success', 'failed', 'warning'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['user_id', 'created_at']);
            $table->index(['action', 'module']);
            $table->index(['created_at']);
            $table->index(['status']);
            
            // Foreign key constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
