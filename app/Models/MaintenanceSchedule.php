<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'title',
        'scheduled_date',
        'status',
        'notes',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}


