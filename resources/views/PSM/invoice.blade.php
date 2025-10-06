<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Procurement & Sourcing Management - Invoice Management</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- PSM Animations -->
  <link rel="stylesheet" href="{{ asset('assets/css/psm-animations.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Enhanced table styles */
    .table-enhanced {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .table-enhanced thead th {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border: none;
      font-weight: 600;
      font-size: 0.875rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      color: #495057;
      padding: 1rem 0.75rem;
      vertical-align: middle;
      position: relative;
    }
    
    .table-enhanced tbody td {
      border: none;
      border-bottom: 1px solid #f1f3f4;
      padding: 1rem 0.75rem;
      vertical-align: middle;
      font-size: 0.9rem;
      color: #495057;
    }
    
    .table-enhanced tbody tr {
      transition: all 0.2s ease;
      background-color: #ffffff;
    }
    
    .table-enhanced tbody tr:hover {
      background-color: #f8f9fa;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .table-enhanced tbody tr:last-child td {
      border-bottom: none;
    }
    
    .sortable {
      cursor: pointer;
      user-select: none;
      position: relative;
      transition: all 0.2s ease;
    }
    
    .sortable:hover {
      background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
      color: #212529;
    }
    
    .sortable i {
      opacity: 0.5;
      transition: opacity 0.2s ease;
    }
    
    .sortable:hover i {
      opacity: 1;
    }
    
    /* Enhanced badges */
    .badge-enhanced {
      padding: 0.4rem 0.8rem;
      border-radius: 20px;
      font-weight: 500;
      font-size: 0.75rem;
      letter-spacing: 0.3px;
      text-transform: uppercase;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      white-space: nowrap;
      min-width: fit-content;
      display: inline-block;
    }
    
    .badge-status-draft {
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      color: white;
    }
    
    .badge-status-pending {
      background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
      color: white;
    }
    
    .badge-status-approved {
      background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
      color: white;
    }
    
    .badge-status-rejected {
      background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
      color: white;
    }
    
    .badge-status-paid {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
    }
    
    .badge-status-partially-paid {
      background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
      color: white;
    }
    
    .badge-status-overdue {
      background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
      color: white;
    }
    
    .badge-payment-unpaid {
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      color: white;
    }
    
    .badge-payment-partial {
      background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
      color: white;
    }
    
    .badge-payment-paid {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
    }
    
    /* Enhanced action buttons */
    .btn-action {
      padding: 0.4rem 0.6rem;
      border-radius: 8px;
      border: 2px solid transparent;
      transition: all 0.2s ease;
      font-size: 0.875rem;
      font-weight: 500;
      margin: 0 2px;
    }
    
    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn-action-view {
      background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
      color: white;
      border-color: #0d6efd;
    }
    
    .btn-action-view:hover {
      background: linear-gradient(135deg, #0b5ed7 0%, #520dc2 100%);
      color: white;
    }
    
    .btn-action-download {
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      color: white;
      border-color: #6c757d;
    }
    
    .btn-action-download:hover {
      background: linear-gradient(135deg, #5a6268 0%, #3d4142 100%);
      color: white;
    }
    
    .btn-action-payment {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
      border-color: #28a745;
    }
    
    .btn-action-payment:hover {
      background: linear-gradient(135deg, #218838 0%, #1aa085 100%);
      color: white;
    }
    
    /* Text alignment improvements */
    .text-center-custom {
      text-align: center !important;
      vertical-align: middle !important;
    }
    
    .text-left-custom {
      text-align: left !important;
      vertical-align: middle !important;
    }
    
    .text-right-custom {
      text-align: right !important;
      vertical-align: middle !important;
    }
    
    /* Invoice number styling */
    .invoice-number {
      font-family: 'Courier New', monospace;
      font-weight: 700;
      color: #6f42c1;
      background: linear-gradient(135deg, #f8f4ff 0%, #ede4ff 100%);
      padding: 0.25rem 0.5rem;
      border-radius: 6px;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
    }
    
    /* PO number styling */
    .po-number {
      font-family: 'Courier New', monospace;
      font-weight: 600;
      color: #495057;
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      padding: 0.2rem 0.4rem;
      border-radius: 4px;
      font-size: 0.75rem;
    }
    
    /* Vendor styling */
    .vendor-text {
      font-weight: 600;
      color: #212529;
    }
    
    /* Amount styling */
    .amount-text {
      font-family: 'Courier New', monospace;
      font-weight: 600;
      color: #28a745;
    }
    
    /* Date styling */
    .date-text {
      color: #6c757d;
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    .table-container {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
    }
  </style>

</head>
<body style="background-color: #f8f9fa !important;">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: var(--jetlouge-primary);">
    <div class="container-fluid">
      <button class="sidebar-toggle desktop-toggle me-3" id="desktop-toggle" title="Toggle Sidebar">
        <i class="bi bi-list fs-5"></i>
      </button>
      <a class="navbar-brand fw-bold" href="#">
        <i class="bi bi-airplane me-2"></i>Jetlouge Travels
      </a>
      <div class="d-flex align-items-center">
        <button class="sidebar-toggle mobile-toggle" id="menu-btn" title="Open Menu">
          <i class="bi bi-list fs-5"></i>
        </button>
      </div>
    </div>
  </nav>

  <!-- Sidebar -->
  <aside id="sidebar" class="bg-white border-end p-3 shadow-sm">
    <!-- Profile Section -->
    <div class="profile-section text-center">
      <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face"
           alt="Admin Profile" class="profile-img mb-2">
      <h6 class="fw-semibold mb-1">{{ Auth::user()->name }}</h6>
      <small class="text-muted">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</small>
    </div>

    
      <!-- Navigation Menu -->
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="{{ url('/dashboard') }}" class="nav-link text-dark">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-dark" data-bs-toggle="collapse" data-bs-target="#warehouseSubmenu" aria-expanded="false" aria-controls="warehouseSubmenu">
          <i class="bi bi-box-seam me-2"></i> Smart Warehousing
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="warehouseSubmenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a href="{{ url('/inventory-receipt') }}" class="nav-link text-dark small">
                <i class="bi bi-box-arrow-in-down me-2"></i> Inventory Receipt
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/storage-organization') }}" class="nav-link text-dark small">
                <i class="bi bi-grid-3x3 me-2"></i> Storage Organization
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/picking-dispatch') }}" class="nav-link text-dark small">
                <i class="bi bi-box-arrow-up me-2"></i> Picking and Dispatch
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/stock-replenishment') }}" class="nav-link text-dark small">
                <i class="bi bi-arrow-repeat me-2"></i> Stock Replenishment
              </a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#procurementSubmenu" aria-expanded="true" aria-controls="procurementSubmenu">
          <i class="bi bi-cart-plus me-2"></i> Procurement & Sourcing
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse show" id="procurementSubmenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a href="{{ url('/psm/request') }}" class="nav-link text-dark small">
                <i class="bi bi-file-earmark-text me-2"></i> Purchase Request
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/vendor') }}" class="nav-link text-dark small">
                <i class="bi bi-building me-2"></i> Vendor Management
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/bidding') }}" class="nav-link text-dark small">
                <i class="bi bi-clipboard-check me-2"></i> Bidding & RFQ
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/contract') }}" class="nav-link text-dark small">
                <i class="bi bi-file-earmark-check me-2"></i> Contract Management
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/order') }}" class="nav-link text-dark small">
                <i class="bi bi-cart-check me-2"></i> Purchase Orders
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/delivery') }}" class="nav-link text-dark small">
                <i class="bi bi-truck me-2"></i> Delivery Tracking
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/invoice') }}" class="nav-link text-dark small active">
                <i class="bi bi-receipt me-2"></i> Invoice Management
              </a>
            </li>
          </ul>
        </div>
      </li>
      @if(Auth::user()->role !== 'procurement_officer')
       <li class="nav-item">
    <a href="#" class="nav-link text-dark" data-bs-toggle="collapse" data-bs-target="#pltSubmenu" aria-expanded="false" aria-controls="pltSubmenu">
      <i class="bi bi-truck me-2"></i> Project Logistics Tracker
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="collapse" id="pltSubmenu">
      <ul class="nav flex-column ms-3">
        <li class="nav-item">
          <a href="{{ url('/plt/toursetup') }}" class="nav-link text-dark small">
            <i class="bi bi-diagram-3 me-2"></i> Project Planning
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/plt/execution') }}" class="nav-link text-dark small">
            <i class="bi bi-bar-chart-steps me-2"></i> Execution Monitoring
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/plt/closure') }}" class="nav-link text-dark small">
            <i class="bi bi-check2-circle me-2"></i> Closure
          </a>
        </li>
      </ul>
    </div>
  </li>
    <!-- Asset Life Cycle & Maintenance -->
      <li class="nav-item">
        <a href="#" class="nav-link text-dark" data-bs-toggle="collapse" data-bs-target="#assetSubmenu" aria-expanded="false" aria-controls="assetSubmenu">
          <i class="bi bi-tools me-2"></i> Asset Life Cycle & Maintenance
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="assetSubmenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a href="{{ url('/alms/assetregistration') }}" class="nav-link text-dark small">
                <i class="bi bi-calendar-check me-2"></i> Asset Register
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/alms/maintenance') }}" class="nav-link text-dark small">
                <i class="bi bi-arrow-repeat me-2"></i> Maintenance Schedule
              </a>
            </li>
             <li class="nav-item">
              <a href="{{ url('/alms/disposalretirement') }}" class="nav-link text-dark small ">
                <i class="bi bi-wrench-adjustable me-2"></i> Disposal/Retirement
              </a>
            </li>
          </ul>
        </div>
      </li>
      <li class="nav-item">
  <a href="#" class="nav-link text-dark" data-bs-toggle="collapse" data-bs-target="#documentSubmenu" aria-expanded="false" aria-controls="documentSubmenu">
    <i class="bi bi-journal-text me-2"></i> Document Tracking & Logistics Records
    <i class="bi bi-chevron-down ms-auto"></i>
  </a>
  <div class="collapse" id="documentSubmenu">
    <ul class="nav flex-column ms-3">
      <li class="nav-item">
        <a href="{{ url('/dtrs/document') }}" class="nav-link text-dark small">
          <i class="bi bi-file-earmark-text me-2"></i> Documents
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/dtrs/audits') }}" class="nav-link text-dark small">
          <i class="bi bi-clipboard-check me-2"></i> Audits
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/dtrs/version') }}" class="nav-link text-dark small">
          <i class="bi bi-clock-history me-2"></i> Version History
        </a>
      </li>
    </ul>
  </div>
</li>
      @endif
      <li class="nav-item mt-3">
        <a href="#" class="nav-link text-danger" id="logoutBtn">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
      </li>
    </ul>
  </aside>

  <!-- Overlay for mobile -->
  <div id="overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="z-index:1040; display: none;"></div>

  <!-- Main Content -->
  <main id="main-content">
    <!-- Page Header -->
    <div class="page-header-container mb-4">
      <div class="d-flex justify-content-between align-items-center page-header">
        <div class="d-flex align-items-center">
          <div class="dashboard-logo me-3">
            <i class="bi bi-box-seam fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">Invoice Management</h2>
            <p class="text-muted mb-0">Welcome back, Sarah! Manage invoices and payment processes.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/psm') }}" class="text-decoration-none">Procurement & Sourcing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Invoice Management</li>
          </ol>
        </nav>
      </div>
    </div>

    {{-- Safe defaults to avoid undefined variables when not passed from controller --}}
    @php
      $invoices = $invoices ?? null;
    @endphp

    <!-- Invoice Statistics Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                <i class="bi bi-hourglass-split"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $pendingApprovalCount ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Pending Approval</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                <i class="bi bi-exclamation-octagon"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $overdueCount ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Overdue</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-success bg-opacity-10 text-success me-3">
                <i class="bi bi-cash-coin"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $paidThisMonth ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Paid This Month</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-info bg-opacity-10 text-info me-3">
                <i class="bi bi-wallet2"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ isset($totalOutstanding) ? number_format($totalOutstanding, 2) : '0.00' }}</h3>
                <p class="text-muted mb-0 small">Total Outstanding</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Invoice List -->
    <div class="row g-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex flex-wrap gap-2 justify-content-between align-items-center">
            <h5 class="card-title mb-0">Vendor Invoices</h5>
            <form method="GET" class="d-flex align-items-center gap-2" action="{{ url()->current() }}">
              <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search invoice #, vendor, PO #">
              <select class="form-select form-select-sm" name="status">
                <option value="">All Statuses</option>
                @php $statusOptions = ['Draft','Submitted','Approved','Rejected','Partially Paid','Paid','Overdue']; @endphp
                @foreach($statusOptions as $opt)
                  <option value="{{ $opt }}" @selected(request('status')===$opt)>{{ $opt }}</option>
                @endforeach
              </select>
              <select class="form-select form-select-sm" name="payment_status">
                <option value="">Payment: Any</option>
                @foreach(['Unpaid','Partial','Paid'] as $opt)
                  <option value="{{ $opt }}" @selected(request('payment_status')===$opt)>{{ $opt }}</option>
                @endforeach
              </select>
              <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
            </form>
          </div>
          <div class="card-body">
            <div class="table-responsive table-container">
              <table class="table table-enhanced">
                <thead>
                  <tr>
                    <th class="text-center-custom sortable">Invoice # <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-left-custom sortable">Vendor <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-center-custom sortable">PO # <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-right-custom sortable">Amount <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-center-custom sortable">Due Date <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-center-custom sortable">Status <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-center-custom sortable">Payment <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-center-custom">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @php
                    $getStatusBadgeClass = function($status) {
                      return match(strtolower($status)) {
                        'draft' => 'badge-status-draft',
                        'pending' => 'badge-status-pending',
                        'approved' => 'badge-status-approved', 
                        'rejected' => 'badge-status-rejected',
                        'partially-paid' => 'badge-status-partially-paid',
                        'paid' => 'badge-status-paid',
                        'overdue' => 'badge-status-overdue',
                        default => 'badge-status-draft',
                      };
                    };
                    $getPaymentBadgeClass = function($payment) {
                      return match(strtolower($payment)) {
                        'unpaid' => 'badge-payment-unpaid',
                        'partial' => 'badge-payment-partial',
                        'paid' => 'badge-payment-paid',
                        default => 'badge-payment-unpaid',
                      };
                    };
                  @endphp

                  @php $list = $invoices ?? []; @endphp
                  @forelse($list as $inv)
                    <tr>
                      <td class="text-center-custom">
                        <span class="invoice-number">{{ $inv->invoice_no }}</span>
                      </td>
                      <td class="text-left-custom">
                        <span class="vendor-text">{{ $inv->vendor_name ?? ($inv->vendor->name ?? '—') }}</span>
                      </td>
                      <td class="text-center-custom">
                        @if($inv->po_number)
                          <span class="po-number">{{ $inv->po_number }}</span>
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                      <td class="text-right-custom">
                        <span class="amount-text">₱{{ number_format($inv->amount, 2) }}</span>
                      </td>
                      <td class="text-center-custom">
                        @php
                          $due = isset($inv->due_date) ? \Carbon\Carbon::parse($inv->due_date) : null;
                        @endphp
                        @if($due)
                          <span class="date-text">{{ $due->format('M d, Y') }}</span>
                        @else
                          <span class="text-muted">—</span>
                        @endif
                      </td>
                      <td class="text-center-custom">
                        <span class="badge-enhanced {{ $getStatusBadgeClass($inv->status ?? 'Draft') }}">{{ $inv->status ?? 'Draft' }}</span>
                      </td>
                      <td class="text-center-custom">
                        <span class="badge-enhanced {{ $getPaymentBadgeClass($inv->payment_status ?? 'Unpaid') }}">{{ $inv->payment_status ?? 'Unpaid' }}</span>
                      </td>
                      <td class="text-center-custom">
                        <div class="d-flex justify-content-center align-items-center gap-1">
                          <button type="button" class="btn btn-action btn-action-view" onclick="viewInvoiceDetails({{ json_encode($inv) }})" title="View Invoice">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button type="button" class="btn btn-action btn-action-download" onclick="downloadInvoice({{ $inv->id }})" title="Download Invoice">
                            <i class="bi bi-download"></i>
                          </button>
                          @if(($inv->payment_status ?? 'Unpaid') !== 'Paid')
                            <a href="{{ \Illuminate\Support\Facades\Route::has('psm.invoice.recordPayment') ? route('psm.invoice.recordPayment', $inv->id) : '#' }}" class="btn btn-action btn-action-payment" title="Record Payment">
                              <i class="bi bi-cash"></i>
                            </a>
                          @endif
                        </div>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="8" class="text-center text-muted py-4">
                        <i class="bi bi-receipt fs-1 d-block mb-2"></i>
                        No invoices found
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      
    </div>
  </main>

  <!-- Invoice Details Modal -->
  <div class="modal fade" id="invoiceDetailsModal" tabindex="-1" aria-labelledby="invoiceDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="invoiceDetailsModalLabel">
            <i class="bi bi-receipt me-2"></i>Invoice Details
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3" id="invoiceDetailsContent">
            <!-- Content will be populated by JavaScript -->
            <div class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2 text-muted">Loading invoice details...</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="downloadInvoiceFromModal">
            <i class="bi bi-download me-2"></i>Download PDF
          </button>
          <button type="button" class="btn btn-success" id="recordPaymentFromModal" style="display: none;">
            <i class="bi bi-cash me-2"></i>Record Payment
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar toggle functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // User is authenticated via Laravel session middleware

      // Logout functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
          e.preventDefault();

          // Use Laravel's session-based logout
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '{{ route('logout') }}';
          
          const csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = '{{ csrf_token() }}';
          form.appendChild(csrfToken);
          
          document.body.appendChild(form);
          form.submit();
          return;
          window.location.href = '{{ url('/login') }}';
        });
      }

      const menuBtn = document.getElementById('menu-btn');
      const desktopToggle = document.getElementById('desktop-toggle');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const mainContent = document.getElementById('main-content');

      // Mobile sidebar toggle
      if (menuBtn && sidebar && overlay) {
        menuBtn.addEventListener('click', (e) => {
          e.preventDefault();
          sidebar.classList.toggle('active');
          overlay.classList.toggle('show');
          document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        });
      }

      // Desktop sidebar toggle
      if (desktopToggle && sidebar && mainContent) {
        desktopToggle.addEventListener('click', (e) => {
          e.preventDefault();
          e.stopPropagation();

          const isCollapsed = sidebar.classList.contains('collapsed');

          // Toggle classes with smooth animation
          sidebar.classList.toggle('collapsed');
          mainContent.classList.toggle('expanded');

          // Store state in localStorage for persistence
          localStorage.setItem('sidebarCollapsed', !isCollapsed);

          // Trigger window resize event to help responsive components adjust
          setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
          }, 300);
        });
      }

      // Restore sidebar state from localStorage
      const savedState = localStorage.getItem('sidebarCollapsed');
      if (savedState === 'true' && sidebar && mainContent) {
        sidebar.classList.add('collapsed');
        mainContent.classList.add('expanded');
      }

      // Close mobile sidebar when clicking overlay
      if (overlay) {
        overlay.addEventListener('click', () => {
          sidebar.classList.remove('active');
          overlay.classList.remove('show');
          document.body.style.overflow = '';
        });
      }

      // Handle Smart Warehousing dropdown active states
      const warehouseDropdown = document.querySelector('[data-bs-target="#warehouseSubmenu"]');
      const warehouseSubmenu = document.getElementById('warehouseSubmenu');
      
      // Check current URL to set active states
      const currentPath = window.location.pathname;
      
      if (warehouseDropdown && warehouseSubmenu) {
        // Always keep Smart Warehousing active on SWS pages
        warehouseDropdown.classList.add('active');
        
        // Check if user manually closed the dropdown
        const userManuallyClosed = localStorage.getItem('warehouseDropdownClosed') === 'true';
        
        // Only auto-expand if user hasn't manually closed it
        if (!userManuallyClosed) {
          // If we're on any warehouse sub-page, expand the dropdown
          if (currentPath.includes('/inventory-receipt') || 
              currentPath.includes('/storage-organization') || 
              currentPath.includes('/picking-dispatch') || 
              currentPath.includes('/stock-replenishment')) {
            
            warehouseSubmenu.classList.add('show');
          }
        }
        
        // Highlight the specific sub-item (always do this regardless of dropdown state)
        if (currentPath.includes('/inventory-receipt') || 
            currentPath.includes('/storage-organization') || 
            currentPath.includes('/picking-dispatch') || 
            currentPath.includes('/stock-replenishment')) {
          
          const activeSubItem = warehouseSubmenu.querySelector(`[href="${currentPath}"]`);
          if (activeSubItem) {
            activeSubItem.classList.add('active');
          }
        }
        
        // Handle dropdown toggle
        warehouseDropdown.addEventListener('click', function(e) {
          e.preventDefault();
          const isExpanded = warehouseSubmenu.classList.contains('show');
          
          if (isExpanded) {
            warehouseSubmenu.classList.remove('show');
            localStorage.setItem('warehouseDropdownClosed', 'true');
          } else {
            warehouseSubmenu.classList.add('show');
            localStorage.setItem('warehouseDropdownClosed', 'false');
          }
        });
      }

      // Add smooth hover effects to nav links
      document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
          // Don't handle dropdown parent links here
          if (link.getAttribute('data-bs-toggle') === 'collapse') {
            return;
          }
          
          // Check if this is a warehouse sub-item
          const href = this.getAttribute('href');
          const isWarehouseSubItem = href && (
            href.includes('/inventory-receipt') || 
            href.includes('/storage-organization') || 
            href.includes('/picking-dispatch') || 
            href.includes('/stock-replenishment')
          );
          
          // Remove active class from all links
          document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
          
          // If clicking a warehouse sub-item, keep Smart Warehousing dropdown active
          if (isWarehouseSubItem) {
            if (warehouseDropdown) {
              warehouseDropdown.classList.add('active');
            }
          }
          
          // Add active class to clicked link
          this.classList.add('active');
        });
      });

      // Add loading animation to quick action buttons
      document.querySelectorAll('.btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          if (!this.classList.contains('loading')) {
            this.classList.add('loading');
            const originalText = this.innerHTML;
            this.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Loading...';

            setTimeout(() => {
              this.innerHTML = originalText;
              this.classList.remove('loading');
            }, 1500);
          }
        });
      });

      // Handle window resize for responsive behavior
      window.addEventListener('resize', () => {
        // Reset mobile sidebar state on desktop
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('active');
          overlay.classList.remove('show');
          document.body.style.overflow = '';
        }
      });
    }); // Close DOMContentLoaded
    // Handle menu active states
document.addEventListener('DOMContentLoaded', function() {
  const currentPath = window.location.pathname;
  
  // Warehouse menu elements
  const warehouseMenu = document.querySelector('[data-bs-target="#warehouseSubmenu"]');
  const warehouseSubmenu = document.getElementById('warehouseSubmenu');
  
  // Procurement menu elements
  const procurementMenu = document.querySelector('[data-bs-target="#procurementSubmenu"]');
  const procurementSubmenu = document.getElementById('procurementSubmenu');

  // Check if we're on a warehouse page
  const isWarehousePage = currentPath.includes('/inventory-receipt') || 
                         currentPath.includes('/storage-organization') || 
                         currentPath.includes('/picking-dispatch') || 
                         currentPath.includes('/stock-replenishment');

  // Check if we're on a procurement page
  const isProcurementPage = currentPath.includes('/psm/');

  // Activate the appropriate menu
  if (isWarehousePage) {
    // Activate warehouse menu
    if (warehouseMenu) {
      warehouseMenu.classList.add('active');
      warehouseSubmenu.classList.add('show');
    }
    
    // Deactivate procurement menu if it's active
    if (procurementMenu) {
      procurementMenu.classList.remove('active');
      procurementSubmenu.classList.remove('show');
    }
    
    // Highlight the specific warehouse sub-item
    if (warehouseSubmenu) {
      const activeSubItem = warehouseSubmenu.querySelector(`[href="${currentPath}"]`);
      if (activeSubItem) {
        activeSubItem.classList.add('active');
      }
    }
  } else if (isProcurementPage) {
    // Activate procurement menu
    if (procurementMenu) {
      procurementMenu.classList.add('active');
      procurementSubmenu.classList.add('show');
    }
    
    // Deactivate warehouse menu if it's active
    if (warehouseMenu) {
      warehouseMenu.classList.remove('active');
      warehouseSubmenu.classList.remove('show');
    }
    
    // Highlight the specific procurement sub-item
    if (procurementSubmenu) {
      const activeSubItem = procurementSubmenu.querySelector(`[href="${currentPath}"]`);
      if (activeSubItem) {
        activeSubItem.classList.add('active');
      }
    }
  }

  // Handle dropdown toggle clicks
  document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(menu => {
    menu.addEventListener('click', function(e) {
      // If clicking the currently active menu, do nothing
      if (this.classList.contains('active')) return;
      
      // Remove active class from all other main menus
      document.querySelectorAll('[data-bs-toggle="collapse"]').forEach(m => {
        if (m !== this) {
          m.classList.remove('active');
          const target = document.querySelector(m.getAttribute('data-bs-target'));
          if (target) target.classList.remove('show');
        }
      });
      
      // Add active class to clicked menu
      this.classList.add('active');
    });
  });
});
document.addEventListener('DOMContentLoaded', function() {
  const currentPath = window.location.pathname;

  // Procurement menu elements
  const procurementDropdown = document.querySelector('[data-bs-target="#procurementSubmenu"]');
  const procurementSubmenu = document.getElementById('procurementSubmenu');

  // Only auto-expand if on a procurement page
  if (procurementDropdown && procurementSubmenu) {
    if (currentPath.includes('/psm/')) {
      procurementDropdown.classList.add('active');
      procurementSubmenu.classList.add('show');

      // Highlight the specific sub-item
      const activeSubItem = procurementSubmenu.querySelector(`[href="${currentPath}"]`);
      if (activeSubItem) {
        // Remove active from all sub-items first
        procurementSubmenu.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        activeSubItem.classList.add('active');
      }
    }

    // Handle dropdown toggle (manual open/close)
    procurementDropdown.addEventListener('click', function(e) {
      e.preventDefault();
      const isExpanded = procurementSubmenu.classList.contains('show');
      if (isExpanded) {
        procurementSubmenu.classList.remove('show');
        procurementDropdown.classList.remove('active');
      } else {
        procurementSubmenu.classList.add('show');
        procurementDropdown.classList.add('active');
      }
    });
  }

  // Prevent parent collapse when clicking submenu links
  if (procurementSubmenu) {
    procurementSubmenu.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function(e) {
        // Remove active from all sub-items
        procurementSubmenu.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        // Add active to clicked link
        this.classList.add('active');
        // Keep parent expanded and active
        procurementDropdown.classList.add('active');
        procurementSubmenu.classList.add('show');
      });
    });
  }
});

  // Project Logistics Tracker dropdown active state logic
  const pltDropdown = document.querySelector('[data-bs-target="#pltSubmenu"]');
  const pltSubmenu = document.getElementById('pltSubmenu');
  const currentPath = window.location.pathname;

  if (pltDropdown && pltSubmenu) {
    if (
      currentPath.includes('/plt/toursetup') ||
      currentPath.includes('/plt/execution') ||
      currentPath.includes('/plt/closure')
    ) {
      pltDropdown.classList.add('active');
      pltSubmenu.classList.add('show');
      // Highlight the specific sub-item
      const activeSubItem = pltSubmenu.querySelector(`[href="${currentPath}"]`);
      if (activeSubItem) {
        pltSubmenu.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        activeSubItem.classList.add('active');
      }
    }
    // Dropdown toggle
    pltDropdown.addEventListener('click', function(e) {
      e.preventDefault();
      const isExpanded = pltSubmenu.classList.contains('show');
      if (isExpanded) {
        pltSubmenu.classList.remove('show');
        pltDropdown.classList.remove('active');
      } else {
        pltSubmenu.classList.add('show');
        pltDropdown.classList.add('active');
      }
    });
    // Prevent parent collapse when clicking submenu links
    pltSubmenu.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function(e) {
        pltSubmenu.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
        pltDropdown.classList.add('active');
        pltSubmenu.classList.add('show');
      });
    });
  }

  // Global variables for invoice management
  let currentInvoiceId = null;
  let invoiceDetailsModal = null;

  // View invoice details in modal
  function viewInvoiceDetails(invoiceData) {
    currentInvoiceId = invoiceData.id;
    
    // Initialize modal if not already done
    if (!invoiceDetailsModal) {
      invoiceDetailsModal = new bootstrap.Modal(document.getElementById('invoiceDetailsModal'));
    }
    
    // Show modal with loading state
    invoiceDetailsModal.show();
    
    // Reset content to loading state
    document.getElementById('invoiceDetailsContent').innerHTML = `
      <div class="text-center py-4">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Loading invoice details...</p>
      </div>
    `;
    
    // Use the real invoice data passed from the template
    setTimeout(() => {
      const invoice = invoiceData;
          
          // Format dates
          let issuedDate = 'Not set';
          if (invoice.created_at) {
            const date = new Date(invoice.created_at);
            issuedDate = date.toLocaleDateString('en-US', {
              year: 'numeric',
              month: 'short',
              day: 'numeric'
            });
          }
          
          let dueDate = 'Not set';
          if (invoice.due_date) {
            const date = new Date(invoice.due_date);
            dueDate = date.toLocaleDateString('en-US', {
              year: 'numeric',
              month: 'short',
              day: 'numeric'
            });
          }
          
          // Get status badge classes
          const statusClass = getInvoiceStatusBadgeClass(invoice.status);
          const paymentClass = getPaymentStatusBadgeClass(invoice.payment_status);
          
          // Populate modal content
          document.getElementById('invoiceDetailsContent').innerHTML = `
            <div class="col-md-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="card-title text-primary mb-3">
                    <i class="bi bi-receipt me-2"></i>Invoice Information
                  </h6>
                  <div class="row g-2">
                    <div class="col-4"><strong>Invoice #:</strong></div>
                    <div class="col-8"><span class="invoice-number">${invoice.invoice_no || 'N/A'}</span></div>
                    <div class="col-4"><strong>PO Number:</strong></div>
                    <div class="col-8">${invoice.po_number ? '<span class="po-number">' + invoice.po_number + '</span>' : 'N/A'}</div>
                    <div class="col-4"><strong>Amount:</strong></div>
                    <div class="col-8"><span class="amount-text">₱${parseFloat(invoice.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</span></div>
                    <div class="col-4"><strong>Status:</strong></div>
                    <div class="col-8"><span class="badge-enhanced ${statusClass}">${invoice.status || 'Draft'}</span></div>
                    <div class="col-4"><strong>Vendor ID:</strong></div>
                    <div class="col-8">${invoice.vendor_id || 'N/A'}</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="card-title text-success mb-3">
                    <i class="bi bi-building me-2"></i>Vendor Information
                  </h6>
                  <div class="row g-2">
                    <div class="col-4"><strong>Vendor:</strong></div>
                    <div class="col-8"><span class="vendor-text">${invoice.vendor_name || 'N/A'}</span></div>
                    <div class="col-4"><strong>Vendor ID:</strong></div>
                    <div class="col-8">${invoice.vendor_id || 'N/A'}</div>
                    <div class="col-4"><strong>Created:</strong></div>
                    <div class="col-8">${issuedDate}</div>
                    <div class="col-4"><strong>Updated:</strong></div>
                    <div class="col-8">${invoice.updated_at ? new Date(invoice.updated_at).toLocaleDateString('en-US', {year: 'numeric', month: 'short', day: 'numeric'}) : 'N/A'}</div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-12">
              <div class="card border-0 shadow-sm">
                <div class="card-body">
                  <h6 class="card-title text-info mb-3">
                    <i class="bi bi-calendar-check me-2"></i>Payment Information
                  </h6>
                  <div class="row g-3">
                    <div class="col-md-3">
                      <div class="text-center">
                        <div class="small text-muted">Payment Status</div>
                        <span class="badge-enhanced ${paymentClass}">${invoice.payment_status || 'Unpaid'}</span>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="text-center">
                        <div class="small text-muted">Issued Date</div>
                        <div class="fw-semibold date-text">${issuedDate}</div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="text-center">
                        <div class="small text-muted">Due Date</div>
                        <div class="fw-semibold date-text">${dueDate}</div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="text-center">
                        <div class="small text-muted">Amount Due</div>
                        <div class="fw-semibold amount-text">₱${parseFloat(invoice.amount || 0).toLocaleString('en-US', {minimumFractionDigits: 2})}</div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          `;
          
          // Show/hide payment button based on payment status
          const paymentBtn = document.getElementById('recordPaymentFromModal');
          if ((invoice.payment_status || 'Unpaid') === 'Paid') {
            paymentBtn.style.display = 'none';
          } else {
            paymentBtn.style.display = 'inline-block';
          }
          
          // Update download button with invoice ID
          const downloadBtn = document.getElementById('downloadInvoiceFromModal');
          downloadBtn.onclick = () => downloadInvoice(currentInvoiceId);
          
          // Update payment button with invoice ID
          paymentBtn.onclick = () => recordPayment(currentInvoiceId);
          
    }, 500); // Small delay to show loading animation
  }
  
  // Helper functions for badge classes
  function getInvoiceStatusBadgeClass(status) {
    const statusMap = {
      'Draft': 'badge-status-draft',
      'Submitted': 'badge-status-pending',
      'Approved': 'badge-status-approved',
      'Rejected': 'badge-status-rejected',
      'Partially Paid': 'badge-status-pending',
      'Paid': 'badge-status-paid',
      'Overdue': 'badge-status-overdue'
    };
    return statusMap[status] || 'badge-status-draft';
  }
  
  function getPaymentStatusBadgeClass(payment) {
    const paymentMap = {
      'Unpaid': 'badge-payment-unpaid',
      'Partial': 'badge-payment-partial',
      'Paid': 'badge-payment-paid'
    };
    return paymentMap[payment] || 'badge-payment-unpaid';
  }
  
  // Download invoice function
  function downloadInvoice(invoiceId) {
    Swal.fire({
      icon: 'info',
      title: 'Feature Not Available',
      text: 'Invoice download functionality is not yet implemented.',
      confirmButtonText: 'OK',
      confirmButtonColor: '#0d6efd'
    });
  }
  
  // Record payment function
  function recordPayment(invoiceId) {
    // Hide details modal and redirect to payment page
    if (invoiceDetailsModal) {
      invoiceDetailsModal.hide();
    }
    window.location.href = `/psm/invoice/${invoiceId}/record-payment`;
  }
</script>
