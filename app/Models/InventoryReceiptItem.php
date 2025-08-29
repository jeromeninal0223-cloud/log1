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

    // Scopes
    public function scopeDamaged($query)
    {
        return $query->where('condition', 'Damaged');
    }

    public function scopeExpired($query)
    {
        return $query->where('condition', 'Expired');
    }

    // Methods
    public function calculateTotalPrice(): void
    {
        $this->total_price = $this->quantity * $this->unit_price;
        $this->save();
    }

    public function isDamaged(): bool
    {
        return $this->condition === 'Damaged';
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
