<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Warehousing Dashboard</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- FullCalendar CSS -->
  <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
  <!-- FullCalendar JS -->
  <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
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
    <a href="#" class="nav-link text-dark " data-bs-toggle="collapse" data-bs-target="#pltSubmenu" aria-expanded="true" aria-controls="pltSubmenu">
      <i class="bi bi-truck me-2"></i> Project Logistics Tracker
      <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="collapse " id="pltSubmenu">
      <ul class="nav flex-column ms-3">
        <li class="nav-item">
          <a href="{{ url('/plt/toursetup') }}" class="nav-link text-dark small ">
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
        <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#assetSubmenu" aria-expanded="false" aria-controls="assetSubmenu">
          <i class="bi bi-tools me-2"></i> Asset Life Cycle & Maintenance
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse show" id="assetSubmenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a href="{{ url('/alms/assetregistration') }}" class="nav-link text-dark small">
                <i class="bi bi-calendar-check me-2"></i> Asset Register
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/alms/maintenance') }}" class="nav-link text-dark small active ">
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
          <i class="bi bi-box-seam fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Maintenance Management</h2>
          <p class="text-muted mb-0">Welcome back, Sarah! Manage asset maintenance and service schedules.</p>
        </div>
      </div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/alms') }}" class="text-decoration-none">Asset Lifecycle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Maintenance Management</li>
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
              <i class="bi bi-box-arrow-in-down"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">24</h3>
              <p class="text-muted mb-0 small">Pending Receipts</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +3 today</small>
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
              <h3 class="fw-bold mb-0">156</h3>
              <p class="text-muted mb-0 small">Completed Today</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +12%</small>
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
              <h3 class="fw-bold mb-0">8</h3>
              <p class="text-muted mb-0 small">Quality Issues</p>
              <small class="text-warning"><i class="bi bi-arrow-up"></i> +2</small>
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
              <i class="bi bi-clock"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">45min</h3>
              <p class="text-muted mb-0 small">Avg Process Time</p>
              <small class="text-success"><i class="bi bi-arrow-down"></i> -5min</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="row g-4">
    <!-- Full Width Content -->
    <div class="col-12">
      <!-- Maintenance Schedules -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
              <i class="bi bi-calendar-check me-2"></i>Maintenance Schedules
            </h5>
            <div class="d-flex align-items-center gap-2">
              <!-- View Toggle Buttons -->
              <div class="btn-group" role="group" aria-label="View toggle">
                <button type="button" class="btn btn-outline-secondary active" id="tableViewBtn">
                  <i class="bi bi-table me-1"></i>Table
                </button>
                <button type="button" class="btn btn-outline-secondary" id="calendarViewBtn">
                  <i class="bi bi-calendar3 me-1"></i>Calendar
                </button>
              </div>
              <!-- Action Buttons -->
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newScheduleModal">
                <i class="bi bi-plus-circle me-1"></i>New Schedule
              </button>
              <button class="btn btn-outline-primary" id="refreshSchedulesBtn">
                <i class="bi bi-arrow-clockwise"></i>
              </button>
            </div>
          </div>
        </div>
        
        <div class="card-body p-0">
          <!-- Table View -->
          <div id="tableView" class="p-3">
            <!-- Filter and Search Bar -->
            <div class="row g-3 mb-3">
              <div class="col-md-3">
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-search"></i>
                  </span>
                  <input type="text" class="form-control" id="searchSchedules" placeholder="Search schedules...">
                </div>
              </div>
              <div class="col-md-2">
                <select class="form-select" id="filterCategory">
                  <option value="">All Categories</option>
                  <option value="Preventive">🔧 Preventive</option>
                  <option value="Corrective">⚠️ Corrective</option>
                  <option value="Emergency">🚨 Emergency</option>
                  <option value="Inspection">🔍 Inspection</option>
                  <option value="Cleaning">🧽 Cleaning</option>
                  <option value="Upgrade">⬆️ Upgrade</option>
                  <option value="Calibration">⚖️ Calibration</option>
                  <option value="Replacement">🔄 Replacement</option>
                </select>
              </div>
              <div class="col-md-2">
                <select class="form-select" id="filterStatus">
                  <option value="">All Status</option>
                  <option value="Scheduled">Scheduled</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Completed">Completed</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>
              <div class="col-md-2">
                <select class="form-select" id="filterPriority">
                  <option value="">All Priority</option>
                  <option value="Low">🟢 Low</option>
                  <option value="Medium">🟡 Medium</option>
                  <option value="High">🟠 High</option>
                  <option value="Critical">🔴 Critical</option>
                </select>
              </div>
              <div class="col-md-2">
                <select class="form-select" id="filterAsset">
                  <option value="">All Assets</option>
                  @isset($assets)
                  @foreach($assets as $asset)
                  <option value="{{ $asset->id }}">{{ $asset->asset_id ?? '#ASSET-' . $asset->id }}</option>
                  @endforeach
                  @endisset
                </select>
              </div>
              <div class="col-md-1">
                <button class="btn btn-outline-secondary w-100" id="clearFilters" title="Clear all filters">
                  <i class="bi bi-x-circle"></i>
                </button>
              </div>
            </div>

            <!-- Enhanced Table -->
            <div class="table-responsive">
              <table class="table table-hover" id="schedulesTable">
                <thead class="table-light">
                  <tr>
                    <th>
                      <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>ID</th>
                    <th>Category</th>
                    <th>Asset</th>
                    <th>Title</th>
                    <th>Scheduled Date</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @if(isset($schedules) && $schedules->count())
                    @foreach($schedules as $s)
                    <tr data-schedule-id="{{ $s->id }}">
                      <td>
                        <input type="checkbox" class="form-check-input schedule-checkbox" value="{{ $s->id }}">
                      </td>
                      <td><strong>#MS-{{ str_pad($s->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                      <td>
                        @php
                          $categoryIcons = [
                            'Preventive' => '🔧',
                            'Corrective' => '⚠️',
                            'Emergency' => '🚨',
                            'Inspection' => '🔍',
                            'Cleaning' => '🧽',
                            'Upgrade' => '⬆️',
                            'Calibration' => '⚖️',
                            'Replacement' => '🔄'
                          ];
                          $categoryIcon = $categoryIcons[$s->category ?? 'Preventive'] ?? '🔧';
                        @endphp
                        <span class="badge bg-info">
                          {{ $categoryIcon }} {{ $s->category ?? 'Preventive' }}
                        </span>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="asset-icon me-2">
                            <i class="bi bi-truck text-primary"></i>
                          </div>
                          <div>
                            <div class="fw-semibold">{{ optional($s->asset)->asset_id ?? '#ASSET-' . $s->asset_id }}</div>
                            <small class="text-muted">{{ optional($s->asset)->plate_number ?? optional($s->asset)->building_name ?? optional($s->asset)->equipment_name ?? 'Asset Details' }}</small>
                          </div>
                        </div>
                      </td>
                      <td>
                        <div class="fw-semibold">{{ $s->title }}</div>
                        @if($s->notes)
                        <small class="text-muted">{{ Str::limit($s->notes, 50) }}</small>
                        @endif
                      </td>
                      <td>
                        <div>{{ \Carbon\Carbon::parse($s->scheduled_date)->format('M d, Y') }}</div>
                        <small class="text-muted">{{ \Carbon\Carbon::parse($s->scheduled_date)->diffForHumans() }}</small>
                      </td>
                      <td>
                        <span class="badge {{ $s->status === 'Completed' ? 'bg-success' : ($s->status === 'In Progress' ? 'bg-primary' : ($s->status === 'Cancelled' ? 'bg-secondary' : 'bg-warning')) }}">
                          {{ $s->status }}
                        </span>
                      </td>
                      <td>
                        @php
                          // Use priority from database if available, otherwise calculate based on date
                          $priority = $s->priority ?? 'Medium';
                          $priorityClass = match($priority) {
                            'Critical' => 'bg-danger',
                            'High' => 'bg-warning text-dark',
                            'Medium' => 'bg-primary',
                            'Low' => 'bg-success',
                            default => 'bg-secondary'
                          };
                          $priorityIcon = match($priority) {
                            'Critical' => '🔴',
                            'High' => '🟠',
                            'Medium' => '🟡',
                            'Low' => '🟢',
                            default => '⚪'
                          };
                        @endphp
                        <span class="badge {{ $priorityClass }}">{{ $priorityIcon }} {{ $priority }}</span>
                      </td>
                      <td>
                        <div class="btn-group btn-group-sm" role="group">
                          <button class="btn btn-outline-primary view-schedule-btn" data-id="{{ $s->id }}" title="View Details">
                            <i class="bi bi-eye"></i>
                          </button>
                          <button class="btn btn-outline-secondary edit-schedule-btn" data-id="{{ $s->id }}" title="Edit Schedule">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button class="btn btn-outline-danger delete-schedule-btn" data-id="{{ $s->id }}" title="Delete Schedule">
                            <i class="bi bi-trash"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  @else
                    <tr>
                      <td colspan="9" class="text-center py-4">
                        <div class="empty-state">
                          <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                          <h6 class="text-muted mt-2">No maintenance schedules yet</h6>
                          <p class="text-muted small">Create your first maintenance schedule to get started</p>
                          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newScheduleModal">
                            <i class="bi bi-plus-circle me-1"></i>Create Schedule
                          </button>
                        </div>
                      </td>
                    </tr>
                  @endif
                </tbody>
              </table>
            </div>

            <!-- Bulk Actions -->
            <div class="d-flex justify-content-between align-items-center mt-3" id="bulkActions" style="display: none !important;">
              <div>
                <span id="selectedCount">0</span> schedules selected
              </div>
              <div class="btn-group" role="group">
                <button class="btn btn-outline-success" id="bulkComplete">
                  <i class="bi bi-check-circle me-1"></i>Mark Complete
                </button>
                <button class="btn btn-outline-warning" id="bulkReschedule">
                  <i class="bi bi-calendar-event me-1"></i>Reschedule
                </button>
                <button class="btn btn-outline-danger" id="bulkDelete">
                  <i class="bi bi-trash me-1"></i>Delete Selected
                </button>
              </div>
            </div>
          </div>

          <!-- Calendar View -->
          <div id="calendarView" class="p-3" style="display: none;">
            <div id="maintenanceCalendar"></div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
      
      <!-- PO Preview (Hidden by default, shown when viewing PO) -->
      <div class="card shadow-sm border-0 mt-4 d-none" id="poPreviewCard">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">PO Preview</h5>
          <button class="btn btn-sm btn-close" id="closePOPreview"></button>
        </div>
        <div class="card-body">
          <h6 class="fw-bold">PO-2024-001</h6>
          <p class="small text-muted">Issued: 15 Jan 2024 | Expected: 20 Jan 2024</p>
          
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Item</th>
                  <th>Qty</th>
                  <th>Unit</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Laptop Pro 15"</td>
                  <td>10</td>
                  <td>pcs</td>
                </tr>
                <tr>
                  <td>Wireless Mouse</td>
                  <td>25</td>
                  <td>pcs</td>
                </tr>
                <tr>
                  <td>USB-C Hub</td>
                  <td>15</td>
                  <td>pcs</td>
                </tr>
              </tbody>
            </table>
          </div>
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

  <!-- New Schedule Modal -->
  <div class="modal fade" id="newScheduleModal" tabindex="-1" aria-labelledby="newScheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="newScheduleModalLabel">
            <i class="bi bi-calendar-plus me-2"></i>New Maintenance Schedule
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="scheduleForm" method="POST" action="{{ url('/alms/maintenance/schedules') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="assetId" class="form-label">Asset <span class="text-danger">*</span></label>
                <select class="form-control" id="assetId" required>
                  <option value="">Select asset</option>
                  @isset($assets)
                  @foreach($assets as $asset)
                  <option value="{{ $asset->id }}">{{ $asset->asset_id ?? '#ASSET-' . $asset->id }} — {{ $asset->plate_number ?? $asset->building_name ?? $asset->equipment_name ?? 'Asset' }} @if($asset->vehicle_type)({{ $asset->vehicle_type }})@endif</option>
                  @endforeach
                  @endisset
                </select>
              </div>
              <div class="col-md-6">
                <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
                <select class="form-control" id="category" required>
                  <option value="">Select category</option>
                  <option value="Preventive">🔧 Preventive Maintenance</option>
                  <option value="Corrective">⚠️ Corrective Maintenance</option>
                  <option value="Emergency">🚨 Emergency Repair</option>
                  <option value="Inspection">🔍 Safety Inspection</option>
                  <option value="Cleaning">🧽 Cleaning & Detailing</option>
                  <option value="Upgrade">⬆️ Upgrade/Modification</option>
                  <option value="Calibration">⚖️ Calibration</option>
                  <option value="Replacement">🔄 Parts Replacement</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="title" placeholder="e.g., Oil change, Brake inspection" required>
              </div>
              <div class="col-md-6">
                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                <select class="form-control" id="priority" required>
                  <option value="">Select priority</option>
                  <option value="Low">🟢 Low Priority</option>
                  <option value="Medium">🟡 Medium Priority</option>
                  <option value="High">🟠 High Priority</option>
                  <option value="Critical">🔴 Critical Priority</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="scheduledDate" class="form-label">Scheduled Date <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="scheduledDate" required>
              </div>
              <div class="col-md-6">
                <label for="estimatedDuration" class="form-label">Estimated Duration</label>
                <select class="form-control" id="estimatedDuration">
                  <option value="">Select duration</option>
                  <option value="30 minutes">30 minutes</option>
                  <option value="1 hour">1 hour</option>
                  <option value="2 hours">2 hours</option>
                  <option value="4 hours">4 hours</option>
                  <option value="1 day">1 day</option>
                  <option value="2 days">2 days</option>
                  <option value="1 week">1 week</option>
                  <option value="Custom">Custom duration</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="assignedTechnician" class="form-label">Assigned Technician</label>
                <input type="text" class="form-control" id="assignedTechnician" placeholder="e.g., John Smith, Maintenance Team">
              </div>
              <div class="col-md-6">
                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                <select class="form-control" id="status" required>
                  <option value="Scheduled">📅 Scheduled</option>
                  <option value="In Progress">⚙️ In Progress</option>
                  <option value="Completed">✅ Completed</option>
                  <option value="Cancelled">❌ Cancelled</option>
                </select>
              </div>
              <div class="col-12">
                <label for="notes" class="form-label">Notes & Instructions</label>
                <textarea class="form-control" id="notes" rows="3" placeholder="Additional maintenance notes, special instructions, or required tools/parts..."></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <button type="reset" form="scheduleForm" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise me-1"></i>Reset
          </button>
          <button type="submit" form="scheduleForm" class="btn btn-primary" id="createScheduleBtn">
            <i class="bi bi-check-circle me-1"></i>Create Schedule
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- View/Edit Schedule Modal -->
  <div class="modal fade" id="scheduleModal" tabindex="-1" aria-labelledby="scheduleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="scheduleModalLabel">Schedule</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="scheduleEditForm">
            <input type="hidden" id="schId">
            <div class="mb-2">
              <label class="form-label">Asset</label>
              <select id="schAssetId" class="form-control">
                @isset($assets)
                @foreach($assets as $asset)
                <option value="{{ $asset->id }}">{{ $asset->asset_id ?? '#ASSET-' . $asset->id }} — {{ $asset->plate_number ?? $asset->building_name ?? $asset->equipment_name ?? 'Asset' }}</option>
                @endforeach
                @endisset
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Title</label>
              <input type="text" id="schTitle" class="form-control">
            </div>
            <div class="mb-2">
              <label class="form-label">Scheduled Date</label>
              <input type="date" id="schDate" class="form-control">
            </div>
            <div class="mb-2">
              <label class="form-label">Status</label>
              <select id="schStatus" class="form-control">
                <option value="Scheduled">Scheduled</option>
                <option value="In Progress">In Progress</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
              </select>
            </div>
            <div class="mb-2">
              <label class="form-label">Notes</label>
              <textarea id="schNotes" class="form-control" rows="3"></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveScheduleBtn">Save changes</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar toggle functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize calendar
      let calendar;
      
      // View switching
      const tableViewBtn = document.getElementById('tableViewBtn');
      const calendarViewBtn = document.getElementById('calendarViewBtn');
      const tableView = document.getElementById('tableView');
      const calendarView = document.getElementById('calendarView');

      tableViewBtn.addEventListener('click', function() {
        tableView.style.display = 'block';
        calendarView.style.display = 'none';
        tableViewBtn.classList.add('active');
        calendarViewBtn.classList.remove('active');
      });

      calendarViewBtn.addEventListener('click', function() {
        tableView.style.display = 'none';
        calendarView.style.display = 'block';
        calendarViewBtn.classList.add('active');
        tableViewBtn.classList.remove('active');
        
        // Initialize calendar if not already done
        if (!calendar) {
          initializeCalendar();
        }
      });

      // Initialize FullCalendar
      function initializeCalendar() {
        const calendarEl = document.getElementById('maintenanceCalendar');
        
        // Prepare events data
        const events = [
          @if(isset($schedules) && $schedules->count())
            @foreach($schedules as $s)
            {
              id: '{{ $s->id }}',
              title: '{{ $s->title }}',
              start: '{{ $s->scheduled_date }}',
              backgroundColor: '{{ $s->status === "Completed" ? "#28a745" : ($s->status === "In Progress" ? "#007bff" : ($s->status === "Cancelled" ? "#6c757d" : "#ffc107")) }}',
              borderColor: '{{ $s->status === "Completed" ? "#28a745" : ($s->status === "In Progress" ? "#007bff" : ($s->status === "Cancelled" ? "#6c757d" : "#ffc107")) }}',
              extendedProps: {
                assetId: '{{ $s->asset_id }}',
                status: '{{ $s->status }}',
                notes: '{{ $s->notes ?? "" }}',
                assetName: '{{ optional($s->asset)->asset_id ?? "#ASSET-" . $s->asset_id }} — {{ optional($s->asset)->plate_number ?? optional($s->asset)->building_name ?? optional($s->asset)->equipment_name ?? "Asset Details" }}'
              }
            },
            @endforeach
          @endif
        ];

        calendar = new FullCalendar.Calendar(calendarEl, {
          initialView: 'dayGridMonth',
          headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
          },
          events: events,
          eventClick: function(info) {
            // Show schedule details when event is clicked
            viewScheduleDetails(info.event.id);
          },
          dateClick: function(info) {
            // Open new schedule modal with selected date
            document.getElementById('scheduledDate').value = info.dateStr;
            new bootstrap.Modal(document.getElementById('newScheduleModal')).show();
          },
          eventDidMount: function(info) {
            // Add tooltip
            info.el.title = `${info.event.title}\nAsset: ${info.event.extendedProps.assetName}\nStatus: ${info.event.extendedProps.status}`;
          }
        });

        calendar.render();
      }

      // Search and filter functionality
      const searchInput = document.getElementById('searchSchedules');
      const filterCategory = document.getElementById('filterCategory');
      const filterStatus = document.getElementById('filterStatus');
      const filterPriority = document.getElementById('filterPriority');
      const filterAsset = document.getElementById('filterAsset');
      const clearFiltersBtn = document.getElementById('clearFilters');

      function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const categoryFilter = filterCategory.value;
        const statusFilter = filterStatus.value;
        const priorityFilter = filterPriority.value;
        const assetFilter = filterAsset.value;
        const rows = document.querySelectorAll('#schedulesTable tbody tr[data-schedule-id]');

        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          const badges = row.querySelectorAll('.badge');
          
          // Find category, status, and priority badges
          let categoryBadge = null;
          let statusBadge = null;
          let priorityBadge = null;
          
          badges.forEach(badge => {
            const badgeText = badge.textContent.trim();
            if (badgeText.includes('🔧') || badgeText.includes('⚠️') || badgeText.includes('🚨') || 
                badgeText.includes('🔍') || badgeText.includes('🧽') || badgeText.includes('⬆️') || 
                badgeText.includes('⚖️') || badgeText.includes('🔄')) {
              categoryBadge = badge;
            } else if (badgeText.includes('🔴') || badgeText.includes('🟠') || 
                      badgeText.includes('🟡') || badgeText.includes('🟢')) {
              priorityBadge = badge;
            } else if (badge.classList.contains('bg-success') || badge.classList.contains('bg-primary') || 
                      badge.classList.contains('bg-warning') || badge.classList.contains('bg-secondary')) {
              statusBadge = badge;
            }
          });
          
          const matchesSearch = text.includes(searchTerm);
          const matchesCategory = !categoryFilter || (categoryBadge && categoryBadge.textContent.includes(categoryFilter));
          const matchesStatus = !statusFilter || (statusBadge && statusBadge.textContent.trim() === statusFilter);
          const matchesPriority = !priorityFilter || (priorityBadge && priorityBadge.textContent.includes(priorityFilter));
          const matchesAsset = !assetFilter || text.includes(`#ASSET-${assetFilter}`) || text.includes(assetFilter);
          
          row.style.display = (matchesSearch && matchesCategory && matchesStatus && matchesPriority && matchesAsset) ? '' : 'none';
        });
      }

      searchInput?.addEventListener('input', filterTable);
      filterCategory?.addEventListener('change', filterTable);
      filterStatus?.addEventListener('change', filterTable);
      filterPriority?.addEventListener('change', filterTable);
      filterAsset?.addEventListener('change', filterTable);

      clearFiltersBtn?.addEventListener('click', function() {
        searchInput.value = '';
        filterCategory.value = '';
        filterStatus.value = '';
        filterPriority.value = '';
        filterAsset.value = '';
        filterTable();
      });

      // Bulk actions
      const selectAllCheckbox = document.getElementById('selectAll');
      const scheduleCheckboxes = document.querySelectorAll('.schedule-checkbox');
      const bulkActions = document.getElementById('bulkActions');
      const selectedCount = document.getElementById('selectedCount');

      function updateBulkActions() {
        const checkedBoxes = document.querySelectorAll('.schedule-checkbox:checked');
        const count = checkedBoxes.length;
        
        selectedCount.textContent = count;
        bulkActions.style.display = count > 0 ? 'flex' : 'none';
        
        // Update select all checkbox
        selectAllCheckbox.indeterminate = count > 0 && count < scheduleCheckboxes.length;
        selectAllCheckbox.checked = count === scheduleCheckboxes.length && count > 0;
      }

      selectAllCheckbox?.addEventListener('change', function() {
        scheduleCheckboxes.forEach(checkbox => {
          checkbox.checked = this.checked;
        });
        updateBulkActions();
      });

      scheduleCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
      });

      // Bulk actions functionality
      document.getElementById('bulkComplete')?.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.schedule-checkbox:checked')).map(cb => cb.value);
        
        if (selectedIds.length === 0) return;
        
        Swal.fire({
          title: 'Mark as Complete?',
          text: `Are you sure you want to mark ${selectedIds.length} schedule(s) as completed?`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#28a745',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, mark complete!',
          cancelButtonText: 'Cancel'
        }).then(async (result) => {
          if (result.isConfirmed) {
            // Show loading
            Swal.fire({
              title: 'Updating Schedules...',
              text: 'Please wait while we update the selected schedules',
              icon: 'info',
              allowOutsideClick: false,
              showConfirmButton: false,
              didOpen: () => Swal.showLoading()
            });
            
            // Simulate bulk update (you'll need to implement the actual API)
            setTimeout(() => {
              Swal.fire({
                title: 'Success!',
                text: `${selectedIds.length} schedule(s) marked as completed!`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                window.location.reload();
              });
            }, 1500);
          }
        });
      });

      document.getElementById('bulkReschedule')?.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.schedule-checkbox:checked')).map(cb => cb.value);
        
        if (selectedIds.length === 0) return;
        
        // Show date picker dialog
        Swal.fire({
          title: 'Reschedule Maintenance',
          text: `Select a new date for ${selectedIds.length} selected schedule(s):`,
          icon: 'question',
          html: `
            <div class="mb-3">
              <label for="newScheduleDate" class="form-label">New Scheduled Date:</label>
              <input type="date" id="newScheduleDate" class="form-control" min="${new Date().toISOString().split('T')[0]}">
            </div>
            <div class="mb-3">
              <label for="rescheduleReason" class="form-label">Reason for Rescheduling (Optional):</label>
              <textarea id="rescheduleReason" class="form-control" rows="3" placeholder="e.g., Equipment unavailable, weather conditions, etc."></textarea>
            </div>
          `,
          showCancelButton: true,
          confirmButtonColor: '#ffc107',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Reschedule',
          cancelButtonText: 'Cancel',
          preConfirm: () => {
            const newDate = document.getElementById('newScheduleDate').value;
            const reason = document.getElementById('rescheduleReason').value;
            
            if (!newDate) {
              Swal.showValidationMessage('Please select a new date');
              return false;
            }
            
            return { newDate, reason };
          }
        }).then(async (result) => {
          if (result.isConfirmed) {
            const { newDate, reason } = result.value;
            
            // Show loading
            Swal.fire({
              title: 'Rescheduling...',
              text: 'Please wait while we update the selected schedules',
              icon: 'info',
              allowOutsideClick: false,
              showConfirmButton: false,
              didOpen: () => Swal.showLoading()
            });
            
            try {
              const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              
              // Make API call to reschedule
              const response = await fetch("{{ url('/alms/maintenance/schedules/bulk-reschedule') }}", {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': csrfToken,
                  'Accept': 'application/json'
                },
                body: JSON.stringify({
                  schedule_ids: selectedIds,
                  new_date: newDate,
                  reason: reason
                })
              });
              
              if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || 'Failed to reschedule schedules');
              }
              
              Swal.fire({
                title: 'Rescheduled!',
                text: `${selectedIds.length} schedule(s) have been rescheduled to ${new Date(newDate).toLocaleDateString()}.`,
                icon: 'success',
                timer: 3000,
                timerProgressBar: true,
                showConfirmButton: false
              }).then(() => {
                window.location.reload();
              });
              
            } catch (error) {
              console.error('Reschedule error:', error);
              Swal.fire({
                title: 'Reschedule Failed!',
                text: error.message || 'Failed to reschedule schedules. Please try again.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
              });
            }
          }
        });
      });

      document.getElementById('bulkDelete')?.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.schedule-checkbox:checked')).map(cb => cb.value);
        
        if (selectedIds.length === 0) return;
        
        Swal.fire({
          title: 'Delete Schedules?',
          text: `Are you sure you want to delete ${selectedIds.length} schedule(s)? This action cannot be undone.`,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete them!',
          cancelButtonText: 'Cancel',
          reverseButtons: true
        }).then(async (result) => {
          if (result.isConfirmed) {
            // Show loading
            Swal.fire({
              title: 'Deleting Schedules...',
              text: 'Please wait while we delete the selected schedules',
              icon: 'info',
              allowOutsideClick: false,
              showConfirmButton: false,
              didOpen: () => Swal.showLoading()
            });
            
            // Simulate bulk delete (you'll need to implement the actual API)
            setTimeout(() => {
              Swal.fire({
                title: 'Deleted!',
                text: `${selectedIds.length} schedule(s) have been deleted successfully.`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
              }).then(() => {
                window.location.reload();
              });
            }, 1500);
          }
        });
      });

      // Refresh button functionality
      document.getElementById('refreshSchedulesBtn')?.addEventListener('click', function() {
        const btn = this;
        const originalText = btn.innerHTML;
        
        // Show loading state
        btn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-1"></i>Refreshing...';
        btn.disabled = true;
        
        // Add spin animation
        const style = document.createElement('style');
        style.textContent = `
          .spin {
            animation: spin 1s linear infinite;
          }
          @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
          }
        `;
        document.head.appendChild(style);
        
        // Simulate refresh (reload page after delay)
        setTimeout(() => {
          window.location.reload();
        }, 1000);
      });

      // New Schedule Modal handlers
      const newScheduleModal = new bootstrap.Modal(document.getElementById('newScheduleModal'));
      const scheduleForm = document.getElementById('scheduleForm');
      const scheduledDate = document.getElementById('scheduledDate');
      const today = new Date().toISOString().split('T')[0];
      
      // Set today's date as default when modal opens
      document.getElementById('newScheduleModal').addEventListener('show.bs.modal', function() {
        if (scheduledDate) scheduledDate.value = today;
      });

      // Reset form when modal is hidden
      document.getElementById('newScheduleModal').addEventListener('hidden.bs.modal', function() {
        if (scheduleForm) {
          scheduleForm.reset();
          if (scheduledDate) scheduledDate.value = today;
        }
      });

      // Handle form submission
      if (scheduleForm) {
        scheduleForm.addEventListener('submit', async function(e) {
          e.preventDefault();
          const assetId = document.getElementById('assetId').value;
          const category = document.getElementById('category').value;
          const title = document.getElementById('title').value;
          const priority = document.getElementById('priority').value;
          const status = document.getElementById('status').value;
          const notes = document.getElementById('notes').value;
          const dateVal = document.getElementById('scheduledDate').value;
          const estimatedDuration = document.getElementById('estimatedDuration').value;
          const assignedTechnician = document.getElementById('assignedTechnician').value;
          
          if (!assetId || !category || !title || !priority || !dateVal || !status) {
            Swal.fire({
              title: 'Missing Information',
              text: 'Please fill all required fields marked with *',
              icon: 'warning',
              confirmButtonColor: '#ffc107'
            });
            return;
          }
          
          // Show loading state
          Swal.fire({
            title: 'Creating Schedule...',
            text: 'Please wait while we save your maintenance schedule',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          
          const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
          try {
            const res = await fetch("{{ url('/alms/maintenance/schedules') }}", {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
              },
              body: JSON.stringify({
                asset_id: assetId,
                category: category,
                title: title,
                priority: priority,
                scheduled_date: dateVal,
                status: status,
                estimated_duration: estimatedDuration || null,
                assigned_technician: assignedTechnician || null,
                notes: notes || null
              })
            });
            
            if (!res.ok) {
              const errorData = await res.json().catch(() => ({}));
              Swal.fire({
                title: 'Creation Failed!',
                text: errorData.message || 'Failed to create maintenance schedule. Please try again.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
              });
              return;
            }
            
            // Show success message
            Swal.fire({
              title: 'Success!',
              text: 'Maintenance schedule created successfully!',
              icon: 'success',
              timer: 2000,
              timerProgressBar: true,
              showConfirmButton: false
            }).then(() => {
              newScheduleModal.hide();
              window.location.href = "{{ url('/alms/maintenance') }}";
            });
            
          } catch (err) {
            console.error(err);
            Swal.fire({
              title: 'Error!',
              text: 'An unexpected error occurred. Please try again.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
          }
        });
      }

      // Helper function to view schedule details
      function viewScheduleDetails(scheduleId) {
        const viewBtn = document.querySelector(`[data-id="${scheduleId}"].view-schedule-btn`);
        if (viewBtn) {
          viewBtn.click();
        }
      }

      // Schedule view/edit handlers
      const scheduleModal = new bootstrap.Modal(document.getElementById('scheduleModal'));
      document.querySelectorAll('.view-schedule-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
          e.preventDefault();
          const id = this.getAttribute('data-id');
          const res = await fetch(`{{ url('/alms/maintenance/schedules') }}/${id}`);
          if (!res.ok) return;
          const json = await res.json();
          const s = json.data;
          document.getElementById('scheduleModalLabel').textContent = `View Schedule #${s.id}`;
          document.getElementById('schId').value = s.id;
          document.getElementById('schAssetId').value = s.asset_id;
          document.getElementById('schTitle').value = s.title;
          document.getElementById('schDate').value = s.scheduled_date;
          document.getElementById('schStatus').value = s.status;
          document.getElementById('schNotes').value = s.notes ?? '';
          document.querySelectorAll('#scheduleEditForm input, #scheduleEditForm select, #scheduleEditForm textarea').forEach(el => el.setAttribute('disabled', 'disabled'));
          document.getElementById('saveScheduleBtn').style.display = 'none';
          scheduleModal.show();
        });
      });

      document.querySelectorAll('.edit-schedule-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
          e.preventDefault();
          const id = this.getAttribute('data-id');
          const res = await fetch(`{{ url('/alms/maintenance/schedules') }}/${id}`);
          if (!res.ok) return;
          const json = await res.json();
          const s = json.data;
          document.getElementById('scheduleModalLabel').textContent = `Edit Schedule #${s.id}`;
          document.getElementById('schId').value = s.id;
          document.getElementById('schAssetId').value = s.asset_id;
          document.getElementById('schTitle').value = s.title;
          document.getElementById('schDate').value = s.scheduled_date;
          document.getElementById('schStatus').value = s.status;
          document.getElementById('schNotes').value = s.notes ?? '';
          document.querySelectorAll('#scheduleEditForm input, #scheduleEditForm select, #scheduleEditForm textarea').forEach(el => el.removeAttribute('disabled'));
          document.getElementById('saveScheduleBtn').style.display = '';
          scheduleModal.show();
        });
      });

      document.getElementById('saveScheduleBtn')?.addEventListener('click', async function() {
        const id = document.getElementById('schId').value;
        
        // Show loading state
        Swal.fire({
          title: 'Updating Schedule...',
          text: 'Please wait while we save your changes',
          icon: 'info',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const payload = {
          asset_id: document.getElementById('schAssetId').value,
          title: document.getElementById('schTitle').value,
          scheduled_date: document.getElementById('schDate').value,
          status: document.getElementById('schStatus').value,
          notes: document.getElementById('schNotes').value || null
        };
        
        try {
          const res = await fetch(`{{ url('/alms/maintenance/schedules') }}/${id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
          });
          
          if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            Swal.fire({
              title: 'Update Failed!',
              text: errorData.message || 'Failed to save schedule changes. Please try again.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
            return;
          }
          
          // Show success message
          Swal.fire({
            title: 'Success!',
            text: 'Schedule updated successfully!',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
          }).then(() => {
            scheduleModal.hide();
            window.location.href = "{{ url('/alms/maintenance') }}";
          });
          
        } catch (err) {
          console.error(err);
          Swal.fire({
            title: 'Error!',
            text: 'An unexpected error occurred. Please try again.',
            icon: 'error',
            confirmButtonColor: '#dc3545'
          });
        }
      });

      // Delete schedule functionality
      document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-schedule-btn')) {
          const btn = e.target.closest('.delete-schedule-btn');
          const scheduleId = btn.getAttribute('data-id');
          
          Swal.fire({
            title: 'Delete Schedule?',
            text: 'Are you sure you want to delete this maintenance schedule? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            reverseButtons: true
          }).then(async (result) => {
            if (result.isConfirmed) {
              // Show loading state
              Swal.fire({
                title: 'Deleting Schedule...',
                text: 'Please wait while we delete the schedule',
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                  Swal.showLoading();
                }
              });
              
              try {
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch(`{{ url('/alms/maintenance/schedules') }}/${scheduleId}`, {
                  method: 'DELETE',
                  headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                  }
                });
                
                if (!res.ok) {
                  const errorData = await res.json().catch(() => ({}));
                  Swal.fire({
                    title: 'Delete Failed!',
                    text: errorData.message || 'Failed to delete schedule. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                  });
                  return;
                }
                
                // Show success message
                Swal.fire({
                  title: 'Deleted!',
                  text: 'Maintenance schedule has been deleted successfully.',
                  icon: 'success',
                  timer: 2000,
                  timerProgressBar: true,
                  showConfirmButton: false
                }).then(() => {
                  window.location.href = "{{ url('/alms/maintenance') }}";
                });
                
              } catch (err) {
                console.error(err);
                Swal.fire({
                  title: 'Error!',
                  text: 'An unexpected error occurred. Please try again.',
                  icon: 'error',
                  confirmButtonColor: '#dc3545'
                });
              }
            }
          });
        }
      });
      // Check if user is authenticated (non-blocking)
      const authToken = localStorage.getItem('auth_token');
      if (!authToken) {
        const nameEl = document.querySelector('.profile-section h6');
        if (nameEl) {
          nameEl.textContent = 'Guest';
        }
      }

      // Verify token is still valid if present
      if (authToken) {
      fetch("{{ url('/api/profile') }}", {
        headers: {
          'Authorization': `Bearer ${authToken}`,
          'Accept': 'application/json'
        }
      })
      .then(response => {
        if (!response.ok) {
          // Token is invalid, clear and continue as guest
          localStorage.removeItem('auth_token');
          localStorage.removeItem('user_data');
          const nameEl = document.querySelector('.profile-section h6');
          if (nameEl) {
            nameEl.textContent = 'Guest';
          }
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
        const nameEl = document.querySelector('.profile-section h6');
        if (nameEl) {
          nameEl.textContent = 'Guest';
        }
      });
      }

      // Logout functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
          e.preventDefault();

          const authToken = localStorage.getItem('auth_token');
          if (!authToken) {
            window.location.href = "{{ url('/login') }}";
            return;
          }

          try {
            // Call logout API
            await fetch("{{ url('/api/logout') }}", {
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
          window.location.href = "{{ url('/login') }}";
        });
      }

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

      // Project Logistics Tracker dropdown active state logic
      const pltDropdown = document.querySelector('[data-bs-target="#pltSubmenu"]');
      const pltSubmenu = document.getElementById('pltSubmenu');
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
      
      // Asset Life Cycle & Maintenance dropdown logic
      const assetDropdown = document.querySelector('[data-bs-target="#assetSubmenu"]');
      const assetSubmenu = document.getElementById('assetSubmenu');
      if (assetDropdown && assetSubmenu) {
        if (
          currentPath.includes('/alms/assetregistration') ||
          currentPath.includes('/alms/maintenance') ||
          currentPath.includes('/alms/disposalretirement')
        ) {
          assetDropdown.classList.add('active');
          assetSubmenu.classList.add('show');
          const activeSubItem = assetSubmenu.querySelector(`[href="${currentPath}"]`);
          if (activeSubItem) {
            activeSubItem.classList.add('active');
          }
        }
        // Prevent dropdown from closing when clicking ALMS sub-links
        assetSubmenu.querySelectorAll('.nav-link').forEach(link => {
          link.addEventListener('click', function() {
            assetSubmenu.classList.add('show');
            assetSubmenu.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
          });
        });
      }

      // Remove 'active' from Smart Warehousing on PLT pages
      const warehouseDropdown = document.querySelector('[data-bs-target="#warehouseSubmenu"]');
      const warehouseSubmenu = document.getElementById('warehouseSubmenu');
      if (warehouseDropdown) {
        if (
          currentPath.includes('/plt/toursetup') ||
          currentPath.includes('/plt/execution') ||
          currentPath.includes('/plt/closure')
        ) {
          warehouseDropdown.classList.remove('active');
          if (warehouseSubmenu) {
            warehouseSubmenu.classList.remove('show');
          }
        }
      }

      // Collapse Procurement dropdown on SWS pages
      const procurementDropdown = document.querySelector('[data-bs-target="#procurementSubmenu"]');
      const procurementSubmenu = document.getElementById('procurementSubmenu');
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
