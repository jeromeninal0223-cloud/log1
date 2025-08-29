<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Vendor extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_name',
        'business_type',
        'phone',
        'address',
        'business_license_path',
        'tax_certificate_path',
        'insurance_certificate_path',
        'additional_documents_paths',
        'documents_verified',
        'documents_verified_at',
        'verification_notes',
        'status',
        'registered_at',
        'approved_at',
        'suspended_at',
        'activated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'additional_documents_paths' => 'array',
            'documents_verified' => 'boolean',
            'documents_verified_at' => 'datetime',
            'registered_at' => 'datetime',
            'approved_at' => 'datetime',
            'suspended_at' => 'datetime',
            'activated_at' => 'datetime',
        ];
    }

    /**
     * Scope for pending vendors
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for active vendors
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    /**
     * Scope for suspended vendors
     */
    public function scopeSuspended($query)
    {
        return $query->where('status', 'Suspended');
    }

    /**
     * Get all bids for this vendor
     */
    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    /**
     * Get all contracts for this vendor
     */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /**
     * Get all purchase orders for this vendor
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get all invoices for this vendor
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }
}
