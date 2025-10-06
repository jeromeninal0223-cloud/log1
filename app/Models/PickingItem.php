<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PickingItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'picking_session_id',
        'item_id',
        'item_name',
        'item_code',
        'description',
        'requested_quantity',
        'picked_quantity',
        'unit',
        'location',
        'status',
        'priority',
        'picked_at',
        'picked_by',
        'notes',
        'metadata',
    ];

    protected $casts = [
        'picked_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the picking session that owns the item.
     */
    public function pickingSession(): BelongsTo
    {
        return $this->belongsTo(PickingSession::class);
    }

    /**
     * Get the user who picked the item.
     */
    public function picker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'picked_by');
    }

    /**
     * Get the dispatch route for this item.
     */
    public function dispatchRoute(): HasOne
    {
        return $this->hasOne(DispatchRoute::class, 'item_id', 'item_id');
    }

    /**
     * Get dispatch items for this picking item.
     */
    public function dispatchItems(): HasMany
    {
        return $this->hasMany(DispatchItem::class);
    }

    /**
     * Check if item is ready for dispatch.
     */
    public function isReadyForDispatch(): bool
    {
        return $this->status === 'picked' && $this->picked_quantity > 0;
    }

    /**
     * Check if item is fully picked.
     */
    public function isFullyPicked(): bool
    {
        return $this->picked_quantity >= $this->requested_quantity;
    }

    /**
     * Get picking completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->requested_quantity === 0) return 0;
        
        return round(($this->picked_quantity / $this->requested_quantity) * 100, 2);
    }

    /**
     * Get remaining quantity to pick.
     */
    public function getRemainingQuantityAttribute(): int
    {
        return max(0, $this->requested_quantity - $this->picked_quantity);
    }

    /**
     * Mark item as picked.
     */
    public function markAsPicked(int $quantity, int $userId = null): void
    {
        $this->update([
            'picked_quantity' => $quantity,
            'status' => 'picked',
            'picked_at' => now(),
            'picked_by' => $userId ?? auth()->id(),
        ]);
    }

    /**
     * Get priority badge color.
     */
    public function getPriorityColorAttribute(): string
    {
        return match($this->priority) {
            'urgent' => 'danger',
            'high' => 'warning',
            'normal' => 'primary',
            'low' => 'secondary',
            default => 'primary',
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'picked' => 'success',
            'picking' => 'warning',
            'pending' => 'secondary',
            'cancelled' => 'danger',
            default => 'secondary',
        };
    }
}
