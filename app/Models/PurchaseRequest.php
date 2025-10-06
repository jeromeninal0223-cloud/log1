<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_number',
        'item_description',
        'category',
        'quantity',
        'unit',
        'estimated_cost',
        'required_date',
        'priority',
        'status',
        'justification',
        'rejection_reason',
        'requested_by',
        'approved_by',
        'approved_at'
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'required_date' => 'date',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Generate request number
    public static function generateRequestNumber()
    {
        $lastRequest = self::latest()->first();
        $number = $lastRequest ? (int)substr($lastRequest->request_number, 3) + 1 : 1;
        return 'PR-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    // Get status badge color
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Pending' => 'warning',
            'Approved' => 'success',
            'Rejected' => 'danger',
            default => 'secondary'
        };
    }

    // Get priority badge color
    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'High' => 'danger',
            'Medium' => 'warning',
            'Low' => 'success',
            default => 'secondary'
        };
    }

    // Method version of getPriorityColorAttribute (for compatibility)
    public function getPriorityColor(): string
    {
        return match(strtolower($this->priority)) {
            'high' => 'danger',
            'medium' => 'warning', 
            'low' => 'success',
            default => 'secondary'
        };
    }

    // Method version of getStatusColorAttribute (for compatibility)
    public function getStatusColor(): string
    {
        return match($this->status) {
            'Pending' => 'warning',
            'Approved' => 'success',
            'Rejected' => 'danger',
            default => 'secondary'
        };
    }
}
