<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard - Jetlouge Travels Admin</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- PSM Animations -->
  <link rel="stylesheet" href="{{ asset('assets/css/psm-animations.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    /* Dashboard Specific Styles */
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

    /* Action buttons */
    .btn-action {
      width: 32px;
      height: 32px;
      border-radius: 8px;
      border: 1px solid;
      background: transparent;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      font-size: 14px;
      transition: all 0.2s ease;
      margin: 0 2px;
    }
    
    .btn-action:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn-action-view {
      border-color: #6c757d;
      color: #6c757d;
    }
    
    .btn-action-view:hover {
      background-color: #6c757d;
      color: white;
    }
    
    .btn-action-edit {
      border-color: #0d6efd;
      color: #0d6efd;
    }
    
    .btn-action-edit:hover {
      background-color: #0d6efd;
      color: white;
    }
    
    .btn-action-delete {
      border-color: #dc3545;
      color: #dc3545;
    }
    
    .btn-action-delete:hover {
      background-color: #dc3545;
      color: white;
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
              <a href="{{ url('/alms/disposalretirement') }}" class="nav-link text-dark small">
                <i class="bi bi-wrench-adjustable me-2"></i> Disposal/Retirement
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ route('alms.vehicle-requests') }}" class="nav-link text-dark small">
                <i class="bi bi-truck me-2"></i> Vehicle Requests
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

    <!-- Key Performance Indicators -->
    <div class="row g-4 mb-5">
      <div class="col-lg-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="bg-primary bg-opacity-10 text-primary me-3 p-3 rounded">
                <i class="bi bi-cart-check fs-4"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0" id="pending-orders">{{ $pendingOrders ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Pending Orders</p>
                <small class="text-{{ ($pendingOrders ?? 0) > 50 ? 'warning' : 'success' }}">
                  {{ ($pendingOrders ?? 0) > 50 ? 'High Volume' : 'Normal' }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="bg-success bg-opacity-10 text-success me-3 p-3 rounded">
                <i class="bi bi-boxes fs-4"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0" id="warehouse-capacity">{{ $warehouseCapacity ?? 0 }}%</h3>
                <p class="text-muted mb-0 small">Warehouse Capacity</p>
                <small class="text-{{ ($warehouseCapacity ?? 0) > 85 ? 'danger' : 'success' }}">
                  {{ ($warehouseCapacity ?? 0) > 85 ? 'Near Full' : 'Available' }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="bg-warning bg-opacity-10 text-warning me-3 p-3 rounded">
                <i class="bi bi-tools fs-4"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0" id="maintenance-due">{{ $maintenanceDue ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Maintenance Due</p>
                <small class="text-{{ ($maintenanceDue ?? 0) > 10 ? 'danger' : 'success' }}">
                  {{ ($maintenanceDue ?? 0) > 10 ? 'Action Required' : 'On Track' }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="bg-info bg-opacity-10 text-info me-3 p-3 rounded">
                <i class="bi bi-cash-stack fs-4"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0" id="monthly-revenue">₱{{ number_format($monthlyRevenue ?? 0, 1) }}K</h3>
                <p class="text-muted mb-0 small">Monthly Revenue</p>
                <small class="text-{{ ($revenueGrowth ?? 0) >= 0 ? 'success' : 'danger' }}">
                  {{ ($revenueGrowth ?? 0) >= 0 ? '+' : '' }}{{ $revenueGrowth ?? 0 }}% vs last month
                </small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Role-Specific Metrics -->
    @if($roleMetrics && count($roleMetrics) > 0)
    <div class="row g-4 mb-5">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-person-badge me-2"></i>
              {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }} Metrics
            </h5>
            <span class="badge bg-primary">Live Data</span>
          </div>
          <div class="card-body">
            <div class="row g-3">
              @foreach($roleMetrics as $key => $value)
              <div class="col-lg-3 col-md-6">
                <div class="text-center p-3 border rounded">
                  <h4 class="fw-bold text-primary mb-1">{{ is_numeric($value) ? number_format($value) : $value }}</h4>
                  <small class="text-muted">{{ ucwords(str_replace('_', ' ', $key)) }}</small>
                </div>
              </div>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif

    <!-- Critical Alerts & Priority Tasks -->
    <div class="row g-4 mb-5">
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Priority Tasks & Alerts</h5>
            <span class="badge bg-primary">{{ count($priorityTasks ?? []) }} Active</span>
          </div>
          <div class="card-body">
            @forelse($priorityTasks ?? [] as $task)
            <div class="task-item d-flex align-items-center mb-3 p-3 rounded" style="background-color: {{ $task['priority'] === 'high' ? '#fff5f5' : ($task['priority'] === 'medium' ? '#fffbf0' : '#f0f9ff') }}">
              <div class="task-priority bg-{{ $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'info') }} me-3"></div>
              <div class="flex-grow-1">
                <div class="fw-semibold">{{ $task['title'] ?? 'Task' }}</div>
                <small class="text-muted">{{ $task['description'] ?? 'No description' }}</small>
                <div class="mt-1">
                  <small class="text-muted"><i class="bi bi-calendar3 me-1"></i>Due: {{ $task['due_date'] ?? 'Not set' }}</small>
                </div>
              </div>
              <div class="text-end">
                <span class="badge bg-{{ $task['priority'] === 'high' ? 'danger' : ($task['priority'] === 'medium' ? 'warning' : 'info') }}">{{ ucfirst($task['priority'] ?? 'low') }}</span>
                <div class="mt-1">
                  <button class="btn btn-sm btn-outline-primary">View</button>
                </div>
              </div>
            </div>
            @empty
            <div class="text-center py-4">
              <i class="bi bi-check-circle text-success fs-1 mb-2"></i>
              <h6 class="text-muted">All caught up!</h6>
              <p class="text-muted small mb-0">No priority tasks at the moment.</p>
            </div>
            @endforelse
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">System Health</h5>
          </div>
          <div class="card-body">
            @foreach($systemStatus ?? [] as $system => $status)
            <div class="d-flex align-items-center justify-content-between mb-3">
              <div class="d-flex align-items-center">
                <div class="status-indicator bg-{{ $status === 'operational' ? 'success' : ($status === 'maintenance' ? 'warning' : 'danger') }} me-3"></div>
                <span class="small">{{ ucwords(str_replace('_', ' ', $system)) }}</span>
              </div>
              <span class="badge bg-{{ $status === 'operational' ? 'success' : ($status === 'maintenance' ? 'warning' : 'danger') }} badge-sm">{{ ucfirst($status) }}</span>
            </div>
            @endforeach
          </div>
        </div>
        
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              @if(Auth::user()->role !== 'logistics_staff')
              <a href="{{ url('/psm/request') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-2"></i>New Purchase Request
              </a>
              @endif
              @if(Auth::user()->role !== 'procurement_officer')
              <a href="{{ url('/plt/toursetup') }}" class="btn btn-outline-success btn-sm">
                <i class="bi bi-flag me-2"></i>Setup Tour
              </a>
              @endif
              <a href="{{ url('/inventory-receipt') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-box-arrow-in-down me-2"></i>Inventory Receipt
              </a>
              <button class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-graph-up me-2"></i>Generate Report
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Activities -->
    <div class="row g-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-activity me-2"></i>Recent Activities
            </h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary" onclick="refreshActivities()">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
              </button>
              <a href="#" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
          </div>
          <div class="card-body">
            @forelse($recentActivities ?? [] as $activity)
            <div class="d-flex align-items-center p-3 mb-3 rounded border">
              <div class="me-3">
                <div class="rounded-circle bg-{{ $activity['status'] === 'completed' ? 'success' : ($activity['status'] === 'pending' ? 'warning' : 'info') }} bg-opacity-10 p-2">
                  <i class="bi bi-{{ $activity['icon'] ?? 'circle' }} text-{{ $activity['status'] === 'completed' ? 'success' : ($activity['status'] === 'pending' ? 'warning' : 'info') }}"></i>
                </div>
              </div>
              <div class="flex-grow-1">
                <div class="fw-semibold">{{ $activity['title'] ?? 'Activity' }}</div>
                <small class="text-muted">{{ $activity['module'] ?? 'System' }} • {{ $activity['user'] ?? 'Unknown' }}</small>
                <div class="mt-1">
                  <small class="text-muted">{{ $activity['time'] ?? 'Recently' }}</small>
                  <span class="badge bg-{{ $activity['status'] === 'completed' ? 'success' : ($activity['status'] === 'pending' ? 'warning' : 'info') }} badge-sm ms-2">{{ ucfirst($activity['status'] ?? 'unknown') }}</span>
                </div>
              </div>
              <div class="text-end">
                <button class="btn btn-action btn-action-view" title="View Details">
                  <i class="bi bi-eye"></i>
                </button>
              </div>
            </div>
            @empty
            <div class="text-center py-5">
              <div class="mb-3">
                <i class="bi bi-clock-history text-muted" style="font-size: 3rem;"></i>
              </div>
              <h6 class="text-muted">No recent activities</h6>
              <p class="text-muted small mb-3">Activities will appear here as they occur in the system.</p>
              <button class="btn btn-sm btn-outline-primary" onclick="refreshActivities()">
                <i class="bi bi-arrow-clockwise me-1"></i>Check for Updates
              </button>
            </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Dashboard JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize variables
      const menuBtn = document.getElementById('menu-btn');
      const desktopToggle = document.getElementById('desktop-toggle');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const mainContent = document.getElementById('main-content');
      const logoutBtn = document.getElementById('logoutBtn');
      const currentPath = window.location.pathname;

      // Initialize dashboard features
      initializeDashboard();

      // Logout functionality
      if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
          e.preventDefault();
          try {
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
            window.location.href = '{{ url('/login') }}';
          }
        });
      }

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
          localStorage.setItem('sidebarCollapsed', sidebar.classList.contains('collapsed'));
          setTimeout(() => window.dispatchEvent(new Event('resize')), 300);
        });

        // Restore sidebar state
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
          sidebar.classList.add('collapsed');
          mainContent.classList.add('expanded');
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

      // Initialize dropdown states
      initializeDropdownStates();
      
      // Navigation link handlers
      document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
          if (link.getAttribute('data-bs-toggle') === 'collapse') return;
          document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
          this.classList.add('active');
        });
      });
    });

    // Initialize dropdown states function
    function initializeDropdownStates() {
      const currentPath = window.location.pathname;
      
      // Document Tracking dropdown
      const documentDropdown = document.querySelector('[data-bs-target="#documentSubmenu"]');
      const documentSubmenu = document.getElementById('documentSubmenu');
      if (documentDropdown && documentSubmenu) {
        if (currentPath.includes('/dtrs/')) {
          documentDropdown.classList.add('active');
          documentSubmenu.classList.add('show');
          const activeSubItem = documentSubmenu.querySelector(`[href="${currentPath}"]`);
          if (activeSubItem) activeSubItem.classList.add('active');
        }
        
        documentSubmenu.querySelectorAll('.nav-link').forEach(link => {
          link.addEventListener('click', function() {
            documentSubmenu.classList.add('show');
            documentSubmenu.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
          });
        });
      }

      // Project Logistics Tracker dropdown
      const pltDropdown = document.querySelector('[data-bs-target="#pltSubmenu"]');
      const pltSubmenu = document.getElementById('pltSubmenu');
      if (pltDropdown && pltSubmenu) {
        if (currentPath.includes('/plt/')) {
          pltDropdown.classList.add('active');
          pltSubmenu.classList.add('show');
          const activeSubItem = pltSubmenu.querySelector(`[href="${currentPath}"]`);
          if (activeSubItem) activeSubItem.classList.add('active');
        }
      }
    }

    // Dashboard initialization
    function initializeDashboard() {
      console.log('Dashboard initialized');
      
      // Update KPI data periodically
      setInterval(updateKPIData, 30000); // 30 seconds
    }

    function updateKPIData() {
      fetch('{{ route("dashboard.quick-stats") }}', {
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        }
      })
      .then(response => response.json())
      .then(data => {
        // Update KPI values
        if (data.pending_orders !== undefined) {
          document.getElementById('pending-orders').textContent = data.pending_orders;
        }
        if (data.maintenance_due !== undefined) {
          document.getElementById('maintenance-due').textContent = data.maintenance_due;
        }
      })
      .catch(error => console.log('KPI update failed:', error));
    }

    function refreshActivities() {
      const refreshBtn = document.querySelector('[onclick="refreshActivities()"]');
      const originalContent = refreshBtn.innerHTML;
      
      refreshBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Refreshing...';
      refreshBtn.disabled = true;
      
      setTimeout(() => {
        refreshBtn.innerHTML = originalContent;
        refreshBtn.disabled = false;
        
        // Show simple alert
        alert('Activities refreshed successfully!');
      }, 1000);
    }

    // Global functions
    window.refreshActivities = refreshActivities;
  </script>
</body>
</html>
