<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_receipts', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_number')->unique();
            $table->date('receipt_date');
            $table->string('supplier_name');
            $table->string('purchase_order_number')->nullable();
            $table->date('delivery_date');
            $table->string('invoice_number')->nullable();
            $table->string('warehouse_location');
            $table->string('received_by');
            $table->text('notes')->nullable();
            $table->string('status')->default('Pending'); // Pending, Completed, Quality Issue, Cancelled
            $table->decimal('total_value', 15, 2)->default(0);
            $table->integer('total_items')->default(0);
            $table->integer('total_quantity')->default(0);
            $table->integer('damaged_items')->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['receipt_date', 'status']);
            $table->index(['supplier_name']);
            $table->index(['purchase_order_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_receipts');
    }
};
