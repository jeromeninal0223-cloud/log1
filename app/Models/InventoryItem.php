<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_code',
        'name',
        'description',
        'category',
        'supplier',
        'current_stock',
        'minimum_stock',
        'reorder_quantity',
        'unit_of_measure',
        'unit_price',
        'storage_location',
        'zone',
        'bin',
        'status',
        'notes',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('current_stock <= minimum_stock');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeBySupplier($query, $supplier)
    {
        return $query->where('supplier', $supplier);
    }

    // Methods
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function needsReorder(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    public function addStock(int $quantity): void
    {
        $this->current_stock += $quantity;
        $this->save();
    }

    public function removeStock(int $quantity): void
    {
        if ($this->current_stock >= $quantity) {
            $this->current_stock -= $quantity;
            $this->save();
        }
    }

    public function getStockValue(): float
    {
        return $this->current_stock * $this->unit_price;
    }

    public static function generateItemCode(): string
    {
        // Get the highest existing item code number
        $lastItem = self::orderBy('item_code', 'desc')->first();
        
        if ($lastItem && $lastItem->item_code) {
            // Extract number from item code (e.g., "ITM-000001" -> 1)
            $lastNumber = intval(substr($lastItem->item_code, 4));
        } else {
            $lastNumber = 0;
        }
        
        // Generate new code with incremented number
        $newNumber = $lastNumber + 1;
        $newCode = 'ITM-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        
        // Check if this code already exists (safety check)
        while (self::where('item_code', $newCode)->exists()) {
            $newNumber++;
            $newCode = 'ITM-' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
        }
        
        return $newCode;
    }

    // Additional methods needed for stock replenishment view
    public function isCriticallyLow(): bool
    {
        return $this->current_stock <= ($this->minimum_stock * 0.5);
    }

    public function getStockStatus(): string
    {
        if ($this->isCriticallyLow()) {
            return 'critical';
        } elseif ($this->isLowStock()) {
            return 'low';
        } else {
            return 'normal';
        }
    }

    public function hasPendingPurchaseRequest(): bool
    {
        // Check if there's a pending item request for this inventory item
        return ItemRequest::where('item_name', $this->name)
                         ->where('status', 'pending')
                         ->exists();
    }

    // Relationships
    public function itemRequests()
    {
        return $this->hasMany(ItemRequest::class, 'item_name', 'name');
    }
}
