<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PSMInvoiceController extends Controller
{
    public function index(Request $request)
    {
        $q = Invoice::query();

        // Filters
        if ($search = $request->string('q')->toString()) {
            $q->where(function ($qq) use ($search) {
                $qq->where('invoice_no', 'like', "%$search%")
                   ->orWhere('vendor_name', 'like', "%$search%")
                   ->orWhere('po_number', 'like', "%$search%");
            });
        }
        if ($status = $request->string('status')->toString()) {
            $q->where('status', $status);
        }
        if ($payment = $request->string('payment_status')->toString()) {
            $q->where('payment_status', $payment);
        }
        if ($vendor = $request->string('vendor')->toString()) {
            $q->where('vendor_name', 'like', "%$vendor%");
        }
        if ($from = $request->date('from')) {
            $q->whereDate('issued_date', '>=', $from);
        }
        if ($to = $request->date('to')) {
            $q->whereDate('issued_date', '<=', $to);
        }
        if ($min = $request->input('min')) {
            $q->where('amount', '>=', (float) $min);
        }
        if ($max = $request->input('max')) {
            $q->where('amount', '<=', (float) $max);
        }

        $invoices = $q->orderByDesc('issued_date')->orderByDesc('id')->get();

        // Metrics
        $pendingApprovalCount = Invoice::where('status', 'Submitted')->count();
        $overdueCount = Invoice::where('status', 'Overdue')->orWhere(function($qq){
            $qq->where('payment_status', '!=', 'Paid')
               ->whereNotNull('due_date')
               ->whereDate('due_date', '<', now()->toDateString());
        })->count();
        $paidThisMonth = Invoice::where('payment_status', 'Paid')
            ->whereMonth('updated_at', now()->month)
            ->whereYear('updated_at', now()->year)
            ->count();
        $totalOutstanding = Invoice::whereIn('payment_status', ['Unpaid','Partial'])->sum('amount');

        return view('PSM.invoice', compact(
            'invoices',
            'pendingApprovalCount',
            'overdueCount',
            'paidThisMonth',
            'totalOutstanding'
        ));
    }

    public function show(Invoice $invoice)
    {
        return response()->json($invoice);
    }

    public function download(Invoice $invoice)
    {
        return response('Download not implemented', 501);
    }

    public function recordPayment(Invoice $invoice)
    {
        $invoice->payment_status = 'Paid';
        if ($invoice->status === 'Submitted') {
            $invoice->status = 'Approved';
        }
        $invoice->save();
        return redirect()->route('psm.invoice.index')->with('status', 'Invoice marked as paid.');
    }

    public function export(Request $request): StreamedResponse
    {
        $filename = 'invoices_export_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($request) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice #', 'Vendor', 'PO #', 'Amount', 'Status', 'Payment', 'Issued', 'Due']);

            $q = Invoice::query();
            // Reuse basic filters
            if ($search = $request->string('q')->toString()) {
                $q->where(function ($qq) use ($search) {
                    $qq->where('invoice_no', 'like', "%$search%")
                       ->orWhere('vendor_name', 'like', "%$search%")
                       ->orWhere('po_number', 'like', "%$search%");
                });
            }
            $q->orderByDesc('issued_date')->orderByDesc('id');

            $q->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $inv) {
                    fputcsv($out, [
                        $inv->invoice_no,
                        $inv->vendor_name,
                        $inv->po_number,
                        number_format($inv->amount, 2),
                        $inv->status,
                        $inv->payment_status,
                        optional($inv->issued_date)->format('Y-m-d'),
                        optional($inv->due_date)->format('Y-m-d'),
                    ]);
                }
            });
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function report()
    {
        $data = [
            'total' => Invoice::count(),
            'paid' => Invoice::where('payment_status', 'Paid')->count(),
            'unpaid' => Invoice::where('payment_status', 'Unpaid')->count(),
            'partial' => Invoice::where('payment_status', 'Partial')->count(),
            'overdue' => Invoice::where(function($q){
                $q->where('status', 'Overdue')
                  ->orWhere(function($qq){
                      $qq->where('payment_status', '!=', 'Paid')
                         ->whereNotNull('due_date')
                         ->whereDate('due_date', '<', now()->toDateString());
                  });
            })->count(),
        ];
        return response()->json($data);
    }
}
