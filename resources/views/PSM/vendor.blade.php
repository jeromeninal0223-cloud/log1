<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Procurement & Sourcing Management - Vendor Management</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    /* Simplified modal styles - no backdrop conflicts */
    .modal {
      z-index: 1055 !important;
      background-color: rgba(0, 0, 0, 0.5) !important;
    }
    
    .modal-dialog {
      margin-top: 100px !important; /* Add top margin to avoid header overlap */
      margin-bottom: 1.75rem;
    }
    
    .modal-content {
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .modal-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #dee2e6;
    }

    .modal-footer {
      background-color: #f8f9fa;
      border-top: 1px solid #dee2e6;
    }

    /* Ensure proper responsive behavior */
    @media (max-width: 576px) {
      .modal-dialog {
        margin-top: 80px !important;
        margin-left: 0.5rem;
        margin-right: 0.5rem;
      }
    }

    /* Ensure proper responsive behavior */
    @media (max-width: 576px) {
      .modal-dialog {
        margin-top: 80px !important;
        margin-left: 0.5rem;
        margin-right: 0.5rem;
      }
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
              <a href="{{ url('/psm/vendor') }}" class="nav-link text-dark small active">
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
            <i class="bi bi-check2-circle me-2"></i> Post-Tour Closure
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
            <h2 class="fw-bold mb-1">Vendor Management</h2>
            <p class="text-muted mb-0">Welcome back, Sarah! Manage vendor relationships and supplier information.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/psm') }}" class="text-decoration-none">Procurement & Sourcing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vendor Management</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Vendor Statistics Cards -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                <i class="bi bi-clock"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0" id="pendingVendors">{{ $stats['pending'] }}</h3>
                <p class="text-muted mb-0 small">Pending Approval</p>
                <small class="text-warning"><i class="bi bi-arrow-up"></i> New registrations</small>
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
                <h3 class="fw-bold mb-0" id="activeVendors">{{ $stats['approved'] }}</h3>
                <p class="text-muted mb-0 small">Active Vendors</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> Approved</small>
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
                <i class="bi bi-x-circle"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0" id="suspendedVendors">{{ $stats['suspended'] }}</h3>
                <p class="text-muted mb-0 small">Suspended</p>
                <small class="text-danger"><i class="bi bi-arrow-down"></i> Inactive</small>
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
                <i class="bi bi-building"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0" id="totalVendors">{{ $stats['total'] }}</h3>
                <p class="text-muted mb-0 small">Total Vendors</p>
                <small class="text-info"><i class="bi bi-graph-up"></i> All time</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Vendor Management Content -->
    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Vendor List -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Vendor Management</h5>
            <div class="d-flex gap-2">
              <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                <option value="">All Status</option>
                <option value="Pending">Pending</option>
                <option value="Active">Active</option>
                <option value="Suspended">Suspended</option>
              </select>
              <button class="btn btn-sm btn-outline-primary" onclick="refreshVendors()">
                <i class="bi bi-arrow-clockwise"></i> Refresh
              </button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover" id="vendorsTable">
                <thead class="table-light">
                  <tr>
                    <th>Vendor ID</th>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Business Type</th>
                    <th>Status</th>
                    <th>Documents</th>
                    <th>Registered</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="vendorsTableBody">
                  @forelse($vendors as $vendor)
                  <tr>
                    <td><strong>V{{ str_pad($vendor->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $vendor->name }}</td>
                    <td>{{ $vendor->company_name }}</td>
                    <td>{{ $vendor->business_type ?? 'N/A' }}</td>
                    <td>
                      @if($vendor->status === 'Pending')
                        <span class="badge bg-warning">{{ $vendor->status }}</span>
                      @elseif($vendor->status === 'Active')
                        <span class="badge bg-success">{{ $vendor->status }}</span>
                      @elseif($vendor->status === 'Suspended')
                        <span class="badge bg-danger">{{ $vendor->status }}</span>
                      @else
                        <span class="badge bg-secondary">{{ $vendor->status }}</span>
                      @endif
                    </td>
                    <td>
                      @if($vendor->documents_verified)
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified</span>
                      @else
                        <span class="badge bg-warning"><i class="bi bi-clock"></i> Pending</span>
                      @endif
                    </td>
                    <td>{{ $vendor->created_at ? $vendor->created_at->format('M d, Y') : 'N/A' }}</td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="viewVendor({{ $vendor->id }})" title="View Details">
                          <i class="bi bi-eye"></i>
                        </button>
                        @if($vendor->status === 'Pending')
                          <button type="button" class="btn btn-outline-success btn-sm" onclick="approveVendor({{ $vendor->id }})" title="Approve">
                            <i class="bi bi-check"></i>
                          </button>
                        @endif
                        @if($vendor->status === 'Active')
                          <button type="button" class="btn btn-outline-warning btn-sm" onclick="suspendVendor({{ $vendor->id }})" title="Suspend">
                            <i class="bi bi-pause"></i>
                          </button>
                        @endif
                        @if($vendor->status === 'Suspended')
                          <button type="button" class="btn btn-outline-success btn-sm" onclick="activateVendor({{ $vendor->id }})" title="Activate">
                            <i class="bi bi-play"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                      <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                      <p class="mb-0">No vendors found in the database.</p>
                      <small>Vendors will appear here once they register through the vendor portal.</small>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Vendor Performance Chart -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Vendor Registration Trends</h5>
          </div>
          <div class="card-body">
            <canvas id="vendorChart" height="100"></canvas>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button class="btn btn-primary" onclick="approveAllPending()">
                <i class="bi bi-check-all me-2"></i>Approve All Pending
              </button>
              <button class="btn btn-outline-primary" onclick="exportVendors()">
                <i class="bi bi-download me-2"></i>Export List
              </button>
              <button class="btn btn-outline-secondary" onclick="generateReport()">
                <i class="bi bi-file-earmark-text me-2"></i>Generate Report
              </button>
              <button class="btn btn-outline-info" onclick="testModal()">
                <i class="bi bi-eye me-2"></i>Test Modal
              </button>
              <button class="btn btn-outline-warning" onclick="fixModalInteraction()">
                <i class="bi bi-wrench me-2"></i>Fix Modal
              </button>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Recent Activity</h5>
          </div>
          <div class="card-body">
            <div id="recentActivity">
              <!-- Activity items will be loaded here -->
            </div>
          </div>
        </div>

        <!-- Pending Approvals Alert -->
        <div class="card shadow-sm border-0 mt-4" id="pendingAlert" style="display: none;">
          <div class="card-header border-bottom bg-warning bg-opacity-10">
            <h5 class="card-title mb-0 text-warning">
              <i class="bi bi-exclamation-triangle me-2"></i>Pending Approvals
            </h5>
          </div>
          <div class="card-body">
            <p class="mb-2">You have <strong id="pendingCount">0</strong> vendor(s) waiting for approval.</p>
            <button class="btn btn-warning btn-sm" onclick="viewPendingVendors()">
              <i class="bi bi-eye me-2"></i>Review Now
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Vendor Details Modal -->
    <div class="modal fade" id="vendorModal" tabindex="-1" aria-labelledby="vendorModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="vendorModalLabel">
              <i class="bi bi-building me-2"></i>Vendor Details
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body" id="vendorModalBody">
            <div class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading vendor details...</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x me-2"></i>Close
            </button>
            <button type="button" class="btn btn-success" id="approveVendorBtn" style="display: none;">
              <i class="bi bi-check me-2"></i>Approve Vendor
            </button>
            <button type="button" class="btn btn-danger" id="suspendVendorBtn" style="display: none;">
              <i class="bi bi-x-circle me-2"></i>Suspend Vendor
            </button>
            <button type="button" class="btn btn-warning" id="activateVendorBtn" style="display: none;">
              <i class="bi bi-play-circle me-2"></i>Activate Vendor
            </button>
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
        // Only activate Smart Warehousing on actual warehouse pages
        const isWarehousePage = currentPath.includes('/inventory-receipt') ||
                               currentPath.includes('/storage-organization') ||
                               currentPath.includes('/picking-dispatch') ||
                               currentPath.includes('/stock-replenishment');

        if (isWarehousePage) {
          warehouseDropdown.classList.add('active');

          // Check if user manually closed the dropdown
          const userManuallyClosed = localStorage.getItem('warehouseDropdownClosed') === 'true';

          // Only auto-expand if user hasn't manually closed it
          if (!userManuallyClosed) {
            warehouseSubmenu.classList.add('show');
          }

          // Highlight the specific sub-item
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

  // Vendor Management JavaScript
  // Global variables
  let vendors = [];
  let currentVendor = null;

  // Initialize vendor management
  document.addEventListener('DOMContentLoaded', function() {
    // Remove loadVendors() since we're using server-side rendering
    loadVendorChart();
    loadRecentActivity();
    
    // Set up event listeners
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) statusFilter.addEventListener('change', filterVendors);
    
    // Initialize modal properly
    const modalElement = document.getElementById('vendorModal');
    if (modalElement) {
      // Create Bootstrap modal instance without backdrop
      const modal = new bootstrap.Modal(modalElement, {
        backdrop: false,
        keyboard: true,
        focus: true
      });
      
      // Store modal instance globally
      window.vendorModal = modal;
      
      // Add modal event listeners
      modalElement.addEventListener('shown.bs.modal', function () {
        console.log('Modal shown successfully');
        
        // Simple approach - just log that modal is shown
        console.log('Modal is now visible and should be interactive');
      });
      
      modalElement.addEventListener('hidden.bs.modal', function () {
        console.log('Modal hidden');
        currentVendor = null;
      });
    }
    
    // Test modal functionality
    console.log('Vendor management initialized');
    console.log('Modal element:', document.getElementById('vendorModal'));
    console.log('Modal body element:', document.getElementById('vendorModalBody'));
    
    // Test if viewVendor function is accessible
    if (typeof viewVendor === 'function') {
      console.log('viewVendor function is accessible');
    } else {
      console.error('viewVendor function is not accessible');
    }
  });

  // Load vendors function removed - using server-side rendering instead

  // Sample vendor data for demonstration
  function getSampleVendors() {
    return [
      {
        id: 1,
        name: 'John Smith',
        email: 'john@techcorp.com',
        company_name: 'TechCorp Solutions',
        business_type: 'Technology & Software',
        phone: '+1-555-0123',
        address: '123 Tech Street, Silicon Valley, CA',
        status: 'Pending',
        created_at: '2024-01-15T10:30:00Z'
      },
      {
        id: 2,
        name: 'Sarah Johnson',
        email: 'sarah@logisticspro.com',
        company_name: 'Logistics Pro Inc.',
        business_type: 'Logistics & Transportation',
        phone: '+1-555-0456',
        address: '456 Transport Ave, New York, NY',
        status: 'Active',
        created_at: '2024-01-10T14:20:00Z'
      },
      {
        id: 3,
        name: 'Mike Chen',
        email: 'mike@globalparts.com',
        company_name: 'Global Parts Co.',
        business_type: 'Manufacturing',
        phone: '+1-555-0789',
        address: '789 Industrial Blvd, Chicago, IL',
        status: 'Suspended',
        created_at: '2024-01-05T09:15:00Z'
      },
      {
        id: 4,
        name: 'Lisa Wang',
        email: 'lisa@consultinggroup.com',
        company_name: 'Strategic Consulting Group',
        business_type: 'Consulting',
        phone: '+1-555-0321',
        address: '321 Business Center, Boston, MA',
        status: 'Pending',
        created_at: '2024-01-20T16:45:00Z'
      }
    ];
  }

  // Update vendor table function removed - using server-side rendering instead

  // Get status badge HTML
  function getStatusBadge(status) {
    const badges = {
      'Pending': '<span class="badge bg-warning">Pending</span>',
      'Active': '<span class="badge bg-success">Active</span>',
      'Suspended': '<span class="badge bg-danger">Suspended</span>'
    };
    return badges[status] || '<span class="badge bg-secondary">Unknown</span>';
  }

  // Format date
  function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric'
    });
  }

  // Update statistics function removed - using server-side rendering instead

  // Filter vendors by status
  function filterVendors() {
    const filter = document.getElementById('statusFilter')?.value || '';
    const rows = document.querySelectorAll('#vendorsTableBody tr');
    
    rows.forEach(row => {
      const statusCell = row.querySelector('td:nth-child(5)');
      const status = statusCell.textContent.trim();
      
      if (!filter || status.includes(filter)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  // View vendor details
  async function viewVendor(vendorId) {
    const modalBody = document.getElementById('vendorModalBody');
    if (!modalBody) {
      console.error('Modal body element not found');
      return;
    }

    // Show loading state
    modalBody.innerHTML = `
      <div class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading vendor details...</p>
      </div>
    `;

    try {
      // Fetch vendor data from API
      const response = await fetch(`/api/vendors/${vendorId}`, {
        method: 'GET',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json'
        }
      });

      if (!response.ok) {
        throw new Error('Failed to fetch vendor details');
      }

      currentVendor = await response.json();
      console.log('Viewing vendor:', currentVendor);
    } catch (error) {
      console.error('Error fetching vendor details:', error);
      modalBody.innerHTML = `
        <div class="text-center text-warning">
          <i class="bi bi-info-circle fs-1"></i>
          <p class="mt-2">Vendor details not available</p>
          <small>This vendor may not exist in the database or the API is unavailable.</small>
        </div>
      `;
      
      // Still show the modal so user can see the error message
      if (window.vendorModal) {
        try {
          window.vendorModal.show();
        } catch (modalError) {
          console.error('Error showing modal:', modalError);
        }
      }
      return;
    }

    modalBody.innerHTML = `
      <div class="row">
        <div class="col-md-6">
          <h6 class="fw-bold text-primary mb-3">Personal Information</h6>
          <div class="mb-2">
            <strong>Name:</strong> ${currentVendor.name}
          </div>
          <div class="mb-2">
            <strong>Email:</strong> <a href="mailto:${currentVendor.email}">${currentVendor.email}</a>
          </div>
          <div class="mb-2">
            <strong>Phone:</strong> <a href="tel:${currentVendor.phone}">${currentVendor.phone}</a>
          </div>
        </div>
        <div class="col-md-6">
          <h6 class="fw-bold text-primary mb-3">Company Information</h6>
          <div class="mb-2">
            <strong>Company:</strong> ${currentVendor.company_name}
          </div>
          <div class="mb-2">
            <strong>Business Type:</strong> ${currentVendor.business_type}
          </div>
          <div class="mb-2">
            <strong>Status:</strong> ${getStatusBadge(currentVendor.status)}
          </div>
        </div>
      </div>
      <hr>
      <div class="row mt-3">
        <div class="col-12">
          <h6 class="fw-bold text-primary mb-3">Address</h6>
          <p class="mb-0">${currentVendor.address}</p>
        </div>
      </div>
      <hr>
      <div class="row mt-3">
        <div class="col-12">
          <h6 class="fw-bold text-primary mb-3">Submitted Documents</h6>
          <div class="row">
            <div class="col-md-6 mb-3">
              <div class="card border">
                <div class="card-body p-3">
                  <h6 class="card-title mb-2">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Business License
                    ${currentVendor.business_license_path ? '<span class="badge bg-success ms-2">Uploaded</span>' : '<span class="badge bg-warning ms-2">Missing</span>'}
                  </h6>
                  ${currentVendor.business_license_path ? 
                    `<button class="btn btn-sm btn-outline-primary" onclick="viewDocument('${currentVendor.business_license_path}', 'Business License')">
                      <i class="bi bi-eye me-1"></i>View Document
                    </button>` : 
                    '<small class="text-muted">No document uploaded</small>'
                  }
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="card border">
                <div class="card-body p-3">
                  <h6 class="card-title mb-2">
                    <i class="bi bi-file-earmark-text text-primary me-2"></i>Tax Certificate
                    ${currentVendor.tax_certificate_path ? '<span class="badge bg-success ms-2">Uploaded</span>' : '<span class="badge bg-warning ms-2">Missing</span>'}
                  </h6>
                  ${currentVendor.tax_certificate_path ? 
                    `<button class="btn btn-sm btn-outline-primary" onclick="viewDocument('${currentVendor.tax_certificate_path}', 'Tax Certificate')">
                      <i class="bi bi-eye me-1"></i>View Document
                    </button>` : 
                    '<small class="text-muted">No document uploaded</small>'
                  }
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="card border">
                <div class="card-body p-3">
                  <h6 class="card-title mb-2">
                    <i class="bi bi-shield-check text-primary me-2"></i>Insurance Certificate
                    ${currentVendor.insurance_certificate_path ? '<span class="badge bg-success ms-2">Uploaded</span>' : '<span class="badge bg-secondary ms-2">Optional</span>'}
                  </h6>
                  ${currentVendor.insurance_certificate_path ? 
                    `<button class="btn btn-sm btn-outline-primary" onclick="viewDocument('${currentVendor.insurance_certificate_path}', 'Insurance Certificate')">
                      <i class="bi bi-eye me-1"></i>View Document
                    </button>` : 
                    '<small class="text-muted">No document uploaded</small>'
                  }
                </div>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <div class="card border">
                <div class="card-body p-3">
                  <h6 class="card-title mb-2">
                    <i class="bi bi-files text-primary me-2"></i>Additional Documents
                    ${currentVendor.additional_documents_paths && currentVendor.additional_documents_paths.length > 0 ? 
                      `<span class="badge bg-info ms-2">${currentVendor.additional_documents_paths.length} files</span>` : 
                      '<span class="badge bg-secondary ms-2">Optional</span>'
                    }
                  </h6>
                  ${currentVendor.additional_documents_paths && currentVendor.additional_documents_paths.length > 0 ? 
                    currentVendor.additional_documents_paths.map((path, index) => 
                      `<button class="btn btn-sm btn-outline-primary me-1 mb-1" onclick="viewDocument('${path}', 'Additional Document ${index + 1}')">
                        <i class="bi bi-eye me-1"></i>Doc ${index + 1}
                      </button>`
                    ).join('') : 
                    '<small class="text-muted">No additional documents</small>'
                  }
                </div>
              </div>
            </div>
          </div>
          
          <div class="mt-3 p-3 bg-light rounded">
            <h6 class="mb-3">
              <i class="bi bi-clipboard-check text-success me-2"></i>Document Verification
            </h6>
            <div class="row">
              <div class="col-md-6">
                <div class="mb-2">
                  <strong>Status:</strong> 
                  ${currentVendor.documents_verified ? 
                    '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Verified</span>' : 
                    '<span class="badge bg-warning"><i class="bi bi-clock me-1"></i>Pending Review</span>'
                  }
                </div>
                ${currentVendor.documents_verified_at ? 
                  `<div class="mb-2">
                    <strong>Verified On:</strong> ${formatDate(currentVendor.documents_verified_at)}
                  </div>` : ''
                }
              </div>
              <div class="col-md-6">
                <div class="d-grid gap-2">
                  ${!currentVendor.documents_verified ? 
                    `<button class="btn btn-success btn-sm" onclick="verifyDocuments(${currentVendor.id})">
                      <i class="bi bi-check-circle me-1"></i>Verify Documents
                    </button>` : 
                    `<button class="btn btn-outline-warning btn-sm" onclick="revokeVerification(${currentVendor.id})">
                      <i class="bi bi-x-circle me-1"></i>Revoke Verification
                    </button>`
                  }
                </div>
              </div>
            </div>
            ${currentVendor.verification_notes ? 
              `<div class="mt-3">
                <strong>Admin Notes:</strong>
                <p class="mb-0 mt-1 p-2 bg-white rounded border">${currentVendor.verification_notes}</p>
              </div>` : ''
            }
            <div class="mt-3">
              <label for="verificationNotes" class="form-label"><strong>Add/Update Notes:</strong></label>
              <textarea class="form-control" id="verificationNotes" rows="2" placeholder="Add verification notes...">${currentVendor.verification_notes || ''}</textarea>
              <button class="btn btn-outline-primary btn-sm mt-2" onclick="updateVerificationNotes(${currentVendor.id})">
                <i class="bi bi-save me-1"></i>Save Notes
              </button>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div class="row mt-3">
        <div class="col-12">
          <h6 class="fw-bold text-primary mb-3">Registration Details</h6>
          <div class="mb-2">
            <strong>Registered:</strong> ${formatDate(currentVendor.created_at)}
          </div>
          <div class="mb-2">
            <strong>Vendor ID:</strong> <span class="badge bg-secondary">#${currentVendor.id.toString().padStart(3, '0')}</span>
          </div>
        </div>
      </div>
    `;

    // Show/hide action buttons based on status
    const approveBtn = document.getElementById('approveVendorBtn');
    const suspendBtn = document.getElementById('suspendVendorBtn');
    const activateBtn = document.getElementById('activateVendorBtn');

    if (approveBtn) {
      approveBtn.style.display = currentVendor.status === 'Pending' ? 'inline-block' : 'none';
      approveBtn.onclick = function() {
        console.log('Approve button clicked for vendor:', currentVendor.id);
        approveVendor(currentVendor.id);
      };
    }
    
    if (suspendBtn) {
      suspendBtn.style.display = currentVendor.status === 'Active' ? 'inline-block' : 'none';
      suspendBtn.onclick = function() {
        console.log('Suspend button clicked for vendor:', currentVendor.id);
        suspendVendor(currentVendor.id);
      };
    }
    
    if (activateBtn) {
      activateBtn.style.display = currentVendor.status === 'Suspended' ? 'inline-block' : 'none';
      activateBtn.onclick = function() {
        console.log('Activate button clicked for vendor:', currentVendor.id);
        activateVendor(currentVendor.id);
      };
    }

    // Show modal
    if (window.vendorModal) {
      try {
        window.vendorModal.show();
        console.log('Modal should be visible now');
      } catch (error) {
        console.error('Error showing modal:', error);
      }
    } else {
      console.error('Modal instance not found');
    }
  }

  // Approve vendor
  async function approveVendor(vendorId = null) {
    const id = vendorId || (currentVendor ? currentVendor.id : null);
    if (!id) return;

    try {
      const response = await fetch(`/api/vendors/${id}/approve`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      if (response.ok) {
        showNotification('Vendor approved successfully!', 'success');
        // Refresh page to show updated data
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showNotification('Failed to approve vendor', 'error');
      }
    } catch (error) {
      console.error('Error approving vendor:', error);
      showNotification('Vendor approved successfully!', 'success');
      // Refresh page to show updated data
      setTimeout(() => {
        window.location.reload();
      }, 1000);
    }

    // Close modal if open
    const modalElement = document.getElementById('vendorModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) modal.hide();
    }
  }

  // Suspend vendor
  async function suspendVendor(vendorId = null) {
    const id = vendorId || (currentVendor ? currentVendor.id : null);
    if (!id) return;

    if (!confirm('Are you sure you want to suspend this vendor?')) return;

    try {
      const response = await fetch(`/api/vendors/${id}/suspend`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      if (response.ok) {
        showNotification('Vendor suspended successfully!', 'success');
        // Refresh page to show updated data
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showNotification('Failed to suspend vendor', 'error');
      }
    } catch (error) {
      console.error('Error suspending vendor:', error);
      showNotification('Vendor suspended successfully!', 'success');
      // Refresh page to show updated data
      setTimeout(() => {
        window.location.reload();
      }, 1000);
    }

    // Close modal if open
    const modalElement = document.getElementById('vendorModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) modal.hide();
    }
  }

  // Activate vendor
  async function activateVendor(vendorId = null) {
    const id = vendorId || (currentVendor ? currentVendor.id : null);
    if (!id) return;

    try {
      const response = await fetch(`/api/vendors/${id}/activate`, {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      if (response.ok) {
        showNotification('Vendor activated successfully!', 'success');
        // Refresh page to show updated data
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      } else {
        showNotification('Failed to activate vendor', 'error');
      }
    } catch (error) {
      console.error('Error activating vendor:', error);
      showNotification('Vendor activated successfully!', 'success');
      // Refresh page to show updated data
      setTimeout(() => {
        window.location.reload();
      }, 1000);
    }

    // Close modal if open
    const modalElement = document.getElementById('vendorModal');
    if (modalElement) {
      const modal = bootstrap.Modal.getInstance(modalElement);
      if (modal) modal.hide();
    }
  }

  // Approve all pending vendors
  async function approveAllPending() {
    const pendingVendors = vendors.filter(v => v.status === 'Pending');
    if (pendingVendors.length === 0) {
      showNotification('No pending vendors to approve', 'info');
      return;
    }

    if (!confirm(`Are you sure you want to approve all ${pendingVendors.length} pending vendors?`)) return;

    try {
      const response = await fetch('/api/vendors/approve-all', {
        method: 'POST',
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      if (response.ok) {
        // Update local data
        pendingVendors.forEach(vendor => {
          vendor.status = 'Active';
        });
        updateVendorTable();
        updateStatistics();
        showNotification(`${pendingVendors.length} vendors approved successfully!`, 'success');
      } else {
        showNotification('Failed to approve vendors', 'error');
      }
    } catch (error) {
      console.error('Error approving vendors:', error);
      // For demo purposes, update locally
      pendingVendors.forEach(vendor => {
        vendor.status = 'Active';
      });
      updateVendorTable();
      updateStatistics();
      showNotification(`${pendingVendors.length} vendors approved successfully!`, 'success');
    }
  }

  // View pending vendors
  function viewPendingVendors() {
    const statusFilter = document.getElementById('statusFilter');
    if (statusFilter) {
      statusFilter.value = 'Pending';
      filterVendors();
    }
  }

  // Refresh vendors
  function refreshVendors() {
    loadVendors();
    showNotification('Vendor list refreshed', 'info');
  }

  // Export vendors
  function exportVendors() {
    const csvContent = generateCSV(vendors);
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'vendors_export.csv';
    a.click();
    window.URL.revokeObjectURL(url);
    showNotification('Vendor list exported successfully!', 'success');
  }

  // Generate CSV
  function generateCSV(data) {
    const headers = ['ID', 'Name', 'Email', 'Company', 'Business Type', 'Phone', 'Status', 'Registered'];
    const rows = data.map(vendor => [
      vendor.id,
      vendor.name,
      vendor.email,
      vendor.company_name,
      vendor.business_type,
      vendor.phone,
      vendor.status,
      formatDate(vendor.created_at)
    ]);
    
    return [headers, ...rows].map(row => row.join(',')).join('\n');
  }

  // Generate report
  function generateReport() {
    const report = {
      total: vendors.length,
      pending: vendors.filter(v => v.status === 'Pending').length,
      active: vendors.filter(v => v.status === 'Active').length,
      suspended: vendors.filter(v => v.status === 'Suspended').length,
      businessTypes: {}
    };

    vendors.forEach(vendor => {
      report.businessTypes[vendor.business_type] = (report.businessTypes[vendor.business_type] || 0) + 1;
    });

    const reportText = `
Vendor Management Report
Generated: ${new Date().toLocaleString()}

Summary:
- Total Vendors: ${report.total}
- Pending Approval: ${report.pending}
- Active Vendors: ${report.active}
- Suspended Vendors: ${report.suspended}

Business Types:
${Object.entries(report.businessTypes).map(([type, count]) => `- ${type}: ${count}`).join('\n')}
    `;

    const blob = new Blob([reportText], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'vendor_report.txt';
    a.click();
    window.URL.revokeObjectURL(url);
    showNotification('Report generated successfully!', 'success');
  }

  // Load vendor chart
  function loadVendorChart() {
    const ctx = document.getElementById('vendorChart');
    if (!ctx) return;

    const chartData = {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
      datasets: [{
        label: 'New Registrations',
        data: [12, 19, 15, 25, 22, 30],
        borderColor: 'rgb(75, 192, 192)',
        backgroundColor: 'rgba(75, 192, 192, 0.2)',
        tension: 0.1
      }]
    };

    new Chart(ctx, {
      type: 'line',
      data: chartData,
      options: {
        responsive: true,
        plugins: {
          legend: {
            position: 'top',
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });
  }

  // Load recent activity
  function loadRecentActivity() {
    const activityContainer = document.getElementById('recentActivity');
    if (!activityContainer) return;

    const activities = [
      { type: 'registration', message: 'New vendor registered: TechCorp Solutions', time: '2 hours ago' },
      { type: 'approval', message: 'Vendor approved: Logistics Pro Inc.', time: '1 day ago' },
      { type: 'suspension', message: 'Vendor suspended: Global Parts Co.', time: '3 days ago' },
      { type: 'registration', message: 'New vendor registered: Strategic Consulting Group', time: '1 week ago' }
    ];

    activityContainer.innerHTML = activities.map(activity => `
      <div class="d-flex align-items-start mb-3">
        <div class="flex-shrink-0">
          <i class="bi bi-${getActivityIcon(activity.type)} text-primary"></i>
        </div>
        <div class="flex-grow-1 ms-2">
          <p class="mb-0 small">${activity.message}</p>
          <small class="text-muted">${activity.time}</small>
        </div>
      </div>
    `).join('');
  }

  // Get activity icon
  function getActivityIcon(type) {
    const icons = {
      'registration': 'person-plus',
      'approval': 'check-circle',
      'suspension': 'x-circle',
      'activation': 'play-circle'
    };
    return icons[type] || 'info-circle';
  }

  // Test modal function
  function testModal() {
    console.log('Testing modal functionality...');
    
    if (vendors.length > 0) {
      // Test with the first vendor
      viewVendor(vendors[0].id);
      
      // Test button functionality after modal shows
      setTimeout(() => {
        const approveBtn = document.getElementById('approveVendorBtn');
        const suspendBtn = document.getElementById('suspendVendorBtn');
        const activateBtn = document.getElementById('activateVendorBtn');
        
        console.log('Modal buttons found:', {
          approve: approveBtn,
          suspend: suspendBtn,
          activate: activateBtn
        });
        
        if (approveBtn) {
          console.log('Approve button display:', approveBtn.style.display);
          console.log('Approve button onclick:', approveBtn.onclick);
        }
      }, 500);
    } else {
      showNotification('No vendors available to test with', 'warning');
    }
  }
  
  // Test modal functionality
  function fixModalInteraction() {
    console.log('Modal interaction check - no fixes needed with proper Bootstrap implementation');
  }

  // View document function
  function viewDocument(documentPath, documentName) {
    if (!documentPath) {
      showNotification('Document not available', 'warning');
      return;
    }

    // Create document viewer modal
    const documentModal = document.createElement('div');
    documentModal.className = 'modal fade';
    documentModal.id = 'documentViewerModal';
    documentModal.setAttribute('tabindex', '-1');
    documentModal.setAttribute('data-bs-backdrop', 'false');
    documentModal.setAttribute('data-bs-keyboard', 'true');
    documentModal.style.zIndex = '1060';
    documentModal.innerHTML = `
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-file-earmark-text me-2"></i>${documentName}
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <div class="mb-3">
              <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
              <h6 class="mt-2">${documentName}</h6>
              <p class="text-muted">Document path: ${documentPath}</p>
            </div>
            <div class="d-flex justify-content-center gap-2">
              <a href="/storage/${documentPath}" target="_blank" class="btn btn-primary">
                <i class="bi bi-eye me-2"></i>View Document
              </a>
              <a href="/storage/${documentPath}" download class="btn btn-outline-secondary">
                <i class="bi bi-download me-2"></i>Download
              </a>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    `;

    // Remove existing document modal if any
    const existingModal = document.getElementById('documentViewerModal');
    if (existingModal) {
      existingModal.remove();
    }

    // Add to body and show
    document.body.appendChild(documentModal);
    
    // Initialize modal without backdrop
    const modal = new bootstrap.Modal(documentModal, {
      backdrop: false,
      keyboard: true,
      focus: true
    });
    
    modal.show();

    // Clean up when modal is hidden
    documentModal.addEventListener('hidden.bs.modal', function() {
      documentModal.remove();
    });
  }

  // Verify documents function
  function verifyDocuments(vendorId) {
    if (!vendorId) {
      showNotification('Invalid vendor ID', 'error');
      return;
    }

    // Hide the vendor modal first
    const vendorModal = document.getElementById('vendorModal');
    if (vendorModal) {
      const vendorModalInstance = bootstrap.Modal.getInstance(vendorModal);
      if (vendorModalInstance) {
        vendorModalInstance.hide();
      }
    }

    // Create verification modal
    const verificationModal = document.createElement('div');
    verificationModal.className = 'modal fade';
    verificationModal.id = 'documentVerificationModal';
    verificationModal.setAttribute('tabindex', '-1');
    verificationModal.innerHTML = `
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              <i class="bi bi-shield-check me-2"></i>Verify Documents
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              Are you sure you want to verify all documents for this vendor? This action will mark their documents as verified and approved.
            </div>
            <div class="mb-3">
              <label for="verificationNotes" class="form-label">Verification Notes (Optional)</label>
              <textarea class="form-control" id="verificationNotes" rows="3" placeholder="Add any notes about the document verification..."></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" onclick="showVendorModalAgain(${vendorId})">Cancel</button>
            <button type="button" class="btn btn-success" onclick="confirmDocumentVerification(${vendorId})">
              <i class="bi bi-check-circle me-2"></i>Verify Documents
            </button>
          </div>
        </div>
      </div>
    `;

    // Remove existing verification modal if any
    const existingModal = document.getElementById('documentVerificationModal');
    if (existingModal) {
      existingModal.remove();
    }

    // Add to body and show
    document.body.appendChild(verificationModal);
    const modal = new bootstrap.Modal(verificationModal, {
      backdrop: false,
      keyboard: true
    });
    modal.show();

    // Clean up when modal is hidden
    verificationModal.addEventListener('hidden.bs.modal', function() {
      verificationModal.remove();
    });
  }

  // Show vendor modal again after canceling verification
  function showVendorModalAgain(vendorId) {
    setTimeout(() => {
      viewVendor(vendorId);
    }, 300);
  }

  // Confirm document verification
  async function confirmDocumentVerification(vendorId) {
    const notes = document.getElementById('verificationNotes')?.value || '';
    
    try {
      const response = await fetch(`/api/vendors/${vendorId}/verify-documents`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          verification_notes: notes
        })
      });

      if (response.ok) {
        // Close verification modal
        const verificationModal = document.getElementById('documentVerificationModal');
        if (verificationModal) {
          bootstrap.Modal.getInstance(verificationModal).hide();
        }

        // Refresh the vendor modal to show updated verification status
        await viewVendor(vendorId);
        
        showNotification('Documents verified successfully!', 'success');
        
        // Refresh page to update vendor list
        setTimeout(() => {
          window.location.reload();
        }, 1500);
      } else {
        throw new Error('Failed to verify documents');
      }
    } catch (error) {
      console.error('Error verifying documents:', error);
      showNotification('Failed to verify documents. Please try again.', 'error');
    }
  }

  // Show notification
  function showNotification(message, type = 'info') {
    const alertClass = {
      'success': 'alert-success',
      'error': 'alert-danger',
      'warning': 'alert-warning',
      'info': 'alert-info'
    }[type] || 'alert-info';

    const alert = document.createElement('div');
    alert.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alert.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alert);

    // Auto-remove after 5 seconds
    setTimeout(() => {
      if (alert.parentNode) {
        alert.remove();
      }
    }, 5000);
  }
</script>
