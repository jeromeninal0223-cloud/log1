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
        Schema::table('contracts', function (Blueprint $table) {
            $table->longText('vendor_signature_image')->nullable()->after('vendor_signature_ip');
            $table->longText('procurement_signature_image')->nullable()->after('procurement_signature_ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(['vendor_signature_image', 'procurement_signature_image']);
        });
    }
};
