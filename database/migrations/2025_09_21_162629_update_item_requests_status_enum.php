<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Use raw SQL to modify the enum since Laravel's dropColumn doesn't work well with enums
        DB::statement("ALTER TABLE item_requests MODIFY COLUMN status ENUM(
            'PENDING', 
            'IN_PROGRESS', 
            'COMPLETED', 
            'CANCELLED',
            'approved',
            'forwarded_to_procurement',
            'pending'
        ) DEFAULT 'PENDING'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original enum values
        DB::statement("ALTER TABLE item_requests MODIFY COLUMN status ENUM(
            'PENDING', 
            'IN_PROGRESS', 
            'COMPLETED', 
            'CANCELLED'
        ) DEFAULT 'PENDING'");
    }
};
