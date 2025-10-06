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
        Schema::create('procurement_plans', function (Blueprint $table) {
            $table->id();
            $table->string('procurement_code')->unique();
            $table->string('procurement_title');
            $table->enum('category', ['goods', 'services', 'construction', 'technology', 'consulting']);
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');
            $table->date('planning_start_date');
            $table->date('required_delivery_date');
            $table->integer('duration_days')->nullable();
            $table->string('delivery_location');
            $table->string('requesting_department')->nullable();
            $table->integer('estimated_quantity')->nullable();
            $table->string('unit_of_measure')->nullable();
            $table->string('procurement_officer')->nullable();
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->decimal('max_budget', 15, 2)->nullable();
            $table->text('description')->nullable();
            $table->text('technical_requirements')->nullable();
            $table->enum('status', ['draft', 'under_review', 'approved', 'in_progress', 'completed', 'cancelled'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->index(['status', 'category']);
            $table->index(['planning_start_date', 'required_delivery_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('procurement_plans');
    }
};
