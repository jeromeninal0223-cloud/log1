<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Invoices - JetLouge Travels</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">

  <style>
    /* Modern Invoices Table Styling */
    .modern-invoices-table {
      border-radius: 0;
      overflow: hidden;
    }

    .invoices-table-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
    }

    .invoices-table-header th {
      padding: 1rem 0.75rem;
      font-weight: 600;
      letter-spacing: 0.5px;
      border: none;
    }

    .invoice-row-hover {
      transition: all 0.3s ease;
      border-left: 3px solid transparent;
    }

    .invoice-row-hover:hover {
      background-color: #f8f9ff;
      border-left-color: #667eea;
      transform: translateX(2px);
      box-shadow: 0 2px 8px rgba(102, 126, 234, 0.1);
    }

    /* Invoice ID Badge */
    .invoice-id-badge {
      background: linear-gradient(135deg, #667eea, #764ba2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-size: 0.95rem;
    }

    /* Invoice Status Badges */
    .invoice-status-badge {
      padding: 0.35rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: none;
    }

    .invoice-status-pending {
      background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
      color: #8b4513;
    }

    .invoice-status-paid {
      background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
      color: #2d5016;
    }

    .invoice-status-overdue {
      background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
      color: #8b0000;
    }

    .invoice-status-cancelled {
      background: linear-gradient(135deg, #a8a8a8 0%, #d3d3d3 100%);
      color: #4a4a4a;
    }

    /* Payment Status Badges */
    .payment-status-badge {
      padding: 0.35rem 0.75rem;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: none;
    }

    .payment-status-unpaid {
      background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
      color: #8b4513;
    }

    .payment-status-partial {
      background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
      color: #1e40af;
    }

    .payment-status-paid {
      background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
      color: #2d5016;
    }

    /* Invoice Actions */
    .invoice-actions .btn {
      transition: all 0.3s ease;
      border-radius: 8px;
    }

    .invoice-actions .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Mobile Invoice Cards */
    .mobile-invoices {
      padding: 0;
    }

    .invoice-card .card {
      transition: all 0.3s ease;
      border-radius: 12px;
      overflow: hidden;
    }

    .invoice-card .card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .invoice-number {
      font-size: 1.1rem;
      background: linear-gradient(135deg, #667eea, #764ba2);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .invoice-detail-item {
      padding: 0.5rem 0;
    }

    .invoice-detail-item small {
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .page-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
      }

      .page-header .breadcrumb {
        margin: 0;
      }

      .invoice-actions {
        flex-wrap: wrap;
      }

      .invoice-actions .btn {
        flex: 1;
        min-width: 100px;
      }
    }

    @media (max-width: 576px) {
      .invoice-card {
        margin: 0 1rem !important;
      }

      .invoice-status-badge,
      .payment-status-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
      }
    }

    /* Amount Display */
    .amount-display {
      font-family: 'Segoe UI', system-ui, sans-serif;
    }

    /* Date Info */
    .date-info {
      font-family: 'Segoe UI', system-ui, sans-serif;
    }

    /* Filter and Export Controls */
    .card-header {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 1px solid #dee2e6;
    }

    .form-select-sm {
      border-radius: 8px;
      border: 1px solid #ced4da;
      transition: all 0.3s ease;
    }

    .form-select-sm:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-outline-primary {
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .btn-outline-primary:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
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
        <i class="bi bi-building me-2"></i>JetLouge Travels
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
           alt="Vendor Profile" class="profile-img mb-2">
      <h6 class="fw-semibold mb-1">{{ auth('vendor')->user()->name }}</h6>
      <small class="text-muted">{{ auth('vendor')->user()->company_name }}</small>
    </div>

    <!-- Navigation Menu -->
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="{{ route('vendor.dashboard') }}" class="nav-link text-dark">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.bidding.landing') }}" class="nav-link text-dark">
          <i class="bi bi-gavel me-2"></i> Browse Opportunities
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.bids') }}" class="nav-link text-dark">
          <i class="bi bi-file-earmark-text me-2"></i> My Bids
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.orders') }}" class="nav-link text-dark">
          <i class="bi bi-cart-check me-2"></i> My Orders
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.invoices') }}" class="nav-link text-dark active">
          <i class="bi bi-receipt me-2"></i> My Invoices
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.damage.reports') }}" class="nav-link text-dark">
          <i class="bi bi-exclamation-triangle me-2"></i> Damage Reports
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.profile') }}" class="nav-link text-dark">
          <i class="bi bi-person me-2"></i> My Profile
        </a>
      </li>
      <li class="nav-item mt-3">
        <a href="{{ route('vendor.logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
      </li>
    </ul>
  </aside>

  <!-- Logout Form -->
  <form id="logout-form" action="{{ route('vendor.logout') }}" method="POST" style="display: none;">
    @csrf
  </form>

  <!-- Overlay for mobile -->
  <div id="overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="z-index:1040; display: none;"></div>

  <!-- Main Content -->
  <main id="main-content">
    <!-- Page Header -->
    <div class="page-header-container mb-4">
      <div class="d-flex justify-content-between align-items-center page-header">
        <div class="d-flex align-items-center">
          <div class="dashboard-logo me-3">
            <i class="bi bi-receipt fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">My Invoices</h2>
            <p class="text-muted mb-0">Track and manage your invoices and payments.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Invoices</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Invoice Statistics -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="bi bi-receipt"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $invoices->count() }}</h3>
                <p class="text-muted mb-0 small">Total Invoices</p>
                <small class="text-primary"><i class="bi bi-arrow-up"></i> All time</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                <i class="bi bi-clock"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $invoices->where('status', 'Pending')->count() }}</h3>
                <p class="text-muted mb-0 small">Pending Payment</p>
                <small class="text-warning"><i class="bi bi-clock"></i> Awaiting</small>
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
                <i class="bi bi-check-circle"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $invoices->where('status', 'Paid')->count() }}</h3>
                <p class="text-muted mb-0 small">Paid</p>
                <small class="text-success"><i class="bi bi-check"></i> Completed</small>
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
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">₱{{ number_format($invoices->sum('amount')) }}</h3>
                <p class="text-muted mb-0 small">Total Amount</p>
                <small class="text-info"><i class="bi bi-graph-up"></i> Combined</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Invoices Content -->
    <div class="row g-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">My Invoices</h5>
            <div class="d-flex gap-2">
              <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                <option value="">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Paid">Paid</option>
                <option value="Overdue">Overdue</option>
                <option value="Cancelled">Cancelled</option>
              </select>
              <button class="btn btn-sm btn-outline-primary" onclick="exportInvoices()">
                <i class="bi bi-download me-2"></i>Export
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            @if($invoices->count() > 0)
              <!-- Desktop Table View -->
              <div class="d-none d-lg-block">
                <div class="table-responsive">
                  <table class="table table-hover mb-0 modern-invoices-table" id="invoicesTable">
                    <thead class="invoices-table-header">
                      <tr>
                        <th class="border-0 fw-semibold text-uppercase small">Invoice #</th>
                        <th class="border-0 fw-semibold text-uppercase small">Order Details</th>
                        <th class="border-0 fw-semibold text-uppercase small">Amount</th>
                        <th class="border-0 fw-semibold text-uppercase small">Status</th>
                        <th class="border-0 fw-semibold text-uppercase small">Payment</th>
                        <th class="border-0 fw-semibold text-uppercase small">Issue Date</th>
                        <th class="border-0 fw-semibold text-uppercase small">Due Date</th>
                        <th class="border-0 fw-semibold text-uppercase small">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($invoices as $invoice)
                      <tr class="invoice-row-hover">
                        <td class="align-middle py-3">
                          <div class="invoice-id-badge">
                            <span class="fw-bold text-primary">{{ $invoice->id ?? 'INV-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <div class="invoice-info">
                            <h6 class="mb-1 fw-semibold text-dark">{{ $invoice->order ?? 'ORD-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</h6>
                            <p class="mb-0 text-muted small">{{ Str::limit($invoice->item_names ?? 'Services rendered', 50) }}</p>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <div class="amount-display">
                            <span class="fw-bold fs-6 text-success">₱{{ number_format($invoice->amount ?? rand(50000, 500000)) }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          @php
                            $status = $invoice->status ?? 'Pending';
                            $statusClass = [
                              'Pending' => 'invoice-status-pending',
                              'Paid' => 'invoice-status-paid',
                              'Overdue' => 'invoice-status-overdue',
                              'Cancelled' => 'invoice-status-cancelled'
                            ][$status] ?? 'invoice-status-pending';
                          @endphp
                          <span class="invoice-status-badge {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td class="align-middle py-3">
                          @php
                            $p = $invoice->payment_status ?? 'Unpaid';
                            $payClass = [
                              'Unpaid' => 'payment-status-unpaid',
                              'Partial' => 'payment-status-partial',
                              'Paid' => 'payment-status-paid',
                            ][$p] ?? 'payment-status-unpaid';
                          @endphp
                          <span class="payment-status-badge {{ $payClass }}">{{ $p }}</span>
                        </td>
                        <td class="align-middle py-3">
                          <div class="date-info">
                            <span class="text-muted small">{{ $invoice->issue_date ?? now()->subDays(rand(1, 30))->format('M d, Y') }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <div class="date-info">
                            <span class="text-muted small">{{ $invoice->due_date ?? now()->addDays(rand(7, 30))->format('M d, Y') }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <div class="invoice-actions">
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="viewInvoice({{ $invoice->id ?? 0 }})" title="View Details">
                              <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="downloadInvoice({{ $loop->iteration }})" title="Download">
                              <i class="bi bi-download"></i>
                            </button>
                          </div>
                        </td>
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Mobile Card View -->
              <div class="d-lg-none mobile-invoices">
                @foreach($invoices as $invoice)
                  @php
                    $status = $invoice->status ?? 'Pending';
                    $statusClass = [
                      'Pending' => 'invoice-status-pending',
                      'Paid' => 'invoice-status-paid',
                      'Overdue' => 'invoice-status-overdue',
                      'Cancelled' => 'invoice-status-cancelled'
                    ][$status] ?? 'invoice-status-pending';
                    
                    $p = $invoice->payment_status ?? 'Unpaid';
                    $payClass = [
                      'Unpaid' => 'payment-status-unpaid',
                      'Partial' => 'payment-status-partial',
                      'Paid' => 'payment-status-paid',
                    ][$p] ?? 'payment-status-unpaid';
                  @endphp
                  <div class="invoice-card mb-3 mx-3">
                    <div class="card border-0 shadow-sm h-100">
                      <div class="card-body p-4">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div class="invoice-header">
                            <span class="invoice-number fw-bold text-primary">{{ $invoice->id ?? 'INV-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                            <div class="mt-2">
                              <span class="invoice-status-badge {{ $statusClass }} me-2">{{ $status }}</span>
                              <span class="payment-status-badge {{ $payClass }}">{{ $p }}</span>
                            </div>
                          </div>
                        </div>

                        <!-- Order Info -->
                        <h6 class="card-title fw-semibold mb-2 text-dark">{{ $invoice->order ?? 'ORD-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</h6>
                        
                        <!-- Description -->
                        <p class="card-text text-muted small mb-3">{{ Str::limit($invoice->item_names ?? 'Services rendered', 80) }}</p>
                        
                        <!-- Details Grid -->
                        <div class="row g-3 mb-3">
                          <div class="col-6">
                            <div class="invoice-detail-item">
                              <small class="text-muted d-block">Amount</small>
                              <span class="fw-bold text-success">₱{{ number_format($invoice->amount ?? rand(50000, 500000)) }}</span>
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="invoice-detail-item">
                              <small class="text-muted d-block">Issue Date</small>
                              <span class="small">{{ $invoice->issue_date ?? now()->subDays(rand(1, 30))->format('M d, Y') }}</span>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="invoice-detail-item">
                              <small class="text-muted d-block">Due Date</small>
                              <span class="small">{{ $invoice->due_date ?? now()->addDays(rand(7, 30))->format('M d, Y') }}</span>
                            </div>
                          </div>
                        </div>

                        <!-- Actions -->
                        <div class="invoice-actions d-flex gap-2 justify-content-center">
                          <button class="btn btn-sm btn-outline-primary" onclick="viewInvoice({{ $invoice->id ?? 0 }})" title="View Details">
                            <i class="bi bi-eye me-1"></i>View
                          </button>
                          <button class="btn btn-sm btn-outline-success" onclick="downloadInvoice({{ $loop->iteration }})" title="Download">
                            <i class="bi bi-download me-1"></i>Download
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center py-5">
                <div class="text-muted">
                  <i class="bi bi-receipt fs-1 d-block mb-3"></i>
                  <h6>No Invoices Generated Yet</h6>
                  <p class="mb-0">Invoices will be generated once you complete orders. Complete your first order to see invoices here!</p>
                  <a href="{{ route('vendor.orders') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-cart-check me-2"></i>View Orders
                  </a>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Invoice Details Modal -->
  <div class="modal fade" id="invoiceModal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="invoiceModalLabel">Invoice Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="inv_no" readonly>
                <label for="inv_no">Invoice #</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="inv_vendor" readonly>
                <label for="inv_vendor">Vendor</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" class="form-control" id="inv_po" readonly>
                <label for="inv_po">PO Number</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" class="form-control" id="inv_amount" readonly>
                <label for="inv_amount">Amount</label>
              </div>
            </div>
            <div class="col-md-4">
              <div class="form-floating">
                <input type="text" class="form-control" id="inv_payment" readonly>
                <label for="inv_payment">Payment Status</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="inv_issued" readonly>
                <label for="inv_issued">Issued Date</label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-floating">
                <input type="text" class="form-control" id="inv_due" readonly>
                <label for="inv_due">Due Date</label>
              </div>
            </div>
            <div class="col-12">
              <div class="form-floating">
                <textarea class="form-control" id="inv_notes" style="height: 100px" readonly></textarea>
                <label for="inv_notes">Notes</label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar toggle functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
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

      // Handle window resize for responsive behavior
      window.addEventListener('resize', () => {
        // Reset mobile sidebar state on desktop
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('active');
          overlay.classList.remove('show');
          document.body.style.overflow = '';
        }
      });

      // Status filter functionality
      const statusFilter = document.getElementById('statusFilter');
      if (statusFilter) {
        statusFilter.addEventListener('change', function() {
          const filter = this.value;
          const rows = document.querySelectorAll('#invoicesTable tbody tr');
          
          rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(5)');
            const status = statusCell.textContent.trim();
            
            if (!filter || status.includes(filter)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
    });

    // View invoice details
    function viewInvoice(invoiceId) {
      if (!invoiceId) {
        return alert('Invoice not found.');
      }
      fetch(`${window.location.origin}/vendor/api/invoices/${invoiceId}`, { credentials: 'same-origin' })
        .then(async (res) => {
          if (!res.ok) {
            const txt = await res.text();
            throw new Error(txt || 'Failed to load invoice');
          }
          return res.json();
        })
        .then((data) => {
          if (!data.success) throw new Error('Failed to load invoice');
          const inv = data.invoice;
          document.getElementById('inv_no').value = inv.invoice_no || inv.id;
          document.getElementById('inv_vendor').value = inv.vendor_name || '';
          document.getElementById('inv_po').value = inv.po_number || '';
          document.getElementById('inv_amount').value = `₱${Number(inv.amount || 0).toLocaleString()}`;
          document.getElementById('inv_payment').value = inv.payment_status || 'Unpaid';
          document.getElementById('inv_issued').value = inv.issued_date || '';
          document.getElementById('inv_due').value = inv.due_date || '';
          document.getElementById('inv_notes').value = inv.notes || '';
          const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
          modal.show();
        })
        .catch((err) => {
          console.error(err);
          alert('Unable to load invoice details.');
        });
    }

    // Download invoice
    function downloadInvoice(invoiceId) {
      alert('Downloading invoice #: ' + invoiceId);
      // In a real application, this would generate and download a PDF invoice
    }

    // Export invoices
    function exportInvoices() {
      alert('Exporting invoices to CSV...');
      // In a real application, this would export invoice data to CSV format
    }
  </script>
</body>
</html>
