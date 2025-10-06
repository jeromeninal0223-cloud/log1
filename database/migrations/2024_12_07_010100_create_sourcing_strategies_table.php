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
        Schema::create('sourcing_strategies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('procurement_plan_id');
            $table->integer('phase_number');
            $table->date('phase_date');
            $table->string('activity');
            $table->string('responsible');
            $table->string('deliverable');
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'delayed'])->default('pending');
            $table->timestamps();

            $table->foreign('procurement_plan_id')->references('id')->on('procurement_plans')->onDelete('cascade');
            $table->index(['procurement_plan_id', 'phase_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sourcing_strategies');
    }
};
