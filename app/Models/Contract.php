<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_number',
        'bid_id',
        'vendor_id',
        'title',
        'description',
        'terms',
        'document_path',
        'value',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'value' => 'decimal:2',
    ];

    // Relationships
    public function bid(): BelongsTo
    {
        return $this->belongsTo(Bid::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    // Methods
    public function isActive(): bool
    {
        return $this->status === 'Active' && 
               $this->start_date <= now() && 
               $this->end_date >= now();
    }

    public function isExpired(): bool
    {
        return $this->end_date < now();
    }

    public function activate(): void
    {
        $this->update(['status' => 'Active']);
    }

    public function expire(): void
    {
        $this->update(['status' => 'Expired']);
    }
}


