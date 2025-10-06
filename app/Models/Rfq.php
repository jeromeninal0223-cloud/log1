<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rfq extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_code',
        'procurement_plan_id',
        'title',
        'description',
        'specifications',
        'submission_deadline',
        'evaluation_date',
        'budget_range_min',
        'budget_range_max',
        'status',
        'evaluation_criteria',
        'created_by',
    ];

    protected $casts = [
        'submission_deadline' => 'date',
        'evaluation_date' => 'date',
        'budget_range_min' => 'decimal:2',
        'budget_range_max' => 'decimal:2',
        'evaluation_criteria' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->rfq_code)) {
                $model->rfq_code = static::generateRfqCode();
            }
        });
    }

    /**
     * Generate unique RFQ code
     */
    public static function generateRfqCode(): string
    {
        $year = date('Y');
        $lastRfq = static::where('rfq_code', 'like', "RFQ-{$year}-%")
            ->orderBy('rfq_code', 'desc')
            ->first();

        if ($lastRfq) {
            $lastNumber = (int) substr($lastRfq->rfq_code, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return "RFQ-{$year}-" . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get the procurement plan that owns this RFQ
     */
    public function procurementPlan(): BelongsTo
    {
        return $this->belongsTo(ProcurementPlan::class);
    }

    /**
     * Get the user who created this RFQ
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the vendor evaluations for this RFQ
     */
    public function vendorEvaluations(): HasMany
    {
        return $this->hasMany(VendorEvaluation::class);
    }

    /**
     * Check if the RFQ submission deadline has passed
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->submission_deadline < now();
    }

    /**
     * Get days remaining until submission deadline
     */
    public function getDaysRemainingAttribute(): int
    {
        return max(0, now()->diffInDays($this->submission_deadline, false));
    }

    /**
     * Get the winning vendor evaluation
     */
    public function getWinningEvaluationAttribute()
    {
        return $this->vendorEvaluations()
            ->where('status', 'awarded')
            ->first();
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for expired RFQs
     */
    public function scopeExpired($query)
    {
        return $query->where('submission_deadline', '<', now());
    }

    /**
     * Scope for active RFQs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'published')
            ->where('submission_deadline', '>=', now());
    }
}
