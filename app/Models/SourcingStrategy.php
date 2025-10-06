<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SourcingStrategy extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_plan_id',
        'phase_number',
        'phase_date',
        'activity',
        'responsible',
        'deliverable',
        'notes',
        'status',
    ];

    protected $casts = [
        'phase_date' => 'date',
        'phase_number' => 'integer',
    ];

    /**
     * Get the procurement plan that owns this sourcing strategy
     */
    public function procurementPlan(): BelongsTo
    {
        return $this->belongsTo(ProcurementPlan::class);
    }

    /**
     * Check if this phase is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->phase_date < now() && $this->status !== 'completed';
    }

    /**
     * Get days remaining for this phase
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->phase_date, false));
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for overdue phases
     */
    public function scopeOverdue($query)
    {
        return $query->where('phase_date', '<', now())
            ->where('status', '!=', 'completed');
    }
}
