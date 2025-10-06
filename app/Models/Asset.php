<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'assets';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        // Legacy vehicle fields (for backward compatibility)
        'plate_number',
        'vehicle_type',
        'brand',
        'model',
        'year',
        'capacity',
        'status',
        'registration_date',
        'notes',
        'image_path',
        
        // New dynamic asset fields
        'asset_category',
        'asset_id',
        'asset_data',
        'created_by',
        
        // Buildings & Structures
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
        
        // Vehicles
        'engine_chassis',
        'supplier',
        'operational_status',
        'assigned_driver',
        'department_location',
        'last_maintenance',
        'next_maintenance',
        
        // Machinery & Equipment
        'equipment_name',
        'brand_model',
        'serial_number',
        'location_site',
        'assigned_to',
        'maintenance_frequency',
        'last_service',
        'next_service',
        
        // Furniture & Fixtures
        'item_description',
        'quantity',
        'unit_cost',
        'total_cost',
        'location_office',
        'assigned_user',
        
        // IT Equipment
        'item_name',
        'cost',
        'department',
        'warranty_expiry',
        
        // Tools & Instruments
        'tool_name',
        'location',
        
        // Office Equipment
        'maintenance_schedule',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }
}


