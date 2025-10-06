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
        Schema::create('vendor_evaluations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('rfq_id');
            $table->unsignedBigInteger('vendor_id');
            $table->decimal('quoted_price', 15, 2);
            $table->date('proposed_delivery_date');
            $table->integer('technical_score')->nullable(); // 0-100
            $table->integer('commercial_score')->nullable(); // 0-100
            $table->integer('compliance_score')->nullable(); // 0-100
            $table->decimal('total_score', 5, 2)->nullable(); // Weighted average
            $table->text('evaluation_notes')->nullable();
            $table->json('evaluation_details')->nullable(); // Detailed scoring breakdown
            $table->enum('recommendation', ['award', 'reject', 'clarification_needed'])->nullable();
            $table->enum('status', ['submitted', 'under_evaluation', 'evaluated', 'awarded', 'rejected'])->default('submitted');
            $table->unsignedBigInteger('evaluated_by')->nullable();
            $table->timestamp('evaluated_at')->nullable();
            $table->timestamps();

            $table->foreign('rfq_id')->references('id')->on('rfqs')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors');
            $table->foreign('evaluated_by')->references('id')->on('users');
            $table->unique(['rfq_id', 'vendor_id']);
            $table->index(['status', 'total_score']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_evaluations');
    }
};
