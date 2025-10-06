<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProcurementPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'procurement_code',
        'procurement_title',
        'category',
        'priority',
        'planning_start_date',
        'required_delivery_date',
        'duration_days',
        'delivery_location',
        'requesting_department',
        'estimated_quantity',
        'unit_of_measure',
        'procurement_officer',
        'estimated_budget',
        'max_budget',
        'description',
        'technical_requirements',
        'status',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'planning_start_date' => 'date',
        'required_delivery_date' => 'date',
        'estimated_budget' => 'decimal:2',
        'max_budget' => 'decimal:2',
        'duration_days' => 'integer',
        'estimated_quantity' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->procurement_code)) {
                $model->procurement_code = static::generateProcurementCode();
            }
            
            // Auto-calculate duration if dates are provided
            if ($model->planning_start_date && $model->required_delivery_date) {
                $model->duration_days = $model->planning_start_date->diffInDays($model->required_delivery_date) + 1;
            }
        });

        static::updating(function ($model) {
            // Recalculate duration if dates change
            if ($model->isDirty(['planning_start_date', 'required_delivery_date'])) {
                if ($model->planning_start_date && $model->required_delivery_date) {
                    $model->duration_days = $model->planning_start_date->diffInDays($model->required_delivery_date) + 1;
                }
            }
        });
    }

    /**
     * Generate unique procurement code
     */
    public static function generateProcurementCode(): string
    {
        $year = date('Y');
        $lastPlan = static::where('procurement_code', 'like', "PROC-{$year}-%")
            ->orderBy('procurement_code', 'desc')
            ->first();

        if ($lastPlan) {
            $lastNumber = (int) substr($lastPlan->procurement_code, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return "PROC-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the user who created this procurement plan
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this procurement plan
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get the sourcing strategies for this procurement plan
     */
    public function sourcingStrategies(): HasMany
    {
        return $this->hasMany(SourcingStrategy::class)->orderBy('phase_number');
    }

    /**
     * Get the RFQs for this procurement plan
     */
    public function rfqs(): HasMany
    {
        return $this->hasMany(Rfq::class);
    }

    /**
     * Get the budget utilization percentage
     */
    public function getBudgetUtilizationAttribute(): float
    {
        if (!$this->max_budget || !$this->estimated_budget) {
            return 0;
        }
        
        return ($this->estimated_budget / $this->max_budget) * 100;
    }

    /**
     * Check if the procurement plan is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->required_delivery_date < now() && !in_array($this->status, ['completed', 'cancelled']);
    }

    /**
     * Get days remaining until delivery
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->required_delivery_date, false));
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for filtering by priority
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for overdue plans
     */
    public function scopeOverdue($query)
    {
        return $query->where('required_delivery_date', '<', now())
            ->whereNotIn('status', ['completed', 'cancelled']);
    }
}
