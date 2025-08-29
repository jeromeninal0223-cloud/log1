<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('vendor_name')->nullable();
            $table->string('po_number')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status')->default('Draft');
            $table->string('payment_status')->default('Unpaid');
            $table->date('issued_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'payment_status']);
            $table->index(['vendor_id']);

            // Optional foreign key (only if vendors table exists)
            // $table->foreign('vendor_id')->references('id')->on('vendors')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
