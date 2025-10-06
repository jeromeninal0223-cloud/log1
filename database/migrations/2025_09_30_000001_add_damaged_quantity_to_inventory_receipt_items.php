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
        Schema::table('inventory_receipt_items', function (Blueprint $table) {
            $table->integer('damaged_quantity')->default(0)->after('quantity');
            $table->integer('good_quantity')->storedAs('quantity - damaged_quantity')->after('damaged_quantity');
            $table->string('damage_reason')->nullable()->after('good_quantity');
            $table->string('damage_image_path')->nullable()->after('damage_reason');
            $table->string('damage_image_name')->nullable()->after('damage_image_path');
            $table->integer('damage_image_size')->nullable()->after('damage_image_name');
            $table->boolean('return_to_vendor')->default(false)->after('damage_image_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['damaged_quantity', 'good_quantity', 'damage_reason', 'damage_image_path', 'damage_image_name', 'damage_image_size', 'return_to_vendor']);
        });
    }
};
