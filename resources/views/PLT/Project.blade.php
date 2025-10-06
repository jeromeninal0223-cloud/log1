<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Project Planning - Project Logistics Tracker</title>

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
        <i class="bi bi-diagram-3 me-2"></i>Project Planning
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
          <a href="{{ url('/plt/toursetup') }}" class="nav-link text-dark small active">
            <i class="bi bi-truck me-2"></i> Planning
          </a>
        </li>
        <li class="nav-item">
          <a href="{{ url('/plt/execution') }}" class="nav-link text-dark small">
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
        <a href="#" class="nav-link text-dark " data-bs-toggle="collapse" data-bs-target="#assetSubmenu" aria-expanded="false" aria-controls="assetSubmenu">
          <i class="bi bi-tools me-2"></i> Asset Life Cycle & Maintenance
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse" id="assetSubmenu">
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
          <i class="bi bi-diagram-3 fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Project Planning</h2>
          <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Plan and manage your projects from initiation to completion.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/plt') }}" class="text-decoration-none">Project Logistics</a></li>
            <li class="breadcrumb-item active" aria-current="page">Project Planning</li>
          </ol>
        </nav>
    </div>
  </div>

  <!-- Simple Plan Statistics Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Total Projects</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> All time</small>
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
              <h3 class="fw-bold mb-0">{{ $stats['active'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Active Projects</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> In progress</small>
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
              <i class="bi bi-clock-history"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $stats['draft'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Draft Projects</p>
              <small class="text-warning"><i class="bi bi-clock"></i> Pending</small>
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
              <i class="bi bi-calendar-check"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $stats['completed'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Completed Projects</p>
              <small class="text-success"><i class="bi bi-check-circle"></i> Finished</small>
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
      <!-- Resources Project Plan Form -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom bg-primary text-white d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Create New Project</h5>
          <button class="btn btn-sm btn-light" id="toggleFormBtn">
            <i class="bi bi-chevron-up"></i>
          </button>
        </div>
        <div class="card-body" id="projectFormBody">
          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif
          @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <strong>Validation Error:</strong>
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif
          <form id="projectForm" method="POST" action="{{ route('plt.projects.store') }}">
            @csrf
            
            <!-- 1. Project Information -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>1. Project Information</h6>
              </div>
              
              <div class="col-md-6">
                <label for="project_title" class="form-label">Project Title <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="project_title" name="project_title" placeholder="Enter project title" required>
              </div>
              
              <div class="col-md-6">
                <label for="project_code" class="form-label">Project Code / ID</label>
                <input type="text" class="form-control" id="project_code" name="project_code" placeholder="Enter project code or ID">
              </div>
              
              <div class="col-md-6">
                <label for="responsible_person" class="form-label">Project Manager <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="responsible_person" name="responsible_person" placeholder="Project manager name" required>
              </div>
              
              <div class="col-md-6">
                <label for="department" class="form-label">Department / Unit <span class="text-danger">*</span></label>
                <select class="form-select" id="department" name="department" required>
                  <option value="">Select Department</option>
                  <option value="IT">Information Technology</option>
                  <option value="Marketing">Marketing</option>
                  <option value="Operations">Operations</option>
                  <option value="Finance">Finance</option>
                  <option value="HR">Human Resources</option>
                  <option value="Logistics">Logistics</option>
                  <option value="R&D">Research & Development</option>
                  <option value="Sales">Sales</option>
                  <option value="Customer Service">Customer Service</option>
                  <option value="Legal">Legal</option>
                </select>
              </div>
            </div>
            
            <!-- 2. Asset Information -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-box me-2"></i>2. Asset Information</h6>
              </div>
              
              <div class="col-md-6">
                <label for="asset_type" class="form-label">Asset Type <span class="text-danger">*</span></label>
                <select class="form-select" id="asset_type" name="asset_type" required>
                  <option value="">Select Asset Type</option>
                  <option value="Vehicle">Vehicle</option>
                  <option value="Building">Building</option>
                  <option value="Equipment">Equipment</option>
                  <option value="IT System">IT System</option>
                  <option value="Furniture">Furniture</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              
              <div class="col-md-6">
                <label for="asset_name" class="form-label">Asset Name / Description <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="asset_name" name="asset_name" placeholder="Enter asset name or description" required>
              </div>
              
              <div class="col-md-6">
                <label for="asset_id" class="form-label">Asset ID / Tag Number</label>
                <input type="text" class="form-control" id="asset_id" name="asset_id" placeholder="Enter asset ID or tag number">
              </div>
              
              <div class="col-md-6">
                <label for="asset_status" class="form-label">Current Status / Condition</label>
                <select class="form-select" id="asset_status" name="asset_status">
                  <option value="">Select Status</option>
                  <option value="Excellent">Excellent</option>
                  <option value="Good">Good</option>
                  <option value="Fair">Fair</option>
                  <option value="Poor">Poor</option>
                  <option value="Needs Repair">Needs Repair</option>
                  <option value="Out of Service">Out of Service</option>
                </select>
              </div>
            </div>
            
            <!-- 3. Objective / Purpose -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-target me-2"></i>3. Objective / Purpose</h6>
              </div>
              
              <div class="col-12">
                <label for="project_objective" class="form-label">Objective / Purpose <span class="text-danger">*</span></label>
                <textarea class="form-control" id="project_objective" name="project_objective" rows="4" placeholder="Describe the main objective and purpose of this project" required></textarea>
              </div>
            </div>
            
            <!-- 4. Scope -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-bullseye me-2"></i>4. Scope</h6>
              </div>
              
              <div class="col-12">
                <label for="project_scope" class="form-label">Scope <span class="text-danger">*</span></label>
                <textarea class="form-control" id="project_scope" name="project_scope" rows="4" placeholder="Define the project scope - what is included and what is excluded" required></textarea>
              </div>
            </div>
            
            <!-- 5. Facilities Needed -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-building me-2"></i>5. Facilities Needed</h6>
              </div>
              
              <div class="col-12">
                <label for="facilities_needed" class="form-label">Facilities Needed</label>
                <textarea class="form-control" id="facilities_needed" name="facilities_needed" rows="3" placeholder="List all facilities, spaces, or infrastructure required for this project"></textarea>
              </div>
            </div>

            <!-- 6. Resources -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-people me-2"></i>6. Resources</h6>
              </div>
              
              <div class="col-md-4">
                <label for="human_resources" class="form-label">Human Resources</label>
                <textarea class="form-control" id="human_resources" name="human_resources" rows="3" placeholder="List required personnel, roles, and skills needed"></textarea>
              </div>
              
              <div class="col-md-4">
                <label for="materials_tools" class="form-label">Materials / Tools</label>
                <textarea class="form-control" id="materials_tools" name="materials_tools" rows="3" placeholder="List required materials, tools, and equipment"></textarea>
              </div>
              
              <div class="col-md-4">
                <label for="estimated_budget" class="form-label">Budget Estimate <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text">₱</span>
                  <input type="number" class="form-control" id="estimated_budget" name="estimated_budget" step="0.01" placeholder="0.00" required>
                </div>
              </div>
            </div>

            <!-- 7. Milestones -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-flag me-2"></i>7. Milestones</h6>
              </div>
              
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table table-bordered" id="milestonesTable">
                    <thead class="table-light">
                      <tr>
                        <th>Milestone Name</th>
                        <th>Target Date</th>
                        <th>Description</th>
                        <th>Deliverable</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><input type="text" class="form-control form-control-sm" name="milestones[0][name]" placeholder="Milestone name"></td>
                        <td><input type="date" class="form-control form-control-sm" name="milestones[0][date]"></td>
                        <td><input type="text" class="form-control form-control-sm" name="milestones[0][description]" placeholder="Brief description"></td>
                        <td><input type="text" class="form-control form-control-sm" name="milestones[0][deliverable]" placeholder="Expected deliverable"></td>
                        <td>
                          <button type="button" class="btn btn-sm btn-outline-danger remove-milestone">
                            <i class="bi bi-trash"></i>
                          </button>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                  <button type="button" class="btn btn-sm btn-outline-primary" id="addMilestone">
                    <i class="bi bi-plus-circle me-1"></i>Add Milestone
                  </button>
                </div>
              </div>
            </div>

            <!-- 8. Risks -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-exclamation-triangle me-2"></i>8. Risks</h6>
              </div>
              
              <div class="col-12">
                <label for="potential_risks" class="form-label">Potential risks and mitigation strategies</label>
                <textarea class="form-control" id="potential_risks" name="potential_risks" rows="5" placeholder="Identify potential risks and describe mitigation strategies for each risk"></textarea>
              </div>
            </div>
            
            <!-- 9. Approval -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary fw-bold mb-3"><i class="bi bi-check2-square me-2"></i>9. Approval</h6>
              </div>
              
              <div class="col-md-4">
                <label for="prepared_by" class="form-label">Prepared by <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="prepared_by" name="prepared_by" placeholder="Name" value="{{ Auth::user()->name }}" required>
              </div>
              
              <div class="col-md-4">
                <label for="reviewed_by" class="form-label">Reviewed by</label>
                <input type="text" class="form-control" id="reviewed_by" name="reviewed_by" placeholder="Name">
              </div>
              
              <div class="col-md-4">
                <label for="approved_by" class="form-label">Approved by</label>
                <input type="text" class="form-control" id="approved_by" name="approved_by" placeholder="Name">
              </div>
            </div>
            
            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
              <div>
                <button type="reset" class="btn btn-outline-secondary me-2">
                  <i class="bi bi-x-circle me-1"></i>Clear Form
                </button>
              </div>
              <div>
                <button type="button" class="btn btn-outline-warning me-2" id="saveDraftBtn">
                  <i class="bi bi-file-earmark me-1"></i>Save as Draft
                </button>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check-circle me-1"></i>Create Project Plan
                </button>
              </div>
          </form>
        </div>
      </div>
      
      <!-- Recent Projects -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Recent Projects ({{ isset($projects) ? count($projects) : 'No data' }})</h5>
          <div class="dropdown">
            <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
              <i class="bi bi-funnel me-1"></i>Filter
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#">All Projects</a></li>
              <li><a class="dropdown-item" href="#">Draft</a></li>
              <li><a class="dropdown-item" href="#">Planning</a></li>
              <li><a class="dropdown-item" href="#">Active</a></li>
              <li><a class="dropdown-item" href="#">Completed</a></li>
            </ul>
          </div>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Project Code</th>
                  <th>Project Title</th>
                  <th>Category</th>
                  <th>Manager</th>
                  <th>Timeline</th>
                  <th>Budget</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($projects ?? [] as $project)
                <tr>
                  <td><strong>{{ $project->project_code ?? 'PROJ-'.date('Y').'-'.str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</strong></td>
                  <td>{{ $project->project_title ?? 'Sample Project' }}</td>
                  <td>
                    <span class="badge bg-info">{{ $project->department ?? 'IT' }}</span>
                    <small class="d-block text-muted">{{ $project->project_category ?? 'Software Development' }}</small>
                  </td>
                  <td>
                    <small class="text-muted">
                      <i class="bi bi-person me-1"></i>{{ $project->responsible_person ?? 'John Doe' }}
                    </small>
                  </td>
                  <td>
                    <small>{{ $project->start_date ? $project->start_date->format('M d, Y') : 'Jan 15, 2024' }}</small>
                    <small class="d-block text-muted">{{ $project->expected_end_date ? $project->expected_end_date->format('M d, Y') : 'Mar 15, 2024' }}</small>
                  </td>
                  <td>₱{{ number_format($project->estimated_budget ?? 150000, 2) }}</td>
                  <td><span class="badge bg-{{ $project->getStatusColor() ?? 'warning' }}">{{ $project->status ?? 'Draft' }}</span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary view-project-btn" 
                            data-project-id="{{ $project->id ?? 1 }}" 
                            data-project-code="{{ $project->project_code ?? 'PROJ-001' }}"
                            title="View Project Details">
                      <i class="bi bi-eye"></i>
                    </button>
                    @if(($project->status ?? 'Draft') === 'Draft')
                    <button class="btn btn-sm btn-outline-warning edit-project-btn" 
                            data-project-id="{{ $project->id ?? 1 }}"
                            title="Edit Project">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger delete-project-btn" 
                            data-project-id="{{ $project->id ?? 1 }}"
                            data-project-title="{{ $project->project_title ?? 'Sample Project' }}"
                            title="Delete Draft">
                      <i class="bi bi-trash"></i>
                    </button>
                    @elseif(($project->status ?? 'Draft') === 'Planning')
                    <button class="btn btn-sm btn-outline-success start-project-btn" 
                            data-project-id="{{ $project->id ?? 1 }}"
                            data-project-title="{{ $project->project_title ?? 'Sample Project' }}"
                            title="Start Project">
                      <i class="bi bi-play-circle"></i>
                    </button>
                    @elseif(($project->status ?? 'Draft') === 'Active')
                    <button class="btn btn-sm btn-outline-info complete-project-btn" 
                            data-project-id="{{ $project->id ?? 1 }}"
                            data-project-title="{{ $project->project_title ?? 'Sample Project' }}"
                            title="Complete Project">
                      <i class="bi bi-check-circle"></i>
                    </button>
                    @else
                    <button class="btn btn-sm btn-outline-secondary print-project-btn" 
                            data-project-id="{{ $project->id ?? 1 }}"
                            title="Print Project Report">
                      <i class="bi bi-printer"></i>
                    </button>
                    @endif
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="8" class="text-center py-4">
                    <i class="bi bi-inbox text-muted fs-1 mb-2"></i>
                    <h6 class="text-muted">No projects found</h6>
                    <p class="text-muted small mb-0">Create a new project to see it here.</p>
                  </td>
                </tr>
                @endforelse
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
            <button class="btn btn-primary" id="newPlanBtn">
              <i class="bi bi-plus-circle me-2"></i>New Resource Plan
            </button>
            <button class="btn btn-outline-primary" id="duplicatePlanBtn">
              <i class="bi bi-files me-2"></i>Duplicate Plan
            </button>
            <button class="btn btn-outline-primary" id="resourceTemplatesBtn">
              <i class="bi bi-bookmark me-2"></i>Resource Templates
            </button>
            <button class="btn btn-outline-secondary" id="exportPlanBtn">
              <i class="bi bi-download me-2"></i>Export Plan
            </button>
            <button class="btn btn-outline-info" id="resourceAnalysisBtn">
              <i class="bi bi-graph-up me-2"></i>Resource Analysis
            </button>
            <button class="btn btn-outline-warning" id="budgetCalculatorBtn">
              <i class="bi bi-calculator me-2"></i>Budget Calculator
            </button>
          </div>
        </div>
      </div>
      
      <!-- Plan Preview (Hidden by default, shown when viewing plan) -->
      <div class="card shadow-sm border-0 mt-4 d-none" id="planPreviewCard">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Plan Preview</h5>
          <button class="btn btn-sm btn-close" id="closePlanPreview"></button>
        </div>
        <div class="card-body">
          <h6 class="fw-bold">PLAN-2024-014</h6>
          <p class="small text-muted">IT Infrastructure Maintenance | Jan 1 - Mar 31, 2024</p>
          
          <div class="mb-3">
            <small class="text-muted d-block">Plan Type</small>
            <strong>Maintenance Plan</strong>
          </div>
          
          <div class="mb-3">
            <small class="text-muted d-block">Priority</small>
            <span class="badge bg-warning">Medium</span>
          </div>
          
          <div class="mb-3">
            <small class="text-muted d-block">Responsible Person</small>
            <strong>Jane Smith</strong>
          </div>
          
          <div class="mb-3">
            <small class="text-muted d-block">Budget</small>
            <strong>$25,000</strong>
          </div>
          
          <div class="mb-3">
            <small class="text-muted d-block">Status</small>
            <span class="badge bg-warning">Planning</span>
          </div>
          
          <div class="d-grid gap-2 mt-3">
            <button class="btn btn-sm btn-primary">
              <i class="bi bi-eye me-1"></i>View Full Plan
            </button>
            <button class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pencil me-1"></i>Edit Plan
            </button>
          </div>
        </div>
      </div>
      
      <!-- Plan Summary -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Plan Summary</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Active Plans</span>
              <span class="small fw-bold">8</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-primary" style="width: 80%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Completed Plans</span>
              <span class="small fw-bold">12</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: 75%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Draft Plans</span>
              <span class="small fw-bold">5</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-secondary" style="width: 50%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Success Rate</span>
              <span class="small fw-bold">95%</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: 95%"></div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Alerts & Notifications -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Alerts & Notifications</h5>
        </div>
        <div class="card-body">
          <div class="alert alert-warning alert-sm mb-2">
            <i class="bi bi-exclamation-triangle me-2"></i>
            PROJ-2024-008 milestone deadline in 3 days
          </div>
          <div class="alert alert-info alert-sm mb-2">
            <i class="bi bi-calendar-check me-2"></i>
            Asset deployment review scheduled for tomorrow
          </div>
          <div class="alert alert-success alert-sm">
            <i class="bi bi-check-circle me-2"></i>
            IT Infrastructure project successfully completed
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

      <!-- Project Planning JavaScript Functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Skip authentication check - using Laravel session authentication
      console.log('Project page loaded successfully!');

      // Logout functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
          e.preventDefault();
          
          // Create a form and submit it to logout route
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '{{ route('logout') }}';
          
          // Add CSRF token
          const csrfToken = document.querySelector('meta[name="csrf-token"]');
          if (csrfToken) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken.getAttribute('content');
            form.appendChild(csrfInput);
          }
          
          document.body.appendChild(form);
          form.submit();
        });
      }

      // Project Planning Functionality
      initializeProjectPlanning();
      initializeActionButtons();

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
      const currentPath = window.location.pathname;

      // FIX: Remove 'active' from Smart Warehousing on PLT pages
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

      if (warehouseDropdown && warehouseSubmenu) {
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
          
          const activeSubItem = warehouseSubmenu.querySelector('[href="' + currentPath + '"]');
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
      
      // Project Logistics Tracker dropdown active state logic
      const pltDropdown = document.querySelector('[data-bs-target="#pltSubmenu"]');
      const pltSubmenu = document.getElementById('pltSubmenu');

      // Only activate PLT dropdown and sub-link on PLT pages
      if (pltDropdown && pltSubmenu) {
        if (
          currentPath.includes('/plt/toursetup') ||
          currentPath.includes('/plt/execution') ||
          currentPath.includes('/plt/closure')
        ) {
          pltDropdown.classList.add('active');
          pltSubmenu.classList.add('show');
          const activeSubItem = pltSubmenu.querySelector('[href="' + currentPath + '"]');
          if (activeSubItem) {
            activeSubItem.classList.add('active');
          }
        }
      }

      // Simple Plan Functions
      function initializeProjectPlanning() {
      // Form toggle functionality
      const toggleFormBtn = document.getElementById('toggleFormBtn');
      const projectFormBody = document.getElementById('projectFormBody');
      
      if (toggleFormBtn && projectFormBody) {
        toggleFormBtn.addEventListener('click', function() {
          const isVisible = projectFormBody.style.display !== 'none';
          projectFormBody.style.display = isVisible ? 'none' : 'block';
          toggleFormBtn.innerHTML = isVisible ? '<i class="bi bi-chevron-down"></i>' : '<i class="bi bi-chevron-up"></i>';
        });
      }

      // Quick Actions
      const newPlanBtn = document.getElementById('newPlanBtn');
      const duplicatePlanBtn = document.getElementById('duplicatePlanBtn');
      const resourceTemplatesBtn = document.getElementById('resourceTemplatesBtn');
      const resourceAnalysisBtn = document.getElementById('resourceAnalysisBtn');
      const budgetCalculatorBtn = document.getElementById('budgetCalculatorBtn');
      
      if (newPlanBtn) {
        newPlanBtn.addEventListener('click', function() {
          document.getElementById('projectForm').reset();
          projectFormBody.style.display = 'block';
          toggleFormBtn.innerHTML = '<i class="bi bi-chevron-up"></i>';
          document.getElementById('project_title').focus();
        });
      }

      if (duplicatePlanBtn) {
        duplicatePlanBtn.addEventListener('click', function() {
          showModal('Duplicate Resource Plan', 'Select a resource plan to duplicate from the recent plans list.');
        });
      }

      if (resourceTemplatesBtn) {
        resourceTemplatesBtn.addEventListener('click', function() {
          showModal('Resource Plan Templates', 'Choose from pre-designed resource plan templates including HR allocation, material planning, and budget templates.');
        });
      }

      if (resourceAnalysisBtn) {
        resourceAnalysisBtn.addEventListener('click', function() {
          showModal('Resource Analysis', 'Generate detailed resource utilization analysis, budget reports, and allocation efficiency metrics.');
        });
      }

      if (budgetCalculatorBtn) {
        budgetCalculatorBtn.addEventListener('click', function() {
          showModal('Budget Calculator', 'Use the advanced budget calculator to estimate resource costs, contingency planning, and ROI analysis.');
        });
      }

      // Plan form submission
      const projectForm = document.getElementById('projectForm');
      if (projectForm) {
        projectForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          // Determine if this is create or update mode
          const methodInput = projectForm.querySelector('input[name="_method"]');
          const isUpdateMode = methodInput && methodInput.value === 'PUT';
          const actionText = isUpdateMode ? 'Updating' : 'Creating';
          const successText = isUpdateMode ? 'Updated' : 'Created';
          
          // Show loading
          Swal.fire({
            title: actionText + ' Project...',
            text: 'Please wait while we ' + actionText.toLowerCase() + ' your project.',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
              Swal.showLoading();
            }
          });
          
          // Submit form
          const formData = new FormData(projectForm);
          
          fetch(projectForm.action, {
            method: 'POST',
            body: formData,
            headers: {
              'X-Requested-With': 'XMLHttpRequest'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Project ' + successText + '!',
                text: data.message,
                confirmButtonText: 'OK'
              }).then(() => {
                // Reset form to create mode if it was in update mode
                if (isUpdateMode) {
                  projectForm.action = '{{ route('plt.projects.store') }}';
                  projectForm.method = 'POST';
                  
                  // Remove method override
                  if (methodInput) {
                    methodInput.remove();
                  }
                  
                  // Reset submit button text
                  const submitBtn = projectForm.querySelector('button[type="submit"]');
                  if (submitBtn) {
                    submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Create Project';
                  }
                  
                  // Remove cancel button
                  const cancelBtn = projectForm.querySelector('.cancel-edit-btn');
                  if (cancelBtn) {
                    cancelBtn.remove();
                  }
                }
                
                // Reset form and reload page
                projectForm.reset();
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to ' + actionText.toLowerCase() + ' project',
                confirmButtonText: 'OK'
              });
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'An unexpected error occurred. Please try again.',
              confirmButtonText: 'OK'
            });
          });
        });
      }

      // Save draft functionality
      const saveDraftBtn = document.getElementById('saveDraftBtn');
      if (saveDraftBtn) {
        saveDraftBtn.addEventListener('click', function() {
          Swal.fire({
            title: 'Save as Draft?',
            text: 'This will save your current progress as a draft.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Save Draft',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              // Show loading
              Swal.fire({
                title: 'Saving Draft...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                  Swal.showLoading();
                }
              });
              
              // Collect form data
              const formData = new FormData(projectForm);
              
              fetch('{{ route("plt.projects.save-draft") }}', {
                method: 'POST',
                body: formData,
                headers: {
                  'X-Requested-With': 'XMLHttpRequest'
                }
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Draft Saved!',
                    text: data.message,
                    confirmButtonText: 'OK'
                  }).then(() => {
                    window.location.reload();
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to save draft',
                    confirmButtonText: 'OK'
                  });
                }
              })
              .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Failed to save draft. Please try again.',
                  confirmButtonText: 'OK'
                });
              });
            }
          });
        });
      }
    }

      function showModal(title, message) {
      const modal = document.getElementById('staticModal');
      const modalTitle = document.getElementById('staticModalLabel');
      const modalBody = document.getElementById('staticModalBody');
      
      if (modal && modalTitle && modalBody) {
        modalTitle.textContent = title;
        modalBody.innerHTML = '<p>' + message + '</p>';
        
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
      }
    }

      // Action Button Functionality
      function initializeActionButtons() {
        console.log('Initializing action buttons...');
      
      // View Project
      document.addEventListener('click', function(e) {
        console.log('Click detected:', e.target);
        if (e.target.closest('.view-project-btn')) {
          console.log('View button clicked');
          const btn = e.target.closest('.view-project-btn');
          const projectId = btn.dataset.projectId;
          const projectCode = btn.dataset.projectCode;
          
          // Validate project ID
          if (!projectId || projectId === 'undefined') {
            console.error('Invalid project ID:', projectId);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Invalid project ID. Please refresh the page and try again.',
              confirmButtonText: 'OK'
            });
            return;
          }
          
          Swal.fire({
            title: 'Loading Project...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
              Swal.showLoading();
            }
          });
          
          fetch('{{ url('/plt/projects') }}/' + projectId, {
            method: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': getCsrfToken()
            }
          })
          .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
              throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            return response.json();
          })
          .then(data => {
            console.log('Response data:', data);
            if (data.success && data.project) {
              const project = data.project;
              
              // Safe data access with fallbacks
              const projectCode = project.project_code || 'N/A';
              const projectTitle = project.project_title || 'Untitled';
              const projectDescription = project.project_description || 'No description available';
              const startDate = project.start_date ? new Date(project.start_date).toLocaleDateString() : 'Not set';
              const endDate = project.expected_end_date ? new Date(project.expected_end_date).toLocaleDateString() : 'Not set';
              const budget = project.estimated_budget ? parseFloat(project.estimated_budget).toLocaleString() : '0';
              const status = project.status || 'Unknown';
              const createdBy = (project.created_by && project.created_by.name) ? project.created_by.name : 'N/A';
              const notes = project.notes || '';
              
              Swal.fire({
                title: 'Project: ' + projectCode,
                html: '<div class="text-start">' +
                      '<p><strong>Title:</strong> ' + projectTitle + '</p>' +
                      '<p><strong>Description:</strong> ' + projectDescription + '</p>' +
                      '<p><strong>Timeline:</strong> ' + startDate + ' - ' + endDate + '</p>' +
                      '<p><strong>Budget:</strong> ₱' + budget + '</p>' +
                      '<p><strong>Status:</strong> <span class="badge bg-' + getStatusColor(status) + '">' + status + '</span></p>' +
                      '<p><strong>Created By:</strong> ' + createdBy + '</p>' +
                      (notes ? '<p><strong>Notes:</strong> ' + notes + '</p>' : '') +
                      '</div>',
                width: 600,
                confirmButtonText: 'Close'
              });
            } else {
              console.error('Invalid response data:', data);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to load project details - invalid response format',
                confirmButtonText: 'OK'
              });
            }
          })
          .catch(error => {
            console.error('Fetch error:', error);
            let errorMessage = 'Failed to load project details';
            
            if (error.message.includes('HTTP 404')) {
              errorMessage = 'Project not found. It may have been deleted.';
            } else if (error.message.includes('HTTP 403')) {
              errorMessage = 'You do not have permission to view this project.';
            } else if (error.message.includes('HTTP 500')) {
              errorMessage = 'Server error occurred. Please try again later.';
            } else if (error.name === 'TypeError') {
              errorMessage = 'Network error. Please check your connection.';
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: errorMessage,
              confirmButtonText: 'OK'
            });
          });
        }
      });

      // Edit Project
      document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-project-btn')) {
          console.log('Edit button clicked - handler triggered!');
          const editBtn = e.target.closest('.edit-project-btn');
          const projectId = editBtn.dataset.projectId;
          console.log('Project ID from button:', projectId);
          
          // Validate project ID
          if (!projectId || projectId === 'undefined') {
            console.error('Invalid project ID:', projectId);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Invalid project ID. Please refresh the page and try again.',
              confirmButtonText: 'OK'
            });
            return;
          }
          
          // Show loading
          Swal.fire({
            title: 'Loading Project...',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
              Swal.showLoading();
            }
          });
          
          // Fetch project data for editing
          fetch('{{ url('/plt/projects') }}/' + projectId, {
            method: 'GET',
            headers: {
              'X-Requested-With': 'XMLHttpRequest',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': getCsrfToken()
            }
          })
          .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
              throw new Error('HTTP ' + response.status + ': ' + response.statusText);
            }
            return response.json();
          })
          .then(data => {
            console.log('Response data:', data);
            if (data.success && data.project) {
              const project = data.project;
              
              // Pre-populate form with project data
              document.getElementById('project_title').value = project.project_title || '';
              document.getElementById('project_description').value = project.project_description || '';
              document.getElementById('start_date').value = project.start_date ? project.start_date.split('T')[0] : '';
              document.getElementById('expected_end_date').value = project.expected_end_date ? project.expected_end_date.split('T')[0] : '';
              document.getElementById('estimated_budget').value = project.estimated_budget || '';
              
              // Show the form
              const projectFormBody = document.getElementById('projectFormBody');
              const toggleFormBtn = document.getElementById('toggleFormBtn');
              if (projectFormBody && toggleFormBtn) {
                projectFormBody.style.display = 'block';
                toggleFormBtn.innerHTML = '<i class="bi bi-chevron-up"></i>';
              }
              
              // Change form to update mode
              const projectForm = document.getElementById('projectForm');
              if (projectForm) {
                projectForm.action = '{{ url('/plt/projects') }}/' + projectId;
                projectForm.method = 'POST';
                
                // Add method override for PUT
                let methodInput = projectForm.querySelector('input[name="_method"]');
                if (!methodInput) {
                  methodInput = document.createElement('input');
                  methodInput.type = 'hidden';
                  methodInput.name = '_method';
                  projectForm.appendChild(methodInput);
                }
                methodInput.value = 'PUT';
                
                // Update submit button text
                const submitBtn = projectForm.querySelector('button[type="submit"]');
                if (submitBtn) {
                  submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Update Project';
                }
                
                // Add cancel edit button
                let cancelBtn = projectForm.querySelector('.cancel-edit-btn');
                if (!cancelBtn) {
                  cancelBtn = document.createElement('button');
                  cancelBtn.type = 'button';
                  cancelBtn.className = 'btn btn-outline-secondary cancel-edit-btn me-2';
                  cancelBtn.innerHTML = '<i class="bi bi-x-circle me-1"></i>Cancel Edit';
                  
                  const submitBtn = projectForm.querySelector('button[type="submit"]');
                  if (submitBtn && submitBtn.parentNode) {
                    submitBtn.parentNode.insertBefore(cancelBtn, submitBtn);
                  }
                  
                  // Cancel edit functionality
                  cancelBtn.addEventListener('click', function() {
                    // Reset form to create mode
                    projectForm.action = '{{ route('plt.projects.store') }}';
                    projectForm.method = 'POST';
                    
                    // Remove method override
                    const methodInput = projectForm.querySelector('input[name="_method"]');
                    if (methodInput) {
                      methodInput.remove();
                    }
                    
                    // Reset form
                    projectForm.reset();
                    
                    // Reset submit button text
                    const submitBtn = projectForm.querySelector('button[type="submit"]');
                    if (submitBtn) {
                      submitBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Create Project';
                    }
                    
                    // Remove cancel button
                    cancelBtn.remove();
                  });
                }
              }
              
              // Close loading and scroll to form
              Swal.close();
              document.getElementById('project_title').focus();
              document.getElementById('project_title').scrollIntoView({ behavior: 'smooth', block: 'center' });
              
            } else {
              console.error('Invalid response data:', data);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to load project details for editing',
                confirmButtonText: 'OK'
              });
            }
          })
          .catch(error => {
            console.error('Fetch error:', error);
            let errorMessage = 'Failed to load project for editing';
            
            if (error.message.includes('HTTP 404')) {
              errorMessage = 'Project not found. It may have been deleted.';
            } else if (error.message.includes('HTTP 403')) {
              errorMessage = 'You do not have permission to edit this project.';
            } else if (error.message.includes('HTTP 500')) {
              errorMessage = 'Server error occurred. Please try again later.';
            } else if (error.name === 'TypeError') {
              errorMessage = 'Network error. Please check your connection.';
            }
            
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: errorMessage,
              confirmButtonText: 'OK'
            });
          });
        }
      });

      // Delete Project
      document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-project-btn')) {
          console.log('Delete button clicked');
          const btn = e.target.closest('.delete-project-btn');
          const projectId = btn.dataset.projectId;
          const projectTitle = btn.dataset.projectTitle;
          
          Swal.fire({
            title: 'Delete Project?',
            text: 'Are you sure you want to delete "' + projectTitle + '"? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              // Show loading
              Swal.fire({
                title: 'Deleting Project...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                  Swal.showLoading();
                }
              });
              
              fetch('{{ url('/plt/projects') }}/' + projectId, {
                method: 'DELETE',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': getCsrfToken()
                }
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: data.message,
                    confirmButtonText: 'OK'
                  }).then(() => {
                    window.location.reload();
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to delete project',
                    confirmButtonText: 'OK'
                  });
                }
              })
              .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Failed to delete project',
                  confirmButtonText: 'OK'
                });
              });
            }
          });
        }
      });

      // Start Project
      document.addEventListener('click', function(e) {
        if (e.target.closest('.start-project-btn')) {
          const btn = e.target.closest('.start-project-btn');
          const projectId = btn.dataset.projectId;
          const projectTitle = btn.dataset.projectTitle;
          
          Swal.fire({
            title: 'Start Project?',
            text: 'Are you sure you want to start "' + projectTitle + '"?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, start it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              fetch('{{ url('/plt/projects') }}/' + projectId + '/start', {
                method: 'POST',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': getCsrfToken()
                }
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Project Started!',
                    text: data.message,
                    confirmButtonText: 'OK'
                  }).then(() => {
                    window.location.reload();
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to start project',
                    confirmButtonText: 'OK'
                  });
                }
              })
              .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Failed to start project',
                  confirmButtonText: 'OK'
                });
              });
            }
          });
        }
      });

      // Complete Project
      document.addEventListener('click', function(e) {
        if (e.target.closest('.complete-project-btn')) {
          const btn = e.target.closest('.complete-project-btn');
          const projectId = btn.dataset.projectId;
          const projectTitle = btn.dataset.projectTitle;
          
          Swal.fire({
            title: 'Complete Project?',
            text: 'Are you sure you want to mark "' + projectTitle + '" as completed?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, complete it!',
            cancelButtonText: 'Cancel'
          }).then((result) => {
            if (result.isConfirmed) {
              fetch('{{ url('/plt/projects') }}/' + projectId + '/complete', {
                method: 'POST',
                headers: {
                  'X-Requested-With': 'XMLHttpRequest',
                  'X-CSRF-TOKEN': getCsrfToken()
                }
              })
              .then(response => response.json())
              .then(data => {
                if (data.success) {
                  Swal.fire({
                    icon: 'success',
                    title: 'Project Completed!',
                    text: data.message,
                    confirmButtonText: 'OK'
                  }).then(() => {
                    window.location.reload();
                  });
                } else {
                  Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message || 'Failed to complete project',
                    confirmButtonText: 'OK'
                  });
                }
              })
              .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Failed to complete project',
                  confirmButtonText: 'OK'
                });
              });
            }
          });
        }
      });
      
      } // Close initializeActionButtons function

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

      // Initialize project planning functionality
      initializeProjectPlanning();
      
      // CSRF token helper function
      function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!token) {
          console.warn('CSRF token not found. This may cause authentication issues.');
        }
        
        return token;
      }
      
      // Test function to verify JavaScript syntax
      console.log('Project management JavaScript loaded successfully!');
      
      // Debug: Test if buttons exist
      console.log('Form toggle button:', document.getElementById('toggleFormBtn'));
      console.log('Save draft button:', document.getElementById('saveDraftBtn'));
      console.log('Add phase button:', document.getElementById('addPhase'));
      console.log('Project form:', document.getElementById('projectForm'));
      

      // Initialize action buttons
      initializeActionButtons();
      
      // Phase Management
      let phaseCounter = 1;
      
      // Add Phase functionality
      const addPhaseBtn = document.getElementById('addPhase');
      if (addPhaseBtn) {
        addPhaseBtn.addEventListener('click', function() {
          const tbody = document.querySelector('#phasesTable tbody');
          const newRow = document.createElement('tr');
          
          newRow.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="phases[${phaseCounter}][name]" placeholder="Phase name (e.g., Planning, Design, Development)"></td>
            <td><input type="date" class="form-control form-control-sm" name="phases[${phaseCounter}][start_time]"></td>
            <td><input type="number" class="form-control form-control-sm" name="phases[${phaseCounter}][duration]" min="1" step="1" placeholder="7"></td>
            <td><input type="text" class="form-control form-control-sm" name="phases[${phaseCounter}][team]" placeholder="Team/Person responsible"></td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-danger remove-phase">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          `;
          
          tbody.appendChild(newRow);
          phaseCounter++;
        });
      }
      
      // Remove Phase functionality
      document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-phase')) {
          const row = e.target.closest('tr');
          const tbody = row.parentNode;
          
          // Don't allow removing the last row
          if (tbody.children.length > 1) {
            row.remove();
          } else {
            Swal.fire({
              icon: 'warning',
              title: 'Cannot Remove',
              text: 'At least one phase is required.',
              confirmButtonText: 'OK'
            });
          }
        }
      });
      
      // Form toggle functionality
      const toggleFormBtn = document.getElementById('toggleFormBtn');
      const projectFormBody = document.getElementById('projectFormBody');
      
      if (toggleFormBtn && projectFormBody) {
        toggleFormBtn.addEventListener('click', function() {
          const icon = toggleFormBtn.querySelector('i');
          if (projectFormBody.style.display === 'none') {
            projectFormBody.style.display = 'block';
            icon.className = 'bi bi-chevron-up';
          } else {
            projectFormBody.style.display = 'none';
            icon.className = 'bi bi-chevron-down';
          }
        });
      }
      
      // Budget calculation
      function calculateTotalBudget() {
        const laborCost = parseFloat(document.getElementById('labor_cost')?.value) || 0;
        const materialCost = parseFloat(document.getElementById('material_cost')?.value) || 0;
        const equipmentCost = parseFloat(document.getElementById('equipment_cost')?.value) || 0;
        const contingencyBudget = parseFloat(document.getElementById('contingency_budget')?.value) || 0;
        
        const calculatedTotal = laborCost + materialCost + equipmentCost + contingencyBudget;
        
        const totalBudgetField = document.getElementById('estimated_budget');
        if (totalBudgetField && (!totalBudgetField.value || confirm('Update total budget with calculated amount?'))) {
          totalBudgetField.value = calculatedTotal.toFixed(2);
        }
      }
      
      // Add budget calculation listeners
      ['labor_cost', 'material_cost', 'equipment_cost', 'contingency_budget'].forEach(id => {
        const field = document.getElementById(id);
        if (field) {
          field.addEventListener('blur', calculateTotalBudget);
        }
      });
      
      // Milestone management
      let milestoneCounter = 1;
      
      // Add Milestone functionality
      const addMilestoneBtn = document.getElementById('addMilestone');
      if (addMilestoneBtn) {
        addMilestoneBtn.addEventListener('click', function() {
          const tbody = document.querySelector('#milestonesTable tbody');
          const newRow = document.createElement('tr');
          
          newRow.innerHTML = `
            <td><input type="text" class="form-control form-control-sm" name="milestones[${milestoneCounter}][name]" placeholder="Milestone name"></td>
            <td><input type="date" class="form-control form-control-sm" name="milestones[${milestoneCounter}][date]"></td>
            <td><input type="text" class="form-control form-control-sm" name="milestones[${milestoneCounter}][description]" placeholder="Brief description"></td>
            <td><input type="text" class="form-control form-control-sm" name="milestones[${milestoneCounter}][deliverable]" placeholder="Expected deliverable"></td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-danger remove-milestone">
                <i class="bi bi-trash"></i>
              </button>
            </td>
          `;
          
          tbody.appendChild(newRow);
          milestoneCounter++;
        });
      }
      
      // Remove milestone functionality
      document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-milestone')) {
          const row = e.target.closest('tr');
          const tbody = row.parentNode;
          if (tbody.children.length > 1) {
            row.remove();
          } else {
            Swal.fire({
              icon: 'warning',
              title: 'Cannot Remove',
              text: 'At least one milestone is required.',
              confirmButtonText: 'OK'
            });
          }
        }
      });
      
    }); // Close DOMContentLoaded
  </script>
</body>
</html>
