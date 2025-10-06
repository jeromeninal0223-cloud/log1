<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryReceiptItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inventory_receipt_id',
        'item_name',
        'description',
        'quantity',
        'damaged_quantity',
        'damage_reason',
        'damage_image_path',
        'damage_image_name',
        'damage_image_size',
        'return_to_vendor',
        'unit',
        'unit_price',
        'total_price',
        'condition',
        'storage_location',
        'batch_number',
        'expiry_date',
        'item_notes',
        'image_path',
        'image_name',
        'image_size',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'expiry_date' => 'date',
    ];

    // Relationships
    public function receipt(): BelongsTo
    {
        return $this->belongsTo(InventoryReceipt::class, 'inventory_receipt_id');
    }

    public function scopeWithDamage($query)
    {
        return $query->where('damaged_quantity', '>', 0);
    }

    public function scopeForReturn($query)
    {
        return $query->where('return_to_vendor', true);
    }

    // Helper methods
    public function getGoodQuantityAttribute()
    {
        return $this->quantity - $this->damaged_quantity;
    }

    public function hasDamage()
    {
        return $this->damaged_quantity > 0;
    }

    public function isFullyDamaged()
    {
        return $this->damaged_quantity >= $this->quantity;
    }

    public function getDamagePercentageAttribute()
    {
        if ($this->quantity == 0) return 0;
        return round(($this->damaged_quantity / $this->quantity) * 100, 2);
    }

    public function isExpired(): bool
    {
        return $this->condition === 'Expired';
    }

    public function getImageUrl(): ?string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return null;
    }

    public function hasImage(): bool
    {
        return !empty($this->image_path);
    }
}
