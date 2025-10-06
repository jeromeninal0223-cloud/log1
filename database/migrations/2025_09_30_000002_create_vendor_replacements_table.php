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
        Schema::create('vendor_replacements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_receipt_item_id')->constrained('inventory_receipt_items')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->integer('quantity');
            $table->date('expected_delivery_date');
            $table->string('tracking_number')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['sent', 'in_transit', 'delivered', 'cancelled'])->default('sent');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('expected_delivery_date');
        });

        // Add additional fields to inventory_receipt_items for tracking acknowledgments and replacements
        Schema::table('inventory_receipt_items', function (Blueprint $table) {
            $table->timestamp('acknowledged_at')->nullable()->after('return_to_vendor');
            $table->unsignedBigInteger('acknowledged_by')->nullable()->after('acknowledged_at');
            $table->integer('replacement_quantity')->nullable()->after('acknowledged_by');
            $table->date('replacement_delivery_date')->nullable()->after('replacement_quantity');
            $table->string('replacement_tracking_number')->nullable()->after('replacement_delivery_date');
            $table->text('replacement_notes')->nullable()->after('replacement_tracking_number');
            $table->timestamp('replacement_sent_at')->nullable()->after('replacement_notes');
            $table->unsignedBigInteger('replacement_sent_by')->nullable()->after('replacement_sent_at');

            $table->foreign('acknowledged_by')->references('id')->on('vendors')->onDelete('set null');
            $table->foreign('replacement_sent_by')->references('id')->on('vendors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_receipt_items', function (Blueprint $table) {
            $table->dropForeign(['acknowledged_by']);
            $table->dropForeign(['replacement_sent_by']);
            $table->dropColumn([
                'acknowledged_at',
                'acknowledged_by',
                'replacement_quantity',
                'replacement_delivery_date',
                'replacement_tracking_number',
                'replacement_notes',
                'replacement_sent_at',
                'replacement_sent_by'
            ]);
        });

        Schema::dropIfExists('vendor_replacements');
    }
};
