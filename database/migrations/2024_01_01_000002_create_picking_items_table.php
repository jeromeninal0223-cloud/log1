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
        Schema::create('picking_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('picking_session_id');
            $table->string('item_id'); // External item reference
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->text('description')->nullable();
            $table->integer('requested_quantity');
            $table->integer('picked_quantity')->default(0);
            $table->string('unit')->default('pcs');
            $table->string('location')->nullable(); // Warehouse location
            $table->string('status')->default('pending'); // pending, picking, picked, cancelled
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->timestamp('picked_at')->nullable();
            $table->unsignedBigInteger('picked_by')->nullable();
            $table->text('notes')->nullable();
            $table->json('metadata')->nullable(); // Additional item data
            $table->timestamps();

            $table->foreign('picking_session_id')->references('id')->on('picking_sessions')->onDelete('cascade');
            $table->foreign('picked_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['picking_session_id', 'status']);
            $table->index('item_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('picking_items');
    }
};
