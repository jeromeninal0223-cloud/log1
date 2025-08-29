<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->string('item_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('category');
            $table->string('supplier')->nullable();
            $table->integer('current_stock')->default(0);
            $table->integer('minimum_stock')->default(0);
            $table->integer('reorder_quantity')->default(0);
            $table->string('unit_of_measure'); // pcs, kg, l, m, box, pack
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->string('storage_location')->nullable();
            $table->string('status')->default('Active'); // Active, Inactive, Discontinued
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['item_code']);
            $table->index(['category']);
            $table->index(['supplier']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
