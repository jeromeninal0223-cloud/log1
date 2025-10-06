<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PickingSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'user_id',
        'status',
        'started_at',
        'completed_at',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user who owns the picking session.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the picking items for the session.
     */
    public function pickingItems(): HasMany
    {
        return $this->hasMany(PickingItem::class);
    }

    /**
     * Get active picking items.
     */
    public function activeItems(): HasMany
    {
        return $this->hasMany(PickingItem::class)->whereIn('status', ['pending', 'picking', 'picked']);
    }

    /**
     * Get completed picking items.
     */
    public function completedItems(): HasMany
    {
        return $this->hasMany(PickingItem::class)->where('status', 'picked');
    }

    /**
     * Check if session is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Get total items count.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->pickingItems()->count();
    }

    /**
     * Get picked items count.
     */
    public function getPickedItemsCountAttribute(): int
    {
        return $this->pickingItems()->where('status', 'picked')->count();
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->total_items;
        if ($total === 0) return 0;
        
        return round(($this->picked_items_count / $total) * 100, 2);
    }

    /**
     * Generate unique session ID.
     */
    public static function generateSessionId(): string
    {
        return 'PS-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
