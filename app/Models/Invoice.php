<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\PurchaseOrder;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no',
        'vendor_id',
        'vendor_name',
        'po_number',
        'amount',
        'status',
        'payment_status',
        'due_date',
        'issued_date',
        'notes',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'issued_date' => 'datetime',
        'amount' => 'decimal:2',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    protected static function booted(): void
    {
        static::updated(function (Invoice $invoice): void {
            if ($invoice->wasChanged('payment_status') && $invoice->payment_status === 'Paid') {
                $invoice->syncContractStatusOnPayment();
            }
        });
    }

    private function syncContractStatusOnPayment(): void
    {
        // Find the purchase order for this invoice
        $purchaseOrder = PurchaseOrder::where('po_number', $this->po_number)->first();
        if (!$purchaseOrder) {
            return;
        }

        // Trigger the contract sync to check if all invoices are now paid
        $purchaseOrder->syncContractStatusOnCompletion();
    }
}
