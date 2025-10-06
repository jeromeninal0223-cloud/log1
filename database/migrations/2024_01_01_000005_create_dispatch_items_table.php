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
        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispatch_schedule_id');
            $table->unsignedBigInteger('picking_item_id');
            $table->unsignedBigInteger('dispatch_route_id')->nullable();
            $table->string('item_id'); // Reference to original item
            $table->string('item_name');
            $table->integer('quantity');
            $table->string('destination');
            $table->string('status')->default('ready'); // ready, dispatched, delivered, failed
            $table->integer('sequence_order')->default(0); // Order in delivery route
            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_signature')->nullable(); // File path to signature
            $table->json('proof_of_delivery')->nullable(); // Photos, documents
            $table->timestamps();

            $table->foreign('dispatch_schedule_id')->references('id')->on('dispatch_schedules')->onDelete('cascade');
            $table->foreign('picking_item_id')->references('id')->on('picking_items')->onDelete('cascade');
            $table->foreign('dispatch_route_id')->references('id')->on('dispatch_routes')->onDelete('set null');
            
            $table->index(['dispatch_schedule_id', 'status']);
            $table->index('item_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_items');
    }
};
