<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Orders - JetLouge Travels</title>

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
        <a href="{{ route('vendor.orders') }}" class="nav-link text-dark active">
          <i class="bi bi-cart-check me-2"></i> My Orders
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.invoices') }}" class="nav-link text-dark">
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
            <i class="bi bi-cart-check fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">My Orders</h2>
            <p class="text-muted mb-0">Track and manage your assigned orders and deliveries.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Orders</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Order Statistics -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="bi bi-cart-check"></i>
              </div>
              <div>
              <h3 class="fw-bold mb-0">{{ $orders->count() }}</h3>
                <p class="text-muted mb-0 small">Total Orders</p>
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
                <h3 class="fw-bold mb-0">{{ $orders->where('status', 'In Progress')->count() }}</h3>
                <p class="text-muted mb-0 small">In Progress</p>
                <small class="text-warning"><i class="bi bi-clock"></i> Active</small>
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
                <h3 class="fw-bold mb-0">{{ $orders->where('status', 'Completed')->count() }}</h3>
                <p class="text-muted mb-0 small">Completed</p>
                <small class="text-success"><i class="bi bi-check"></i> Finished</small>
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
                <h3 class="fw-bold mb-0">₱{{ number_format($orders->sum('total_amount')) }}</h3>
                <p class="text-muted mb-0 small">Total Value</p>
                <small class="text-info"><i class="bi bi-graph-up"></i> Combined</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders Content -->
    <div class="row g-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">My Orders</h5>
            <div class="d-flex gap-2">
              <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                <option value="">All Status</option>
                <option value="Pending">Pending</option>
                <option value="In Progress">In Progress</option>
                <option value="Delivered">Delivered</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
              </select>
              <button class="btn btn-sm btn-outline-primary" onclick="refreshOrders()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
              </button>
            </div>
          </div>
          <div class="card-body p-0">
            @if($orders->count() > 0)
              <!-- Desktop Table View -->
              <div class="d-none d-lg-block">
                <div class="table-responsive">
                  <table class="table table-hover mb-0 modern-orders-table" id="ordersTable">
                    <thead class="orders-table-header">
                      <tr>
                        <th class="border-0 fw-semibold text-uppercase small">Order ID</th>
                        <th class="border-0 fw-semibold text-uppercase small">Project Details</th>
                        <th class="border-0 fw-semibold text-uppercase small">Category</th>
                        <th class="border-0 fw-semibold text-uppercase small">Amount</th>
                        <th class="border-0 fw-semibold text-uppercase small">Status</th>
                        <th class="border-0 fw-semibold text-uppercase small">Due Date</th>
                        <th class="border-0 fw-semibold text-uppercase small">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($orders as $order)
                      <tr class="order-row-hover">
                        <td class="align-middle py-3">
                          <div class="order-id-badge">
                            <span class="fw-bold text-primary">{{ $order->po_number ?? 'PO-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <div class="order-info">
                            <h6 class="mb-1 fw-semibold text-dark">{{ $order->title ?? 'Purchase Order ' . $loop->iteration }}</h6>
                            <p class="mb-0 text-muted small">{{ Str::limit($order->description ?? 'No description available', 60) }}</p>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <span class="category-tag">{{ $order->contract->category ?? 'Logistics & Transportation' }}</span>
                        </td>
                        <td class="align-middle py-3">
                          <div class="amount-display">
                            <span class="fw-bold fs-6 text-success">₱{{ number_format($order->total_amount ?? 0, 2) }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          @php
                            $status = $order->status ?? 'Draft';
                            $statusClass = [
                              'Draft' => 'order-status-draft',
                              'Pending Approval' => 'order-status-pending',
                              'Approved' => 'order-status-approved',
                              'Issued' => 'order-status-issued',
                              'In Progress' => 'order-status-progress',
                              'Delivered' => 'order-status-delivered',
                              'Completed' => 'order-status-completed',
                              'Cancelled' => 'order-status-cancelled'
                            ][$status] ?? 'order-status-draft';
                          @endphp
                          <span class="order-status-badge {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td class="align-middle py-3">
                          <div class="due-date">
                            <span class="text-muted small">{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('M d, Y') : 'Not set' }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <div class="order-actions">
                            <button class="btn btn-sm btn-outline-primary me-1" onclick="viewOrderDetails({{ $order->id }})" title="View Details">
                              <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-success" onclick="openStatusModal({{ $order->id }}, '{{ $order->status ?? 'In Progress' }}')" title="Update Status">
                              <i class="bi bi-clipboard-check"></i>
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
              <div class="d-lg-none mobile-orders">
                @foreach($orders as $order)
                  @php
                    $status = $order->status ?? 'Draft';
                    $statusClass = [
                      'Draft' => 'order-status-draft',
                      'Pending Approval' => 'order-status-pending',
                      'Approved' => 'order-status-approved',
                      'Issued' => 'order-status-issued',
                      'In Progress' => 'order-status-progress',
                      'Delivered' => 'order-status-delivered',
                      'Completed' => 'order-status-completed',
                      'Cancelled' => 'order-status-cancelled'
                    ][$status] ?? 'order-status-draft';
                  @endphp
                  <div class="order-card mb-3 mx-3">
                    <div class="card border-0 shadow-sm h-100">
                      <div class="card-body p-4">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div class="order-header">
                            <span class="order-number fw-bold text-primary">{{ $order->po_number ?? 'PO-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                            <span class="order-status-badge {{ $statusClass }} ms-2">{{ $status }}</span>
                          </div>
                        </div>

                        <!-- Order Title -->
                        <h6 class="card-title fw-semibold mb-2 text-dark">{{ $order->title ?? 'Purchase Order ' . $loop->iteration }}</h6>
                        
                        <!-- Description -->
                        <p class="card-text text-muted small mb-3">{{ Str::limit($order->description ?? 'No description available', 80) }}</p>
                        
                        <!-- Details Grid -->
                        <div class="row g-3 mb-3">
                          <div class="col-6">
                            <div class="order-detail-item">
                              <small class="text-muted d-block">Amount</small>
                              <span class="fw-bold text-success">₱{{ number_format($order->total_amount ?? 0, 2) }}</span>
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="order-detail-item">
                              <small class="text-muted d-block">Category</small>
                              <span class="small">{{ $order->contract->category ?? 'Logistics & Transportation' }}</span>
                            </div>
                          </div>
                          <div class="col-12">
                            <div class="order-detail-item">
                              <small class="text-muted d-block">Due Date</small>
                              <span class="small">{{ $order->expected_delivery_date ? $order->expected_delivery_date->format('M d, Y') : 'Not set' }}</span>
                            </div>
                          </div>
                        </div>

                        <!-- Actions -->
                        <div class="order-actions d-flex gap-2 justify-content-center">
                          <button class="btn btn-sm btn-outline-primary" onclick="viewOrderDetails({{ $order->id }})" title="View Details">
                            <i class="bi bi-eye me-1"></i>View
                          </button>
                          <button class="btn btn-sm btn-outline-success" onclick="openStatusModal({{ $order->id }}, '{{ $order->status ?? 'In Progress' }}')" title="Update Status">
                            <i class="bi bi-clipboard-check me-1"></i>Update
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
                  <i class="bi bi-cart-check fs-1 d-block mb-3"></i>
                  <h6>No Orders Assigned Yet</h6>
                  <p class="mb-0">You don't have any orders assigned to you yet. Keep submitting competitive bids!</p>
                  <a href="{{ route('vendor.bidding.landing') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-gavel me-2"></i>Submit Bids
                  </a>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </main>

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
          const rows = document.querySelectorAll('#ordersTable tbody tr');
          
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

    // CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // View order details via API
    function viewOrderDetails(orderId) {
      fetch(`{{ url('vendor/orders') }}/${orderId}/details`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          const po = data.purchase_order;
          alert(`PO: ${po.po_number}\nTitle: ${po.title}\nStatus: ${po.status}\nExpected: ${po.expected_delivery_date || 'N/A'}`);
        } else {
          alert(data.message || 'Unable to load order details');
        }
      })
      .catch(() => alert('Unable to load order details'));
    }

    let selectedOrderId = null;
    function openStatusModal(orderId, currentStatus) {
      selectedOrderId = orderId;
      const modal = new bootstrap.Modal(document.getElementById('statusModal'));
      const statusSelect = document.getElementById('status');
      statusSelect.value = currentStatus;
      // Toggle allowed options based on current status
      Array.from(statusSelect.options).forEach(opt => {
        opt.disabled = false;
      });
      if (currentStatus === 'Issued') {
        // All allowed
      } else if (currentStatus === 'In Progress') {
        document.querySelector('#status option[value="Issued"]').disabled = true;
      } else if (currentStatus === 'Completed' || currentStatus === 'Cancelled') {
        Array.from(statusSelect.options).forEach(opt => { if (opt.value !== currentStatus) opt.disabled = true; });
      }
      document.getElementById('actual_delivery_date').value = '';
      document.getElementById('notes').value = '';
      modal.show();
    }

    function saveStatus() {
      if (!selectedOrderId) return;
      const status = document.getElementById('status').value;
      const actualDate = document.getElementById('actual_delivery_date').value;
      const notes = document.getElementById('notes').value;

      const formData = new FormData();
      formData.append('_token', csrfToken);
      formData.append('status', status);
      if (actualDate) formData.append('actual_delivery_date', actualDate);
      if (notes) formData.append('notes', notes);

      fetch(`{{ url('vendor/orders') }}/${selectedOrderId}/status`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
          location.reload();
        } else {
          alert(data.message || 'Failed to update status');
        }
      })
      .catch(() => alert('Failed to update status'));
    }

    // Refresh orders
    function refreshOrders() {
      location.reload();
    }
  </script>
  
  <!-- Status Update Modal -->
  <div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="statusModalLabel">Update Delivery Status</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select id="status" class="form-select">
              <option value="Issued">Issued</option>
              <option value="In Progress">In Progress</option>
              <option value="Delivered">Delivered</option>
              <option value="Completed">Completed</option>
              <option value="Cancelled">Cancelled</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="actual_delivery_date" class="form-label">Actual Delivery Date</label>
            <input type="date" id="actual_delivery_date" class="form-control" />
            <small class="text-muted">Required when marking as Completed</small>
          </div>
          <div class="mb-3">
            <label for="notes" class="form-label">Notes</label>
            <textarea id="notes" class="form-control" rows="3" placeholder="Optional notes..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" onclick="saveStatus()">Save</button>
        </div>
      </div>
    </div>
  </div>

  <style>
    /* Enhanced Modern Orders Table Styling */
    .modern-orders-table {
      border-radius: 0;
      overflow: hidden;
    }
    
    .orders-table-header {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 3px solid #dee2e6;
    }
    
    .orders-table-header th {
      font-weight: 600;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      color: #495057;
      padding: 16px 12px;
      border: none;
      position: relative;
    }
    
    .modern-orders-table tbody tr {
      border-bottom: 1px solid #f1f3f4;
      transition: all 0.2s ease;
    }
    
    .order-row-hover:hover {
      background-color: #f8f9fa;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .modern-orders-table td {
      vertical-align: middle;
      padding: 16px 12px;
      border: none;
    }
    
    /* Order ID Badge */
    .order-id-badge {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
      border-radius: 20px;
      border: 1px solid #e1f5fe;
    }
    
    /* Order Info Styling */
    .order-info h6 {
      font-size: 0.95rem;
      line-height: 1.4;
      margin-bottom: 4px;
    }
    
    .order-info p {
      font-size: 0.8rem;
      line-height: 1.3;
    }
    
    /* Category Tag */
    .category-tag {
      display: inline-block;
      padding: 4px 10px;
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 12px;
      font-size: 0.8rem;
      color: #6c757d;
      font-weight: 500;
    }
    
    /* Amount Display */
    .amount-display {
      font-family: 'Segoe UI', system-ui, sans-serif;
    }
    
    /* Order Status Badges */
    .order-status-badge {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 2px solid transparent;
    }
    
    .order-status-draft {
      background: linear-gradient(135deg, #e2e3e5 0%, #ced4da 100%);
      color: #383d41;
      border-color: #ced4da;
    }
    
    .order-status-pending {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      color: #856404;
      border-color: #ffeaa7;
    }
    
    .order-status-approved {
      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
      color: #0c5460;
      border-color: #bee5eb;
    }
    
    .order-status-issued {
      background: linear-gradient(135deg, #cce7ff 0%, #b3d9ff 100%);
      color: #004085;
      border-color: #b3d9ff;
    }
    
    .order-status-progress {
      background: linear-gradient(135deg, #d4e6f1 0%, #aed6f1 100%);
      color: #1b4f72;
      border-color: #aed6f1;
    }
    
    .order-status-delivered {
      background: linear-gradient(135deg, #d5f4e6 0%, #82e0aa 100%);
      color: #0e4b2a;
      border-color: #82e0aa;
    }
    
    .order-status-completed {
      background: linear-gradient(135deg, #d4edda 0%, #a8e6cf 100%);
      color: #155724;
      border-color: #a8e6cf;
    }
    
    .order-status-cancelled {
      background: linear-gradient(135deg, #f8d7da 0%, #ff7675 100%);
      color: #721c24;
      border-color: #ff7675;
    }
    
    /* Order Actions */
    .order-actions .btn {
      border-radius: 8px;
      transition: all 0.2s ease;
    }
    
    .order-actions .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    /* Mobile Order Cards */
    .mobile-orders {
      padding: 0;
    }
    
    .order-card {
      transition: all 0.3s ease;
    }
    
    .order-card:hover {
      transform: translateY(-2px);
    }
    
    .order-card .card {
      border-radius: 16px;
      overflow: hidden;
      background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .order-card .card-body {
      position: relative;
    }
    
    .order-card .card-body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #28a745, #17a2b8, #ffc107);
      border-radius: 16px 16px 0 0;
    }
    
    .order-header {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }
    
    .order-number {
      font-size: 0.9rem;
      padding: 4px 10px;
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
      border-radius: 12px;
      border: 1px solid #e1f5fe;
    }
    
    .order-detail-item {
      padding: 8px 0;
    }
    
    .order-detail-item small {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }
    
    /* Due Date */
    .due-date {
      text-align: left;
    }
    
    /* Responsive Improvements */
    @media (max-width: 768px) {
      .page-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
      }
      
      .page-header .breadcrumb {
        margin-top: 0.5rem;
      }
      
      .card-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
      }
      
      .card-header .d-flex {
        width: 100%;
        justify-content: space-between;
      }
      
      .mobile-orders .order-card {
        margin-left: 1rem !important;
        margin-right: 1rem !important;
      }
    }
    
    @media (max-width: 576px) {
      .order-header {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .order-actions {
        margin-top: 8px;
        flex-wrap: wrap;
      }
      
      .order-detail-item {
        text-align: center;
      }
    }
  </style>
</body>
</html>
