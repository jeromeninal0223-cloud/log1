<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Procurement & Sourcing Management - Purchase Request</title>

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
      <li class="nav-item">
        <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#procurementSubmenu" aria-expanded="true" aria-controls="procurementSubmenu">
          <i class="bi bi-cart-plus me-2"></i> Procurement & Sourcing
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse show" id="procurementSubmenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a href="{{ url('/psm/request') }}" class="nav-link text-dark small active">
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
            <i class="bi bi-cart-plus fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">Purchase Request Management</h2>
            <p class="text-muted mb-0">Welcome back, John! Manage your purchase requests and procurement processes.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/psm') }}" class="text-decoration-none">Procurement & Sourcing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Request</li>
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
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $pendingRequests->count() }}</h3>
                <p class="text-muted mb-0 small">Pending Requests</p>
                <small class="text-info"><i class="bi bi-info-circle"></i> From stock replenishment</small>
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
                <h3 class="fw-bold mb-0">{{ $approvedRequests->count() }}</h3>
                <p class="text-muted mb-0 small">Approved Requests</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> Ready for bidding</small>
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
                <h3 class="fw-bold mb-0">{{ $purchaseRequests->where('priority', 'urgent')->count() }}</h3>
                <p class="text-muted mb-0 small">Urgent Requests</p>
                <small class="text-warning"><i class="bi bi-arrow-up"></i> Critical stock</small>
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
                <h3 class="fw-bold mb-0">${{ number_format($purchaseRequests->sum('total_estimated_cost'), 0) }}</h3>
                <p class="text-muted mb-0 small">Total Est. Cost</p>
                <small class="text-success"><i class="bi bi-arrow-down"></i> -45min</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Requisition Processing Features -->
    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Requisition Form Creation -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Create New Requisition</h5>
            <button class="btn btn-sm btn-primary">
              <i class="bi bi-plus-circle me-2"></i>New Requisition
            </button>
          </div>
          <div class="card-body">
            <form>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Item Description</label>
                  <input type="text" class="form-control" placeholder="e.g., Vehicle brake pads, Office supplies">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Category</label>
                  <select class="form-select">
                    <option>Vehicle Parts</option>
                    <option>Office Supplies</option>
                    <option>Tour Materials</option>
                    <option>IT Equipment</option>
                    <option>Maintenance Tools</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Quantity</label>
                  <input type="number" class="form-control" placeholder="0">
                </div>
                <div class="col-md-4">
                  <label class="form-label">Unit</label>
                  <select class="form-select">
                    <option>Pieces</option>
                    <option>Boxes</option>
                    <option>Sets</option>
                    <option>Liters</option>
                    <option>Meters</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Estimated Cost</label>
                  <input type="number" class="form-control" placeholder="$0.00">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Required Date</label>
                  <input type="date" class="form-control">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Priority</label>
                  <select class="form-select">
                    <option>Low</option>
                    <option>Medium</option>
                    <option>High</option>
                    <option>Urgent</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Justification</label>
                  <textarea class="form-control" rows="3" placeholder="Explain why this item is needed..."></textarea>
                </div>
                <div class="col-12">
                  <label class="form-label">Attachments</label>
                  <input type="file" class="form-control" multiple>
                  <small class="text-muted">Upload specs, supplier references, or requirement documents</small>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">
                    <i class="bi bi-send me-2"></i>Submit Requisition
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>

        <!-- Purchase Requests from Stock Replenishment -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Purchase Requests from Stock Replenishment</h5>
            <a href="{{ url('/stock-replenishment') }}" class="btn btn-sm btn-outline-primary">
              <i class="bi bi-arrow-left"></i> Back to Stock
            </a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Request #</th>
                    <th>Stock Item</th>
                    <th>Quantity</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Est. Cost</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($purchaseRequests as $request)
                  <tr>
                    <td><strong>{{ $request->request_number }}</strong></td>
                    <td>
                      <div>
                        <strong>{{ $request->stockItem->name ?? 'N/A' }}</strong>
                        <br><small class="text-muted">{{ $request->stockItem->category ?? '' }}</small>
                      </div>
                    </td>
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
                        <button class="btn btn-sm btn-success approve-request-btn" data-request-id="{{ $request->id }}">
                          <i class="bi bi-check-circle"></i> Approve
                        </button>
                      @elseif($request->status === 'approved')
                        <button class="btn btn-sm btn-primary create-bid-form-btn" data-request-id="{{ $request->id }}">
                          <i class="bi bi-file-earmark-plus"></i> Create Bid Form
                        </button>
                      @elseif($request->status === 'in_bidding')
                        <a href="{{ route('psm.bidding') }}?opportunity={{ $request->opportunity_id }}" class="btn btn-sm btn-info">
                          <i class="bi bi-eye"></i> View Bidding
                        </a>
                      @endif
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted">
                      <div class="py-4">
                        <i class="bi bi-inbox fs-1 text-muted"></i>
                        <p class="mt-2">No purchase requests found</p>
                        <a href="{{ url('/stock-replenishment') }}" class="btn btn-primary">
                          <i class="bi bi-plus-circle"></i> Create from Stock Replenishment
                        </a>
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Approval Workflow Chart -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Approval Workflow</h5>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-md-3">
                <div class="border rounded p-3 mb-3">
                  <i class="bi bi-person-plus fs-1 text-primary"></i>
                  <h6 class="mt-2">Submit</h6>
                  <small class="text-muted">Staff creates requisition</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="border rounded p-3 mb-3">
                  <i class="bi bi-building fs-1 text-warning"></i>
                  <h6 class="mt-2">Department Head</h6>
                  <small class="text-muted">Initial approval</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="border rounded p-3 mb-3">
                  <i class="bi bi-cart-check fs-1 text-info"></i>
                  <h6 class="mt-2">Procurement</h6>
                  <small class="text-muted">Technical review</small>
                </div>
              </div>
              <div class="col-md-3">
                <div class="border rounded p-3 mb-3">
                  <i class="bi bi-currency-dollar fs-1 text-success"></i>
                  <h6 class="mt-2">Finance</h6>
                  <small class="text-muted">Budget approval</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <!-- Role-Based Access & Quick Actions -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Requisition
              </button>
              <button class="btn btn-outline-primary">
                <i class="bi bi-eye me-2"></i>View My Requests
              </button>
              <button class="btn btn-outline-primary">
                <i class="bi bi-check-circle me-2"></i>Approve Requests
              </button>
              <button class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-text me-2"></i>Generate Report
              </button>
            </div>
          </div>
        </div>

        <!-- Budget Checking -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Budget Status</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Department Budget</span>
                <span class="small text-muted">$45,000</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: 65%"></div>
              </div>
              <small class="text-muted">$29,250 used (65%)</small>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Vehicle Parts</span>
                <span class="small text-muted">$12,500</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: 78%"></div>
              </div>
              <small class="text-muted">$9,750 used (78%)</small>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Office Supplies</span>
                <span class="small text-muted">$8,000</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 45%"></div>
              </div>
              <small class="text-muted">$3,600 used (45%)</small>
            </div>
            <div>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Tour Materials</span>
                <span class="small text-muted">$6,000</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-danger" style="width: 92%"></div>
              </div>
              <small class="text-muted">$5,520 used (92%)</small>
            </div>
          </div>
        </div>

        <!-- Tracking & Notifications -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Recent Notifications</h5>
          </div>
          <div class="card-body">
            <div class="alert alert-success alert-sm mb-2">
              <i class="bi bi-check-circle me-2"></i>
              Requisition #REQ-2024-001 approved by Department Head
            </div>
            <div class="alert alert-info alert-sm mb-2">
              <i class="bi bi-arrow-right me-2"></i>
              Requisition #REQ-2024-002 moved to Procurement review
            </div>
            <div class="alert alert-warning alert-sm mb-2">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Budget limit reached for Tour Materials category
            </div>
            <div class="alert alert-primary alert-sm">
              <i class="bi bi-bell me-2"></i>
              New urgent request from Maintenance team
            </div>
          </div>
        </div>

        <!-- Audit Trail Summary -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Recent Activity</h5>
          </div>
          <div class="card-body">
            <div class="timeline">
              <div class="timeline-item mb-3">
                <small class="text-muted">2 hours ago</small>
                <p class="mb-1 small">John Driver submitted vehicle parts request</p>
              </div>
              <div class="timeline-item mb-3">
                <small class="text-muted">4 hours ago</small>
                <p class="mb-1 small">Sarah Admin approved office supplies</p>
              </div>
              <div class="timeline-item mb-3">
                <small class="text-muted">6 hours ago</small>
                <p class="mb-1 small">Finance rejected tour materials (budget exceeded)</p>
              </div>
              <div class="timeline-item">
                <small class="text-muted">1 day ago</small>
                <p class="mb-1 small">Procurement converted approved request to RFQ</p>
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

  // Purchase Request Workflow JavaScript
  document.addEventListener('DOMContentLoaded', function() {
    // Set up CSRF token for AJAX requests
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Approve Request functionality
    document.querySelectorAll('.approve-request-btn').forEach(button => {
      button.addEventListener('click', function() {
        const requestId = this.dataset.requestId;
        
        if (confirm('Are you sure you want to approve this purchase request?')) {
          fetch('/psm/request/approve', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ request_id: requestId })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              alert('Purchase request approved successfully!');
              location.reload();
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while approving the request.');
          });
        }
      });
    });

    // Create Bid Form functionality
    document.querySelectorAll('.create-bid-form-btn').forEach(button => {
      button.addEventListener('click', function() {
        const requestId = this.dataset.requestId;
        
        // Fetch bid form data and show modal
        fetch(`/psm/request/${requestId}/bid-data`)
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showBidFormModal(data.request);
            } else {
              alert('Error: ' + data.message);
            }
          })
          .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading bid form data.');
          });
      });
    });

    // Show Bid Form Modal
    function showBidFormModal(requestData) {
      // Create modal HTML
      const modalHtml = `
        <div class="modal fade" id="bidFormModal" tabindex="-1" aria-labelledby="bidFormModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="bidFormModalLabel">Create Bid Form - ${requestData.request_number}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form id="bidForm">
                  <input type="hidden" name="request_id" value="${requestData.id}">
                  
                  <div class="row g-3">
                    <div class="col-12">
                      <div class="alert alert-info">
                        <strong>Stock Item:</strong> ${requestData.stock_item.name}<br>
                        <strong>Quantity Needed:</strong> ${requestData.quantity_requested} ${requestData.stock_item.unit_of_measure}<br>
                        <strong>Estimated Cost:</strong> $${parseFloat(requestData.total_estimated_cost).toFixed(2)}
                      </div>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label">Bid Title</label>
                      <input type="text" class="form-control" name="bid_title" value="${requestData.title}" required>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label">Bid Category</label>
                      <select class="form-select" name="bid_category" required>
                        <option value="supplies">Office Supplies</option>
                        <option value="equipment">Equipment</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="services">Services</option>
                        <option value="other">Other</option>
                      </select>
                    </div>
                    
                    <div class="col-12">
                      <label class="form-label">Bid Description</label>
                      <textarea class="form-control" name="bid_description" rows="4" required>${requestData.description}</textarea>
                    </div>
                    
                    <div class="col-md-4">
                      <label class="form-label">Quantity</label>
                      <input type="number" class="form-control" name="quantity" value="${requestData.quantity_requested}" required>
                    </div>
                    
                    <div class="col-md-4">
                      <label class="form-label">Unit of Measure</label>
                      <input type="text" class="form-control" name="unit_of_measure" value="${requestData.stock_item.unit_of_measure}" required>
                    </div>
                    
                    <div class="col-md-4">
                      <label class="form-label">Budget Range</label>
                      <input type="number" class="form-control" name="budget_range" value="${requestData.total_estimated_cost}" step="0.01" required>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label">Bid Opening Date</label>
                      <input type="datetime-local" class="form-control" name="bid_opening_date" required>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label">Bid Closing Date</label>
                      <input type="datetime-local" class="form-control" name="bid_closing_date" required>
                    </div>
                    
                    <div class="col-12">
                      <label class="form-label">Special Requirements</label>
                      <textarea class="form-control" name="special_requirements" rows="3" placeholder="Any specific requirements, certifications, or conditions..."></textarea>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label">Delivery Location</label>
                      <input type="text" class="form-control" name="delivery_location" placeholder="Warehouse, Office, etc." required>
                    </div>
                    
                    <div class="col-md-6">
                      <label class="form-label">Contact Person</label>
                      <input type="text" class="form-control" name="contact_person" placeholder="Name of contact person" required>
                    </div>
                  </div>
                </form>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitBidForm">
                  <i class="bi bi-send"></i> Submit to Bidding
                </button>
              </div>
            </div>
          </div>
        </div>
      `;
      
      // Remove existing modal if any
      const existingModal = document.getElementById('bidFormModal');
      if (existingModal) {
        existingModal.remove();
      }
      
      // Add modal to body
      document.body.insertAdjacentHTML('beforeend', modalHtml);
      
      // Set default dates (opening: now, closing: 7 days from now)
      const now = new Date();
      const openingDate = new Date(now.getTime() + 24 * 60 * 60 * 1000); // Tomorrow
      const closingDate = new Date(now.getTime() + 7 * 24 * 60 * 60 * 1000); // 7 days from now
      
      document.querySelector('input[name="bid_opening_date"]').value = openingDate.toISOString().slice(0, 16);
      document.querySelector('input[name="bid_closing_date"]').value = closingDate.toISOString().slice(0, 16);
      
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('bidFormModal'));
      modal.show();
      
      // Handle form submission
      document.getElementById('submitBidForm').addEventListener('click', function() {
        const formData = new FormData(document.getElementById('bidForm'));
        const data = Object.fromEntries(formData.entries());
        
        fetch('/psm/request/submit-bid', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Bid form submitted successfully and posted to bidding landing!');
            modal.hide();
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while submitting the bid form.');
        });
      });
    }
  });
</script>

</body>
</html>
