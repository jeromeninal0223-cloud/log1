<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Procurement & Sourcing Management - Bidding & RFQ</title>

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

    <!-- Flash Messages -->
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
    @if ($errors->any())
      <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>Please fix the following:
        <ul class="mb-0 mt-1">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif
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
              <a href="{{ url('/psm/vendor') }}" class="nav-link text-dark small">
                <i class="bi bi-building me-2"></i> Vendor Management
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/bidding') }}" class="nav-link text-dark small active">
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
            <h2 class="fw-bold mb-1">Bidding & RFQ Management</h2>
            <p class="text-muted mb-0">Welcome back, Sarah! Manage bidding processes and RFQ submissions.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/psm') }}" class="text-decoration-none">Procurement & Sourcing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Bidding & RFQ</li>
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
                <h3 class="fw-bold mb-0">{{ $stats['active_rfqs'] }}</h3>
                <p class="text-muted mb-0 small">Active RFQs</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +2 today</small>
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
                <h3 class="fw-bold mb-0">{{ $stats['pending_evaluation'] }}</h3>
                <p class="text-muted mb-0 small">Pending Evaluation</p>
                <small class="text-warning"><i class="bi bi-arrow-up"></i> +3</small>
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
                <i class="bi bi-trophy"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['bids_won'] }}</h3>
                <p class="text-muted mb-0 small">Bids Won</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +5 this week</small>
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
                <h3 class="fw-bold mb-0">${{ number_format($stats['total_value'], 0) }}</h3>
                <p class="text-muted mb-0 small">Total Value</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +15%</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bidding Overview & Quick Actions -->
    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Create New Opportunity (RFQ) -->
        <div class="card shadow-sm border-0 mb-4">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Create New Bidding Opportunity</h5>
          </div>
          <div class="card-body">
            <form method="POST" action="{{ route('psm.opportunities.store') }}">
              @csrf
              <div class="row g-3">
                <div class="col-md-8">
                  <label class="form-label">Title<span class="text-danger">*</span></label>
                  <input type="text" name="title" class="form-control" placeholder="e.g., Logistics Services for Metro Manila" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label">Category</label>
                  <input type="text" name="category" class="form-control" placeholder="e.g., Logistics & Transportation">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Start Date</label>
                  <input type="date" name="start_date" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">End Date</label>
                  <input type="date" name="end_date" class="form-control">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Budget</label>
                  <input type="number" step="0.01" min="0" name="budget" class="form-control" placeholder="0.00">
                </div>
                <div class="col-md-3">
                  <label class="form-label">Status<span class="text-danger">*</span></label>
                  <select name="current_status" class="form-select" required>
                    <option value="Open" selected>Open</option>
                    <option value="Ended">Ended</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Description</label>
                  <textarea name="description" class="form-control" rows="3" placeholder="Short description or scope"></textarea>
                </div>
              </div>
              <div class="mt-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-cloud-upload me-2"></i>Publish Opportunity
                </button>
              </div>
            </form>
          </div>
        </div>
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Submitted Bids for Evaluation</h5>
            <div class="d-flex gap-2">
              <select class="form-select form-select-sm" style="width: auto;">
                <option value="">All RFQs</option>
                <option value="pending">Pending Evaluation</option>
                <option value="under_review">Under Review</option>
                <option value="completed">Completed</option>
              </select>
              <button class="btn btn-sm btn-outline-primary">View All</button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Bid ID</th>
                    <th>RFQ Title</th>
                    <th>Vendor</th>
                    <th>Bid Amount</th>
                    <th>Submitted</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="bidsTableBody">
                  @forelse($bids as $bid)
                  <tr data-bid-id="{{ $bid->id }}">
                    <td><strong>#{{ str_pad((string) $bid->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $bid->title ?? ('Bid for Opportunity #' . ($bid->opportunity_id ?? '')) }}</td>
                    <td>{{ optional($bid->vendor)->company_name ?? optional($bid->vendor)->name ?? '—' }}</td>
                    <td><strong>₱{{ number_format($bid->amount, 2) }}</strong></td>
                    <td>{{ optional($bid->submitted_at)->diffForHumans() ?? '—' }}</td>
                    <td>
                      @php
                        $status = $bid->status ?? 'Under Review';
                        $badge = match ($status) {
                          'Won' => 'bg-success',
                          'Rejected' => 'bg-danger',
                          'Under Review' => 'bg-warning',
                          'Pending Evaluation' => 'bg-info',
                          default => 'bg-secondary',
                        };
                      @endphp
                      <span class="badge {{ $badge }}">{{ $status }}</span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary btn-view-bid" data-bid-id="{{ $bid->id }}" title="View Details">
                          <i class="bi bi-eye"></i>
                        </button>
                        @if(in_array($status, ['Under Review','Pending Evaluation']))
                          <button type="button" class="btn btn-outline-success btn-select-winner" data-bid-id="{{ $bid->id }}" title="Select as Winner">
                            <i class="bi bi-trophy"></i>
                          </button>
                          <button type="button" class="btn btn-outline-danger btn-reject-bid" data-bid-id="{{ $bid->id }}" title="Reject Bid">
                            <i class="bi bi-x-circle"></i>
                          </button>
                        @elseif($status === 'Won')
                          <button type="button" class="btn btn-outline-info btn-generate-contract" data-bid-id="{{ $bid->id }}" title="Generate Contract">
                            <i class="bi bi-file-earmark-check"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                      <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                      No bids submitted yet
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Bid Comparison Chart -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Bid Comparison Analysis</h5>
          </div>
          <div class="card-body">
            <canvas id="bidComparisonChart" height="100"></canvas>
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
              <button class="btn btn-primary" onclick="createNewRFQ()">
                <i class="bi bi-plus-circle me-2"></i>Create New RFQ
              </button>
              <button class="btn btn-outline-primary" onclick="bulkEvaluate()">
                <i class="bi bi-check2-all me-2"></i>Bulk Evaluate
              </button>
              <button class="btn btn-outline-primary" onclick="exportBids()">
                <i class="bi bi-download me-2"></i>Export Bids
              </button>
              <button class="btn btn-outline-secondary" onclick="generateBidReport()">
                <i class="bi bi-file-earmark-text me-2"></i>Generate Report
              </button>
            </div>
          </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Evaluation Progress</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Office Equipment RFQ</span>
                <span class="small text-muted">2/3 Evaluated</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: 67%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">IT Infrastructure RFQ</span>
                <span class="small text-muted">0/4 Evaluated</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-danger" style="width: 0%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Catering Services RFQ</span>
                <span class="small text-muted">Completed</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: 100%"></div>
              </div>
            </div>
            <div>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Marketing Materials RFQ</span>
                <span class="small text-muted">1/2 Evaluated</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 50%"></div>
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
              RFQ deadline approaching: IT Infrastructure (2 days)
            </div>
            <div class="alert alert-warning alert-sm mb-2">
              <i class="bi bi-clock me-2"></i>
              8 bids pending evaluation for over 24 hours
            </div>
            <div class="alert alert-info alert-sm">
              <i class="bi bi-info-circle me-2"></i>
              New bid submitted: Office Equipment RFQ
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bid Details Modal -->
  <div class="modal fade" id="bidDetailsModal" tabindex="-1" aria-labelledby="bidDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="bidDetailsModalLabel">Bid Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <h6 class="fw-bold">Bid Information</h6>
              <p><strong>Bid ID:</strong> <span id="modalBidId"></span></p>
              <p><strong>RFQ Title:</strong> <span id="modalRfqTitle"></span></p>
              <p><strong>Vendor:</strong> <span id="modalVendor"></span></p>
              <p><strong>Bid Amount:</strong> <span id="modalAmount" class="fw-bold text-primary"></span></p>
              <p><strong>Submitted:</strong> <span id="modalSubmitted"></span></p>
              <p><strong>Status:</strong> <span id="modalStatus"></span></p>
            </div>
            <div class="col-md-6">
              <h6 class="fw-bold">Evaluation Criteria</h6>
              <div class="mb-2">
                <label class="form-label small">Price Score (40%)</label>
                <div class="d-flex align-items-center">
                  <input type="range" class="form-range me-2" min="1" max="10" value="8" id="priceScore">
                  <span class="badge bg-primary" id="priceScoreValue">8</span>
                </div>
              </div>
              <div class="mb-2">
                <label class="form-label small">Quality Score (30%)</label>
                <div class="d-flex align-items-center">
                  <input type="range" class="form-range me-2" min="1" max="10" value="7" id="qualityScore">
                  <span class="badge bg-primary" id="qualityScoreValue">7</span>
                </div>
              </div>
              <div class="mb-2">
                <label class="form-label small">Delivery Score (20%)</label>
                <div class="d-flex align-items-center">
                  <input type="range" class="form-range me-2" min="1" max="10" value="9" id="deliveryScore">
                  <span class="badge bg-primary" id="deliveryScoreValue">9</span>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label small">Experience Score (10%)</label>
                <div class="d-flex align-items-center">
                  <input type="range" class="form-range me-2" min="1" max="10" value="8" id="experienceScore">
                  <span class="badge bg-primary" id="experienceScoreValue">8</span>
                </div>
              </div>
              <div class="alert alert-info">
                <strong>Total Score:</strong> <span id="totalScore" class="fw-bold">7.9/10</span>
              </div>
            </div>
          </div>
          <div class="mt-3">
            <h6 class="fw-bold">Proposal Details</h6>
            <div class="border rounded p-3 bg-light">
              <p id="modalProposal">Complete office equipment supply including desks, chairs, computers, and accessories. All items meet specified quality standards with 2-year warranty. Delivery within 15 business days.</p>
            </div>
          </div>
          <div class="mt-3">
            <h6 class="fw-bold">Attachments</h6>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-pdf me-1"></i>Technical Specs.pdf
              </button>
              <button class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-image me-1"></i>Product Images.zip
              </button>
              <button class="btn btn-sm btn-outline-primary">
                <i class="bi bi-file-earmark-text me-1"></i>Warranty Terms.docx
              </button>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-danger" onclick="rejectBidFromModal()">
            <i class="bi bi-x-circle me-1"></i>Reject Bid
          </button>
          <button type="button" class="btn btn-success" onclick="selectWinnerFromModal()">
            <i class="bi bi-trophy me-1"></i>Select as Winner
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Winner Confirmation Modal -->
  <div class="modal fade" id="winnerConfirmModal" tabindex="-1" aria-labelledby="winnerConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="winnerConfirmModalLabel">Confirm Winner Selection</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Are you sure you want to select this bid as the winner? This action will:
          </div>
          <ul>
            <li>Mark this bid as "Won"</li>
            <li>Automatically reject all other bids for this RFQ</li>
            <li>Send notification to the winning vendor</li>
            <li>Generate a contract for approval</li>
          </ul>
          <p><strong>Selected Bid:</strong> <span id="confirmBidId"></span></p>
          <p><strong>Vendor:</strong> <span id="confirmVendor"></span></p>
          <p><strong>Amount:</strong> <span id="confirmAmount"></span></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" onclick="confirmWinnerSelection()">
            <i class="bi bi-trophy me-1"></i>Confirm Selection
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- AI Bid Analysis JavaScript -->
  <script src="{{ asset('assets/js/ai-bid-analysis.js') }}"></script>

  <!-- Sidebar toggle functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Bind action buttons in bids table (avoid inline JS to prevent lint issues)
      document.querySelectorAll('.btn-view-bid').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-bid-id');
          if (id) { viewBidDetails(id); }
        });
      });
      document.querySelectorAll('.btn-select-winner').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-bid-id');
          if (id) { selectWinner(id); }
        });
      });
      document.querySelectorAll('.btn-reject-bid').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-bid-id');
          if (id) { rejectBid(id); }
        });
      });
      document.querySelectorAll('.btn-generate-contract').forEach(btn => {
        btn.addEventListener('click', () => {
          const id = btn.getAttribute('data-bid-id');
          if (id) { generateContract(id); }
        });
      });
      // User is authenticated via Laravel session middleware

      // Logout functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
          e.preventDefault();

          // Use Laravel's session-based logout
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = "{{ route('logout') }}";
          
          const csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = '{{ csrf_token() }}';
          form.appendChild(csrfToken);
          
          document.body.appendChild(form);
          form.submit();
          return;
          window.location.href = "{{ url('/login') }}";
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

  // Bidding Management Functions
  let currentBidData = {};

  async function viewBidDetails(bidId) {
    try {
      const response = await fetch(`/api/psm/bidding/bids/${bidId}`);
      const data = await response.json();
      
      if (!data.success) {
        showNotification('error', 'Failed to load bid details');
        return;
      }

      const bid = data.bid;
      currentBidData = bid;
      
      // Populate modal with bid data
      document.getElementById('modalBidId').textContent = bid.bid_number;
      document.getElementById('modalRfqTitle').textContent = bid.title;
      document.getElementById('modalVendor').textContent = bid.vendor_name;
      document.getElementById('modalAmount').textContent = '$' + bid.amount;
      document.getElementById('modalSubmitted').textContent = bid.submitted_at;
      
      const statusClass = getStatusBadgeClass(bid.status);
      document.getElementById('modalStatus').innerHTML = `<span class="badge ${statusClass}">${bid.status}</span>`;
      document.getElementById('modalProposal').textContent = bid.proposal;

      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('bidDetailsModal'));
      modal.show();
    } catch (error) {
      console.error('Error fetching bid details:', error);
      showNotification('error', 'Failed to load bid details');
    }
  }

  async function selectWinner(bidId) {
    try {
      const response = await fetch(`/api/psm/bidding/bids/${bidId}`);
      const data = await response.json();
      
      if (!data.success) {
        showNotification('error', 'Failed to load bid details');
        return;
      }

      const bid = data.bid;
      currentBidData = bid;
      
      // Populate confirmation modal
      document.getElementById('confirmBidId').textContent = bid.bid_number;
      document.getElementById('confirmVendor').textContent = bid.vendor_name;
      document.getElementById('confirmAmount').textContent = '$' + bid.amount;

      // Show confirmation modal
      const modal = new bootstrap.Modal(document.getElementById('winnerConfirmModal'));
      modal.show();
    } catch (error) {
      console.error('Error loading bid for winner selection:', error);
      showNotification('error', 'Failed to load bid details');
    }
  }

  function selectWinnerFromModal() {
    selectWinner(currentBidData.id);
    // Close details modal
    const detailsModal = bootstrap.Modal.getInstance(document.getElementById('bidDetailsModal'));
    if (detailsModal) detailsModal.hide();
  }

  async function confirmWinnerSelection() {
    try {
      const response = await fetch(`/api/psm/bidding/bids/${currentBidData.id}/select-winner`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      const data = await response.json();
      
      if (data.success) {
        // Update bid status in the table
        updateBidRowStatus(currentBidData.id, 'Won', 'bg-success');
        showNotification('success', data.message);
        
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('winnerConfirmModal'));
        if (modal) modal.hide();
        
        // Refresh the page to show updated data
        setTimeout(() => location.reload(), 2000);
      } else {
        showNotification('error', data.error || 'Failed to select winner');
      }
    } catch (error) {
      console.error('Error selecting winner:', error);
      showNotification('error', 'Failed to select winner');
    }
  }

  async function rejectBid(bidId) {
    if (!confirm('Are you sure you want to reject this bid?')) return;

    try {
      const response = await fetch(`/api/psm/bidding/bids/${bidId}/reject`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      const data = await response.json();
      
      if (data.success) {
        updateBidRowStatus(bidId, 'Rejected', 'bg-danger');
        showNotification('success', data.message);
      } else {
        showNotification('error', data.error || 'Failed to reject bid');
      }
    } catch (error) {
      console.error('Error rejecting bid:', error);
      showNotification('error', 'Failed to reject bid');
    }
  }

  function rejectBidFromModal() {
    rejectBid(currentBidData.id);
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('bidDetailsModal'));
    if (modal) modal.hide();
  }

  async function startEvaluation(bidId) {
    try {
      const response = await fetch(`/api/psm/bidding/bids/${bidId}/start-evaluation`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
      });

      const data = await response.json();
      
      if (data.success) {
        updateBidRowStatus(bidId, 'Under Review', 'bg-warning');
        showNotification('success', data.message);
      } else {
        showNotification('error', data.error || 'Failed to start evaluation');
      }
    } catch (error) {
      console.error('Error starting evaluation:', error);
      showNotification('error', 'Failed to start evaluation');
    }
  }

  async function generateContract(bidId) {
    try {
      // Get bid details first
      const response = await fetch(`/api/psm/bidding/bids/${bidId}`);
      const data = await response.json();
      
      if (data.success) {
        const bid = data.bid;
        
        // Store contract data in sessionStorage for contract page
        sessionStorage.setItem('contractData', JSON.stringify({
          bidId: bid.id,
          bidNumber: bid.bid_number,
          vendorName: bid.vendor_name,
          amount: bid.amount,
          title: bid.title,
          proposal: bid.proposal,
          generatedAt: new Date().toISOString()
        }));
        
        showNotification('success', `Redirecting to contract generation for ${bid.bid_number}`);
        
        // Redirect to contract management page
        setTimeout(() => {
          window.location.href = '/psm/contract';
        }, 1500);
      } else {
        showNotification('error', 'Failed to load bid details for contract generation');
      }
    } catch (error) {
      console.error('Error generating contract:', error);
      showNotification('error', 'Failed to generate contract');
    }
  }

  // Utility Functions
  function getStatusBadgeClass(status) {
    switch (status) {
      case 'Won': return 'bg-success';
      case 'Rejected': return 'bg-danger';
      case 'Under Review': return 'bg-warning';
      case 'Pending Evaluation': return 'bg-info';
      default: return 'bg-secondary';
    }
  }

  function updateBidRowStatus(bidId, status, badgeClass) {
    const row = document.querySelector(`tr[data-bid-id="${bidId}"]`);
    if (row) {
      const statusBadge = row.querySelector('.badge');
      if (statusBadge) {
        statusBadge.className = `badge ${badgeClass}`;
        statusBadge.textContent = status;
      }
    }
  }

  // Quick Action Functions
  function createNewRFQ() {
    window.location.href = '/psm/request';
  }

  function bulkEvaluate() {
    showNotification('info', 'Bulk evaluation feature coming soon');
  }

  async function exportBids() {
    try {
      const response = await fetch('/api/psm/bidding/export');
      if (response.ok) {
        const blob = await response.blob();
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'bids_export.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
        showNotification('success', 'Bid data exported successfully');
      } else {
        showNotification('error', 'Failed to export bid data');
      }
    } catch (error) {
      console.error('Error exporting bids:', error);
      showNotification('error', 'Failed to export bid data');
    }
  }

  function generateBidReport() {
    showNotification('info', 'Generating comprehensive bid report...');
  }

  // Utility function for notifications
  function showNotification(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : type === 'info' ? 'alert-info' : type === 'error' ? 'alert-danger' : 'alert-warning';
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
      if (notification.parentNode) {
        notification.remove();
      }
    }, 5000);
  }

  // Score calculation for evaluation
  document.addEventListener('DOMContentLoaded', function() {
    const scoreInputs = ['priceScore', 'qualityScore', 'deliveryScore', 'experienceScore'];
    const weights = [0.4, 0.3, 0.2, 0.1];
    
    scoreInputs.forEach((inputId, index) => {
      const input = document.getElementById(inputId);
      const valueSpan = document.getElementById(inputId + 'Value');
      
      if (input && valueSpan) {
        input.addEventListener('input', function() {
          valueSpan.textContent = this.value;
          calculateTotalScore();
        });
      }
    });
    
    function calculateTotalScore() {
      let totalScore = 0;
      scoreInputs.forEach((inputId, index) => {
        const input = document.getElementById(inputId);
        if (input) {
          totalScore += parseInt(input.value) * weights[index];
        }
      });
      
      const totalScoreElement = document.getElementById('totalScore');
      if (totalScoreElement) {
        totalScoreElement.textContent = totalScore.toFixed(1) + '/10';
      }
    }
  });

  // Initialize bid comparison chart
  document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('bidComparisonChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['TechCorp Inc.', 'Global Electronics', 'Smart Tech Solutions'],
          datasets: [{
            label: 'Bid Amount ($)',
            data: [45000, 42500, 125000],
            backgroundColor: [
              'rgba(54, 162, 235, 0.8)',
              'rgba(255, 99, 132, 0.8)',
              'rgba(255, 206, 86, 0.8)'
            ],
            borderColor: [
              'rgba(54, 162, 235, 1)',
              'rgba(255, 99, 132, 1)',
              'rgba(255, 206, 86, 1)'
            ],
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: function(value) {
                  return '$' + value.toLocaleString();
                }
              }
            }
          },
          plugins: {
            legend: {
              display: false
            },
            title: {
              display: true,
              text: 'Current Active Bids Comparison'
            }
          }
        }
      });
    }
  });
</script>
