<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Invoice;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'contract_id',
        'vendor_id',
        'title',
        'description',
        'total_amount',
        'status',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'delivery_address',
        'payment_terms',
        'currency',
        'notes',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'order_date' => 'date',
        'expected_delivery_date' => 'date',
        'actual_delivery_date' => 'date',
        'approved_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    // Scopes
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'Pending Approval');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', ['Issued', 'In Progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    // Methods
    protected static function booted(): void
    {
        static::updated(function (PurchaseOrder $purchaseOrder): void {
            if ($purchaseOrder->wasChanged('status') && $purchaseOrder->status === 'Completed') {
                \Log::info('PurchaseOrder booted method triggered', [
                    'po_id' => $purchaseOrder->id,
                    'po_number' => $purchaseOrder->po_number,
                    'status' => $purchaseOrder->status
                ]);
                $purchaseOrder->syncContractStatusOnCompletion();
            }
        });
    }
    public function canBeApproved(): bool
    {
        return $this->status === 'Pending Approval';
    }

    public function canBeIssued(): bool
    {
        return $this->status === 'Approved';
    }

    public function approve(User $user): void
    {
        $this->update([
            'status' => 'Approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);
    }

    public function issue(): void
    {
        $this->update(['status' => 'Issued']);
    }

    public function markInProgress(): void
    {
        $this->update(['status' => 'In Progress']);
    }

    public function complete(): void
    {
        $this->update(['status' => 'Completed']);
        $this->syncContractStatusOnCompletion();
    }

    public function cancel(): void
    {
        $this->update(['status' => 'Cancelled']);
    }

    // Generate PO number
    public static function generatePoNumber(): string
    {
        $lastPo = self::latest()->first();
        $lastNumber = $lastPo ? intval(substr($lastPo->po_number, 3)) : 0;
        return 'PO-' . str_pad($lastNumber + 1, 6, '0', STR_PAD_LEFT);
    }

    public function syncContractStatusOnCompletion(): void
    {
        $contract = $this->contract()->with('purchaseOrders')->first();
        if (!$contract) {
            return;
        }

        $hasOpenOrders = $contract->purchaseOrders()
            ->whereIn('status', ['Draft', 'Pending Approval', 'Approved', 'Issued', 'In Progress'])
            ->exists();

        // If there are no open orders, also ensure all invoices for this contract's POs are paid
        if (!$hasOpenOrders) {
            $poNumbers = $contract->purchaseOrders()->pluck('po_number');
            $hasUnpaidInvoices = Invoice::whereIn('po_number', $poNumbers)
                ->where(function($q){
                    $q->whereNull('payment_status')
                      ->orWhere('payment_status', '!=', 'Paid');
                })
                ->exists();

            if ($hasUnpaidInvoices) {
                return; // wait until all invoices are paid
            }
            $contract->update([
                'status' => 'Completed',
                'end_date' => $contract->end_date ?: now()->toDateString(),
            ]);
        }
    }
}
