<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Asset Movement Tracking - Project Logistics Tracker</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
       <li class="nav-item">
    <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#pltSubmenu" aria-expanded="true" aria-controls="pltSubmenu">
      <i class="bi bi-truck me-2"></i> Project Logistics Tracker
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="collapse show" id="pltSubmenu">
      <ul class="nav flex-column ms-3">
        <li class="nav-item">
          <a href="{{ url('/plt/toursetup') }}" class="nav-link text-dark small ">
            <i class="bi bi-truck me-2"></i> Planning
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/plt/execution') }}" class="nav-link text-dark small active">
            <i class="bi bi-bar-chart-steps me-2"></i> Execution Monitoring
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/plt/closure') }}" class="nav-link text-dark small">
            <i class="bi bi-file-earmark-bar-graph me-2"></i> Closure
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
      <li class="nav-item mt-3">
        <a href="#" class="nav-link text-danger" id="logoutBtn">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
      </li>
    </ul>
  </aside>

  <!-- Overlay for mobile -->
  <div id="overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="z-index:1040; display: none;"></div>

  <main id="main-content">
  <!-- Page Header -->
  <div class="page-header-container mb-4">
    <div class="d-flex justify-content-between align-items-center page-header">
      <div class="d-flex align-items-center">
        <div class="dashboard-logo me-3">
          <i class="bi bi-bar-chart-steps fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Asset Movement Tracking</h2>
          <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Track asset movements, monitor progress, and manage logistics operations.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/plt') }}" class="text-decoration-none">Project Logistics</a></li>
            <li class="breadcrumb-item active" aria-current="page">Asset Movement Tracking</li>
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
              <i class="bi bi-play-circle"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0" id="activeMovementsCount">{{ $stats['active_movements'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Active Movements</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> {{ $stats['active_change'] ?? '0' }} this week</small>
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
              <h3 class="fw-bold mb-0" id="completedMovementsCount">{{ $stats['completed_movements'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Completed Movements</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> {{ $stats['completed_change'] ?? '0' }} this month</small>
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
              <h3 class="fw-bold mb-0" id="delayedMovementsCount">{{ $stats['delayed_movements'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Delayed Movements</p>
              <small class="text-warning"><i class="bi bi-arrow-up"></i> {{ $stats['delayed_change'] ?? '0' }} alert</small>
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
              <i class="bi bi-graph-up"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0" id="avgProgressCount">{{ $stats['avg_progress'] ?? 0 }}%</h3>
              <p class="text-muted mb-0 small">Average Completion</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> {{ $stats['progress_change'] ?? '0%' }} improvement</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="row g-4">
    <!-- Left Column -->
    <div class="col-lg-8">
      <!-- Asset Movement Progress Overview -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">
            <i class="bi bi-truck me-2"></i>Asset Movement Progress Overview
          </h5>
          <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#" data-filter="all">All Movements</a></li>
              <li><a class="dropdown-item" href="#" data-filter="scheduled">Scheduled</a></li>
              <li><a class="dropdown-item" href="#" data-filter="in_progress">In Progress</a></li>
              <li><a class="dropdown-item" href="#" data-filter="delayed">Delayed</a></li>
              <li><a class="dropdown-item" href="#" data-filter="completed">Completed</a></li>
            </ul>
          </div>
        </div>
        <div class="card-body">
          <div class="row g-3" id="movementProgressContainer">
            @forelse($movements ?? [] as $movement)
            <div class="col-12 movement-item" data-status="{{ strtolower($movement->status ?? 'scheduled') }}">
              <div class="card border-start border-4 border-{{ $movement->getStatusColor() ?? 'info' }}">
                <div class="card-body">
                  <div class="row align-items-center">
                    <div class="col-md-4">
                      <h6 class="fw-bold mb-1">{{ $movement->movement_code ?? 'MOV-001' }}</h6>
                      <p class="text-muted mb-0 small">{{ $movement->movement_title ?? 'Office Equipment Relocation' }}</p>
                      <small class="text-muted">
                        <i class="bi bi-person me-1"></i>{{ $movement->supervisor ?? 'John Doe' }}
                      </small>
                    </div>
                    <div class="col-md-3">
                      <div class="d-flex align-items-center mb-2">
                        <span class="badge bg-{{ $movement->getStatusColor() ?? 'info' }} me-2">{{ $movement->status ?? 'In Progress' }}</span>
                        @if(($movement->is_delayed ?? false))
                        <span class="badge bg-danger">
                          <i class="bi bi-exclamation-triangle me-1"></i>Delayed
                        </span>
                        @endif
                      </div>
                      <small class="text-muted">
                        <i class="bi bi-geo-alt me-1"></i>
                        {{ $movement->origin_location ?? 'Building A' }} → {{ $movement->destination_location ?? 'Building B' }}
                      </small>
                    </div>
                    <div class="col-md-3">
                      <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                          <span class="small">Progress</span>
                          <span class="small fw-bold">{{ $movement->progress_percentage ?? '65' }}%</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                          <div class="progress-bar bg-{{ ($movement->progress_percentage ?? 65) >= 100 ? 'success' : (($movement->progress_percentage ?? 65) >= 75 ? 'info' : (($movement->progress_percentage ?? 65) >= 50 ? 'warning' : 'danger')) }}" 
                               style="width: {{ $movement->progress_percentage ?? '65' }}%"></div>
                        </div>
                      </div>
                      <small class="text-muted">
                        <i class="bi bi-box me-1"></i>{{ $movement->total_assets ?? '15' }} assets
                      </small>
                    </div>
                    <div class="col-md-2 text-end">
                      <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary view-movement-details" 
                                data-movement-id="{{ $movement->id ?? 1 }}" 
                                title="View Details">
                          <i class="bi bi-eye"></i>
                        </button>
                        @if(($movement->status ?? 'In Progress') === 'In Progress')
                        <button class="btn btn-sm btn-outline-success update-movement-progress-btn" 
                                data-movement-id="{{ $movement->id ?? 1 }}" 
                                title="Update Progress">
                          <i class="bi bi-arrow-up-circle"></i>
                        </button>
                        @endif
                        @if(($movement->status ?? 'In Progress') !== 'Completed' && ($movement->status ?? 'In Progress') !== 'Cancelled')
                        <button class="btn btn-sm btn-outline-warning track-location-btn" 
                                data-movement-id="{{ $movement->id ?? 1 }}" 
                                title="Track Location">
                          <i class="bi bi-geo-alt"></i>
                        </button>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            @empty
            <div class="col-12 text-center py-4">
              <i class="bi bi-inbox text-muted fs-1 mb-2"></i>
              <h6 class="text-muted">No active movements found</h6>
              <p class="text-muted small mb-0">Asset movements will appear here when they are scheduled or in progress.</p>
            </div>
            @endforelse
          </div>
        </div>
      </div>
      
      <!-- Movement Timeline & Activities -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">
            <i class="bi bi-clock-history me-2"></i>Movement Timeline & Activities
          </h5>
          <button class="btn btn-sm btn-outline-primary" id="refreshTimelineBtn">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
          </button>
        </div>
        <div class="card-body">
          <div class="timeline" id="movementTimeline">
            @forelse($recentActivities ?? [
              ['type' => 'start', 'title' => 'Movement Started', 'description' => 'Office Equipment Relocation has begun', 'user' => 'John Doe', 'time' => '2 hours ago', 'status_color' => 'success', 'movement_code' => 'MOV-001'],
              ['type' => 'checkpoint', 'title' => 'Checkpoint Reached', 'description' => 'Assets loaded and secured in transport vehicle', 'user' => 'Jane Smith', 'time' => '1 hour ago', 'status_color' => 'info', 'movement_code' => 'MOV-001'],
              ['type' => 'delay', 'title' => 'Delay Reported', 'description' => 'Traffic congestion causing 30-minute delay', 'user' => 'Mike Johnson', 'time' => '30 minutes ago', 'status_color' => 'warning', 'movement_code' => 'MOV-002'],
              ['type' => 'complete', 'title' => 'Movement Completed', 'description' => 'IT Equipment relocation successfully completed', 'user' => 'Sarah Wilson', 'time' => '15 minutes ago', 'status_color' => 'success', 'movement_code' => 'MOV-003']
            ] as $activity)
            <div class="timeline-item">
              <div class="timeline-marker bg-{{ $activity['type'] === 'start' ? 'success' : ($activity['type'] === 'checkpoint' ? 'info' : ($activity['type'] === 'delay' ? 'warning' : ($activity['type'] === 'complete' ? 'success' : 'secondary'))) }}">
                <i class="bi bi-{{ $activity['type'] === 'start' ? 'play-circle' : ($activity['type'] === 'checkpoint' ? 'geo-alt' : ($activity['type'] === 'delay' ? 'exclamation-triangle' : ($activity['type'] === 'complete' ? 'check-circle' : 'arrow-right'))) }}"></i>
              </div>
              <div class="timeline-content">
                <div class="d-flex justify-content-between align-items-start">
                  <div>
                    <h6 class="mb-1">{{ $activity['title'] }}</h6>
                    <p class="text-muted mb-1 small">{{ $activity['description'] }}</p>
                    <small class="text-muted">
                      <i class="bi bi-person me-1"></i>{{ $activity['user'] }}
                      <i class="bi bi-clock ms-2 me-1"></i>{{ $activity['time'] }}
                    </small>
                  </div>
                  <span class="badge bg-{{ $activity['status_color'] }}">{{ $activity['movement_code'] }}</span>
                </div>
              </div>
            </div>
            @empty
            <div class="text-center py-4">
              <i class="bi bi-clock-history text-muted fs-1 mb-2"></i>
              <h6 class="text-muted">No recent activities</h6>
              <p class="text-muted small mb-0">Movement activities will appear here when they occur.</p>
            </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
      <!-- Quick Actions -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">
            <i class="bi bi-lightning me-2"></i>Quick Actions
          </h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button class="btn btn-primary" id="createMovementBtn">
              <i class="bi bi-plus-circle me-2"></i>New Movement Plan
            </button>
            <button class="btn btn-outline-primary" id="trackAllMovementsBtn">
              <i class="bi bi-geo-alt me-2"></i>Track All Movements
            </button>
            <button class="btn btn-outline-primary" id="updateProgressBtn">
              <i class="bi bi-arrow-up-circle me-2"></i>Update Progress
            </button>
            <button class="btn btn-outline-primary" id="generateMovementReportBtn">
              <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Report
            </button>
            <button class="btn btn-outline-secondary" id="exportMovementDataBtn">
              <i class="bi bi-download me-2"></i>Export Data
            </button>
          </div>
        </div>
      </div>
      
      <!-- Movement Performance Chart -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">
            <i class="bi bi-graph-up me-2"></i>Movement Performance
          </h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <canvas id="movementPerformanceChart" width="400" height="200"></canvas>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <div class="text-center p-2 bg-light rounded">
                <small class="text-muted d-block">On Schedule</small>
                <strong class="text-success">{{ $stats['on_time_percentage'] ?? 85 }}%</strong>
              </div>
            </div>
            <div class="col-6">
              <div class="text-center p-2 bg-light rounded">
                <small class="text-muted d-block">Cost Efficiency</small>
                <strong class="text-info">{{ $stats['cost_efficiency'] ?? 92 }}%</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Movement Summary -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">
            <i class="bi bi-clipboard-data me-2"></i>Today's Movement Summary
          </h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Movements Started</span>
              <span class="small fw-bold">{{ $stats['movements_started_today'] ?? 3 }}</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: {{ ($stats['movements_started_today'] ?? 3) * 20 }}%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Assets Moved</span>
              <span class="small fw-bold">{{ $stats['assets_moved_today'] ?? 47 }}</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-warning" style="width: {{ ($stats['assets_moved_today'] ?? 47) * 2 }}%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Movements Completed</span>
              <span class="small fw-bold">{{ $stats['movements_completed_today'] ?? 2 }}</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-info" style="width: {{ ($stats['movements_completed_today'] ?? 2) * 25 }}%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Avg. Completion Time</span>
              <span class="small fw-bold">{{ $stats['avg_completion_time'] ?? '3.2' }}hrs</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-primary" style="width: 80%"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Movement Alerts -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">
            <i class="bi bi-bell me-2"></i>Movement Alerts
          </h5>
        </div>
        <div class="card-body">
          @forelse($alerts ?? [
            ['type' => 'warning', 'icon' => 'exclamation-triangle', 'message' => 'MOV-002: Traffic delay reported - ETA extended by 30 minutes'],
            ['type' => 'info', 'icon' => 'info-circle', 'message' => 'MOV-001: Asset loading completed, transport in progress'],
            ['type' => 'success', 'icon' => 'check-circle', 'message' => 'MOV-003: IT Equipment movement completed successfully']
          ] as $alert)
          <div class="alert alert-{{ $alert['type'] }} alert-sm mb-2">
            <i class="bi bi-{{ $alert['icon'] }} me-2"></i>
            {{ $alert['message'] }}
          </div>
          @empty
          <div class="text-center py-3">
            <i class="bi bi-bell text-muted fs-1 mb-2"></i>
            <h6 class="text-muted">No alerts</h6>
            <p class="text-muted small mb-0">Movement alerts will appear here when needed.</p>
          </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</main>

  <!-- Static Modals for Quick Actions -->
  <div class="modal fade" id="staticModal" tabindex="-1" aria-labelledby="staticModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="staticModalLabel">Action</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="staticModalBody">
          <!-- Content will be set by JS -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Custom CSS for Timeline -->
  <style>
    .timeline {
      position: relative;
      padding-left: 30px;
    }
    
    .timeline::before {
      content: '';
      position: absolute;
      left: 15px;
      top: 0;
      bottom: 0;
      width: 2px;
      background: #dee2e6;
    }
    
    .timeline-item {
      position: relative;
      margin-bottom: 20px;
    }
    
    .timeline-marker {
      position: absolute;
      left: -22px;
      top: 0;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 12px;
      border: 3px solid white;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .timeline-content {
      background: #f8f9fa;
      padding: 15px;
      border-radius: 8px;
      border-left: 3px solid #dee2e6;
    }
    
    .project-item {
      transition: all 0.3s ease;
    }
    
    .project-item:hover {
      transform: translateY(-2px);
    }
    
    .project-item.filtered-out {
      display: none;
    }
  </style>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Project Execution JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Check if user is authenticated
      const authToken = localStorage.getItem('auth_token');
      if (!authToken) {
        // Redirect to login if no token
        window.location.href = '{{ url('/login') }}';
        return;
      }

      // Verify token is still valid
      fetch('{{ url('/api/profile') }}', {
        headers: {
          'Authorization': `Bearer ${authToken}`,
          'Accept': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok) {
          // Token is invalid, redirect to login
          localStorage.removeItem('auth_token');
          localStorage.removeItem('user_data');
          window.location.href = '{{ url('/login') }}';
          return;
        }
        return response.json();
      })
      .then(data => {
        if (data && data.data && data.data.user) {
          // Update user info in the sidebar
          const userData = data.data.user;
          document.querySelector('.profile-section h6').textContent = userData.name;
        }
      })
      .catch(error => {
        console.error('Auth check error:', error);
        localStorage.removeItem('auth_token');
        localStorage.removeItem('user_data');
        window.location.href = '{{ url('/login') }}';
      });

      // Logout functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
          e.preventDefault();

          const authToken = localStorage.getItem('auth_token');
          if (!authToken) {
            window.location.href = '{{ url('/login') }}';
            return;
          }

          try {
            // Call logout API
            await fetch('{{ url('/api/logout') }}', {
              method: 'POST',
              headers: {
                'Authorization': `Bearer ${authToken}`,
                'Accept': 'application/json'
              }
            });
          } catch (error) {
            console.error('Logout API error:', error);
          }

          // Clear local storage and redirect regardless of API response
          localStorage.removeItem('auth_token');
          localStorage.removeItem('user_data');
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

      // Initialize Project Execution Features
      initializeProjectExecution();

      // Handle Smart Warehousing dropdown active states
      const warehouseDropdown = document.querySelector('[data-bs-target="#warehouseSubmenu"]');
      const currentPath = window.location.pathname;

      // Remove 'active' from Smart Warehousing on PLT pages
      if (warehouseDropdown) {
        if (
          currentPath.includes('/plt/toursetup') ||
          currentPath.includes('/plt/execution') ||
          currentPath.includes('/plt/closure')
        ) {
          warehouseDropdown.classList.remove('active');
        }
      }

      const warehouseSubmenu = document.getElementById('warehouseSubmenu');
      const procurementDropdown = document.querySelector('[data-bs-target="#procurementSubmenu"]');
      const procurementSubmenu = document.getElementById('procurementSubmenu');
      


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

      // FIX: Collapse Procurement dropdown on SWS pages
      if (procurementDropdown && procurementSubmenu) {
        if (
          currentPath.includes('/inventory-receipt') ||
          currentPath.includes('/storage-organization') ||
          currentPath.includes('/picking-dispatch') ||
          currentPath.includes('/stock-replenishment')
        ) {
          procurementDropdown.classList.remove('active');
          procurementSubmenu.classList.remove('show');
        }
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

      // Quick Actions static handlers
      const newReceiptBtn = document.getElementById('newReceiptBtn');
      const scanBarcodeBtn = document.getElementById('scanBarcodeBtn');
      const importCSVBtn = document.getElementById('importCSVBtn');
      const printReceiptBtn = document.getElementById('printReceiptBtn');
      const staticModal = new bootstrap.Modal(document.getElementById('staticModal'));
      const staticModalBody = document.getElementById('staticModalBody');
      const staticModalLabel = document.getElementById('staticModalLabel');

      if (newReceiptBtn) {
        newReceiptBtn.addEventListener('click', function(e) {
          e.preventDefault();
          staticModalLabel.textContent = 'New Receipt';
          staticModalBody.innerHTML = '<p>This would start a new inventory receipt. (Static demo only)</p>';
          staticModal.show();
        });
      }
      if (scanBarcodeBtn) {
        scanBarcodeBtn.addEventListener('click', function(e) {
          e.preventDefault();
          staticModalLabel.textContent = 'Scan Barcode';
          staticModalBody.innerHTML = '<p>Barcode scanning is not available in static mode.</p>';
          staticModal.show();
        });
      }
      if (importCSVBtn) {
        importCSVBtn.addEventListener('click', function(e) {
          e.preventDefault();
          staticModalLabel.textContent = 'Import from CSV';
          staticModalBody.innerHTML = '<p>CSV import is not available in static mode.</p>';
          staticModal.show();
        });
      }
      if (printReceiptBtn) {
        printReceiptBtn.addEventListener('click', function(e) {
          e.preventDefault();
          staticModalLabel.textContent = 'Print Receipt';
          staticModalBody.innerHTML = '<p>This would print the receipt. (Static demo only)</p>';
          staticModal.show();
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

  // Project Execution Functions
  function initializeProjectExecution() {
    console.log('Initializing Project Execution features...');
    
    // Initialize Chart
    initializePerformanceChart();
    
    // Initialize Event Listeners
    initializeEventListeners();
    
    // Initialize Filters
    initializeFilters();
  }

  // Initialize Performance Chart
  function initializePerformanceChart() {
    const ctx = document.getElementById('performanceChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'doughnut',
        data: {
          labels: ['Completed', 'Active', 'Overdue', 'Draft'],
          datasets: [{
            data: [{{ $stats['completed_projects'] ?? 0 }}, {{ $stats['active_projects'] ?? 0 }}, {{ $stats['overdue_projects'] ?? 0 }}, {{ $stats['draft_projects'] ?? 0 }}],
            backgroundColor: ['#28a745', '#007bff', '#dc3545', '#6c757d'],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: {
                usePointStyle: true,
                padding: 15,
                font: {
                  size: 11
                }
              }
            }
          }
        }
      });
    }
  }

  // Initialize Event Listeners
  function initializeEventListeners() {
    // Project Details View
    document.addEventListener('click', function(e) {
      if (e.target.closest('.view-project-details')) {
        const btn = e.target.closest('.view-project-details');
        const projectId = btn.dataset.projectId;
        viewProjectDetails(projectId);
      }
    });

    // Update Progress
    document.addEventListener('click', function(e) {
      if (e.target.closest('.update-progress-btn')) {
        const btn = e.target.closest('.update-progress-btn');
        const projectId = btn.dataset.projectId;
        updateProjectProgress(projectId);
      }
    });

    // Add Milestone
    document.addEventListener('click', function(e) {
      if (e.target.closest('.add-milestone-btn')) {
        const btn = e.target.closest('.add-milestone-btn');
        const projectId = btn.dataset.projectId;
        addProjectMilestone(projectId);
      }
    });

    // Quick Actions
    document.getElementById('createProjectBtn')?.addEventListener('click', () => {
      window.location.href = '{{ url("/plt/toursetup") }}';
    });

    document.getElementById('generateReportBtn')?.addEventListener('click', generateProjectReport);
    document.getElementById('exportDataBtn')?.addEventListener('click', exportProjectData);
    document.getElementById('refreshTimelineBtn')?.addEventListener('click', refreshTimeline);
  }

  // Initialize Filters
  function initializeFilters() {
    document.querySelectorAll('[data-filter]').forEach(filter => {
      filter.addEventListener('click', function(e) {
        e.preventDefault();
        const filterType = this.dataset.filter;
        filterProjects(filterType);
      });
    });
  }

  // Filter Projects
  function filterProjects(filterType) {
    const projectItems = document.querySelectorAll('.project-item');
    
    projectItems.forEach(item => {
      const status = item.dataset.status;
      let shouldShow = true;
      
      switch(filterType) {
        case 'active':
          shouldShow = status === 'active';
          break;
        case 'overdue':
          shouldShow = item.querySelector('.badge.bg-danger') !== null;
          break;
        case 'completed':
          shouldShow = status === 'completed';
          break;
        case 'all':
        default:
          shouldShow = true;
          break;
      }
      
      if (shouldShow) {
        item.classList.remove('filtered-out');
      } else {
        item.classList.add('filtered-out');
      }
    });
  }

  // View Project Details
  function viewProjectDetails(projectId) {
    Swal.fire({
      title: 'Loading Project Details...',
      allowOutsideClick: false,
      showConfirmButton: false,
      willOpen: () => {
        Swal.showLoading();
      }
    });

    fetch(`{{ url('/plt/projects') }}/${projectId}`, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': getCsrfToken()
      }
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        const project = data.project;
        Swal.fire({
          title: `${project.project_code}`,
          html: `
            <div class="text-start">
              <h6 class="fw-bold">${project.project_title}</h6>
              <p class="text-muted mb-3">${project.project_description}</p>
              
              <div class="row g-3">
                <div class="col-6">
                  <small class="text-muted d-block">Timeline</small>
                  <strong>${new Date(project.start_date).toLocaleDateString()} - ${new Date(project.expected_end_date).toLocaleDateString()}</strong>
                </div>
                <div class="col-6">
                  <small class="text-muted d-block">Budget</small>
                  <strong>₱${parseFloat(project.estimated_budget).toLocaleString()}</strong>
                </div>
                <div class="col-6">
                  <small class="text-muted d-block">Status</small>
                  <span class="badge bg-${getStatusColor(project.status)}">${project.status}</span>
                </div>
                <div class="col-6">
                  <small class="text-muted d-block">Progress</small>
                  <div class="progress mt-1" style="height: 6px;">
                    <div class="progress-bar bg-primary" style="width: ${project.progress || 0}%"></div>
                  </div>
                  <small>${project.progress || 0}%</small>
                </div>
              </div>
              
              ${project.notes ? `<div class="mt-3"><small class="text-muted d-block">Notes</small><p class="mb-0">${project.notes}</p></div>` : ''}
            </div>
          `,
          width: 600,
          confirmButtonText: 'Close'
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to load project details'
        });
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error',
        text: 'Failed to load project details'
      });
    });
  }

  // Update Project Progress
  function updateProjectProgress(projectId) {
    Swal.fire({
      title: 'Update Project Progress',
      html: `
        <div class="mb-3">
          <label for="progressValue" class="form-label">Progress Percentage</label>
          <input type="range" class="form-range" id="progressValue" min="0" max="100" value="0">
          <div class="d-flex justify-content-between">
            <small>0%</small>
            <small id="progressDisplay">0%</small>
            <small>100%</small>
          </div>
        </div>
        <div class="mb-3">
          <label for="progressNotes" class="form-label">Progress Notes</label>
          <textarea class="form-control" id="progressNotes" rows="3" placeholder="Describe the progress made..."></textarea>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Update Progress',
      cancelButtonText: 'Cancel',
      didOpen: () => {
        const slider = document.getElementById('progressValue');
        const display = document.getElementById('progressDisplay');
        slider.addEventListener('input', function() {
          display.textContent = this.value + '%';
        });
      }
    }).then((result) => {
      if (result.isConfirmed) {
        const progress = document.getElementById('progressValue').value;
        const notes = document.getElementById('progressNotes').value;
        
        // Submit progress update
        fetch(`{{ url('/plt/projects') }}/${projectId}/progress`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            progress: progress,
            notes: notes
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Progress Updated!',
              text: data.message
            }).then(() => {
              window.location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.message || 'Failed to update progress'
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to update progress'
          });
        });
      }
    });
  }

  // Add Project Milestone
  function addProjectMilestone(projectId) {
    Swal.fire({
      title: 'Add Project Milestone',
      html: `
        <div class="mb-3">
          <label for="milestoneTitle" class="form-label">Milestone Title</label>
          <input type="text" class="form-control" id="milestoneTitle" placeholder="Enter milestone title">
        </div>
        <div class="mb-3">
          <label for="milestoneDate" class="form-label">Target Date</label>
          <input type="date" class="form-control" id="milestoneDate">
        </div>
        <div class="mb-3">
          <label for="milestoneDescription" class="form-label">Description</label>
          <textarea class="form-control" id="milestoneDescription" rows="3" placeholder="Describe the milestone..."></textarea>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Add Milestone',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        const title = document.getElementById('milestoneTitle').value;
        const date = document.getElementById('milestoneDate').value;
        const description = document.getElementById('milestoneDescription').value;
        
        if (!title || !date) {
          Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please fill in the milestone title and date'
          });
          return;
        }
        
        // Submit milestone
        fetch(`{{ url('/plt/projects') }}/${projectId}/milestones`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': getCsrfToken()
          },
          body: JSON.stringify({
            title: title,
            target_date: date,
            description: description
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Milestone Added!',
              text: data.message
            }).then(() => {
              refreshTimeline();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.message || 'Failed to add milestone'
            });
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to add milestone'
          });
        });
      }
    });
  }

  // Generate Project Report
  function generateProjectReport() {
    Swal.fire({
      title: 'Generate Project Report',
      html: `
        <div class="mb-3">
          <label class="form-label">Report Type</label>
          <select class="form-select" id="reportType">
            <option value="summary">Project Summary</option>
            <option value="progress">Progress Report</option>
            <option value="timeline">Timeline Report</option>
            <option value="budget">Budget Analysis</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label">Date Range</label>
          <div class="row">
            <div class="col-6">
              <input type="date" class="form-control" id="reportStartDate">
            </div>
            <div class="col-6">
              <input type="date" class="form-control" id="reportEndDate">
            </div>
          </div>
        </div>
      `,
      showCancelButton: true,
      confirmButtonText: 'Generate Report',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        Swal.fire({
          icon: 'success',
          title: 'Report Generated!',
          text: 'Your project report has been generated and will be downloaded shortly.'
        });
      }
    });
  }

  // Export Project Data
  function exportProjectData() {
    Swal.fire({
      title: 'Export Project Data',
      text: 'Choose export format:',
      showCancelButton: true,
      showDenyButton: true,
      confirmButtonText: 'Excel (.xlsx)',
      denyButtonText: 'CSV (.csv)',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = '{{ url("/plt/projects/export") }}?format=xlsx';
      } else if (result.isDenied) {
        window.location.href = '{{ url("/plt/projects/export") }}?format=csv';
      }
    });
  }

  // Refresh Timeline
  function refreshTimeline() {
    const timeline = document.getElementById('projectTimeline');
    if (timeline) {
      // Add loading state
      timeline.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div></div>';
      
      // Simulate refresh (in real app, this would fetch new data)
      setTimeout(() => {
        window.location.reload();
      }, 1000);
    }
  }

  // Helper function to get CSRF token
  function getCsrfToken() {
    const metaToken = document.querySelector('meta[name="csrf-token"]');
    return metaToken ? metaToken.getAttribute('content') : '';
  }

  // Helper function to get status color
  function getStatusColor(status) {
    const colors = {
      'Draft': 'secondary',
      'Planning': 'warning',
      'Active': 'success',
      'On Hold': 'warning',
      'Completed': 'success',
      'Cancelled': 'danger'
    };
    return colors[status] || 'secondary';
  }

  console.log('Project Execution Monitoring loaded successfully!');
  </script>
</body>
</html>
