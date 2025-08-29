<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryReceipt extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'receipt_date',
        'supplier_name',
        'purchase_order_number',
        'delivery_date',
        'invoice_number',
        'warehouse_location',
        'received_by',
        'notes',
        'status',
        'total_value',
        'total_items',
        'total_quantity',
        'damaged_items',
        'created_by',
        'purchase_order_id',
    ];

    protected $casts = [
        'receipt_date' => 'date',
        'delivery_date' => 'date',
        'total_value' => 'decimal:2',
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryReceiptItem::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopeQualityIssue($query)
    {
        return $query->where('status', 'Quality Issue');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('receipt_date', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('receipt_date', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    // Methods
    public static function generateReceiptNumber(): string
    {
        $lastReceipt = self::latest()->first();
        $lastNumber = $lastReceipt ? intval(substr($lastReceipt->receipt_number, 4)) : 0;
        return 'REC-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    public function updateTotals(): void
    {
        $items = $this->items;
        
        $this->update([
            'total_items' => $items->count(),
            'total_quantity' => $items->sum('quantity'),
            'total_value' => $items->sum('total_price'),
            'damaged_items' => $items->where('condition', 'Damaged')->count(),
        ]);
    }

    public function complete(): void
    {
        $this->update(['status' => 'Completed']);
        
        // Update purchase order status if linked
        if ($this->purchaseOrder) {
            $this->purchaseOrder->markInProgress();
        }
    }

    public function markQualityIssue(): void
    {
        $this->update(['status' => 'Quality Issue']);
    }

    public function cancel(): void
    {
        $this->update(['status' => 'Cancelled']);
    }
}
