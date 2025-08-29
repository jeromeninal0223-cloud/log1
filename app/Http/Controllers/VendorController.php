<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    public function showLoginForm()
    {
        return view('VendorPortal.login');
    }

    /**
     * Vendor: Get invoice details (ensures vendor owns the invoice)
     */
    public function getVendorInvoiceDetails(\App\Models\Invoice $invoice)
    {
        $vendor = \Auth::guard('vendor')->user();
        if (!$vendor || $invoice->vendor_id !== $vendor->id) {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        return response()->json([
            'success' => true,
            'invoice' => [
                'id' => $invoice->id,
                'invoice_no' => $invoice->invoice_no,
                'vendor_name' => $invoice->vendor_name,
                'po_number' => $invoice->po_number,
                'amount' => (float) $invoice->amount,
                'status' => $invoice->status,
                'payment_status' => $invoice->payment_status,
                'issued_date' => optional($invoice->issued_date)->format('Y-m-d'),
                'due_date' => optional($invoice->due_date)->format('Y-m-d'),
                'notes' => $invoice->notes,
            ],
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        
        if (Auth::guard('vendor')->attempt($credentials)) {
            $vendor = Auth::guard('vendor')->user();
            
            // Check if vendor is active or approved (case-insensitive)
            $statusLower = strtolower((string) $vendor->status);
            if (!in_array($statusLower, ['active', 'approved'], true)) {
                Auth::guard('vendor')->logout();
                
                $message = match ($statusLower) {
                    'pending' => 'Your account is pending approval. Please wait for admin approval.',
                    'suspended' => 'Your account has been suspended. Please contact support.',
                    default => 'Your account is not active. Please contact support.'
                };
                
                return back()->withErrors([
                    'email' => $message,
                ]);
            }
            
            $request->session()->regenerate();
            return redirect()->intended(route('vendor.dashboard'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function showRegisterForm()
    {
        return view('VendorPortal.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:vendors',
            'password' => 'required|string|min:8|confirmed',
            'company_name' => 'required|string|max:255',
            'business_type' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'business_license' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'tax_certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'insurance_certificate' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'additional_documents.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Handle file uploads
        $businessLicensePath = null;
        $taxCertificatePath = null;
        $insuranceCertificatePath = null;
        $additionalDocumentsPaths = [];

        // Store business license
        if ($request->hasFile('business_license')) {
            $businessLicensePath = $request->file('business_license')->store('vendor-documents/business-licenses', 'public');
        }

        // Store tax certificate
        if ($request->hasFile('tax_certificate')) {
            $taxCertificatePath = $request->file('tax_certificate')->store('vendor-documents/tax-certificates', 'public');
        }

        // Store insurance certificate (optional)
        if ($request->hasFile('insurance_certificate')) {
            $insuranceCertificatePath = $request->file('insurance_certificate')->store('vendor-documents/insurance-certificates', 'public');
        }

        // Store additional documents (optional, multiple files)
        if ($request->hasFile('additional_documents')) {
            foreach ($request->file('additional_documents') as $file) {
                $additionalDocumentsPaths[] = $file->store('vendor-documents/additional', 'public');
            }
        }

        \App\Models\Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'company_name' => $request->company_name,
            'business_type' => $request->business_type,
            'phone' => $request->phone,
            'address' => $request->address,
            'business_license_path' => $businessLicensePath,
            'tax_certificate_path' => $taxCertificatePath,
            'insurance_certificate_path' => $insuranceCertificatePath,
            'additional_documents_paths' => !empty($additionalDocumentsPaths) ? json_encode($additionalDocumentsPaths) : null,
            'documents_verified' => false,
            'status' => 'Pending', // Default status for approval workflow
        ]);

        return redirect()->route('vendor.login')->with('success', 'Registration successful! Your account and documents are pending approval. Please wait for admin approval before you can login.');
    }

    public function dashboard()
    {
        $vendor = Auth::guard('vendor')->user();
        $stats = [
            'total_bids' => \App\Models\Bid::where('vendor_id', $vendor->id)->count(),
            // Consider active orders as anything not Cancelled
            'total_orders' => \App\Models\PurchaseOrder::where('vendor_id', $vendor->id)
                ->where('status', '!=', 'Cancelled')
                ->count(),
            'pending_invoices' => \App\Models\Invoice::where('vendor_id', $vendor->id)
                ->whereIn('payment_status', ['Unpaid', 'Partial'])
                ->count(),
            // Sum of Paid invoices as total revenue
            'total_revenue' => (float) \App\Models\Invoice::where('vendor_id', $vendor->id)
                ->where('payment_status', 'Paid')
                ->sum('amount'),
        ];

        // Recent activity (latest events across bids, orders, invoices)
        $recentBids = \App\Models\Bid::where('vendor_id', $vendor->id)
            ->orderByDesc('submitted_at')
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function ($bid) {
                $time = $bid->submitted_at ?: $bid->created_at;
                return [
                    'type' => 'bid',
                    'title' => 'Bid Submitted',
                    'description' => $bid->title ?: 'Bid #' . $bid->id,
                    'time' => $time,
                    'color' => 'success',
                ];
            });

        $recentOrders = \App\Models\PurchaseOrder::where('vendor_id', $vendor->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function ($po) {
                $status = (string) $po->status;
                $color = match ($status) {
                    'Issued' => 'primary',
                    'In Progress' => 'primary',
                    'Completed' => 'success',
                    'Cancelled' => 'secondary',
                    default => 'info',
                };
                return [
                    'type' => 'order',
                    'title' => 'Order ' . ($po->status ?: 'Updated'),
                    'description' => 'PO ' . ($po->po_number ?: ('#' . $po->id)) . ' - ' . ($po->title ?: 'Order'),
                    'time' => $po->updated_at ?: $po->created_at,
                    'color' => $color,
                ];
            });

        $recentInvoices = \App\Models\Invoice::where('vendor_id', $vendor->id)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(function ($inv) {
                $pay = (string) ($inv->payment_status ?: 'Unpaid');
                $color = match ($pay) {
                    'Paid' => 'success',
                    'Partial' => 'info',
                    default => 'warning',
                };
                return [
                    'type' => 'invoice',
                    'title' => 'Invoice ' . $pay,
                    'description' => 'Invoice ' . ($inv->invoice_no ?: ('#' . $inv->id)) . ' for PO ' . ($inv->po_number ?: '-'),
                    'time' => $inv->updated_at ?: $inv->created_at,
                    'color' => $color,
                ];
            });

        $recentActivity = $recentBids
            ->merge($recentOrders)
            ->merge($recentInvoices)
            ->sortByDesc(fn ($e) => $e['time'])
            ->take(8)
            ->values();

        return view('VendorPortal.dashboard', [
            'vendor' => $vendor,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('vendor.login')->with('success', 'You have been logged out successfully.');
    }

    public function showBids()
    {
        $vendor = Auth::guard('vendor')->user();
        $bids = \App\Models\Bid::where('vendor_id', $vendor->id)->with('opportunity')->latest()->get();
        return view('VendorPortal.bids', compact('bids'));
    }

    public function showOrders()
    {
        $vendor = \Auth::guard('vendor')->user();
        $orders = \App\Models\PurchaseOrder::where('vendor_id', $vendor->id)
            ->with(['contract', 'vendor'])
            ->latest()
            ->get();
        
        return view('VendorPortal.vendor_orders', compact('orders'));
    }

    public function showInvoices()
    {
        $vendor = \Auth::guard('vendor')->user();
        $invoices = collect();
        if ($vendor) {
            $invoices = \App\Models\Invoice::where('vendor_id', $vendor->id)
                ->orderByDesc('issued_date')
                ->orderByDesc('id')
                ->get()
                ->map(function ($invoice) {
                    // Get purchase order items for this invoice
                    $purchaseOrder = \App\Models\PurchaseOrder::where('po_number', $invoice->po_number)->first();
                    $itemNames = [];
                    
                    if ($purchaseOrder && $purchaseOrder->items) {
                        $itemNames = $purchaseOrder->items->pluck('item_name')->toArray();
                    }
                    
                    $invoice->item_names = !empty($itemNames) ? implode(', ', $itemNames) : 'Services rendered';
                    return $invoice;
                });
        }
        return view('VendorPortal.vendor_invoices', compact('invoices'));
    }

    public function showProfile()
    {
        $vendor = Auth::guard('vendor')->user();
        return view('VendorPortal.profile', compact('vendor'));
    }

    public function updateProfile(Request $request)
    {
        // Logic for updating vendor profile
        return response()->json(['success' => true, 'message' => 'Profile updated successfully']);
    }

    public function showBidForm($id)
    {
        $opportunityModel = \App\Models\Opportunity::findOrFail((int) $id);
        $opportunity = [
            'id' => $opportunityModel->id,
            'title' => $opportunityModel->title,
            'bid_number' => 'OPP-' . str_pad((string) $opportunityModel->id, 4, '0', STR_PAD_LEFT),
            'category' => $opportunityModel->category,
            'budget' => $opportunityModel->budget,
            'description' => $opportunityModel->description,
            'start_date' => optional($opportunityModel->start_date)->toDateString(),
            'end_date' => optional($opportunityModel->end_date)->toDateString(),
            'submission_count' => $opportunityModel->submission_count,
            'current_status' => $opportunityModel->current_status,
        ];
        return view('VendorPortal.bid_form', compact('opportunity'));
    }

    public function submitBid($id, Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'proposal' => ['required', 'string', 'min:50'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:today'],
            'attachments.*' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $vendor = \Auth::guard('vendor')->user();

        $storedAttachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $storedAttachments[] = $file->store('bids/attachments', 'public');
            }
        }

        \App\Models\Bid::create([
            'vendor_id' => $vendor->id,
            'opportunity_id' => (int) $id,
            'title' => 'Bid for Opportunity #' . $id,
            'description' => $request->input('proposal'),
            'category' => 'Logistics & Transportation',
            'amount' => $request->input('amount'),
            'status' => 'Under Review',
            'completion_date' => $request->input('completion_date'),
            'attachments' => $storedAttachments,
            'submitted_at' => now(),
        ]);

        // Increment submission count on the opportunity
        \App\Models\Opportunity::where('id', (int) $id)->increment('submission_count');

        return redirect()->route('vendor.bids')->with('success', 'Bid submitted successfully');
    }

    public function getBidDetails($id)
    {
        $vendor = \Auth::guard('vendor')->user();
        $bid = \App\Models\Bid::where('id', (int) $id)
            ->where('vendor_id', $vendor->id)
            ->with('opportunity')
            ->first();

        if (!$bid) {
            return response()->json(['success' => false, 'error' => 'Bid not found'], 404);
        }

        $attachments = [];
        if (is_array($bid->attachments)) {
            foreach ($bid->attachments as $path) {
                $attachments[] = [
                    'name' => basename($path),
                    'url' => Storage::disk('public')->url($path),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'bid' => [
                'id' => $bid->id,
                'title' => $bid->opportunity ? $bid->opportunity->title : ($bid->title ?? ('Bid for Opportunity #' . ($bid->opportunity_id ?? ''))),
                'amount' => (float) $bid->amount,
                'status' => $bid->status ?? 'Under Review',
                'proposal' => $bid->description ?? '',
                'submitted_at' => optional($bid->submitted_at)->toDateTimeString(),
                'completion_date' => optional($bid->completion_date)->toDateString(),
                'attachments' => $attachments,
            ],
        ]);
    }

    public function showBiddingLanding()
    {
        $isLoggedIn = \Auth::guard('vendor')->check();
        $vendor = \Auth::guard('vendor')->user();
        $activeBids = \App\Models\Opportunity::where('current_status', 'Open')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($opp) {
                $now = now()->startOfDay();
                $endedByDate = $opp->end_date && $opp->end_date->lt($now);
                $hasWinner = \App\Models\Bid::where('opportunity_id', $opp->id)
                    ->where('status', 'Won')
                    ->exists();

                $computedStatus = ($hasWinner || $endedByDate) ? 'Ended' : ($opp->current_status ?: 'Open');

                return [
                    'id' => $opp->id,
                    'title' => $opp->title,
                    'category' => $opp->category,
                    'budget' => $opp->budget,
                    'submission_count' => $opp->submission_count,
                    'start_date' => optional($opp->start_date)->toDateString(),
                    'end_date' => optional($opp->end_date)->toDateString(),
                    'current_status' => $computedStatus,
                ];
            })
            ->toArray();
        return view('VendorPortal.bidding_landing', compact('isLoggedIn', 'vendor', 'activeBids'));
    }

    public function getVendors()
    {
        $vendors = \App\Models\Vendor::all();
        return response()->json(['vendors' => $vendors]);
    }

    public function approveVendor($id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        $vendor->update(['status' => 'Active']);
        return response()->json(['success' => true, 'message' => 'Vendor approved successfully']);
    }

    public function suspendVendor($id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        $vendor->update(['status' => 'Suspended']);
        return response()->json(['success' => true, 'message' => 'Vendor suspended successfully']);
    }

    public function activateVendor($id)
    {
        $vendor = \App\Models\Vendor::findOrFail($id);
        $vendor->update(['status' => 'Active']);
        return response()->json(['success' => true, 'message' => 'Vendor activated successfully']);
    }

    public function approveAllVendors()
    {
        \App\Models\Vendor::where('status', 'Pending')->update(['status' => 'Active']);
        return response()->json(['success' => true, 'message' => 'All pending vendors approved successfully']);
    }

    /**
     * Vendor: Get PO details (ensures vendor owns the order)
     */
    public function getVendorOrderDetails(PurchaseOrder $purchaseOrder)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor || $purchaseOrder->vendor_id !== $vendor->id) {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        $purchaseOrder->load(['contract', 'vendor', 'items']);

        return response()->json([
            'success' => true,
            'purchase_order' => [
                'id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'title' => $purchaseOrder->title,
                'description' => $purchaseOrder->description,
                'status' => $purchaseOrder->status,
                'total_amount' => (float) $purchaseOrder->total_amount,
                'expected_delivery_date' => optional($purchaseOrder->expected_delivery_date)->toDateString(),
                'actual_delivery_date' => optional($purchaseOrder->actual_delivery_date)->toDateString(),
                'vendor' => [
                    'id' => $purchaseOrder->vendor->id,
                    'name' => $purchaseOrder->vendor->name,
                    'company_name' => $purchaseOrder->vendor->company_name,
                ],
            ],
        ]);
    }

    /**
     * Vendor: Update delivery status for own PO
     */
    public function updateVendorDeliveryStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor || $purchaseOrder->vendor_id !== $vendor->id) {
            return response()->json(['success' => false, 'message' => 'Not authorized'], 403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:Issued,In Progress,Completed,Cancelled'],
            'actual_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Only allow forward-moving transitions for vendors
        $currentStatus = $purchaseOrder->status;
        $allowed = [
            'Issued' => ['In Progress', 'Completed', 'Cancelled'],
            'In Progress' => ['Completed', 'Cancelled'],
            'Completed' => [],
            'Cancelled' => [],
            'Approved' => ['Issued', 'In Progress', 'Completed'],
            'Draft' => [],
            'Pending Approval' => [],
        ];
        $nextStatus = $validated['status'];
        if (!in_array($nextStatus, $allowed[$currentStatus] ?? [], true) && $nextStatus !== $currentStatus) {
            return response()->json(['success' => false, 'message' => 'Invalid status transition'], 422);
        }

        $purchaseOrder->status = $nextStatus;
        if ($nextStatus === 'Completed') {
            $purchaseOrder->actual_delivery_date = $validated['actual_delivery_date'] ?? now()->toDateString();
        }
        if (array_key_exists('notes', $validated)) {
            $purchaseOrder->notes = $validated['notes'];
        }
        $purchaseOrder->save();

        // If order completed, auto-generate an invoice for the vendor if none exists yet
        if ($nextStatus === 'Completed') {
            $vendor = Auth::guard('vendor')->user();
            \Log::info('Invoice generation check', [
                'po_id' => $purchaseOrder->id,
                'po_number' => $purchaseOrder->po_number,
                'vendor_id' => $vendor ? $vendor->id : 'null',
                'total_amount' => $purchaseOrder->total_amount
            ]);
            
            if ($vendor) {
                $hasInvoice = \App\Models\Invoice::where('po_number', $purchaseOrder->po_number)->exists();
                \Log::info('Invoice exists check', [
                    'po_number' => $purchaseOrder->po_number,
                    'has_invoice' => $hasInvoice
                ]);
                
                if (!$hasInvoice) {
                    try {
                        $invoice = \App\Models\Invoice::create([
                            'invoice_no' => 'INV-' . ($purchaseOrder->po_number ?: str_pad((string) $purchaseOrder->id, 6, '0', STR_PAD_LEFT)),
                            'vendor_id' => $vendor->id,
                            'vendor_name' => $vendor->company_name ?: $vendor->name,
                            'po_number' => $purchaseOrder->po_number,
                            'amount' => (float) ($purchaseOrder->total_amount ?? 0),
                            'status' => 'Submitted',
                            'payment_status' => 'Unpaid',
                            'issued_date' => now()->toDateString(),
                            'due_date' => now()->addDays(30)->toDateString(),
                            'notes' => 'Auto-generated from completed PO #' . ($purchaseOrder->po_number ?? $purchaseOrder->id),
                        ]);
                        \Log::info('Invoice created successfully', ['invoice_id' => $invoice->id]);
                    } catch (\Exception $e) {
                        \Log::error('Invoice creation failed', [
                            'error' => $e->getMessage(),
                            'po_number' => $purchaseOrder->po_number,
                            'vendor_id' => $vendor->id
                        ]);
                    }
                }
            } else {
                \Log::warning('No vendor found for invoice generation');
            }
        }

        return response()->json(['success' => true, 'message' => 'Delivery status updated', 'status' => $purchaseOrder->status]);
    }
}
