<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Opportunity extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'category',
        'start_date',
        'end_date',
        'budget',
        'current_status',
        'description',
        'submission_count',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'submission_count' => 'integer',
    ];

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get the computed status based on dates and current status
     */
    public function getComputedStatusAttribute()
    {
        // If manually set to Ended or Closed, respect that
        if (in_array($this->current_status, ['Ended', 'Closed'])) {
            return $this->current_status;
        }

        // Check if opportunity has started and ended based on dates
        $now = now();
        
        if ($this->start_date && $now->lt($this->start_date)) {
            return 'Not Started';
        }
        
        if ($this->end_date && $now->gt($this->end_date)) {
            return 'Ended';
        }
        
        // If we're between start and end dates (or no dates set), use current_status
        return $this->current_status ?? 'Open';
    }

    /**
     * Check if opportunity is currently active for bidding
     */
    public function isActive()
    {
        $status = $this->getComputedStatusAttribute();
        return $status === 'Open';
    }
}


