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
        Schema::create('rfqs', function (Blueprint $table) {
            $table->id();
            $table->string('rfq_code')->unique();
            $table->unsignedBigInteger('procurement_plan_id');
            $table->string('title');
            $table->text('description');
            $table->text('specifications')->nullable();
            $table->date('submission_deadline');
            $table->date('evaluation_date')->nullable();
            $table->decimal('budget_range_min', 15, 2)->nullable();
            $table->decimal('budget_range_max', 15, 2)->nullable();
            $table->enum('status', ['draft', 'published', 'closed', 'awarded', 'cancelled'])->default('draft');
            $table->json('evaluation_criteria')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('procurement_plan_id')->references('id')->on('procurement_plans')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users');
            $table->index(['status', 'submission_deadline']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rfqs');
    }
};
