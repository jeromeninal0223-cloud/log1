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
        Schema::create('dispatch_routes', function (Blueprint $table) {
            $table->id();
            $table->string('item_id'); // Reference to picking item
            $table->string('destination');
            $table->decimal('pickup_latitude', 10, 8)->nullable();
            $table->decimal('pickup_longitude', 11, 8)->nullable();
            $table->text('pickup_address')->nullable();
            $table->decimal('destination_latitude', 10, 8)->nullable();
            $table->decimal('destination_longitude', 11, 8)->nullable();
            $table->text('destination_address')->nullable();
            $table->decimal('distance_km', 8, 2)->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->boolean('is_straight_line')->default(false);
            $table->json('route_coordinates')->nullable(); // GeoJSON coordinates
            $table->string('route_type')->default('road'); // road, direct, custom
            $table->timestamp('calculated_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->index('item_id');
            $table->index(['destination', 'route_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_routes');
    }
};
