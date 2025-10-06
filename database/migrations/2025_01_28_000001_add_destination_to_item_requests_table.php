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
        Schema::table('item_requests', function (Blueprint $table) {
            $table->string('delivery_location')->nullable()->after('notes');
            $table->string('delivery_department')->nullable()->after('delivery_location');
            $table->text('delivery_instructions')->nullable()->after('delivery_department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_requests', function (Blueprint $table) {
            $table->dropColumn(['delivery_location', 'delivery_department', 'delivery_instructions']);
        });
    }
};
