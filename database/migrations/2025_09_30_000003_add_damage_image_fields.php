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
            $table->string('damage_image_path')->nullable()->after('damage_reason');
            $table->string('damage_image_name')->nullable()->after('damage_image_path');
            $table->integer('damage_image_size')->nullable()->after('damage_image_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_receipt_items', function (Blueprint $table) {
            $table->dropColumn(['damage_image_path', 'damage_image_name', 'damage_image_size']);
        });
    }
};
