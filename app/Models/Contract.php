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
        // Contract workflow fields
        'workflow_status',
        'negotiated_value',
        'negotiated_terms',
        'negotiation_notes',
        // Digital signature fields
        'vendor_signed_at',
        'vendor_signature_hash',
        'vendor_signature_ip',
        'vendor_signature_image',
        'procurement_signed_at',
        'procurement_signature_hash',
        'procurement_signature_ip',
        'procurement_signature_image',
        'procurement_officer_id',
        // Contract document fields
        'draft_document_path',
        'final_document_path',
        'revision_history',
        // Approval workflow
        'sent_for_review_at',
        'approved_at',
        'approved_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'value' => 'decimal:2',
        'negotiated_value' => 'decimal:2',
        'negotiated_terms' => 'array',
        'revision_history' => 'array',
        'vendor_signed_at' => 'datetime',
        'procurement_signed_at' => 'datetime',
        'sent_for_review_at' => 'datetime',
        'approved_at' => 'datetime',
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

    public function procurementOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procurement_officer_id');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
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

    // Contract signing workflow methods
    public function isFullySigned(): bool
    {
        return $this->workflow_status === 'fully_signed' && 
               !is_null($this->vendor_signed_at) && 
               !is_null($this->procurement_signed_at);
    }

    public function isPendingVendorSignature(): bool
    {
        return $this->workflow_status === 'pending_vendor_signature';
    }

    public function isPendingProcurementSignature(): bool
    {
        return $this->workflow_status === 'pending_procurement_signature';
    }

    public function canBeNegotiated(): bool
    {
        return in_array($this->workflow_status, ['draft', 'under_negotiation']);
    }

    public function getSigningProgress(): array
    {
        return [
            'draft_created' => !is_null($this->created_at),
            'terms_negotiated' => $this->workflow_status !== 'draft',
            'vendor_signed' => !is_null($this->vendor_signed_at),
            'procurement_signed' => !is_null($this->procurement_signed_at),
            'fully_executed' => $this->isFullySigned()
        ];
    }

    public function generateInvoice(): void
    {
        // Similar to PurchaseOrder invoice generation
        if ($this->isFullySigned() && $this->status === 'Active') {
            $invoice = \App\Models\Invoice::create([
                'invoice_number' => 'INV-' . $this->contract_number . '-' . time(),
                'contract_id' => $this->id,
                'vendor_id' => $this->vendor_id,
                'amount' => $this->negotiated_value ?? $this->value,
                'status' => 'Pending',
                'due_date' => now()->addDays(30),
                'description' => 'Invoice for contract: ' . $this->title,
            ]);
            
            \Log::info('Invoice generated for contract', [
                'contract_id' => $this->id,
                'invoice_id' => $invoice->id
            ]);
        }
    }

    /**
     * Generate or regenerate contract terms and conditions
     */
    public function generateTerms(): void
    {
        $contractTerms = \App\Services\ContractTermsService::generateContractTerms($this);
        $this->update(['terms' => $contractTerms]);
    }

    /**
     * Check if contract has terms populated
     */
    public function hasTerms(): bool
    {
        return !empty($this->terms) && $this->terms !== 'No terms specified';
    }
}


