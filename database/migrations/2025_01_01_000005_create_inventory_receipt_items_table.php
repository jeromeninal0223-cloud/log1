<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_receipt_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_receipt_id')->constrained('inventory_receipts')->cascadeOnDelete();
            $table->string('item_name');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->string('unit'); // pcs, kg, l, m, box, pack
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('condition'); // New, Good, Fair, Damaged, Expired
            $table->string('storage_location');
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->text('item_notes')->nullable();
            $table->timestamps();
            
            $table->index(['item_name']);
            $table->index(['condition']);
            $table->index(['storage_location']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_receipt_items');
    }
};
