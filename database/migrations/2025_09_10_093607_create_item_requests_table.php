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
        Schema::create('item_requests', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->string('asset_code');
            $table->string('category');
            $table->string('storage_location');
            $table->integer('requested_quantity');
            $table->integer('available_quantity');
            $table->integer('picked_quantity')->default(0);
            $table->enum('priority', ['HIGH', 'MEDIUM', 'LOW'])->default('MEDIUM');
            $table->enum('status', ['PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->unsignedBigInteger('requested_by');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('requested_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_requests');
    }
};
