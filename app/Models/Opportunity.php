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
}


