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
        Schema::create('dispatch_tracking', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispatch_schedule_id');
            $table->string('status'); // started, en_route, arrived, completed, delayed, failed
            $table->text('description');
            $table->decimal('current_latitude', 10, 8)->nullable();
            $table->decimal('current_longitude', 11, 8)->nullable();
            $table->text('current_address')->nullable();
            $table->timestamp('timestamp');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->json('additional_data')->nullable(); // Photos, notes, etc.
            $table->timestamps();

            $table->foreign('dispatch_schedule_id')->references('id')->on('dispatch_schedules')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['dispatch_schedule_id', 'timestamp']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_tracking');
    }
};
