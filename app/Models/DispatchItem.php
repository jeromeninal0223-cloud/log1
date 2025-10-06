<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_schedule_id',
        'picking_item_id',
        'dispatch_route_id',
        'item_id',
        'item_name',
        'quantity',
        'destination',
        'status',
        'sequence_order',
        'dispatched_at',
        'delivered_at',
        'delivery_notes',
        'recipient_name',
        'recipient_signature',
        'proof_of_delivery',
    ];

    protected $casts = [
        'dispatched_at' => 'datetime',
        'delivered_at' => 'datetime',
        'proof_of_delivery' => 'array',
    ];

    /**
     * Get the dispatch schedule that owns the item.
     */
    public function dispatchSchedule(): BelongsTo
    {
        return $this->belongsTo(DispatchSchedule::class);
    }

    /**
     * Get the picking item.
     */
    public function pickingItem(): BelongsTo
    {
        return $this->belongsTo(PickingItem::class);
    }

    /**
     * Get the dispatch route.
     */
    public function dispatchRoute(): BelongsTo
    {
        return $this->belongsTo(DispatchRoute::class);
    }

    /**
     * Check if item is dispatched.
     */
    public function isDispatched(): bool
    {
        return in_array($this->status, ['dispatched', 'delivered']);
    }

    /**
     * Check if item is delivered.
     */
    public function isDelivered(): bool
    {
        return $this->status === 'delivered';
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'delivered' => 'success',
            'dispatched' => 'warning',
            'ready' => 'primary',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Mark item as dispatched.
     */
    public function markAsDispatched(): void
    {
        $this->update([
            'status' => 'dispatched',
            'dispatched_at' => now(),
        ]);
    }

    /**
     * Mark item as delivered.
     */
    public function markAsDelivered(string $recipientName = null, string $notes = null): void
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
            'recipient_name' => $recipientName,
            'delivery_notes' => $notes,
        ]);
    }

    /**
     * Get delivery time in hours.
     */
    public function getDeliveryTimeHoursAttribute(): ?float
    {
        if (!$this->dispatched_at || !$this->delivered_at) {
            return null;
        }
        
        return $this->dispatched_at->diffInMinutes($this->delivered_at) / 60;
    }

    /**
     * Get formatted delivery time.
     */
    public function getFormattedDeliveryTimeAttribute(): ?string
    {
        $hours = $this->delivery_time_hours;
        
        if ($hours === null) {
            return null;
        }
        
        if ($hours < 1) {
            return round($hours * 60) . ' min';
        }
        
        return round($hours, 1) . ' hours';
    }
}
