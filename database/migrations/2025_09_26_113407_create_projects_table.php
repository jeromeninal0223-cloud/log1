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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique();
            $table->string('project_title');
            $table->text('project_description');
            $table->date('start_date');
            $table->date('expected_end_date');
            $table->date('actual_end_date')->nullable();
            $table->decimal('estimated_budget', 15, 2);
            $table->decimal('actual_budget', 15, 2)->nullable();
            $table->enum('status', ['Draft', 'Planning', 'Active', 'On Hold', 'Completed', 'Cancelled'])->default('Draft');
            $table->string('responsible_person')->nullable();
            $table->string('department')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('approved_by')->references('id')->on('users');

            // Indexes for better performance
            $table->index('status');
            $table->index('start_date');
            $table->index('expected_end_date');
            $table->index('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
