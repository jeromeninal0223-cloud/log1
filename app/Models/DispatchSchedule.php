<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DispatchSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'title',
        'scheduled_datetime',
        'priority',
        'status',
        'driver_name',
        'vehicle_info',
        'special_instructions',
        'total_items',
        'estimated_duration_minutes',
        'total_distance_km',
        'started_at',
        'completed_at',
        'created_by',
        'assigned_to',
        'metadata',
    ];

    protected $casts = [
        'scheduled_datetime' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'total_distance_km' => 'decimal:2',
        'metadata' => 'array',
    ];

    /**
     * Get the user who created the schedule.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user assigned to the schedule.
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the dispatch items for this schedule.
     */
    public function dispatchItems(): HasMany
    {
        return $this->hasMany(DispatchItem::class);
    }

    /**
     * Get the tracking records for this schedule.
     */
    public function trackingRecords(): HasMany
    {
        return $this->hasMany(DispatchTracking::class)->orderBy('timestamp', 'desc');
    }

    /**
     * Get the latest tracking record.
     */
    public function latestTracking(): HasMany
    {
        return $this->hasMany(DispatchTracking::class)->latest('timestamp')->limit(1);
    }

    /**
     * Check if schedule is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['scheduled', 'in_progress']);
    }

    /**
     * Check if schedule is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
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
            default => 'primary',
        };
    }

    /**
     * Get status badge color.
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'completed' => 'success',
            'in_progress' => 'warning',
            'scheduled' => 'primary',
            'cancelled' => 'danger',
            'delayed' => 'warning',
            default => 'secondary',
        };
    }

    /**
     * Get formatted scheduled time.
     */
    public function getFormattedScheduledTimeAttribute(): string
    {
        return $this->scheduled_datetime->format('M j, Y g:i A');
    }

    /**
     * Get formatted duration.
     */
    public function getFormattedDurationAttribute(): string
    {
        if (!$this->estimated_duration_minutes) {
            return 'Not estimated';
        }
        
        if ($this->estimated_duration_minutes < 60) {
            return $this->estimated_duration_minutes . ' min';
        }
        
        $hours = floor($this->estimated_duration_minutes / 60);
        $minutes = $this->estimated_duration_minutes % 60;
        
        return $hours . 'h ' . ($minutes > 0 ? $minutes . 'm' : '');
    }

    /**
     * Get completed items count.
     */
    public function getCompletedItemsCountAttribute(): int
    {
        return $this->dispatchItems()->where('status', 'delivered')->count();
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentageAttribute(): float
    {
        if ($this->total_items === 0) return 0;
        
        return round(($this->completed_items_count / $this->total_items) * 100, 2);
    }

    /**
     * Start the dispatch.
     */
    public function start(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        $this->trackingRecords()->create([
            'status' => 'started',
            'description' => 'Dispatch started',
            'timestamp' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Complete the dispatch.
     */
    public function complete(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->trackingRecords()->create([
            'status' => 'completed',
            'description' => 'Dispatch completed successfully',
            'timestamp' => now(),
            'updated_by' => auth()->id(),
        ]);
    }

    /**
     * Generate unique schedule ID.
     */
    public static function generateScheduleId(): string
    {
        return 'DSP-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
