<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Smart Warehousing Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
      <h6 class="fw-semibold mb-1">{{ Auth::user()->name ?? 'User' }}</h6>
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
        <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#warehouseSubmenu" aria-expanded="true" aria-controls="warehouseSubmenu">
          <i class="bi bi-box-seam me-2"></i> Smart Warehousing
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse show" id="warehouseSubmenu">
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
              <a href="{{ url('/stock-replenishment') }}" class="nav-link text-dark small active">
                <i class="bi bi-arrow-repeat me-2"></i> Stock Replenishment
              </a>
            </li>
          </ul>
        </div>
      </li>
      @if(Auth::user()->role !== 'logistics_staff')
       <li class="nav-item">
        <a href="#" class="nav-link text-dark" data-bs-toggle="collapse" data-bs-target="#procurementSubmenu" aria-expanded="false" aria-controls="procurementSubmenu">
          <i class="bi bi-cart-plus me-2"></i> Procurement & Sourcing Management
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="procurementSubmenu">
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
                <i class="bi bi-gavel me-2"></i> Bidding & RFQ
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
              <a href="{{ url('/psm/invoice') }}" class="nav-link text-dark small">
                <i class="bi bi-receipt me-2"></i> Invoice Management
              </a>
            </li>
          </ul>
        </div>
      </li>
      @endif
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
            <i class="bi bi-flag me-2"></i> Tour Setup
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
              <a href="{{ url('/alms/disposalretirement') }}" class="nav-link text-dark small">
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
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="nav-link text-danger btn btn-link p-0">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
          </button>
        </form>
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
            <h2 class="fw-bold mb-1">Stock Replenishment</h2>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name ?? 'User' }}! Monitor and manage stock replenishment processes.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/smartwarehousing') }}" class="text-decoration-none">Smart Warehousing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Stock Replenishment</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="bi bi-arrow-repeat"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['pending_replenishment'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Pending Replenishment</p>
                <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> {{ $stats['critical_items'] ?? 0 }} critical</small>
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
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['pending_requests'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Purchase Requests</p>
                <small class="text-info"><i class="bi bi-info-circle"></i> Pending approval</small>
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
                <i class="bi bi-exclamation-triangle"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['critical_items'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Critical Stock Items</p>
                <small class="text-danger"><i class="bi bi-arrow-up"></i> Urgent action needed</small>
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
                <h3 class="fw-bold mb-0">${{ number_format($stats['total_estimated_cost'] ?? 0, 0) }}</h3>
                <p class="text-muted mb-0 small">Estimated Cost</p>
                <small class="text-success"><i class="bi bi-calculator"></i> Pending requests</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Low Stock Items & Purchase Requests -->
    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Low Stock Items -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Low Stock Items</h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-success" id="autoGenerateBtn">
                <i class="bi bi-magic"></i> Auto Generate Requests
              </button>
              <button class="btn btn-sm btn-outline-primary" onclick="refreshLowStock()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover" id="lowStockTable">
                <thead class="table-light">
                  <tr>
                    <th>
                      <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Current Stock</th>
                    <th>Min Stock</th>
                    <th>Status</th>
                    <th>Est. Cost</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($lowStockItems as $item)
                  <tr data-item-id="{{ $item->id }}">
                    <td>
                      <input type="checkbox" class="form-check-input item-checkbox" value="{{ $item->id }}">
                    </td>
                    <td><strong>{{ $item->item_code }}</strong></td>
                    <td>{{ $item->name }}</td>
                    <td>
                      <span class="badge bg-{{ $item->isCriticallyLow() ? 'danger' : 'warning' }}">
                        {{ $item->current_stock }} {{ $item->unit_of_measure }}
                      </span>
                    </td>
                    <td>{{ $item->minimum_stock }} {{ $item->unit_of_measure }}</td>
                    <td>
                      <span class="badge bg-{{ $item->getStockStatus() === 'critical' ? 'danger' : 'warning' }}">
                        {{ ucfirst($item->getStockStatus()) }}
                      </span>
                    </td>
                    <td>${{ number_format($item->unit_price * $item->reorder_quantity, 2) }}</td>
                    <td>
                      @if($item->hasPendingPurchaseRequest())
                        <span class="badge bg-info">Request Pending</span>
                      @else
                        <button class="btn btn-sm btn-primary create-request-btn" data-item-id="{{ $item->id }}">
                          <i class="bi bi-plus-circle"></i> Create Request
                        </button>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted">No low stock items found</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            @if($lowStockItems->count() > 0)
            <div class="mt-3">
              <button class="btn btn-primary" id="bulkCreateBtn" disabled>
                <i class="bi bi-file-earmark-plus"></i> Create Requests for Selected
              </button>
            </div>
            @endif
          </div>
        </div>

        <!-- Purchase Requests -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Purchase Requests</h5>
            <a href="{{ url('/psm/request') }}" class="btn btn-sm btn-outline-primary">View All</a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Request #</th>
                    <th>Item</th>
                    <th>Quantity</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Est. Cost</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($recentRequests as $request)
                  <tr>
                    <td><strong>{{ $request->request_number }}</strong></td>
                    <td>{{ $request->stockItem->name ?? 'N/A' }}</td>
                    <td>{{ $request->quantity_requested }} {{ $request->stockItem->unit_of_measure ?? '' }}</td>
                    <td>
                      <span class="badge bg-{{ $request->getPriorityColor() }}">
                        {{ ucfirst($request->priority) }}
                      </span>
                    </td>
                    <td>
                      <span class="badge bg-{{ $request->getStatusColor() }}">
                        {{ ucfirst(str_replace('_', ' ', $request->status)) }}
                      </span>
                    </td>
                    <td>${{ number_format($request->total_estimated_cost, 2) }}</td>
                    <td>
                      @if($request->status === 'pending')
                        <button class="btn btn-sm btn-success approve-btn" data-request-id="{{ $request->id }}">
                          <i class="bi bi-check-circle"></i> Approve
                        </button>
                      @elseif($request->status === 'approved')
                        <button class="btn btn-sm btn-primary send-to-procurement-btn" data-request-id="{{ $request->id }}">
                          <i class="bi bi-send"></i> Send to Procurement
                        </button>
                      @elseif($request->status === 'forwarded_to_procurement')
                        <span class="badge bg-info">
                          <i class="bi bi-check-circle"></i> Sent to Procurement
                        </span>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted">No purchase requests found</td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Inventory Chart -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Inventory Levels</h5>
          </div>
          <div class="card-body">
            <canvas id="inventoryChart" height="100"></canvas>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Order
              </button>
              <button class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-in-down me-2"></i>Receive Stock
              </button>
              <button class="btn btn-outline-primary">
                <i class="bi bi-geo-alt-fill me-2"></i>Update Location
              </button>
              <button class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-text me-2"></i>Generate Report
              </button>
            </div>
          </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Storage Utilization</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone A - Electronics</span>
                <span class="small text-muted">92%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-danger" style="width: 92%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone B - Clothing</span>
                <span class="small text-muted">78%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: 78%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone C - Furniture</span>
                <span class="small text-muted">65%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: 65%"></div>
              </div>
            </div>
            <div>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone D - Books</span>
                <span class="small text-muted">45%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 45%"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Alerts -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Alerts</h5>
          </div>
          <div class="card-body">
            <div class="alert alert-danger alert-sm mb-2">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Low stock: Laptop batteries (5 units)
            </div>
            <div class="alert alert-warning alert-sm mb-2">
              <i class="bi bi-clock me-2"></i>
              Order #ORD-2024-001 delayed
            </div>
            <div class="alert alert-info alert-sm">
              <i class="bi bi-info-circle me-2"></i>
              New shipment arriving in 2 hours
            </div>
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
      // Sidebar toggle elements
      const menuBtn = document.getElementById('menu-btn');
      const desktopToggle = document.getElementById('desktop-toggle');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const mainContent = document.getElementById('main-content');
      const currentPath = window.location.pathname;

      // Mobile sidebar toggle
      if (menuBtn && sidebar && overlay) {
        menuBtn.addEventListener('click', function(e) {
          e.preventDefault();
          sidebar.classList.toggle('active');
          overlay.classList.toggle('show');
          document.body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
        });
      }

      // Desktop sidebar toggle
      if (desktopToggle && sidebar && mainContent) {
        desktopToggle.addEventListener('click', function(e) {
          e.preventDefault();
          e.stopPropagation();

          sidebar.classList.toggle('collapsed');
          mainContent.classList.toggle('expanded');

          // Store state in localStorage for persistence
          localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));

          // Trigger window resize event to help responsive components adjust
          setTimeout(() => {
            window.dispatchEvent(new Event('resize'));
          }, 300);
        });

        // Restore sidebar state from localStorage
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
          sidebar.classList.add('collapsed');
          mainContent.classList.add('expanded');
        } else {
          sidebar.classList.remove('collapsed');
          mainContent.classList.remove('expanded');
        }
      }

      // Close mobile sidebar when clicking overlay
      if (overlay) {
        overlay.addEventListener('click', function() {
          sidebar.classList.remove('active');
          overlay.classList.remove('show');
          document.body.style.overflow = '';
        });
      }

      // Reset mobile sidebar state on desktop
      window.addEventListener('resize', function() {
        if (window.innerWidth >= 992) {
          sidebar.classList.remove('active');
          overlay.classList.remove('show');
          document.body.style.overflow = '';
        }
      });

      // Document Tracking & Logistics Records dropdown logic
      const documentDropdown = document.querySelector('[data-bs-target="#documentSubmenu"]');
      const documentSubmenu = document.getElementById('documentSubmenu');
      if (documentDropdown && documentSubmenu) {
        if (
          currentPath.includes('/dtrs/document') ||
          currentPath.includes('/dtrs/audits') ||
          currentPath.includes('/dtrs/version')
        ) {
          documentDropdown.classList.add('active');
          documentSubmenu.classList.add('show');
          const activeSubItem = documentSubmenu.querySelector(`[href="${currentPath}"]`);
          if (activeSubItem) {
            activeSubItem.classList.add('active');
          }
        }
        // Prevent dropdown from closing when clicking DTRS sub-links
        documentSubmenu.querySelectorAll('.nav-link').forEach(link => {
          link.addEventListener('click', function() {
            documentSubmenu.classList.add('show');
            documentSubmenu.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
          });
        });
      }

      // Add smooth hover effects to nav links
      document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
          // Don't handle dropdown parent links here
          if (link.getAttribute('data-bs-toggle') === 'collapse') {
            return;
          }
          // Remove active class from all links
          document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
          // Add active class to clicked link
          this.classList.add('active');
        });
      });
    }); // Close DOMContentLoaded

      // Project Logistics Tracker dropdown active state logic
  const pltDropdown = document.querySelector('[data-bs-target="#pltSubmenu"]');
  const pltSubmenu = document.getElementById('pltSubmenu');
  const currentPath = window.location.pathname;

  // Only activate PLT dropdown and sub-link on PLT pages
  if (pltDropdown && pltSubmenu) {
    if (
      currentPath.includes('/plt/toursetup') ||
      currentPath.includes('/plt/execution') ||
      currentPath.includes('/plt/closure')
    ) {
      pltDropdown.classList.add('active');
      pltSubmenu.classList.add('show');
      const activeSubItem = pltSubmenu.querySelector(`[href="${currentPath}"]`);
      if (activeSubItem) {
        activeSubItem.classList.add('active');
      }
    }
  }
    
    // Stock Replenishment Functionality
    let selectedItems = [];

    // Select All functionality
    document.getElementById('selectAll')?.addEventListener('change', function() {
      const checkboxes = document.querySelectorAll('.item-checkbox');
      checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
      });
      updateSelectedItems();
    });

    // Individual checkbox functionality
    document.querySelectorAll('.item-checkbox').forEach(checkbox => {
      checkbox.addEventListener('change', updateSelectedItems);
    });

    function updateSelectedItems() {
      selectedItems = Array.from(document.querySelectorAll('.item-checkbox:checked')).map(cb => cb.value);
      const bulkBtn = document.getElementById('bulkCreateBtn');
      if (bulkBtn) {
        bulkBtn.disabled = selectedItems.length === 0;
      }
    }

    // Create single purchase request
    document.querySelectorAll('.create-request-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const itemId = this.dataset.itemId;
        createPurchaseRequest(itemId);
      });
    });

    // Bulk create purchase requests
    document.getElementById('bulkCreateBtn')?.addEventListener('click', function() {
      if (selectedItems.length === 0) return;
      bulkCreatePurchaseRequests(selectedItems);
    });

    // Auto generate all requests
    document.getElementById('autoGenerateBtn')?.addEventListener('click', function() {
      autoGenerateRequests();
    });

    // Approve purchase request
    document.querySelectorAll('.approve-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const requestId = this.dataset.requestId;
        approvePurchaseRequest(requestId);
      });
    });

    // Send to Procurement functionality
    document.querySelectorAll('.send-to-procurement-btn').forEach(button => {
      button.addEventListener('click', function() {
        const requestId = this.dataset.requestId;
        
        if (confirm('Are you sure you want to send this purchase request to procurement?')) {
          fetch('/api/stock/send-to-procurement', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
              request_id: requestId
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showAlert('success', data.message);
              setTimeout(() => location.reload(), 1500);
            } else {
              showAlert('error', data.message);
            }
          })
          .catch(error => {
            showAlert('error', 'Failed to send to procurement');
            console.error('Error:', error);
          });
        }
      });
    });

    // Send to bidding
    document.querySelectorAll('.send-to-bidding-btn').forEach(btn => {
      btn.addEventListener('click', function() {
        const requestId = this.dataset.requestId;
        sendToBidding(requestId);
      });
    });

    // API Functions
    async function createPurchaseRequest(itemId) {
      try {
        const response = await fetch('/api/stock/purchase-request', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            stock_item_id: itemId,
            requested_by: 'Current User'
          })
        });

        const data = await response.json();
        
        if (data.success) {
          showAlert('success', data.message);
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert('error', data.message);
        }
      } catch (error) {
        showAlert('error', 'Failed to create purchase request');
        console.error('Error:', error);
      }
    }

    async function bulkCreatePurchaseRequests(itemIds) {
      try {
        const response = await fetch('/api/stock/bulk-purchase-requests', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            stock_item_ids: itemIds,
            requested_by: 'Current User'
          })
        });

        const data = await response.json();
        
        if (data.success) {
          showAlert('success', data.message);
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert('error', data.message);
        }
      } catch (error) {
        showAlert('error', 'Failed to create purchase requests');
        console.error('Error:', error);
      }
    }

    async function autoGenerateRequests() {
      if (!confirm('This will create purchase requests for all low stock items without existing requests. Continue?')) {
        return;
      }

      try {
        const response = await fetch('/api/stock/auto-generate', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        });

        const data = await response.json();
        
        if (data.success) {
          showAlert('success', data.message);
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert('error', data.message);
        }
      } catch (error) {
        showAlert('error', 'Failed to auto-generate requests');
        console.error('Error:', error);
      }
    }

    async function approvePurchaseRequest(requestId) {
      try {
        const response = await fetch('/api/stock/approve-request', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            purchase_request_id: requestId,
            approved_by: 'Current User'
          })
        });

        const data = await response.json();
        
        if (data.success) {
          showAlert('success', data.message);
          setTimeout(() => location.reload(), 1500);
        } else {
          showAlert('error', data.message);
        }
      } catch (error) {
        showAlert('error', 'Failed to approve request');
        console.error('Error:', error);
      }
    }

    async function sendToBidding(requestId) {
      try {
        const response = await fetch('/api/stock/send-to-bidding', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          },
          body: JSON.stringify({
            purchase_request_id: requestId
          })
        });

        const data = await response.json();
        
        if (data.success) {
          showAlert('success', data.message);
          if (data.bidding_url) {
            setTimeout(() => {
              if (confirm('Would you like to view the bidding page now?')) {
                window.open(data.bidding_url, '_blank');
              }
              location.reload();
            }, 1500);
          } else {
            setTimeout(() => location.reload(), 1500);
          }
        } else {
          showAlert('error', data.message);
        }
      } catch (error) {
        showAlert('error', 'Failed to send to bidding');
        console.error('Error:', error);
      }
    }

    function refreshLowStock() {
      location.reload();
    }

    function showAlert(type, message) {
      // Create alert element
      const alertDiv = document.createElement('div');
      alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
      alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
      alertDiv.innerHTML = `
        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'}"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      `;
      
      document.body.appendChild(alertDiv);
      
      // Auto remove after 5 seconds
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
    }
  
  </script>
  </body>
  </html>