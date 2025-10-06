<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'vendor_id',
        'quoted_price',
        'proposed_delivery_date',
        'technical_score',
        'commercial_score',
        'compliance_score',
        'total_score',
        'evaluation_notes',
        'evaluation_details',
        'recommendation',
        'status',
        'evaluated_by',
        'evaluated_at',
    ];

    protected $casts = [
        'proposed_delivery_date' => 'date',
        'quoted_price' => 'decimal:2',
        'total_score' => 'decimal:2',
        'technical_score' => 'integer',
        'commercial_score' => 'integer',
        'compliance_score' => 'integer',
        'evaluation_details' => 'array',
        'evaluated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            // Auto-calculate total score if individual scores are provided
            if ($model->technical_score !== null && $model->commercial_score !== null && $model->compliance_score !== null) {
                // Weighted average: Technical 40%, Commercial 40%, Compliance 20%
                $model->total_score = ($model->technical_score * 0.4) + 
                                    ($model->commercial_score * 0.4) + 
                                    ($model->compliance_score * 0.2);
            }
        });
    }

    /**
     * Get the RFQ that owns this evaluation
     */
    public function rfq(): BelongsTo
    {
        return $this->belongsTo(Rfq::class);
    }

    /**
     * Get the vendor being evaluated
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who evaluated this vendor
     */
    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluated_by');
    }

    /**
     * Check if this is the winning evaluation
     */
    public function getIsWinnerAttribute(): bool
    {
        return $this->status === 'awarded';
    }

    /**
     * Get the evaluation grade based on total score
     */
    public function getGradeAttribute(): string
    {
        if ($this->total_score >= 90) return 'A+';
        if ($this->total_score >= 80) return 'A';
        if ($this->total_score >= 70) return 'B';
        if ($this->total_score >= 60) return 'C';
        return 'D';
    }

    /**
     * Scope for filtering by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for filtering by recommendation
     */
    public function scopeByRecommendation($query, $recommendation)
    {
        return $query->where('recommendation', $recommendation);
    }

    /**
     * Scope for top-scoring evaluations
     */
    public function scopeTopScoring($query, $limit = 3)
    {
        return $query->orderBy('total_score', 'desc')->limit($limit);
    }
}
