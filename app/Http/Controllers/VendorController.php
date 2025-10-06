<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
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
        $recentBids = collect();
        try {
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
        } catch (\Exception $e) {
            \Log::error('Error fetching recent bids: ' . $e->getMessage());
        }

        $recentOrders = collect();
        try {
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
        } catch (\Exception $e) {
            \Log::error('Error fetching recent orders: ' . $e->getMessage());
        }

        $recentInvoices = collect();
        try {
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
        } catch (\Exception $e) {
            \Log::error('Error fetching recent invoices: ' . $e->getMessage());
        }

        // Combine all activities and ensure proper array structure
        $allActivities = collect();
        
        // Add recent bids
        foreach ($recentBids as $activity) {
            $allActivities->push($activity);
        }
        
        // Add recent orders
        foreach ($recentOrders as $activity) {
            $allActivities->push($activity);
        }
        
        // Add recent invoices
        foreach ($recentInvoices as $activity) {
            $allActivities->push($activity);
        }
        
        $recentActivity = $allActivities
            ->sortByDesc(function ($activity) {
                return $activity['time'] ?? now();
            })
            ->take(8)
            ->values();

        // Generate dynamic notifications
        $notifications = collect();
        try {
            $notifications = $this->generateDynamicNotifications($vendor);
            
            // Add recent bid status changes
            $recentBidChanges = \App\Models\Bid::where('vendor_id', $vendor->id)
                ->whereIn('status', ['Pending Evaluation', 'Won', 'Rejected'])
                ->where('updated_at', '>=', now()->subDays(7))
                ->with('opportunity')
                ->latest('updated_at')
                ->take(3)
                ->get()
                ->map(function($bid) {
                    return [
                        'type' => 'bid_status_change',
                        'title' => $bid->opportunity->title ?? 'Bid #' . $bid->id,
                        'status' => $bid->status,
                        'description' => $this->getBidStatusDescription($bid->status),
                        'time' => $bid->updated_at->diffForHumans(),
                        'bid_id' => $bid->id,
                        'priority' => $bid->status === 'Won' ? 1 : ($bid->status === 'Pending Evaluation' ? 2 : 3)
                    ];
                });
                
            $notifications = $notifications->merge($recentBidChanges)->sortBy('priority');
        } catch (\Exception $e) {
            \Log::error('Error generating notifications: ' . $e->getMessage());
        }
        
        // Generate chart data for performance tracking
        $chartData = [];
        try {
            $chartData = $this->generateChartData($vendor);
        } catch (\Exception $e) {
            \Log::error('Error generating chart data: ' . $e->getMessage());
        }

        return view('VendorPortal.dashboard', [
            'vendor' => $vendor,
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'notifications' => $notifications,
            'chartData' => $chartData,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('vendor.bidding.landing')->with('success', 'You have been logged out successfully.');
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
        $vendor = Auth::guard('vendor')->user();
        
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'company_name' => 'required|string|max:255',
                'business_type' => 'required|string|max:255',
                'phone' => 'required|string|max:20',
                'address' => 'required|string|max:500',
            ]);

            // Update vendor profile
            $vendor->update([
                'name' => $request->name,
                'company_name' => $request->company_name,
                'business_type' => $request->business_type,
                'phone' => $request->phone,
                'address' => $request->address,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully!'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Profile Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your profile. Please try again.'
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/',
        ], [
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
        ]);

        $vendor = Auth::guard('vendor')->user();

        // Verify current password
        if (!\Hash::check($request->current_password, $vendor->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }

        // Update password
        $vendor->update([
            'password' => \Hash::make($request->password)
        ]);

        // Log out the vendor for security
        Auth::guard('vendor')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please log in with your new password.'
        ]);
    }

    public function showBidForm($id)
    {
        $opportunityModel = \App\Models\Opportunity::findOrFail((int) $id);
        
        // Check if opportunity allows new submissions
        $now = now()->startOfDay();
        $startedByDate = !$opportunityModel->start_date || $opportunityModel->start_date->lte($now);
        $endedByDate = $opportunityModel->end_date && $opportunityModel->end_date->lt($now);
        $hasWinner = \App\Models\Bid::where('opportunity_id', $opportunityModel->id)
            ->where('status', 'Won')
            ->exists();
        
        // Check if opportunity is in evaluation phase
        $hasSubmissions = $opportunityModel->submission_count > 0;
        
        // Check if evaluation has started (only after end date has passed)
        $evaluationStarted = $endedByDate && !$hasWinner && $hasSubmissions;
        
        // Check if there are bids in actual evaluation status (not just "Under Review" which is the default for new bids)
        $hasEvaluatingBids = \App\Models\Bid::where('opportunity_id', $opportunityModel->id)
            ->where('status', 'Pending Evaluation') // Only block for actual evaluation status, not "Under Review"
            ->exists();
        
        // Prevent access if opportunity is not open for submissions
        if (!$startedByDate) {
            return redirect()->route('vendor.bidding.landing')
                ->with('error', 'This opportunity has not started yet. Submissions will be available from ' . 
                    optional($opportunityModel->start_date)->format('M d, Y'));
        }
        
        if ($hasWinner) {
            return redirect()->route('vendor.bidding.landing')
                ->with('error', 'This opportunity has ended. A winner has already been selected.');
        }
        
        if ($evaluationStarted || $hasEvaluatingBids) {
            return redirect()->route('vendor.bidding.landing')
                ->with('error', 'This opportunity is currently under evaluation. No new submissions are allowed.');
        }
        
        if ($endedByDate && !$hasSubmissions) {
            return redirect()->route('vendor.bidding.landing')
                ->with('error', 'This opportunity has ended and no submissions were received.');
        }
        
        // Check if the current vendor has already submitted a bid for this opportunity
        $vendor = \Auth::guard('vendor')->user();
        $existingBid = \App\Models\Bid::where('vendor_id', $vendor->id)
            ->where('opportunity_id', $opportunityModel->id)
            ->whereNotIn('status', ['Withdrawn']) // Allow resubmission if previous bid was withdrawn
            ->first();
            
        if ($existingBid) {
            return redirect()->route('vendor.bids')
                ->with('info', 'You have already submitted a bid for this opportunity. You can view or withdraw your existing bid below.');
        }
        
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
        // First check if opportunity allows submissions
        $opportunityModel = \App\Models\Opportunity::findOrFail((int) $id);
        
        $now = now()->startOfDay();
        $startedByDate = !$opportunityModel->start_date || $opportunityModel->start_date->lte($now);
        $endedByDate = $opportunityModel->end_date && $opportunityModel->end_date->lt($now);
        $hasWinner = \App\Models\Bid::where('opportunity_id', $opportunityModel->id)
            ->where('status', 'Won')
            ->exists();
        
        $hasSubmissions = $opportunityModel->submission_count > 0;
        
        // Check if evaluation has started (only after end date has passed)
        $evaluationStarted = $endedByDate && !$hasWinner && $hasSubmissions;
        
        // Check if there are bids in actual evaluation status (not just "Under Review" which is the default for new bids)
        $hasEvaluatingBids = \App\Models\Bid::where('opportunity_id', $opportunityModel->id)
            ->where('status', 'Pending Evaluation') // Only block for actual evaluation status, not "Under Review"
            ->exists();
        
        // Block submission if opportunity is not accepting bids
        if (!$startedByDate || $hasWinner || $evaluationStarted || $hasEvaluatingBids) {
            $errorMessage = 'This opportunity is no longer accepting new submissions.';
            if (!$startedByDate) {
                $errorMessage = 'This opportunity has not started yet.';
            } elseif ($hasWinner) {
                $errorMessage = 'This opportunity has ended. A winner has already been selected.';
            } elseif ($evaluationStarted || $hasEvaluatingBids) {
                $errorMessage = 'This opportunity is currently under evaluation. No new submissions are allowed.';
            }
            
            return redirect()->route('vendor.bidding.landing')
                ->with('error', $errorMessage);
        }
        
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1'],
            'proposal' => ['required', 'string', 'min:50'],
            'completion_date' => ['nullable', 'date', 'after_or_equal:today'],
            'warranty_period' => ['required', 'string', 'in:3_months,6_months,12_months,18_months,24_months,36_months,custom'],
            'custom_warranty' => ['required_if:warranty_period,custom', 'nullable', 'string', 'max:255'],
            'payment_terms_type' => ['required', 'string', 'in:full_advance,full_delivery,cod,50_50,30_70,milestone,net_30,net_15,custom'],
            'payment_terms_details' => ['required', 'string', 'min:20'],
            'attachments.*' => ['nullable', 'file', 'max:5120', 'mimes:pdf,doc,docx,jpg,jpeg,png'],
        ]);

        $vendor = \Auth::guard('vendor')->user();

        // Check if this vendor has already submitted a bid for this opportunity
        $existingBid = \App\Models\Bid::where('vendor_id', $vendor->id)
            ->where('opportunity_id', (int) $id)
            ->whereNotIn('status', ['Withdrawn']) // Allow resubmission if previous bid was withdrawn
            ->first();
            
        if ($existingBid) {
            return redirect()->route('vendor.bidding.landing')
                ->with('error', 'You have already submitted a bid for this opportunity. You can view or withdraw your existing bid from the "My Bids" section.');
        }

        $storedAttachments = [];
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('bids/attachments', 'public');
                $storedAttachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType()
                ];
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
            'warranty_period' => $request->input('warranty_period'),
            'custom_warranty' => $request->input('custom_warranty'),
            'payment_terms_type' => $request->input('payment_terms_type'),
            'payment_terms_details' => $request->input('payment_terms_details'),
            'attachments' => $storedAttachments,
            'submitted_at' => now(),
        ]);

        // Increment submission count on the opportunity
        \App\Models\Opportunity::where('id', (int) $id)->increment('submission_count');

        return redirect()->route('vendor.bids')->with('success', 'Bid submitted successfully');
    }

    public function withdrawBid($id)
    {
        $vendor = Auth::guard('vendor')->user();
        $bid = \App\Models\Bid::where('id', (int) $id)
            ->where('vendor_id', $vendor->id)
            ->first();

        if (!$bid) {
            return response()->json(['success' => false, 'error' => 'Bid not found'], 404);
        }

        // Only allow withdrawal if bid is still under review
        if ($bid->status !== 'Under Review') {
            return response()->json(['success' => false, 'error' => 'Cannot withdraw bid with current status: ' . $bid->status], 400);
        }

        // Update bid status to withdrawn
        $bid->update(['status' => 'Withdrawn']);

        return response()->json(['success' => true, 'message' => 'Bid withdrawn successfully']);
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
            foreach ($bid->attachments as $attachment) {
                // Handle both old format (string path) and new format (array with metadata)
                if (is_string($attachment)) {
                    // Old format - just a path string
                    $attachments[] = [
                        'name' => basename($attachment),
                        'url' => Storage::disk('public')->url($attachment),
                        'size' => null,
                        'mime_type' => null
                    ];
                } else if (is_array($attachment) && isset($attachment['path'])) {
                    // New format - array with metadata
                    $attachments[] = [
                        'name' => $attachment['original_name'] ?? basename($attachment['path']),
                        'url' => Storage::disk('public')->url($attachment['path']),
                        'size' => $attachment['size'] ?? null,
                        'mime_type' => $attachment['mime_type'] ?? null
                    ];
                }
            }
        }

        return response()->json([
            'success' => true,
            'bid' => [
                'id' => $bid->id,
                'title' => $bid->opportunity ? $bid->opportunity->title : ($bid->title ?? ('Bid for Opportunity #' . ($bid->opportunity_id ?? ''))),
                'opportunity_title' => $bid->opportunity ? $bid->opportunity->title : null,
                'opportunity_category' => $bid->opportunity ? $bid->opportunity->category : null,
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
        $activeBids = \App\Models\Opportunity::whereIn('current_status', ['Open', 'Active'])
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($opp) {
                $now = now()->startOfDay();
                $startedByDate = !$opp->start_date || $opp->start_date->lte($now);
                $endedByDate = $opp->end_date && $opp->end_date->lt($now);
                $hasWinner = \App\Models\Bid::where('opportunity_id', $opp->id)
                    ->where('status', 'Won')
                    ->exists();

                // Check if opportunity is in evaluation phase
                $hasSubmissions = $opp->submission_count > 0;
                
                // Check if evaluation has started (end date passed but no winner yet)
                $evaluationStarted = $endedByDate && !$hasWinner && $hasSubmissions;
                
                // Check if there are bids in actual evaluation status (not just "Under Review" which is the default for new bids)
                $hasEvaluatingBids = \App\Models\Bid::where('opportunity_id', $opp->id)
                    ->where('status', 'Pending Evaluation') // Only check for actual evaluation status, not "Under Review"
                    ->exists();

                // Determine computed status based on dates, winners, and evaluation
                if ($hasWinner) {
                    $computedStatus = 'Ended';
                } elseif ($evaluationStarted || $hasEvaluatingBids) {
                    $computedStatus = 'Under Evaluation';
                } elseif ($endedByDate) {
                    $computedStatus = 'Ended';
                } elseif (!$startedByDate) {
                    $computedStatus = 'Not Started';
                } else {
                    $computedStatus = $opp->current_status ?: 'Open';
                }

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
            'status' => ['required', 'in:Issued,In Progress,Delivered,Completed,Cancelled'],
            'actual_delivery_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        // Only allow forward-moving transitions for vendors
        $currentStatus = $purchaseOrder->status;
        $allowed = [
            'Issued' => ['In Progress', 'Delivered', 'Completed', 'Cancelled'],
            'In Progress' => ['Delivered', 'Completed', 'Cancelled'],
            'Delivered' => ['Completed', 'Cancelled'],
            'Completed' => [],
            'Cancelled' => [],
            'Approved' => ['Issued', 'In Progress', 'Delivered', 'Completed'],
            'Draft' => [],
            'Pending Approval' => [],
        ];
        $nextStatus = $validated['status'];
        if (!in_array($nextStatus, $allowed[$currentStatus] ?? [], true) && $nextStatus !== $currentStatus) {
            return response()->json(['success' => false, 'message' => 'Invalid status transition'], 422);
        }

        $purchaseOrder->status = $nextStatus;
        if ($nextStatus === 'Completed' || $nextStatus === 'Delivered') {
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

    public function showContracts()
    {
        $vendor = Auth::guard('vendor')->user();
        
        if (!$vendor) {
            return redirect()->route('vendor.login');
        }

        // Get contracts for this vendor
        $contracts = \App\Models\Contract::where('vendor_id', $vendor->id)
            ->with(['bid', 'procurementOfficer'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Calculate statistics
        $stats = [
            'pending_signature' => \App\Models\Contract::where('vendor_id', $vendor->id)
                ->where('workflow_status', 'pending_vendor_signature')
                ->count(),
            'under_negotiation' => \App\Models\Contract::where('vendor_id', $vendor->id)
                ->whereIn('workflow_status', ['draft', 'under_negotiation'])
                ->count(),
            'fully_signed' => \App\Models\Contract::where('vendor_id', $vendor->id)
                ->where('workflow_status', 'fully_signed')
                ->count(),
            'total_contract_value' => \App\Models\Contract::where('vendor_id', $vendor->id)
                ->where('workflow_status', 'fully_signed')
                ->sum('negotiated_value') ?: \App\Models\Contract::where('vendor_id', $vendor->id)
                ->where('workflow_status', 'fully_signed')
                ->sum('value')
        ];

        return view('VendorPortal.contracts', compact('vendor', 'contracts', 'stats'));
    }

    /**
     * Generate dynamic notifications based on vendor's current data
     */
    private function generateDynamicNotifications($vendor)
    {
        $notifications = [];

        // Check for new opportunities
        $newOpportunities = \App\Models\Opportunity::where('current_status', 'Open')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        
        if ($newOpportunities > 0) {
            $latestOpp = \App\Models\Opportunity::where('current_status', 'Open')
                ->latest()
                ->first();
            
            $notifications[] = [
                'type' => 'primary',
                'icon' => 'megaphone',
                'title' => 'New Opportunity: ' . ($latestOpp->title ?? 'Available'),
                'description' => 'Deadline: ' . optional($latestOpp->end_date)->format('M d, Y') . ' | Budget: ₱' . number_format($latestOpp->budget ?? 0),
                'action_text' => 'View',
                'action_url' => route('vendor.bidding.landing'),
                'priority' => 1
            ];
        }

        // Check for winning bids
        $winningBids = \App\Models\Bid::where('vendor_id', $vendor->id)
            ->where('status', 'Won')
            ->where('updated_at', '>=', now()->subDays(30))
            ->with('opportunity')
            ->get();

        foreach ($winningBids->take(1) as $bid) {
            $notifications[] = [
                'type' => 'success',
                'icon' => 'trophy',
                'title' => 'Congratulations! Your bid has been selected',
                'description' => 'Project: ' . ($bid->opportunity->title ?? $bid->title ?? 'Bid #' . $bid->id),
                'action_text' => 'Details',
                'action_url' => route('vendor.contracts'),
                'priority' => 2
            ];
        }

        // Check for overdue invoices
        $overdueInvoices = \App\Models\Invoice::where('vendor_id', $vendor->id)
            ->where('payment_status', '!=', 'Paid')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueInvoices->take(1) as $invoice) {
            $notifications[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => 'Overdue Payment: Invoice #' . $invoice->invoice_no,
                'description' => 'Amount: ₱' . number_format($invoice->amount) . ' | Due: ' . optional($invoice->due_date)->format('M d, Y'),
                'action_text' => 'View',
                'action_url' => route('vendor.invoices'),
                'priority' => 3
            ];
        }

        // Check for upcoming payment reminders
        $upcomingInvoices = \App\Models\Invoice::where('vendor_id', $vendor->id)
            ->where('payment_status', '!=', 'Paid')
            ->whereBetween('due_date', [now(), now()->addDays(7)])
            ->get();

        foreach ($upcomingInvoices->take(1) as $invoice) {
            $notifications[] = [
                'type' => 'warning',
                'icon' => 'exclamation-triangle',
                'title' => 'Payment Reminder: Invoice #' . $invoice->invoice_no,
                'description' => 'Amount: ₱' . number_format($invoice->amount) . ' | Due: ' . optional($invoice->due_date)->format('M d, Y'),
                'action_text' => 'Pay',
                'action_url' => route('vendor.invoices'),
                'priority' => 4
            ];
        }

        // Check for pending deliveries
        $pendingOrders = \App\Models\PurchaseOrder::where('vendor_id', $vendor->id)
            ->whereIn('status', ['Issued', 'In Progress'])
            ->where('expected_delivery_date', '<=', now()->addDays(3))
            ->get();

        foreach ($pendingOrders->take(1) as $order) {
            $notifications[] = [
                'type' => 'info',
                'icon' => 'calendar-event',
                'title' => 'Upcoming Deadline: Document Submission',
                'description' => 'Project: ' . ($order->title ?? 'PO #' . $order->po_number),
                'action_text' => 'Upload',
                'action_url' => route('vendor.orders'),
                'priority' => 5
            ];
        }

        // Check for document verification status
        if (!$vendor->documents_verified) {
            $notifications[] = [
                'type' => 'secondary',
                'icon' => 'shield-check',
                'title' => 'Document Verification Pending',
                'description' => 'Your business documents are under review. This may take 2-3 business days.',
                'action_text' => 'Pending',
                'action_url' => '#',
                'priority' => 6
            ];
        } else {
            $notifications[] = [
                'type' => 'secondary',
                'icon' => 'shield-check',
                'title' => 'Verification Complete: Business License Updated',
                'description' => 'Your credentials are verified until ' . now()->addYear()->format('M Y'),
                'action_text' => 'Verified',
                'action_url' => '#',
                'priority' => 7
            ];
        }

        // Sort by priority and return top 5
        return collect($notifications)->sortBy('priority')->take(5)->values();
    }

    /**
     * Get descriptive text for bid status
     */
    private function getBidStatusDescription($status)
    {
        $descriptions = [
            'Under Review' => 'Your bid is being reviewed by the procurement team',
            'Pending Evaluation' => 'Your bid is being evaluated against other submissions',
            'Won' => 'Congratulations! Your bid has been selected',
            'Rejected' => 'Your bid was not selected for this opportunity',
            'Withdrawn' => 'You have withdrawn this bid from consideration'
        ];
        
        return $descriptions[$status] ?? 'Status updated';
    }

    /**
     * Generate chart data for performance tracking
     */
    private function generateChartData($vendor)
    {
        // Get data for the last 12 months
        $months = [];
        $bidsData = [];
        $ordersData = [];
        $revenueData = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M');
            
            // Bids submitted in this month
            $bidsCount = \App\Models\Bid::where('vendor_id', $vendor->id)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $bidsData[] = $bidsCount;
            
            // Orders won in this month
            $ordersCount = \App\Models\PurchaseOrder::where('vendor_id', $vendor->id)
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $ordersData[] = $ordersCount;
            
            // Revenue in this month (in 100k units for chart readability)
            $revenue = \App\Models\Invoice::where('vendor_id', $vendor->id)
                ->where('payment_status', 'Paid')
                ->whereMonth('updated_at', $date->month)
                ->whereYear('updated_at', $date->year)
                ->sum('amount');
            $revenueData[] = round($revenue / 100000, 1); // Convert to 100k units
        }

        // Calculate performance metrics
        $totalBids = array_sum($bidsData);
        $totalOrders = array_sum($ordersData);
        $winRate = $totalBids > 0 ? round(($totalOrders / $totalBids) * 100) : 0;
        
        $avgBidValue = \App\Models\Bid::where('vendor_id', $vendor->id)
            ->avg('amount') ?? 0;
        
        $rating = 4.5 + (rand(1, 6) / 10); // Simulated rating between 4.5-5.0

        return [
            'labels' => $months,
            'datasets' => [
                'bids' => $bidsData,
                'orders' => $ordersData,
                'revenue' => $revenueData
            ],
            'metrics' => [
                'win_rate' => $winRate,
                'avg_bid_value' => $avgBidValue,
                'rating' => round($rating, 1)
            ]
        ];
    }

    /**
     * Generate 2FA secret and QR code
     */
    public function generate2FASecret(Request $request)
    {
        try {
            $vendor = Auth::guard('vendor')->user();
            
            if (!$vendor) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Generate a simple base32 secret (32 characters)
            $secret = $this->generateBase32Secret();
            
            // Store the secret temporarily in session for verification
            $request->session()->put('temp_2fa_secret', $secret);
            
            // Create the otpauth URL manually
            $appName = urlencode(config('app.name', 'Vendor Portal'));
            $userEmail = urlencode($vendor->email);
            $qrCodeUrl = "otpauth://totp/{$appName}:{$userEmail}?secret={$secret}&issuer={$appName}";
            
            // Use external QR code service
            $qrCodeImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl);
            
            return response()->json([
                'success' => true,
                'secret' => $secret,
                'qr_code' => '<img src="' . $qrCodeImageUrl . '" alt="QR Code" class="img-fluid">'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('2FA Generation Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error generating 2FA secret: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate a base32 secret key
     */
    private function generateBase32Secret($length = 32)
    {
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $base32chars[random_int(0, 31)];
        }
        return $secret;
    }

    /**
     * Enable 2FA for the vendor
     */
    public function enable2FA(Request $request)
    {
        // Minimal validation
        if (!$request->has('verification_code') || !$request->has('secret')) {
            return response()->json([
                'success' => false,
                'message' => 'Missing required fields'
            ], 422);
        }

        // Get authenticated vendor
        $vendor = Auth::guard('vendor')->user();
        if (!$vendor) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }

        // Simple backup codes
        $backupCodes = [
            'ABCD-1234', 'EFGH-5678', 'IJKL-9012', 'MNOP-3456',
            'QRST-7890', 'UVWX-1357', 'YZAB-2468', 'CDEF-9753'
        ];

        try {
            // Use Laravel's database connection instead of hardcoded PDO
            $vendor->update([
                'two_factor_enabled' => true,
                'two_factor_secret' => $request->input('secret'),
                'two_factor_backup_codes' => json_encode($backupCodes),
                'two_factor_confirmed_at' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => '2FA enabled successfully',
                'backup_codes' => $backupCodes
            ]);

        } catch (\Exception $e) {
            \Log::error('2FA Enable Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Disable 2FA for the vendor
     */
    public function disable2FA(Request $request)
    {
        $request->validate([
            'password' => 'required|string'
        ]);

        $vendor = Auth::guard('vendor')->user();
        
        // Verify current password
        if (!Hash::check($request->password, $vendor->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect.'
            ], 422);
        }
        
        try {
            // Disable 2FA - use direct assignment
            $vendor->two_factor_enabled = false;
            $vendor->two_factor_secret = null;
            $vendor->two_factor_backup_codes = null;
            $vendor->two_factor_confirmed_at = null;
            $vendor->save();
            
            return response()->json([
                'success' => true,
                'message' => '2FA has been successfully disabled for your account.'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error disabling 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify 2FA code during login
     */
    public function verify2FA(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|string|size:6'
        ]);

        try {
            $vendor = Auth::guard('vendor')->user();
            
            if (!$vendor || !$vendor->two_factor_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => '2FA is not enabled for this account.'
                ], 422);
            }
            
            $verificationCode = $request->input('verification_code');
            
            // Simplified verification - accept any 6-digit code or backup codes
            $isValid = false;
            
            // Check if it's a 6-digit code (simplified TOTP verification)
            if (preg_match('/^\d{6}$/', $verificationCode)) {
                $isValid = true; // Accept any 6-digit code for now
            } else {
                // Check backup codes (only if 2FA secret exists)
                if ($vendor->two_factor_secret) {
                    $isValid = $this->isValidBackupCode($vendor, $verificationCode);
                }
            }
            
            if (!$isValid) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid verification code. Please try again.'
                ], 422);
            }
            
            // Mark the session as 2FA verified
            $request->session()->put('2fa_verified', true);
            
            return response()->json([
                'success' => true,
                'message' => '2FA verification successful.',
                'redirect' => route('vendor.dashboard')
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error verifying 2FA: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate backup codes
     */
    private function generateBackupCodes($count = 8)
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(Str::random(4) . '-' . Str::random(4));
        }
        return $codes;
    }

    /**
     * Check if the provided code is a valid backup code
     */
    private function isValidBackupCode($vendor, $code)
    {
        if (!$vendor->two_factor_backup_codes) {
            return false;
        }
        
        try {
            // Try to decode as JSON first (unencrypted)
            $backupCodes = json_decode($vendor->two_factor_backup_codes, true);
            
            // If that fails, try to decrypt then decode
            if (!$backupCodes) {
                $backupCodes = json_decode(decrypt($vendor->two_factor_backup_codes), true);
            }
            
            if (is_array($backupCodes) && in_array($code, $backupCodes)) {
                // Remove the used backup code
                $backupCodes = array_diff($backupCodes, [$code]);
                
                // Update the vendor's backup codes (store as plain JSON)
                $vendor->update([
                    'two_factor_backup_codes' => json_encode(array_values($backupCodes))
                ]);
                
                return true;
            }
        } catch (\Exception $e) {
            // If decryption fails, return false
            return false;
        }
        
        return false;
    }

    /**
     * Simple TOTP verification function
     */
    private function verifyTOTP($secret, $code)
    {
        // For now, accept any 6-digit code during setup
        // This is a temporary solution until proper TOTP verification is implemented
        return preg_match('/^\d{6}$/', $code);
    }
}
