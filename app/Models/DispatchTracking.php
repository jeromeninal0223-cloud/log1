<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DispatchTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'dispatch_schedule_id',
        'status',
        'description',
        'current_latitude',
        'current_longitude',
        'current_address',
        'timestamp',
        'updated_by',
        'additional_data',
    ];

    protected $casts = [
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'timestamp' => 'datetime',
        'additional_data' => 'array',
    ];

    /**
     * Get the dispatch schedule.
     */
    public function dispatchSchedule(): BelongsTo
    {
        return $this->belongsTo(DispatchSchedule::class);
    }

    /**
     * Get the user who updated the tracking.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get current coordinates as array.
     */
    public function getCurrentCoordinatesAttribute(): ?array
    {
        if (!$this->current_latitude || !$this->current_longitude) {
            return null;
        }
        
        return [
            'lat' => (float) $this->current_latitude,
            'lng' => (float) $this->current_longitude,
        ];
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'arrived' => 'info',
            'en_route' => 'warning',
            'started' => 'primary',
            'delayed' => 'warning',
            'failed' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get formatted timestamp.
     */
    public function getFormattedTimestampAttribute(): string
    {
        return $this->timestamp->format('M j, Y g:i A');
    }

    /**
     * Get time ago format.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->timestamp->diffForHumans();
    }

    /**
     * Check if tracking has location data.
     */
    public function hasLocation(): bool
    {
        return $this->current_coordinates !== null;
    }

    /**
     * Create tracking record with location.
     */
    public static function createWithLocation(
        int $scheduleId,
        string $status,
        string $description,
        ?float $lat = null,
        ?float $lng = null,
        ?string $address = null,
        ?int $userId = null
    ): self {
        return self::create([
            'dispatch_schedule_id' => $scheduleId,
            'status' => $status,
            'description' => $description,
            'current_latitude' => $lat,
            'current_longitude' => $lng,
            'current_address' => $address,
            'timestamp' => now(),
            'updated_by' => $userId ?? auth()->id(),
        ]);
    }
}
