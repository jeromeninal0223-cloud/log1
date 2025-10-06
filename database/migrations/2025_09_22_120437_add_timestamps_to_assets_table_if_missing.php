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
        Schema::table('assets', function (Blueprint $table) {
            // Check if created_at column doesn't exist and add it
            if (!Schema::hasColumn('assets', 'created_at')) {
                $table->timestamp('created_at')->nullable();
            }
            
            // Check if updated_at column doesn't exist and add it
            if (!Schema::hasColumn('assets', 'updated_at')) {
                $table->timestamp('updated_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Only drop if they exist
            if (Schema::hasColumn('assets', 'created_at')) {
                $table->dropColumn('created_at');
            }
            
            if (Schema::hasColumn('assets', 'updated_at')) {
                $table->dropColumn('updated_at');
            }
        });
    }
};
