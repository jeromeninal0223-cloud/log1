<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'category',
        'title',
        'priority',
        'scheduled_date',
        'status',
        'estimated_duration',
        'assigned_technician',
        'notes',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}


