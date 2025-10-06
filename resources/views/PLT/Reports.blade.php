<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Asset Movement Reports - Project Logistics Tracker</title>

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
          <a href="{{ url('/plt/execution') }}" class="nav-link text-dark small ">
            <i class="bi bi-bar-chart-steps me-2"></i> Execution Monitoring
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/plt/closure') }}" class="nav-link text-dark small active">
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
        <div class="collapse " id="assetSubmenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a href="{{ url('/alms/assetregistration') }}" class="nav-link text-dark small ">
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
          <i class="bi bi-file-earmark-bar-graph fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Asset Movement Reports</h2>
          <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Generate comprehensive reports and reflections on completed asset movements.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/plt') }}" class="text-decoration-none">Project Logistics</a></li>
            <li class="breadcrumb-item active" aria-current="page">Asset Movement Reports</li>
          </ol>
        </nav>
    </div>
  </div>

  <!-- Movement Statistics Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
              <i class="bi bi-file-earmark-bar-graph"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $stats['total_reports'] ?? 47 }}</h3>
              <p class="text-muted mb-0 small">Generated Reports</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +5 this month</small>
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
              <h3 class="fw-bold mb-0">{{ $stats['completed_movements'] ?? 156 }}</h3>
              <p class="text-muted mb-0 small">Completed Movements</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +12 this week</small>
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
              <h3 class="fw-bold mb-0">{{ $stats['incidents_reported'] ?? 3 }}</h3>
              <p class="text-muted mb-0 small">Incidents Reported</p>
              <small class="text-warning"><i class="bi bi-arrow-down"></i> -2 from last month</small>
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
              <h3 class="fw-bold mb-0">{{ $stats['avg_completion_time'] ?? '3.2' }}hrs</h3>
              <p class="text-muted mb-0 small">Avg Movement Time</p>
              <small class="text-success"><i class="bi bi-arrow-down"></i> -0.5hrs improvement</small>
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
      <!-- Report Generation Form -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom bg-primary text-white">
          <h5 class="card-title mb-0">Generate Movement Report</h5>
        </div>
        <div class="card-body">
          <form id="reportForm">
            <div class="row g-3">
              <!-- Report Configuration -->
              <div class="col-md-6">
                <label for="reportType" class="form-label">Report Type <span class="text-danger">*</span></label>
                <select class="form-select" id="reportType" required>
                  <option value="">Select Report Type</option>
                  <option value="movement_summary">Movement Summary Report</option>
                  <option value="performance_analysis">Performance Analysis</option>
                  <option value="cost_analysis">Cost Analysis Report</option>
                  <option value="incident_report">Incident & Issues Report</option>
                  <option value="efficiency_report">Efficiency & KPI Report</option>
                  <option value="reflection_report">Movement Reflection Report</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="reportPeriod" class="form-label">Report Period <span class="text-danger">*</span></label>
                <select class="form-select" id="reportPeriod" required>
                  <option value="">Select Period</option>
                  <option value="today">Today</option>
                  <option value="this_week">This Week</option>
                  <option value="this_month">This Month</option>
                  <option value="last_month">Last Month</option>
                  <option value="quarter">This Quarter</option>
                  <option value="custom">Custom Date Range</option>
                </select>
              </div>
              
              <!-- Custom Date Range (hidden by default) -->
              <div class="col-md-6" id="customDateStart" style="display:none;">
                <label for="startDate" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="startDate">
              </div>
              <div class="col-md-6" id="customDateEnd" style="display:none;">
                <label for="endDate" class="form-label">End Date</label>
                <input type="date" class="form-control" id="endDate">
              </div>
              
              <!-- Filters -->
              <div class="col-md-4">
                <label for="movementStatus" class="form-label">Movement Status</label>
                <select class="form-select" id="movementStatus">
                  <option value="">All Statuses</option>
                  <option value="completed">Completed Only</option>
                  <option value="in_progress">In Progress</option>
                  <option value="delayed">Delayed</option>
                  <option value="cancelled">Cancelled</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="assetCategory" class="form-label">Asset Category</label>
                <select class="form-select" id="assetCategory">
                  <option value="">All Categories</option>
                  <option value="IT Equipment">IT Equipment</option>
                  <option value="Office Furniture">Office Furniture</option>
                  <option value="Vehicles">Vehicles</option>
                  <option value="Machinery">Machinery</option>
                  <option value="Documents">Documents</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col-md-4">
                <label for="department" class="form-label">Department</label>
                <select class="form-select" id="department">
                  <option value="">All Departments</option>
                  <option value="Logistics">Logistics</option>
                  <option value="Operations">Operations</option>
                  <option value="IT">Information Technology</option>
                  <option value="Facilities">Facilities</option>
                  <option value="Security">Security</option>
                </select>
              </div>
              
              <!-- Report Options -->
              <div class="col-12">
                <label class="form-label">Include in Report</label>
                <div class="row">
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="includeCharts" checked>
                      <label class="form-check-label" for="includeCharts">
                        Charts & Graphs
                      </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="includeTimeline" checked>
                      <label class="form-check-label" for="includeTimeline">
                        Movement Timeline
                      </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="includeCosts">
                      <label class="form-check-label" for="includeCosts">
                        Cost Breakdown
                      </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="includeIncidents">
                      <label class="form-check-label" for="includeIncidents">
                        Incidents & Issues
                      </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="includeRecommendations" checked>
                      <label class="form-check-label" for="includeRecommendations">
                        Recommendations
                      </label>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="includeReflections" checked>
                      <label class="form-check-label" for="includeReflections">
                        Lessons Learned
                      </label>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Additional Notes -->
              <div class="col-12">
                <label for="reportNotes" class="form-label">Additional Notes</label>
                <textarea class="form-control" id="reportNotes" rows="3" placeholder="Add any specific requirements or notes for this report"></textarea>
              </div>
            </div>
            
            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-4">
              <button type="reset" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Clear Form
              </button>
              <div>
                <button type="button" class="btn btn-outline-primary me-2" id="previewReportBtn">
                  <i class="bi bi-eye me-1"></i>Preview Report
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-file-earmark-bar-graph me-1"></i>Generate Report
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Recent Reports -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Recent Movement Reports</h5>
          <button class="btn btn-sm btn-outline-primary">View All</button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Report ID</th>
                  <th>Type</th>
                  <th>Period</th>
                  <th>Generated</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><strong>#RPT-2024-015</strong></td>
                  <td>Movement Summary</td>
                  <td>This Month</td>
                  <td>Today, 2:30 PM</td>
                  <td><span class="badge bg-success">Completed</span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" title="View Report">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" title="Download PDF">
                      <i class="bi bi-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" title="Share Report">
                      <i class="bi bi-share"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td><strong>#RPT-2024-014</strong></td>
                  <td>Performance Analysis</td>
                  <td>Last Week</td>
                  <td>Yesterday, 4:15 PM</td>
                  <td><span class="badge bg-success">Completed</span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" title="View Report">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" title="Download PDF">
                      <i class="bi bi-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" title="Share Report">
                      <i class="bi bi-share"></i>
                    </button>
                  </td>
                </tr>
                <tr>
                  <td><strong>#RPT-2024-013</strong></td>
                  <td>Reflection Report</td>
                  <td>Q4 2024</td>
                  <td>2 days ago</td>
                  <td><span class="badge bg-warning">Processing</span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" title="View Report" disabled>
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" title="Download PDF" disabled>
                      <i class="bi bi-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" title="Cancel Generation">
                      <i class="bi bi-x-circle"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
      <!-- Quick Actions -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Quick Actions</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button class="btn btn-primary" id="generateQuickReportBtn">
              <i class="bi bi-lightning me-2"></i>Quick Report
            </button>
            <button class="btn btn-outline-primary" id="scheduleReportBtn">
              <i class="bi bi-calendar-plus me-2"></i>Schedule Report
            </button>
            <button class="btn btn-outline-primary" id="exportDataBtn">
              <i class="bi bi-file-earmark-excel me-2"></i>Export Data
            </button>
            <button class="btn btn-outline-primary" id="shareReportBtn">
              <i class="bi bi-share me-2"></i>Share Report
            </button>
            <button class="btn btn-outline-secondary" id="printReportBtn">
              <i class="bi bi-printer me-2"></i>Print Report
            </button>
          </div>
        </div>
      </div>
      
      <!-- Movement Analytics -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Movement Analytics</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <canvas id="movementTrendsChart" width="400" height="200"></canvas>
          </div>
          <div class="row g-2">
            <div class="col-6">
              <div class="text-center p-2 bg-light rounded">
                <small class="text-muted d-block">Success Rate</small>
                <strong class="text-success">{{ $analytics['success_rate'] ?? 94 }}%</strong>
              </div>
            </div>
            <div class="col-6">
              <div class="text-center p-2 bg-light rounded">
                <small class="text-muted d-block">Efficiency</small>
                <strong class="text-info">{{ $analytics['efficiency'] ?? 87 }}%</strong>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Report Summary -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">This Month's Summary</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Reports Generated</span>
              <span class="small fw-bold">{{ $summary['reports_generated'] ?? 15 }}</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: {{ ($summary['reports_generated'] ?? 15) * 5 }}%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Movements Analyzed</span>
              <span class="small fw-bold">{{ $summary['movements_analyzed'] ?? 127 }}</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-primary" style="width: 85%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Issues Identified</span>
              <span class="small fw-bold">{{ $summary['issues_identified'] ?? 8 }}</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-warning" style="width: 15%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Avg. Report Time</span>
              <span class="small fw-bold">{{ $summary['avg_report_time'] ?? '2.5' }}min</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-info" style="width: 70%"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Key Insights -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Key Insights</h5>
        </div>
        <div class="card-body">
          <div class="alert alert-success alert-sm mb-2">
            <i class="bi bi-check-circle me-2"></i>
            Movement efficiency improved by 12% this month
          </div>
          <div class="alert alert-info alert-sm mb-2">
            <i class="bi bi-info-circle me-2"></i>
            IT Equipment movements show highest success rate
          </div>
          <div class="alert alert-warning alert-sm">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Consider optimizing routes for Building A-C transfers
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Report Form Functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Report period change handler
      const reportPeriod = document.getElementById('reportPeriod');
      const customDateStart = document.getElementById('customDateStart');
      const customDateEnd = document.getElementById('customDateEnd');
      
      reportPeriod.addEventListener('change', function() {
        if (this.value === 'custom') {
          customDateStart.style.display = 'block';
          customDateEnd.style.display = 'block';
        } else {
          customDateStart.style.display = 'none';
          customDateEnd.style.display = 'none';
        }
      });

      // Report form submission
      const reportForm = document.getElementById('reportForm');
      reportForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const reportType = document.getElementById('reportType').value;
        const reportPeriod = document.getElementById('reportPeriod').value;
        
        if (!reportType || !reportPeriod) {
          Swal.fire({
            icon: 'warning',
            title: 'Missing Information',
            text: 'Please select both report type and period.',
            confirmButtonColor: '#0d6efd'
          });
          return;
        }

        // Show loading
        Swal.fire({
          title: 'Generating Report...',
          text: 'Please wait while we compile your movement data.',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        // Simulate report generation
        setTimeout(() => {
          Swal.fire({
            icon: 'success',
            title: 'Report Generated Successfully!',
            text: `Your ${reportType.replace('_', ' ')} report for ${reportPeriod.replace('_', ' ')} is ready.`,
            showCancelButton: true,
            confirmButtonText: 'Download PDF',
            cancelButtonText: 'View Online',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#0d6efd'
          }).then((result) => {
            if (result.isConfirmed) {
              // Simulate PDF download
              const link = document.createElement('a');
              link.href = '#';
              link.download = `movement_report_${Date.now()}.pdf`;
              link.click();
            } else if (result.dismiss === Swal.DismissReason.cancel) {
              // Show report preview
              window.open('#', '_blank');
            }
          });
        }, 3000);
      });

      // Preview report button
      const previewBtn = document.getElementById('previewReportBtn');
      previewBtn.addEventListener('click', function() {
        const reportType = document.getElementById('reportType').value;
        
        if (!reportType) {
          Swal.fire({
            icon: 'info',
            title: 'Select Report Type',
            text: 'Please select a report type to preview.',
            confirmButtonColor: '#0d6efd'
          });
          return;
        }

        Swal.fire({
          title: 'Report Preview',
          html: `
            <div class="text-start">
              <h6>Report Type: ${reportType.replace('_', ' ').toUpperCase()}</h6>
              <hr>
              <p><strong>Sample Content:</strong></p>
              <ul class="small">
                <li>Executive Summary</li>
                <li>Movement Statistics</li>
                <li>Performance Metrics</li>
                <li>Cost Analysis</li>
                <li>Recommendations</li>
                <li>Lessons Learned</li>
              </ul>
            </div>
          `,
          width: 600,
          confirmButtonText: 'Generate Full Report',
          showCancelButton: true,
          cancelButtonText: 'Close Preview',
          confirmButtonColor: '#0d6efd'
        }).then((result) => {
          if (result.isConfirmed) {
            reportForm.dispatchEvent(new Event('submit'));
          }
        });
      });

      // Quick action buttons
      document.getElementById('generateQuickReportBtn').addEventListener('click', function() {
        document.getElementById('reportType').value = 'movement_summary';
        document.getElementById('reportPeriod').value = 'this_week';
        reportForm.dispatchEvent(new Event('submit'));
      });

      document.getElementById('scheduleReportBtn').addEventListener('click', function() {
        Swal.fire({
          title: 'Schedule Report',
          html: `
            <div class="mb-3">
              <label class="form-label">Frequency</label>
              <select class="form-select" id="scheduleFrequency">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Email Recipients</label>
              <input type="email" class="form-control" id="scheduleEmail" placeholder="Enter email addresses">
            </div>
          `,
          confirmButtonText: 'Schedule Report',
          showCancelButton: true,
          confirmButtonColor: '#198754'
        });
      });

      // Initialize movement trends chart
      const ctx = document.getElementById('movementTrendsChart');
      if (ctx) {
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
              label: 'Completed Movements',
              data: [12, 19, 15, 22],
              borderColor: '#0d6efd',
              backgroundColor: 'rgba(13, 110, 253, 0.1)',
              tension: 0.4
            }, {
              label: 'Delayed Movements',
              data: [2, 3, 1, 4],
              borderColor: '#ffc107',
              backgroundColor: 'rgba(255, 193, 7, 0.1)',
              tension: 0.4
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                display: false
              }
            },
            scales: {
              y: {
                beginAtZero: true,
                grid: {
                  display: false
                }
              },
              x: {
                grid: {
                  display: false
                }
              }
            }
          }
        });
      }
    });
  </script>

  <!-- Sidebar toggle functionality -->
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

      // Only auto-expand Procurement dropdown on PSM pages
      if (procurementDropdown && procurementSubmenu) {
        if (
          currentPath.includes('/psm/request') ||
          currentPath.includes('/psm/vendor') ||
          currentPath.includes('/psm/bidding') ||
          currentPath.includes('/psm/contract') ||
          currentPath.includes('/psm/order') ||
          currentPath.includes('/psm/delivery') ||
          currentPath.includes('/psm/invoice')
        ) {
          procurementDropdown.setAttribute('aria-expanded', 'true');
          procurementSubmenu.classList.add('show');
        } else {
          procurementDropdown.setAttribute('aria-expanded', 'false');
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
  </script>
</body>
</html>
