<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Make legacy vehicle fields nullable for backward compatibility
            $table->string('plate_number')->nullable()->change();
            $table->string('vehicle_type')->nullable()->change();
            $table->date('registration_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Revert back to NOT NULL (but this might cause issues if there's data)
            $table->string('plate_number')->nullable(false)->change();
            $table->string('vehicle_type')->nullable(false)->change();
            $table->date('registration_date')->nullable(false)->change();
        });
    }
};
