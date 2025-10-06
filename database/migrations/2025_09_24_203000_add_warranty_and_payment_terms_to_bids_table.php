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
        Schema::table('bids', function (Blueprint $table) {
            // Add warranty period fields
            $table->string('warranty_period')->nullable()->after('completion_date');
            $table->text('custom_warranty')->nullable()->after('warranty_period');
            
            // Add payment terms fields
            $table->string('payment_terms_type')->nullable()->after('custom_warranty');
            $table->text('payment_terms_details')->nullable()->after('payment_terms_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropColumn([
                'warranty_period',
                'custom_warranty',
                'payment_terms_type',
                'payment_terms_details'
            ]);
        });
    }
};
