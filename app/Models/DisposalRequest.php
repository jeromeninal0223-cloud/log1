<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DisposalRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_id',
        'asset_id',
        'disposal_reason',
        'disposal_method',
        'department',
        'estimated_value',
        'urgency',
        'justification',
        'additional_notes',
        'requested_by',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
        'disposed_at',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'approved_at' => 'datetime',
        'disposed_at' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
