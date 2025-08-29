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
          <div class="card-body">
            @if($invoices->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover" id="invoicesTable">
                  <thead class="table-light">
                    <tr>
                      <th>Invoice #</th>
                      <th>Order</th>
                      <th>Description</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Payment</th>
                      <th>Issue Date</th>
                      <th>Due Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($invoices as $invoice)
                    <tr>
                      <td><strong>{{ $invoice->id ?? 'INV-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</strong></td>
                      <td>{{ $invoice->order ?? 'ORD-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</td>
                      <td>{{ $invoice->item_names ?? 'Services rendered' }}</td>
                      <td><strong>₱{{ number_format($invoice->amount ?? rand(50000, 500000)) }}</strong></td>
                      <td>
                        @php
                          $status = $invoice->status ?? 'Pending';
                          $statusClass = [
                            'Pending' => 'bg-warning',
                            'Paid' => 'bg-success',
                            'Overdue' => 'bg-danger',
                            'Cancelled' => 'bg-secondary'
                          ][$status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $status }}</span>
                      </td>
                      <td>
                        @php
                          $p = $invoice->payment_status ?? 'Unpaid';
                          $payClass = [
                            'Unpaid' => 'bg-secondary',
                            'Partial' => 'bg-info',
                            'Paid' => 'bg-success',
                          ][$p] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $payClass }}">{{ $p }}</span>
                      </td>
                      <td>{{ $invoice->issue_date ?? now()->subDays(rand(1, 30))->format('M d, Y') }}</td>
                      <td>{{ $invoice->due_date ?? now()->addDays(rand(7, 30))->format('M d, Y') }}</td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button class="btn btn-outline-primary" onclick="viewInvoice({{ $invoice->id ?? 0 }})">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button class="btn btn-outline-success" onclick="downloadInvoice({{ $loop->iteration }})">
                            <i class="bi bi-download"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            @else
              <div class="text-center py-5">
                <i class="bi bi-receipt fa-3x text-muted mb-3"></i>
                <h5>No Invoices Generated Yet</h5>
                <p class="text-muted">Invoices will be generated once you complete orders. Complete your first order to see invoices here!</p>
                <a href="{{ route('vendor.orders') }}" class="btn btn-primary">
                  <i class="bi bi-cart-check me-2"></i>View Orders
                </a>
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
