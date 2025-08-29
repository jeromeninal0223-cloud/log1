<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->string('plate_number');
                $table->string('vehicle_type');
                $table->string('brand')->nullable();
                $table->string('model')->nullable();
                $table->integer('year')->nullable();
                $table->integer('capacity')->nullable();
                $table->string('status')->default('Available');
                $table->date('registration_date');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};


