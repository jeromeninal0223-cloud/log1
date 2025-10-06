<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Core dynamic asset fields
            $table->string('asset_category')->nullable()->after('id');
            $table->string('asset_id')->nullable()->after('asset_category');
            $table->json('asset_data')->nullable()->after('asset_id');
            $table->unsignedBigInteger('created_by')->nullable()->after('asset_data');
            
            // Buildings & Structures fields
            $table->string('building_name')->nullable();
            $table->text('location_address')->nullable();
            $table->decimal('floor_area', 10, 2)->nullable();
            $table->date('date_acquired')->nullable();
            $table->decimal('acquisition_cost', 15, 2)->nullable();
            $table->string('condition')->nullable();
            $table->string('current_use')->nullable();
            $table->string('responsible_department')->nullable();
            $table->string('custodian')->nullable();
            $table->date('last_inspection')->nullable();
            $table->text('remarks')->nullable();
            
            // Vehicles fields (additional to existing)
            $table->string('engine_chassis')->nullable();
            $table->string('supplier')->nullable();
            $table->string('operational_status')->nullable();
            $table->string('assigned_driver')->nullable();
            $table->string('department_location')->nullable();
            $table->date('last_maintenance')->nullable();
            $table->date('next_maintenance')->nullable();
            
            // Machinery & Equipment fields
            $table->string('equipment_name')->nullable();
            $table->string('brand_model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('location_site')->nullable();
            $table->string('assigned_to')->nullable();
            $table->string('maintenance_frequency')->nullable();
            $table->date('last_service')->nullable();
            $table->date('next_service')->nullable();
            
            // Furniture & Fixtures fields
            $table->string('item_description')->nullable();
            $table->integer('quantity')->nullable();
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_cost', 10, 2)->nullable();
            $table->string('location_office')->nullable();
            $table->string('assigned_user')->nullable();
            
            // IT Equipment fields
            $table->string('item_name')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('department')->nullable();
            $table->date('warranty_expiry')->nullable();
            
            // Tools & Instruments fields
            $table->string('tool_name')->nullable();
            $table->string('location')->nullable();
            
            // Office Equipment fields
            $table->string('maintenance_schedule')->nullable();
            
            // Add foreign key constraint for created_by
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['created_by']);
            
            // Drop all added columns
            $table->dropColumn([
                'asset_category',
                'asset_id', 
                'asset_data',
                'created_by',
                'building_name',
                'location_address',
                'floor_area',
                'date_acquired',
                'acquisition_cost',
                'condition',
                'current_use',
                'responsible_department',
                'custodian',
                'last_inspection',
                'remarks',
                'engine_chassis',
                'supplier',
                'operational_status',
                'assigned_driver',
                'department_location',
                'last_maintenance',
                'next_maintenance',
                'equipment_name',
                'brand_model',
                'serial_number',
                'location_site',
                'assigned_to',
                'maintenance_frequency',
                'last_service',
                'next_service',
                'item_description',
                'quantity',
                'unit_cost',
                'total_cost',
                'location_office',
                'assigned_user',
                'item_name',
                'cost',
                'department',
                'warranty_expiry',
                'tool_name',
                'location',
                'maintenance_schedule'
            ]);
        });
    }
};
