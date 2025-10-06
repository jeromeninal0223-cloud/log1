<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DispatchRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'destination',
        'pickup_latitude',
        'pickup_longitude',
        'pickup_address',
        'destination_latitude',
        'destination_longitude',
        'destination_address',
        'distance_km',
        'duration_minutes',
        'is_straight_line',
        'route_coordinates',
        'route_type',
        'calculated_at',
        'created_by',
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'destination_latitude' => 'decimal:8',
        'destination_longitude' => 'decimal:8',
        'distance_km' => 'decimal:2',
        'is_straight_line' => 'boolean',
        'route_coordinates' => 'array',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get the user who created the route.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the picking item for this route.
     */
    public function pickingItem(): BelongsTo
    {
        return $this->belongsTo(PickingItem::class, 'item_id', 'item_id');
    }

    /**
     * Get dispatch items using this route.
     */
    public function dispatchItems(): HasMany
    {
        return $this->hasMany(DispatchItem::class);
    }

    /**
     * Get formatted distance.
     */
    public function getFormattedDistanceAttribute(): string
    {
        return number_format($this->distance_km, 1) . ' km';
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_minutes < 60) {
            return $this->duration_minutes . ' min';
        }
        
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;
        
        return $hours . 'h ' . $minutes . 'm';
    }

    /**
     * Get route type display name.
     */
    public function getRouteTypeDisplayAttribute(): string
    {
        return match($this->route_type) {
            'road' => $this->is_straight_line ? 'Direct Route' : 'Road Route',
            'direct' => 'Direct Route',
            'custom' => 'Custom Route',
            default => 'Unknown Route',
        };
    }

    /**
     * Get pickup coordinates as array.
     */
    public function getPickupCoordinatesAttribute(): ?array
    {
        if (!$this->pickup_latitude || !$this->pickup_longitude) {
            return null;
        }
        
        return [
            'lat' => (float) $this->pickup_latitude,
            'lng' => (float) $this->pickup_longitude,
        ];
    }

    /**
     * Get destination coordinates as array.
     */
    public function getDestinationCoordinatesAttribute(): ?array
    {
        if (!$this->destination_latitude || !$this->destination_longitude) {
            return null;
        }
        
        return [
            'lat' => (float) $this->destination_latitude,
            'lng' => (float) $this->destination_longitude,
        ];
    }

    /**
     * Check if route has valid coordinates.
     */
    public function hasValidCoordinates(): bool
    {
        return $this->pickup_coordinates && $this->destination_coordinates;
    }

    /**
     * Get route summary for display.
     */
    public function getSummaryAttribute(): string
    {
        if (!$this->distance_km || !$this->duration_minutes) {
            return 'Route not calculated';
        }
        
        return $this->route_type_display . ': ' . $this->formatted_distance . ', ' . $this->formatted_duration;
    }

    /**
     * Create route from frontend data.
     */
    public static function createFromRouteData(string $itemId, string $destination, array $routeData, int $userId): self
    {
        return self::create([
            'item_id' => $itemId,
            'destination' => $destination,
            'pickup_latitude' => $routeData['pickup']['lat'] ?? null,
            'pickup_longitude' => $routeData['pickup']['lng'] ?? null,
            'pickup_address' => $routeData['pickup']['address'] ?? null,
            'destination_latitude' => $routeData['destination']['lat'] ?? null,
            'destination_longitude' => $routeData['destination']['lng'] ?? null,
            'destination_address' => $routeData['destination']['address'] ?? null,
            'distance_km' => $routeData['distance'] ?? null,
            'duration_minutes' => $routeData['duration'] ?? null,
            'is_straight_line' => $routeData['is_straight_line'] ?? false,
            'route_coordinates' => $routeData['coordinates'] ?? null,
            'route_type' => 'road',
            'calculated_at' => now(),
            'created_by' => $userId,
        ]);
    }
}
