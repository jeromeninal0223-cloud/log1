<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Jetlouge Travels Admin</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">

  <style>
    .status-indicator {
      width: 12px;
      height: 12px;
      border-radius: 50%;
      display: inline-block;
    }
    
    .task-priority {
      width: 4px;
      height: 40px;
      border-radius: 2px;
      display: inline-block;
    }
    
    .task-item {
      padding: 8px 0;
      border-bottom: 1px solid #f0f0f0;
    }
    
    .task-item:last-child {
      border-bottom: none;
    }
    
    .stat-card {
      transition: transform 0.2s ease-in-out;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
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
        <a href="#" class="nav-link text-dark active">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-dark" data-bs-toggle="collapse" data-bs-target="#warehouseSubmenu" aria-expanded="false" aria-controls="warehouseSubmenu">
          <i class="bi bi-box-seam me-2"></i> Smart Warehousing System
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
              <a href="{{ url('/alms/disposalretirement') }}" class="nav-link text-dark small active">
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
            <img src="{{ asset('assets/images/jetlouge_logo.png') }}" alt="Jetlouge Travels" class="logo-img">
          </div>
          <div>
            <h2 class="fw-bold mb-1">Travel Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Here's what's happening with your travel business today.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
                <i class="bi bi-airplane"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">1,247</h3>
                <p class="text-muted mb-0 small">Active Tours</p>
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
                <i class="bi bi-cart-check"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">342</h3>
                <p class="text-muted mb-0 small">Pending Orders</p>
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
                <i class="bi bi-truck"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">89%</h3>
                <p class="text-muted mb-0 small">Warehouse Capacity</p>
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
                <i class="bi bi-clock-history"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">2.3hrs</h3>
                <p class="text-muted mb-0 small">Avg Pick Time</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Additional Statistics Row -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
                <i class="bi bi-tools"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">15</h3>
                <p class="text-muted mb-0 small">Maintenance Due</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-secondary bg-opacity-10 text-secondary me-3">
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">2,847</h3>
                <p class="text-muted mb-0 small">Documents</p>
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
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">$45.2K</h3>
                <p class="text-muted mb-0 small">Monthly Revenue</p>
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
                <i class="bi bi-people"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">156</h3>
                <p class="text-muted mb-0 small">Active Customers</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- System Status & Performance -->
    <div class="row g-4 mb-4">
      <div class="col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">System Status</h5>
          </div>
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="d-flex align-items-center">
                <div class="status-indicator bg-success me-3"></div>
                <span>Warehouse Management</span>
              </div>
              <span class="badge bg-success">Operational</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="d-flex align-items-center">
                <div class="status-indicator bg-success me-3"></div>
                <span>Procurement System</span>
              </div>
              <span class="badge bg-success">Operational</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="d-flex align-items-center">
                <div class="status-indicator bg-warning me-3"></div>
                <span>Asset Management</span>
              </div>
              <span class="badge bg-warning">Maintenance</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="d-flex align-items-center">
                <div class="status-indicator bg-success me-3"></div>
                <span>Document Tracking</span>
              </div>
              <span class="badge bg-success">Operational</span>
            </div>
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-center">
                <div class="status-indicator bg-success me-3"></div>
                <span>Tour Logistics</span>
              </div>
              <span class="badge bg-success">Operational</span>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Upcoming Tasks</h5>
          </div>
          <div class="card-body">
            <div class="task-item d-flex align-items-center mb-3">
              <div class="task-priority bg-danger me-3"></div>
              <div class="flex-grow-1">
                <div class="fw-semibold">Vendor Contract Renewal</div>
                <small class="text-muted">Due: Dec 20, 2024</small>
              </div>
              <span class="badge bg-danger">High</span>
            </div>
            <div class="task-item d-flex align-items-center mb-3">
              <div class="task-priority bg-warning me-3"></div>
              <div class="flex-grow-1">
                <div class="fw-semibold">Asset Maintenance Check</div>
                <small class="text-muted">Due: Dec 22, 2024</small>
              </div>
              <span class="badge bg-warning">Medium</span>
            </div>
            <div class="task-item d-flex align-items-center mb-3">
              <div class="task-priority bg-info me-3"></div>
              <div class="flex-grow-1">
                <div class="fw-semibold">Inventory Audit</div>
                <small class="text-muted">Due: Dec 25, 2024</small>
              </div>
              <span class="badge bg-info">Low</span>
            </div>
            <div class="task-item d-flex align-items-center">
              <div class="task-priority bg-info me-3"></div>
              <div class="flex-grow-1">
                <div class="fw-semibold">Monthly Report Generation</div>
                <small class="text-muted">Due: Dec 31, 2024</small>
              </div>
              <span class="badge bg-info">Low</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activity & Quick Actions -->
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Recent Activities</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Activity</th>
                    <th>Module</th>
                    <th>User</th>
                    <th>Time</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-box-arrow-in-down text-primary me-2"></i>
                        <span>Inventory Receipt #INV-2024-001</span>
                      </div>
                    </td>
                    <td>Warehouse</td>
                    <td>{{ Auth::user()->name }}</td>
                    <td>2 hours ago</td>
                    <td><span class="badge bg-success">Completed</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-cart-plus text-warning me-2"></i>
                        <span>Purchase Order #PO-2024-156</span>
                      </div>
                    </td>
                    <td>Procurement</td>
                    <td>Sarah Johnson</td>
                    <td>4 hours ago</td>
                    <td><span class="badge bg-warning">Pending</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-tools text-info me-2"></i>
                        <span>Asset Maintenance #AM-2024-089</span>
                      </div>
                    </td>
                    <td>Asset Management</td>
                    <td>Mike Chen</td>
                    <td>6 hours ago</td>
                    <td><span class="badge bg-info">In Progress</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-flag text-success me-2"></i>
                        <span>Tour Setup #TS-2024-234</span>
                      </div>
                    </td>
                    <td>Logistics</td>
                    <td>Emma Wilson</td>
                    <td>8 hours ago</td>
                    <td><span class="badge bg-success">Completed</span></td>
                  </tr>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text text-secondary me-2"></i>
                        <span>Document Upload #DOC-2024-567</span>
                      </div>
                    </td>
                    <td>Document Tracking</td>
                    <td>David Brown</td>
                    <td>1 day ago</td>
                    <td><span class="badge bg-success">Completed</span></td>
                  </tr>
                </tbody>
              </table>
            </div>
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
              @if(Auth::user()->role !== 'logistics_staff')
              <a href="{{ url('/psm/request') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Purchase Request
              </a>
              @endif
              <a href="{{ url('/inventory-receipt') }}" class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-in-down me-2"></i>Inventory Receipt
              </a>
              @if(Auth::user()->role !== 'procurement_officer')
              <a href="{{ url('/plt/toursetup') }}" class="btn btn-outline-success">
                <i class="bi bi-flag me-2"></i>Setup New Tour
              </a>
              <a href="{{ url('/alms/assetregistration') }}" class="btn btn-outline-warning">
                <i class="bi bi-tools me-2"></i>Register Asset
              </a>
              <a href="{{ url('/dtrs/document') }}" class="btn btn-outline-info">
                <i class="bi bi-file-earmark-text me-2"></i>Upload Document
              </a>
              @endif
              <button class="btn btn-outline-secondary">
                <i class="bi bi-graph-up me-2"></i>Generate Report
              </button>
            </div>
          </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Module Performance</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Warehouse Management</span>
                <span class="small text-muted">95% efficiency</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar" style="width: 95%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Procurement System</span>
                <span class="small text-muted">87% efficiency</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: 87%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Asset Management</span>
                <span class="small text-muted">78% efficiency</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: 78%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Tour Logistics</span>
                <span class="small text-muted">92% efficiency</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 92%"></div>
              </div>
            </div>
            <div>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Document Tracking</span>
                <span class="small text-muted">89% efficiency</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-secondary" style="width: 89%"></div>
              </div>
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
      // Logout functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
          e.preventDefault();

          try {
            // Call logout route
            const response = await fetch('{{ url('/logout') }}', {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
              }
            });

            if (response.ok) {
              window.location.href = '{{ url('/login') }}';
            }
          } catch (error) {
            console.error('Logout error:', error);
            // Redirect anyway
            window.location.href = '{{ url('/login') }}';
          }
        });
      }

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
  
  </script>
</body>
</html>
