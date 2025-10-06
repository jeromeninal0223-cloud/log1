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
        Schema::create('disposal_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_id')->unique();
            $table->unsignedBigInteger('asset_id');
            $table->string('disposal_reason');
            $table->string('disposal_method');
            $table->string('department');
            $table->decimal('estimated_value', 10, 2)->default(0);
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('justification');
            $table->text('additional_notes')->nullable();
            $table->string('requested_by');
            $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed'])->default('pending');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('disposed_at')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index(['status', 'created_at']);
            $table->index(['asset_id', 'status']);
            $table->index('urgency');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposal_requests');
    }
};
