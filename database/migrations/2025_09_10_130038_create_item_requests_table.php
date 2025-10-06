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
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('asset_code')->nullable();
            $table->string('category')->nullable();
            $table->string('storage_location')->nullable();
            $table->integer('requested_quantity');
            $table->integer('available_quantity')->nullable();
            $table->integer('picked_quantity')->default(0);
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW'])->default('MEDIUM');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->unsignedBigInteger('requested_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('requested_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['status', 'item_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_requests');
    }
};
