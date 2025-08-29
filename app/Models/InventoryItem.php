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
        $lastItem = self::latest()->first();
        $lastNumber = $lastItem ? intval(substr($lastItem->item_code, 3)) : 0;
        return 'ITM-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }
}
