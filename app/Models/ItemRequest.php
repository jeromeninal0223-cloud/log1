<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_name',
        'asset_code',
        'category',
        'storage_location',
        'requested_quantity',
        'available_quantity',
        'picked_quantity',
        'priority',
        'status',
        'requested_by',
        'notes',
        'delivery_location',
        'delivery_department',
        'delivery_instructions'
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'available_quantity' => 'integer',
        'picked_quantity' => 'integer',
    ];

    // Relationship with User (who requested)
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // Get priority badge color
    public function getPriorityBadgeColorAttribute()
    {
        return match($this->priority) {
            'HIGH' => 'bg-danger',
            'MEDIUM' => 'bg-warning',
            'LOW' => 'bg-success',
            default => 'bg-secondary'
        };
    }

    // Get available quantity color
    public function getAvailableQuantityColorAttribute()
    {
        if ($this->available_quantity >= $this->requested_quantity) {
            return 'text-success';
        } elseif ($this->available_quantity > 0) {
            return 'text-warning';
        } else {
            return 'text-danger';
        }
    }

    // Check if request is complete
    public function getIsCompleteAttribute()
    {
        return $this->picked_quantity >= $this->requested_quantity;
    }

    // Get completion percentage
    public function getCompletionPercentageAttribute()
    {
        if ($this->requested_quantity == 0) return 0;
        return min(100, ($this->picked_quantity / $this->requested_quantity) * 100);
    }

    // Additional methods for stock replenishment
    public function getPriorityColor(): string
    {
        return match(strtolower($this->priority)) {
            'high' => 'danger',
            'medium' => 'warning', 
            'low' => 'success',
            default => 'secondary'
        };
    }

    public function getStatusColor(): string
    {
        return match(strtoupper($this->status)) {
            'PENDING' => 'warning',
            'IN_PROGRESS' => 'info',
            'COMPLETED' => 'success',
            'CANCELLED' => 'danger',
            default => 'secondary'
        };
    }

    // Generate request number
    public function getRequestNumberAttribute()
    {
        return 'REQ-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }

    // Calculate total estimated cost
    public function getTotalEstimatedCostAttribute()
    {
        // Try to get the unit price from related inventory item
        $inventoryItem = InventoryItem::where('name', $this->item_name)->first();
        $unitPrice = $inventoryItem ? $inventoryItem->unit_price : 0;
        return $this->requested_quantity * $unitPrice;
    }

    // Relationship with inventory item
    public function stockItem()
    {
        return $this->belongsTo(InventoryItem::class, 'item_name', 'name');
    }
}
