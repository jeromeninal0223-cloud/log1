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
    <!-- Left Column -->
    <div class="col-lg-8">
      <!-- Maintenance Scheduling Form -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom bg-primary text-white">
          <h5 class="card-title mb-0">New Maintenance Schedule</h5>
        </div>
        <div class="card-body">
          <!-- Schedule Entry -->
          <form id="scheduleForm" method="POST" action="{{ url('/alms/maintenance/schedules') }}">
            @csrf
            <div class="row g-3">
              <div class="col-md-6">
                <label for="assetId" class="form-label">Asset</label>
                <select class="form-control" id="assetId" required>
                  <option value="">Select asset</option>
                  @isset($assets)
                  @foreach($assets as $asset)
                  <option value="{{ $asset->id }}">#VEH-{{ $asset->id }} — {{ $asset->plate_number }} ({{ $asset->vehicle_type }})</option>
                  @endforeach
                  @endisset
                </select>
              </div>
              <div class="col-md-6">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" placeholder="e.g., Oil change" required>
              </div>
              <div class="col-md-6">
                <label for="scheduledDate" class="form-label">Scheduled Date</label>
                <input type="date" class="form-control" id="scheduledDate" required>
              </div>
              <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status" required>
                  <option value="Scheduled">Scheduled</option>
                  <option value="In Progress">In Progress</option>
                  <option value="Completed">Completed</option>
                  <option value="Cancelled">Cancelled</option>
                </select>
              </div>
              <div class="col-12">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" rows="2" placeholder="Optional"></textarea>
              </div>
            </div>
            
            <!-- Form Actions -->
            <div class="d-flex justify-content-between">
              <button type="reset" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Clear
              </button>
              <button type="submit" class="btn btn-primary" id="createScheduleBtn">
                <i class="bi bi-check-circle me-1"></i>Create Schedule
              </button>
            </div>
          </form>

          <!-- Maintenance Scheduling JS -->
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const scheduleForm = document.getElementById('scheduleForm');
              const scheduledDate = document.getElementById('scheduledDate');
              const today = new Date().toISOString().split('T')[0];
              if (scheduledDate) scheduledDate.value = today;

              if (scheduleForm) {
                scheduleForm.addEventListener('submit', async function(e) {
                  e.preventDefault();
                  const assetId = document.getElementById('assetId').value;
                  const title = document.getElementById('title').value;
                  const status = document.getElementById('status').value;
                  const notes = document.getElementById('notes').value;
                  const dateVal = document.getElementById('scheduledDate').value;
                  if (!assetId || !title || !dateVal || !status) {
                    alert('Please fill all required fields.');
                    return;
                  }
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
                        title: title,
                        scheduled_date: dateVal,
                        status: status,
                        notes: notes || null
                      })
                    });
                    if (!res.ok) {
                      alert('Failed to create schedule.');
                      return;
                    }
                    window.location.href = "{{ url('/alms/maintenance') }}";
                  } catch (err) {
                    console.error(err);
                    alert('Unexpected error.');
                  }
                });
              }
            });
          </script>
        </div>
      </div>
      
      <!-- Upcoming Maintenance Schedules -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Maintenance Schedules</h5>
          <button class="btn btn-sm btn-outline-primary" id="refreshSchedulesBtn">Refresh</button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Asset</th>
                  <th>Title</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @if(isset($schedules) && $schedules->count())
                  @foreach($schedules as $s)
                  <tr>
                    <td><strong>#MS-{{ $s->id }}</strong></td>
                    <td>#VEH-{{ $s->asset_id }} — {{ optional($s->asset)->plate_number }}</td>
                    <td>{{ $s->title }}</td>
                    <td>{{ $s->scheduled_date }}</td>
                    <td>
                      <span class="badge {{ $s->status === 'Completed' ? 'bg-success' : ($s->status === 'In Progress' ? 'bg-primary' : ($s->status === 'Cancelled' ? 'bg-secondary' : 'bg-warning')) }}">{{ $s->status }}</span>
                  </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary view-schedule-btn" data-id="{{ $s->id }}">
                      <i class="bi bi-eye"></i>
                    </button>
                      <button class="btn btn-sm btn-outline-secondary edit-schedule-btn" data-id="{{ $s->id }}">
                        <i class="bi bi-pencil"></i>
                    </button>
                  </td>
                </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="6" class="text-muted">No maintenance schedules yet.</td>
                </tr>
                @endif
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
            <button class="btn btn-primary" id="newReceiptBtn">
              <i class="bi bi-plus-circle me-2"></i>New Receipt
            </button>
            <button class="btn btn-outline-primary" id="scanBarcodeBtn">
              <i class="bi bi-upc-scan me-2"></i>Scan Barcode
            </button>
            <button class="btn btn-outline-primary" id="importCSVBtn">
              <i class="bi bi-file-earmark-excel me-2"></i>Import from CSV
            </button>
            <button class="btn btn-outline-secondary" id="printReceiptBtn">
              <i class="bi bi-printer me-2"></i>Print Receipt
            </button>
          </div>
        </div>
      </div>
      
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
      
      <!-- Receipt Summary -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Today's Summary</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Receipts Completed</span>
              <span class="small fw-bold">12</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: 80%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Items Received</span>
              <span class="small fw-bold">187</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-primary" style="width: 65%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Damaged Items</span>
              <span class="small fw-bold">3</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-danger" style="width: 5%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Avg. Time per Receipt</span>
              <span class="small fw-bold">32min</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-info" style="width: 75%"></div>
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
                <option value="{{ $asset->id }}">#VEH-{{ $asset->id }} — {{ $asset->plate_number }}</option>
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
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const payload = {
          asset_id: document.getElementById('schAssetId').value,
          title: document.getElementById('schTitle').value,
          scheduled_date: document.getElementById('schDate').value,
          status: document.getElementById('schStatus').value,
          notes: document.getElementById('schNotes').value || null
        };
        const res = await fetch(`{{ url('/alms/maintenance/schedules') }}/${id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify(payload)
        });
        if (!res.ok) { alert('Failed to save schedule'); return; }
        scheduleModal.hide();
        window.location.href = "{{ url('/alms/maintenance') }}";
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
