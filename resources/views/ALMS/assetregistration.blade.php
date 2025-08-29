<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vehicle Registration Dashboard</title>
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
              <a href="{{ url('/alms/assetregistration') }}" class="nav-link text-dark small active">
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
          <i class="bi bi-truck fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Vehicle Registration</h2>
          <p class="text-muted mb-0">Welcome back, Sarah! Register and manage vehicle assets for the fleet.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/alms') }}" class="text-decoration-none">Asset Lifecycle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Vehicle Registration</li>
          </ol>
        </nav>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-0">
        <div class="card-body">
          @if (session('success'))
          <div class="alert alert-success" role="alert">{{ session('success') }}</div>
          @endif
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
              <i class="bi bi-box-arrow-in-down"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">24</h3>
              <p class="text-muted mb-0 small">Active Vehicles</p>
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
              <p class="text-muted mb-0 small">Total Fleet</p>
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
              <p class="text-muted mb-0 small">Under Maintenance</p>
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
              <h3 class="fw-bold mb-0">92%</h3>
              <p class="text-muted mb-0 small">Fleet Availability</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +2%</small>
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
      <!-- Vehicle Registration Form -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom bg-primary text-white">
          <h5 class="card-title mb-0">New Vehicle Registration</h5>
        </div>
        <div class="card-body">
          <!-- Vehicle Registration Entry -->
          <form id="vehicleForm" method="POST" action="{{ url('/alms/assetregistration') }}">
            @csrf
            <div class="row g-3">
              <!-- Basic Vehicle Information -->
              <div class="col-md-6">
                <label for="plateNumber" class="form-label">Plate Number *</label>
                <input type="text" class="form-control" id="plateNumber" placeholder="Enter plate number" required>
              </div>
              <div class="col-md-6">
                <label for="vehicleType" class="form-label">Vehicle Type *</label>
                <select class="form-control" id="vehicleType" required>
                  <option value="">Select vehicle type</option>
                  <option value="Van">Van</option>
                  <option value="Bus">Bus</option>
                  <option value="Car">Car</option>
                  <option value="Shuttle">Shuttle</option>
                  <option value="Truck">Truck</option>
                  <option value="Motorcycle">Motorcycle</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="brand" class="form-label">Brand</label>
                <input type="text" class="form-control" id="brand" placeholder="Enter vehicle brand">
              </div>
              <div class="col-md-6">
                <label for="model" class="form-label">Model</label>
                <input type="text" class="form-control" id="model" placeholder="Enter vehicle model">
              </div>
              <div class="col-md-4">
                <label for="year" class="form-label">Year</label>
                <input type="number" class="form-control" id="year" placeholder="2024" min="1900" max="2030">
              </div>
              <div class="col-md-4">
                <label for="capacity" class="form-label">Seating Capacity</label>
                <input type="number" class="form-control" id="capacity" placeholder="Number of seats" min="1">
              </div>
              <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-control" id="status">
                  <option value="Available">Available</option>
                  <option value="In Use">In Use</option>
                  <option value="Under Maintenance">Under Maintenance</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="registrationDate" class="form-label">Registration Date *</label>
                <input type="date" class="form-control" id="registrationDate" required>
              </div>
              <div class="col-12">
                <label for="notes" class="form-label">Additional Notes</label>
                <textarea class="form-control" id="notes" rows="3" placeholder="Enter any additional information about the vehicle"></textarea>
              </div>
            </div>
            
            
            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-4">
              <button type="reset" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Clear Form
              </button>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle me-1"></i>Register Vehicle
              </button>
            </div>
          </form>

          <!-- Vehicle Registration JS -->
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const vehicleForm = document.getElementById('vehicleForm');
              const registrationDate = document.getElementById('registrationDate');
              const today = new Date().toISOString().split('T')[0];
              
              // Set today's date as default
              if (registrationDate) {
                registrationDate.value = today;
              }
              
              // Handle form submission
              if (vehicleForm) {
                vehicleForm.addEventListener('submit', async function(e) {
                  e.preventDefault();
                  
                  // Get form values
                  const plateNumber = document.getElementById('plateNumber').value;
                  const vehicleType = document.getElementById('vehicleType').value;
                  const brand = document.getElementById('brand').value;
                  const model = document.getElementById('model').value;
                  const year = document.getElementById('year').value;
                  const capacity = document.getElementById('capacity').value;
                  const status = document.getElementById('status').value;
                  const regDate = document.getElementById('registrationDate').value;
                  const notes = document.getElementById('notes').value;
                  
                  // Basic validation
                  if (!plateNumber || !vehicleType || !regDate) {
                    alert('Please fill in all required fields (marked with *)');
                    return;
                  }
                  
                  try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    const response = await fetch("{{ url('/alms/assetregistration') }}", {
                      method: 'POST',
                      headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                      },
                      body: JSON.stringify({
                        plate_number: plateNumber,
                        vehicle_type: vehicleType,
                        brand: brand,
                        model: model,
                        year: year || null,
                        capacity: capacity || null,
                        status: status,
                        registration_date: regDate,
                        notes: notes
                      })
                    });

                    if (!response.ok) {
                      alert('Failed to register vehicle. Please check inputs.');
                      return;
                    }

                    // Reload to fetch updated list from database
                    window.location.href = "{{ url('/alms/assetregistration') }}";
                    return;
                  } catch (ex) {
                    console.error(ex);
                    alert('Unexpected error occurred.');
                  }
                });
              }
              
              // Client-side row injection removed; table now renders from database only
            });
          </script>
        </div>
      </div>
      
      <!-- Recent Vehicle Registrations -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Recent Vehicle Registrations</h5>
          <button class="btn btn-sm btn-outline-primary">View All</button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Vehicle ID</th>
                  <th>Plate Number</th>
                  <th>Type</th>
                  <th>Brand/Model</th>
                  <th>Year</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @if(isset($assets) && $assets->count())
                @foreach($assets as $asset)
                <tr>
                  <td><strong>#VEH-{{ $asset->id }}</strong></td>
                  <td><strong>{{ $asset->plate_number }}</strong></td>
                  <td>{{ $asset->vehicle_type }}</td>
                  <td>{{ trim(($asset->brand ?? '').' '.($asset->model ?? '')) }}</td>
                  <td>{{ $asset->year ?? 'N/A' }}</td>
                  <td><span class="badge {{ $asset->status === 'In Use' ? 'bg-primary' : ($asset->status === 'Under Maintenance' ? 'bg-warning' : ($asset->status === 'Inactive' ? 'bg-secondary' : 'bg-success')) }}">{{ $asset->status }}</span></td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary view-asset-btn" data-id="{{ $asset->id }}">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary edit-asset-btn" data-id="{{ $asset->id }}">
                      <i class="bi bi-pencil"></i>
                    </button>
                  </td>
                </tr>
                @endforeach
                @else
                <tr>
                  <td colspan="7" class="text-muted">No assets yet.</td>
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
            <button class="btn btn-primary" id="newVehicleBtn">
              <i class="bi bi-plus-circle me-2"></i>New Vehicle
            </button>
            <button class="btn btn-outline-primary" id="scanVinBtn">
              <i class="bi bi-upc-scan me-2"></i>Scan VIN
            </button>
            <button class="btn btn-outline-primary" id="importVehicleCSVBtn">
              <i class="bi bi-file-earmark-excel me-2"></i>Import from CSV
            </button>
            <button class="btn btn-outline-secondary" id="printRegistrationBtn">
              <i class="bi bi-printer me-2"></i>Print Registration
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
              <span class="small">Vehicles Registered</span>
              <span class="small fw-bold">12</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-success" style="width: 80%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Available Vehicles</span>
              <span class="small fw-bold">142</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-primary" style="width: 91%"></div>
            </div>
          </div>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">In Maintenance</span>
              <span class="small fw-bold">8</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-warning" style="width: 5%"></div>
            </div>
          </div>
          <div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Fleet Utilization</span>
              <span class="small fw-bold">78%</span>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar bg-info" style="width: 78%"></div>
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
            Vehicle ABC-1234 requires immediate maintenance
          </div>
          <div class="alert alert-warning alert-sm mb-2">
            <i class="bi bi-clock me-2"></i>
            Registration renewal due for 3 vehicles
          </div>
          <div class="alert alert-info alert-sm">
            <i class="bi bi-info-circle me-2"></i>
            New vehicle delivery scheduled for tomorrow
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

  <!-- View/Edit Asset Modal -->
  <div class="modal fade" id="assetModal" tabindex="-1" aria-labelledby="assetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="assetModalLabel">Asset</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="assetEditForm">
            <input type="hidden" id="editId">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Plate Number</label>
                <input type="text" class="form-control" id="editPlate" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Vehicle Type</label>
                <input type="text" class="form-control" id="editType" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Brand</label>
                <input type="text" class="form-control" id="editBrand">
              </div>
              <div class="col-md-6">
                <label class="form-label">Model</label>
                <input type="text" class="form-control" id="editModel">
              </div>
              <div class="col-md-4">
                <label class="form-label">Year</label>
                <input type="number" class="form-control" id="editYear" min="1900" max="2100">
              </div>
              <div class="col-md-4">
                <label class="form-label">Capacity</label>
                <input type="number" class="form-control" id="editCapacity" min="1">
              </div>
              <div class="col-md-4">
                <label class="form-label">Status</label>
                <select class="form-control" id="editStatus">
                  <option value="Available">Available</option>
                  <option value="In Use">In Use</option>
                  <option value="Under Maintenance">Under Maintenance</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Registration Date</label>
                <input type="date" class="form-control" id="editRegDate" required>
              </div>
              <div class="col-12">
                <label class="form-label">Notes</label>
                <textarea class="form-control" id="editNotes" rows="3"></textarea>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="saveAssetBtn">Save changes</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Sidebar toggle functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
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

      // Asset action handlers
      const assetModal = new bootstrap.Modal(document.getElementById('assetModal'));
      document.querySelectorAll('.view-asset-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
          e.preventDefault();
          const id = this.getAttribute('data-id');
          try {
            const res = await fetch(`{{ url('/alms/assets') }}/${id}`);
            if (!res.ok) return;
            const json = await res.json();
            const a = json.data;
            // Populate fields in read-only mode
            document.getElementById('assetModalLabel').textContent = `View Asset #${a.id}`;
            document.getElementById('editId').value = a.id;
            document.getElementById('editPlate').value = a.plate_number;
            document.getElementById('editType').value = a.vehicle_type;
            document.getElementById('editBrand').value = a.brand ?? '';
            document.getElementById('editModel').value = a.model ?? '';
            document.getElementById('editYear').value = a.year ?? '';
            document.getElementById('editCapacity').value = a.capacity ?? '';
            document.getElementById('editStatus').value = a.status ?? 'Available';
            document.getElementById('editRegDate').value = a.registration_date;
            document.getElementById('editNotes').value = a.notes ?? '';
            // Disable inputs
            document.querySelectorAll('#assetEditForm input, #assetEditForm select, #assetEditForm textarea').forEach(el => el.setAttribute('disabled', 'disabled'));
            document.getElementById('saveAssetBtn').style.display = 'none';
            assetModal.show();
          } catch (err) {
            console.error(err);
          }
        });
      });

      document.querySelectorAll('.edit-asset-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
          e.preventDefault();
          const id = this.getAttribute('data-id');
          try {
            const res = await fetch(`{{ url('/alms/assets') }}/${id}`);
            if (!res.ok) return;
            const json = await res.json();
            const a = json.data;
            // Populate fields in edit mode
            document.getElementById('assetModalLabel').textContent = `Edit Asset #${a.id}`;
            document.getElementById('editId').value = a.id;
            document.getElementById('editPlate').value = a.plate_number;
            document.getElementById('editType').value = a.vehicle_type;
            document.getElementById('editBrand').value = a.brand ?? '';
            document.getElementById('editModel').value = a.model ?? '';
            document.getElementById('editYear').value = a.year ?? '';
            document.getElementById('editCapacity').value = a.capacity ?? '';
            document.getElementById('editStatus').value = a.status ?? 'Available';
            document.getElementById('editRegDate').value = a.registration_date;
            document.getElementById('editNotes').value = a.notes ?? '';
            // Enable inputs
            document.querySelectorAll('#assetEditForm input, #assetEditForm select, #assetEditForm textarea').forEach(el => el.removeAttribute('disabled'));
            document.getElementById('saveAssetBtn').style.display = '';
            assetModal.show();
          } catch (err) {
            console.error(err);
          }
        });
      });

      document.getElementById('saveAssetBtn')?.addEventListener('click', async function() {
        const id = document.getElementById('editId').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        try {
          const res = await fetch(`{{ url('/alms/assets') }}/${id}`, {
            method: 'PUT',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            },
            body: JSON.stringify({
              plate_number: document.getElementById('editPlate').value,
              vehicle_type: document.getElementById('editType').value,
              brand: document.getElementById('editBrand').value || null,
              model: document.getElementById('editModel').value || null,
              year: document.getElementById('editYear').value || null,
              capacity: document.getElementById('editCapacity').value || null,
              status: document.getElementById('editStatus').value,
              registration_date: document.getElementById('editRegDate').value,
              notes: document.getElementById('editNotes').value || null
            })
          });
          if (!res.ok) {
            alert('Failed to save changes.');
            return;
          }
          assetModal.hide();
          window.location.href = "{{ url('/alms/assetregistration') }}";
        } catch (err) {
          console.error(err);
          alert('Unexpected error.');
        }
      });

      // Quick Actions static handlers
      const newVehicleBtn = document.getElementById('newVehicleBtn');
      const scanVinBtn = document.getElementById('scanVinBtn');
      const importVehicleCSVBtn = document.getElementById('importVehicleCSVBtn');
      const printRegistrationBtn = document.getElementById('printRegistrationBtn');
      const staticModal = new bootstrap.Modal(document.getElementById('staticModal'));
      const staticModalBody = document.getElementById('staticModalBody');
      const staticModalLabel = document.getElementById('staticModalLabel');

      if (newVehicleBtn) {
        newVehicleBtn.addEventListener('click', function(e) {
          e.preventDefault();
          // Scroll to the form
          document.getElementById('vehicleForm').scrollIntoView({ behavior: 'smooth' });
        });
      }
      if (scanVinBtn) {
        scanVinBtn.addEventListener('click', function(e) {
          e.preventDefault();
          staticModalLabel.textContent = 'Scan VIN';
          staticModalBody.innerHTML = '<p>VIN scanning is not available in static mode.</p>';
          staticModal.show();
        });
      }
      if (importVehicleCSVBtn) {
        importVehicleCSVBtn.addEventListener('click', function(e) {
          e.preventDefault();
          staticModalLabel.textContent = 'Import Vehicles from CSV';
          staticModalBody.innerHTML = '<p>CSV import is not available in static mode.</p>';
          staticModal.show();
        });
      }
      if (printRegistrationBtn) {
        printRegistrationBtn.addEventListener('click', function(e) {
          e.preventDefault();
          staticModalLabel.textContent = 'Print Registration';
          staticModalBody.innerHTML = '<p>This would print the vehicle registration. (Static demo only)</p>';
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

  document.querySelectorAll('#assetSubmenu .nav-link').forEach(link => {
  link.addEventListener('click', function(e) {
    // Keep the Asset Life Cycle & Maintenance dropdown open
    const assetSubmenu = document.getElementById('assetSubmenu');
    if (assetSubmenu && !assetSubmenu.classList.contains('show')) {
      assetSubmenu.classList.add('show');
    }
    // Optionally, highlight the active link
    document.querySelectorAll('#assetSubmenu .nav-link').forEach(l => l.classList.remove('active'));
    this.classList.add('active');
    // Let navigation happen (do not preventDefault)
  });
});
  </script>
</body>
</html>
