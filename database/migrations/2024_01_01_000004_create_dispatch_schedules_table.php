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
        Schema::create('dispatch_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('schedule_id')->unique();
            $table->string('title')->nullable();
            $table->timestamp('scheduled_datetime');
            $table->string('priority')->default('normal'); // normal, high, urgent
            $table->string('status')->default('scheduled'); // scheduled, in_progress, completed, cancelled, delayed
            $table->string('driver_name')->nullable();
            $table->string('vehicle_info')->nullable();
            $table->text('special_instructions')->nullable();
            $table->integer('total_items')->default(0);
            $table->integer('estimated_duration_minutes')->nullable();
            $table->decimal('total_distance_km', 8, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['status', 'scheduled_datetime']);
            $table->index('schedule_id');
            $table->index('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_schedules');
    }
};
