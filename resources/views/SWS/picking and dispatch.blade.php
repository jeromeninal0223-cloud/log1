@php
// Access Control: Only logistics_staff and admin can access SWS
if (!auth()->check()) {
    header('Location: /login');
    exit();
}

$userRole = auth()->user()->role;
if (!in_array($userRole, ['logistics_staff', 'admin'])) {
    // Redirect procurement officers to their dashboard
    if ($userRole === 'procurement_officer') {
        header('Location: /officer/dashboard');
        exit();
    }
    // Redirect other unauthorized users to main dashboard
    header('Location: /dashboard');
    exit();
}
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Warehousing Dashboard</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Leaflet Map -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
      <h6 class="fw-semibold mb-1">{{ Auth::user()->name ?? 'User' }}</h6>
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
        <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#warehouseSubmenu" aria-expanded="true" aria-controls="warehouseSubmenu">
          <i class="bi bi-box-seam me-2"></i> Smart Warehousing
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse show" id="warehouseSubmenu">
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
              <a href="{{ url('/picking-dispatch') }}" class="nav-link text-dark small active">
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
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="nav-link text-danger btn btn-link p-0">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
          </button>
        </form>
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
            <h2 class="fw-bold mb-1">Picking and Dispatch</h2>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name ?? 'User' }}! Manage order picking and dispatch operations.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/smartwarehousing') }}" class="text-decoration-none">Smart Warehousing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Picking and Dispatch</li>
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
                <i class="bi bi-box-arrow-up"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">89</h3>
                <p class="text-muted mb-0 small">Active Picks</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +5 today</small>
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
                <i class="bi bi-truck"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">234</h3>
                <p class="text-muted mb-0 small">Dispatched Today</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +18%</small>
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
                <i class="bi bi-clock"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">1.8hrs</h3>
                <p class="text-muted mb-0 small">Avg Pick Time</p>
                <small class="text-success"><i class="bi bi-arrow-down"></i> -0.3hrs</small>
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
                <i class="bi bi-percent"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">96.7%</h3>
                <p class="text-muted mb-0 small">Pick Accuracy</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +1.2%</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Item Requests Section -->
    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Inventory Items</h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-funnel me-1"></i>Filter
              </button>
              <a href="{{ url('/inventory-receipt') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Add Inventory
              </a>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              @forelse($inventoryItems as $item)
              <div class="col-md-6 col-lg-4">
                <div class="card border h-100" data-item-id="{{ $item->id }}">
                  <div class="card-body p-3 d-flex flex-column">
                    <!-- Content that can grow -->
                    <div class="flex-grow-1">
                      <div class="d-flex justify-content-between align-items-start mb-2">
                        <h6 class="card-title mb-0 fw-semibold">{{ $item->item_name }}</h6>
                        <div class="d-flex gap-1">
                          <span class="badge bg-info">
                            {{ $item->request_count }} {{ $item->request_count == 1 ? 'Request' : 'Requests' }}
                          </span>
                          <span class="badge {{ $item->quantity <= 5 ? 'bg-danger' : ($item->quantity <= 10 ? 'bg-warning' : 'bg-success') }}">
                            {{ $item->quantity <= 5 ? 'LOW' : ($item->quantity <= 10 ? 'MEDIUM' : 'HIGH') }}
                          </span>
                        </div>
                      </div>
                    <p class="text-muted small mb-2">
                      {{ $item->receipt->receipt_number ?? 'N/A' }} 
                      @if($item->description == 'Electronics') 📱
                      @elseif($item->description == 'Furniture') 🪑
                      @elseif($item->description == 'Accessories') 🖱️
                      @elseif($item->description == 'Office Supplies') 📝
                      @elseif($item->description == 'Equipment') 🔧
                      @else 📦
                      @endif
                      {{ $item->description }}
                    </p>
                    
                    <!-- Requester Information -->
                    @if($item->requests && $item->requests->count() > 0)
                    <div class="mb-3">
                      <small class="text-muted fw-semibold">Requested by:</small>
                      <div class="mt-1">
                        @foreach($item->requests as $request)
                        <div class="d-flex justify-content-between align-items-center mb-1 p-2 bg-light rounded-2">
                          <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-1 me-2" style="width: 24px; height: 24px;">
                              <i class="bi bi-person-fill text-primary" style="font-size: 12px;"></i>
                            </div>
                            <div>
                              <div class="fw-semibold" style="font-size: 12px;">
                                {{ $request->requestedBy->name ?? 'Unknown User' }}
                              </div>
                              <div class="text-muted" style="font-size: 10px;">
                                @php
                                  $role = $request->requestedBy->role ?? 'N/A';
                                  $roleName = ucfirst(str_replace('_', ' ', $role));
                                  
                                  // Map roles to departments/locations
                                  $departmentMap = [
                                    'admin' => 'Administration',
                                    'procurement_officer' => 'Procurement Dept.',
                                    'logistics_staff' => 'Logistics Dept.',
                                    'warehouse_manager' => 'Warehouse',
                                    'inventory_manager' => 'Inventory Dept.',
                                    'finance_officer' => 'Finance Dept.',
                                    'project_manager' => 'Project Management',
                                    'operations_manager' => 'Operations',
                                    'maintenance_staff' => 'Maintenance',
                                    'security_officer' => 'Security',
                                    'hr_manager' => 'Human Resources',
                                    'it_support' => 'IT Department'
                                  ];
                                  
                                  $department = $departmentMap[strtolower($role)] ?? $roleName;
                                @endphp
                                {{ $roleName }}
                              </div>
                              <div class="text-primary" style="font-size: 9px;">
                                <i class="bi bi-building me-1"></i>{{ $department }}
                              </div>
                            </div>
                          </div>
                          <div class="text-end">
                            <span class="badge bg-info" style="font-size: 10px;">
                              Qty: {{ $request->requested_quantity }}
                            </span>
                            <div class="text-muted" style="font-size: 9px;">
                              {{ $request->priority ?? 'MEDIUM' }}
                            </div>
                          </div>
                        </div>
                        @if($request->delivery_location)
                        <div class="mt-1 p-2 bg-warning bg-opacity-10 rounded-2">
                          <div class="d-flex align-items-center">
                            <i class="bi bi-truck text-warning me-2" style="font-size: 12px;"></i>
                            <div>
                              <div class="fw-semibold text-warning" style="font-size: 11px;">
                                Deliver to: {{ ucfirst(str_replace('_', ' ', $request->delivery_location)) }}
                              </div>
                              @if($request->delivery_department)
                              <div class="text-muted" style="font-size: 9px;">
                                {{ $request->delivery_department }}
                              </div>
                              @endif
                            </div>
                          </div>
                        </div>
                        @endif
                        @endforeach
                      </div>
                    </div>
                    @endif
                    
                    <!-- Location Display -->
                    <div class="mb-3">
                      <small class="text-muted d-flex align-items-center">
                        <i class="bi bi-geo-alt me-1"></i>
                        @if($item->storage_location && $item->storage_location !== 'receiving_area')
                          @php
                            $zoneChar = substr($item->storage_location, 0, 1);
                            $bin = substr($item->storage_location, 1);
                            $zoneNames = [
                              'A' => 'Zone A - Vehicle Parts & Components',
                              'B' => 'Zone B - Tools & Equipment',
                              'C' => 'Zone C',
                              'D' => 'Zone D',
                              'E' => 'Zone E'
                            ];
                            $zoneName = $zoneNames[$zoneChar] ?? "Zone $zoneChar";
                          @endphp
                          {{ $zoneName }} - Bin {{ $bin }}
                        @elseif($item->storage_location === 'receiving_area')
                          Receiving Area
                        @else
                          No location set
                        @endif
                      </small>
                    </div>
                    </div>
                    
                    <!-- Fixed bottom section -->
                    <div class="mt-auto">
                      <!-- Quantity Info -->
                    <div class="row g-2 mb-3">
                      <div class="col-4">
                        <div class="text-center p-2 bg-info bg-opacity-10 rounded">
                          <div class="fw-bold text-info">{{ $item->total_requested_quantity ?? 0 }}</div>
                          <small class="text-muted">Requested</small>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="text-center p-2 bg-light rounded">
                          <div class="fw-semibold text-primary">{{ $item->quantity }}</div>
                          <small class="text-muted">Available</small>
                        </div>
                      </div>
                      <div class="col-4">
                        <div class="text-center p-2 bg-light rounded">
                          <div class="fw-semibold text-success picked-quantity" data-item-id="{{ $item->id }}">
                            {{ $item->picked_quantity ?? 0 }}
                          </div>
                          <small class="text-muted">Picked</small>
                        </div>
                      </div>
                    </div>

                    <!-- Picking Controls -->
                    <div class="d-flex align-items-center gap-2 mb-3">
                      <button class="btn btn-sm btn-outline-secondary decrement-btn" data-item-id="{{ $item->id }}">
                        <i class="bi bi-dash"></i>
                      </button>
                      <input type="number" class="form-control form-control-sm text-center picked-input" 
                             data-item-id="{{ $item->id }}" 
                             value="{{ $item->picked_quantity ?? 0 }}" 
                             min="0" 
                             max="{{ $item->quantity }}"
                             data-requested-quantity="{{ $item->total_requested_quantity ?? 0 }}">
                      <button class="btn btn-sm btn-outline-secondary increment-btn" data-item-id="{{ $item->id }}">
                        <i class="bi bi-plus"></i>
                      </button>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                      <button class="btn btn-sm btn-success pick-all-btn flex-fill" 
                              data-item-id="{{ $item->id }}" 
                              data-max-quantity="{{ $item->quantity }}"
                              data-requested-quantity="{{ $item->total_requested_quantity ?? 0 }}">
                        <i class="bi bi-cart-plus me-1"></i>Pick Requested ({{ $item->total_requested_quantity ?? 0 }})
                      </button>
                      <button class="btn btn-sm btn-outline-danger clear-btn" data-item-id="{{ $item->id }}">
                        <i class="bi bi-x-circle"></i>
                      </button>
                    </div>
                    </div>
                  </div>
                </div>
              </div>
              @empty
              <div class="col-12">
                <div class="text-center py-5">
                  <i class="bi bi-inbox display-1 text-muted"></i>
                  <h5 class="mt-3 text-muted">No items requested for picking</h5>
                  <p class="text-muted">Items will appear here when there are active item requests that need to be fulfilled.</p>
                  <a href="{{ url('/item-requests/create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i>Create Item Request
                  </a>
                </div>
              </div>
              @endforelse
            </div>
          </div>
          <!-- Summary Section -->
          <div class="card-footer bg-light border-top">
            <div class="d-flex justify-content-between align-items-center">
              <span class="text-muted">Total items to pick: <strong id="totalItemsToPick">20</strong></span>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary" id="saveProgressBtn">
                  <i class="bi bi-save me-1"></i>Save Progress
                </button>
                <button class="btn btn-warning" id="completePickSessionBtn">
                  <i class="bi bi-check-circle me-1"></i>Complete Pick Session
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dispatch Configuration Section -->
    <div class="row g-4 mt-2" id="dispatch-section">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Dispatch Configuration{{ !empty($dispatchItems) ? ' (' . count($dispatchItems) . ' items ready)' : '' }}</h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-funnel me-1"></i>Filter
              </button>
              <button class="btn btn-sm btn-success">
                <i class="bi bi-truck me-1"></i>Schedule Dispatch
              </button>
            </div>
          </div>
          <div class="card-body">
            <!-- Dispatch Orders Section -->
            <div class="mb-4">
              <h6 class="mb-3">Items Ready for Dispatch</h6>
              <div class="row g-3" id="dispatchItemsContainer">
                @if(empty($dispatchItems))
                  <!-- Show when no items are ready for dispatch -->
                  <div class="col-12" id="noDispatchItems">
                    <div class="text-center py-4">
                      <i class="bi bi-truck display-1 text-muted"></i>
                      <h5 class="mt-3 text-muted">No Items Ready for Dispatch</h5>
                      <p class="text-muted">Complete a pick session to see items ready for dispatch here.</p>
                    </div>
                  </div>
                @else
                  <!-- Hide the no items message when items exist -->
                  <div class="col-12" id="noDispatchItems" style="display: none;">
                    <div class="text-center py-4">
                      <i class="bi bi-truck display-1 text-muted"></i>
                      <h5 class="mt-3 text-muted">No Items Ready for Dispatch</h5>
                      <p class="text-muted">Complete a pick session to see items ready for dispatch here.</p>
                    </div>
                  </div>
                  
                  <!-- Render existing dispatch items from session -->
                  @foreach($dispatchItems as $item)
                    @php
                      $priority = $item['picked_quantity'] >= 10 ? ['level' => 'HIGH', 'class' => 'bg-danger'] : 
                                 ($item['picked_quantity'] >= 5 ? ['level' => 'MEDIUM', 'class' => 'bg-warning'] : 
                                 ['level' => 'LOW', 'class' => 'bg-success']);
                      
                      // Smart destination assignment based on item name
                      $destination = 'Warehouse'; // Default for unknown items
                      $itemLower = strtolower($item['item_name']);
                      
                      // Enhanced destination mapping
                      $destinations = [
                        // IT Equipment
                        'laptop' => 'IT Department', 'computer' => 'IT Department', 'monitor' => 'IT Department',
                        'mouse' => 'IT Department', 'keyboard' => 'IT Department', 'printer' => 'IT Department',
                        'server' => 'IT Department', 'router' => 'IT Department', 'switch' => 'IT Department',
                        
                        // Office Supplies
                        'chair' => 'Admin Office', 'desk' => 'Admin Office', 'paper' => 'Admin Office', 
                        'pen' => 'Admin Office', 'folder' => 'Admin Office', 'stapler' => 'Admin Office',
                        
                        // Vehicles & Logistics
                        'truck' => 'Logistics Department', 'vehicle' => 'Logistics Department', 
                        'van' => 'Logistics Department', 'forklift' => 'Logistics Department',
                        
                        // Tools & Maintenance
                        'tool' => 'Maintenance Department', 'wrench' => 'Maintenance Department',
                        'hammer' => 'Maintenance Department', 'drill' => 'Maintenance Department',
                        
                        // General Equipment
                        'equipment' => 'Operations Department', 'machine' => 'Operations Department',
                        
                        // Medical/Safety
                        'medical' => 'Medical Department', 'safety' => 'Safety Department',
                        'first aid' => 'Medical Department', 'helmet' => 'Safety Department'
                      ];
                      
                      // Check for keyword matches
                      foreach($destinations as $keyword => $dest) {
                        if(str_contains($itemLower, $keyword)) {
                          $destination = $dest;
                          break;
                        }
                      }
                      
                      // Special handling for unclear items - suggest based on context
                      if($destination === 'Warehouse' && strlen($item['item_name']) <= 10) {
                        $destination = 'Admin Office'; // Short names likely office supplies
                      }
                    @endphp
                    
                    <div class="col-12 dispatch-item">
                      <div class="card border-0 shadow-sm bg-white" style="border-radius: 12px;">
                        <div class="card-body p-4">
                          <div class="d-flex align-items-center justify-content-between">
                            <!-- Item Info -->
                            <div class="d-flex align-items-center flex-grow-1">
                              <div class="bg-light rounded-circle p-3 me-3" style="width: 48px; height: 48px;">
                                <i class="bi bi-box-seam text-primary"></i>
                              </div>
                              <div>
                                <h6 class="mb-1 fw-semibold text-dark">{{ $item['item_name'] }}</h6>
                                <span class="text-muted small">
                                  <i class="bi bi-layers me-1"></i>Qty: {{ $item['picked_quantity'] }} • Ready for dispatch
                                </span>
                              </div>
                            </div>

                            <!-- Destination Info -->
                            <div class="text-center mx-4">
                              <div class="d-flex align-items-center justify-content-center mb-1">
                                <i class="bi bi-geo-alt me-2 text-muted"></i>
                                <span class="fw-medium text-dark">{{ $destination }}</span>
                                @if($destination === 'Warehouse' || $destination === 'Admin Office')
                                  <i class="bi bi-exclamation-triangle-fill text-warning ms-2" 
                                     title="Auto-assigned destination - click edit to change" style="font-size: 12px;"></i>
                                @endif
                              </div>
                              <div class="route-preview small text-muted" id="routePreview_{{ $item['item_id'] }}">
                                <i class="bi bi-map me-1"></i>
                                <span class="route-text">No route set - Click edit to plan route</span>
                              </div>
                            </div>

                            <!-- Status & Actions -->
                            <div class="d-flex align-items-center gap-2">
                              <div class="text-center me-3">
                                <div class="text-primary rounded-pill px-3 py-1" style="font-size: 12px; font-weight: 500; background-color: #e7f3ff;">
                                  <i class="bi bi-check-circle me-1"></i>Ready
                                </div>
                              </div>
                              
                              <div class="d-flex gap-2">
                                <button class="btn btn-light btn-sm edit-dispatch-btn rounded-pill px-3" 
                                        data-item-id="{{ $item['item_id'] }}" 
                                        data-item-name="{{ $item['item_name'] }}"
                                        data-destination="{{ $destination }}"
                                        title="Edit Route & Destination"
                                        style="border: 1px solid #e9ecef;">
                                  <i class="bi bi-pencil me-1"></i>Edit
                                </button>
                                <button class="btn btn-primary btn-sm dispatch-now-btn rounded-pill px-3" 
                                        data-item-id="{{ $item['item_id'] }}" 
                                        data-item-name="{{ $item['item_name'] }}"
                                        data-quantity="{{ $item['picked_quantity'] }}"
                                        title="Dispatch Now">
                                  <i class="bi bi-send me-1"></i>Dispatch
                                </button>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  @endforeach
                @endif
                
                <!-- Dynamic picked items will be inserted here -->
              </div>
            </div>

            <!-- Route Planning is now handled individually per item through the edit modal -->

            <!-- Dispatch Actions -->
            <div class="row g-3">
              <div class="col-md-6">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-calendar-check display-6 text-primary mb-2"></i>
                    <h6>Schedule Dispatch</h6>
                    <p class="text-muted small mb-3">Set delivery schedules and routes</p>
                    <button class="btn btn-primary btn-sm" id="scheduleDispatchBtn">
                      <i class="bi bi-plus-circle me-1"></i>Schedule Dispatch
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card bg-light">
                  <div class="card-body text-center">
                    <i class="bi bi-truck display-6 text-success mb-2"></i>
                    <h6>Bulk Dispatch</h6>
                    <p class="text-muted small mb-3">Dispatch multiple items at once</p>
                    <button class="btn btn-success btn-sm" id="bulkDispatchBtn">
                      <i class="bi bi-check-all me-1"></i>Bulk Dispatch
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Schedule Dispatch Modal -->
    <div class="modal fade" id="scheduleDispatchModal" tabindex="-1" aria-labelledby="scheduleDispatchModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="scheduleDispatchModalLabel">
              <i class="bi bi-calendar-check me-2"></i>Schedule Dispatch
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-4">
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h6 class="mb-0">Dispatch Details</h6>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label fw-bold">Dispatch Date & Time</label>
                      <input type="datetime-local" class="form-control" id="scheduleDateTime" required>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label fw-bold">Priority Level</label>
                      <select class="form-select" id="schedulePriority">
                        <option value="normal">Normal</option>
                        <option value="high">High Priority</option>
                        <option value="urgent">Urgent</option>
                      </select>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label fw-bold">Driver/Vehicle</label>
                      <select class="form-select" id="scheduleDriver">
                        <option value="">Select Driver</option>
                        <option value="driver1">John Doe - Truck A (TRK-001)</option>
                        <option value="driver2">Jane Smith - Van B (VAN-002)</option>
                        <option value="driver3">Mike Johnson - Truck C (TRK-003)</option>
                        <option value="driver4">Sarah Wilson - Van D (VAN-004)</option>
                      </select>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label fw-bold">Special Instructions</label>
                      <textarea class="form-control" id="scheduleInstructions" rows="3" placeholder="Enter any special delivery instructions..."></textarea>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-6">
                <div class="card">
                  <div class="card-header">
                    <h6 class="mb-0">Items to Dispatch</h6>
                  </div>
                  <div class="card-body">
                    <div id="scheduleItemsList" class="schedule-items-list">
                      <!-- Items will be populated here -->
                    </div>
                    
                    <div class="mt-3 p-3 bg-light rounded">
                      <div class="d-flex justify-content-between">
                        <span class="fw-bold">Total Items:</span>
                        <span id="scheduleTotalItems" class="fw-bold text-primary">0</span>
                      </div>
                      <div class="d-flex justify-content-between">
                        <span class="fw-bold">Estimated Duration:</span>
                        <span id="scheduleEstimatedTime" class="fw-bold text-success">-</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="confirmScheduleDispatch">
              <i class="bi bi-calendar-check me-1"></i>Schedule Dispatch
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Fullscreen Map Modal -->
    <div class="modal fade" id="fullscreenMapModal" tabindex="-1" aria-labelledby="fullscreenMapModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="fullscreenMapModalLabel">
              <i class="bi bi-map me-2"></i>Route Planning Map
            </h5>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-light" id="fullscreenSetPickupBtn">
                <i class="bi bi-geo-alt me-1"></i>Set Pickup
              </button>
              <button class="btn btn-sm btn-outline-light" id="fullscreenSetDestinationBtn">
                <i class="bi bi-geo-alt-fill me-1"></i>Set Destination
              </button>
              <button class="btn btn-sm btn-warning" id="fullscreenCalculateRouteBtn" style="display: none;">
                <i class="bi bi-map me-1"></i>Calculate Route
              </button>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          </div>
          <div class="modal-body p-0">
            <div class="row g-0 h-100">
              <div class="col-md-3 bg-light border-end">
                <div class="p-3">
                  <h6 class="mb-3">Location Information</h6>
                  
                  <div class="mb-3">
                    <label class="form-label small fw-bold text-primary">
                      <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Pickup Location:
                    </label>
                    <div id="fullscreenPickupLocationText" class="text-muted small">Click "Set Pickup" to select on map</div>
                    <div id="fullscreenPickupLocationAddress" class="text-primary small fw-bold" style="display: none;"></div>
                  </div>
                  
                  <div class="mb-3">
                    <label class="form-label small fw-bold text-success">
                      <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Destination:
                    </label>
                    <div id="fullscreenDestinationLocationText" class="text-muted small">Click "Set Destination" to select on map</div>
                    <div id="fullscreenDestinationLocationAddress" class="text-success small fw-bold" style="display: none;"></div>
                  </div>
                  
                  <div class="route-info" id="fullscreenRouteInfo" style="display: none;">
                    <div class="border-top pt-3">
                      <h6 class="mb-2">Route Details</h6>
                      <div class="d-flex justify-content-between small mb-1">
                        <span>Distance:</span>
                        <span id="fullscreenRouteDistance" class="fw-bold">-</span>
                      </div>
                      <div class="d-flex justify-content-between small mb-1">
                        <span>Est. Time:</span>
                        <span id="fullscreenRouteTime" class="fw-bold">-</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="mt-4">
                    <button class="btn btn-primary btn-sm w-100" id="applyRouteBtn">
                      <i class="bi bi-check-circle me-1"></i>Apply Route
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-md-9">
                <div id="fullscreenDispatchMap" style="height: calc(100vh - 60px);"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Dispatch Route Modal -->
    <div class="modal fade" id="editDispatchModal" tabindex="-1" aria-labelledby="editDispatchModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="editDispatchModalLabel">
              <i class="bi bi-map me-2"></i>Edit Dispatch Route
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-4">
              <div class="col-md-4">
                <div class="card h-100">
                  <div class="card-header">
                    <h6 class="mb-0">Item Details</h6>
                  </div>
                  <div class="card-body">
                    <div class="mb-3">
                      <label class="form-label fw-bold">Item Name</label>
                      <div id="editItemName" class="form-control-plaintext fw-semibold">-</div>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label fw-bold">Destination</label>
                      <select class="form-select" id="editDestinationSelect">
                        <option value="Warehouse">Warehouse</option>
                        <option value="IT Department">IT Department</option>
                        <option value="Admin Office">Admin Office</option>
                        <option value="Logistics Department">Logistics Department</option>
                        <option value="Maintenance Department">Maintenance Department</option>
                        <option value="Operations Department">Operations Department</option>
                        <option value="Medical Department">Medical Department</option>
                        <option value="Safety Department">Safety Department</option>
                        <option value="Custom">Custom Location...</option>
                      </select>
                    </div>
                    
                    <div class="mb-3" id="customDestinationGroup" style="display: none;">
                      <label class="form-label fw-bold">Custom Destination</label>
                      <input type="text" class="form-control" id="customDestinationInput" placeholder="Enter custom destination">
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label fw-bold">Location Details</label>
                      <div class="mb-2">
                        <label class="form-label small fw-bold text-primary">
                          <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Pickup Location:
                        </label>
                        <div id="editPickupLocationText" class="text-muted small">Click "Set Pickup" to select on map</div>
                        <div id="editPickupLocationAddress" class="text-primary small fw-bold" style="display: none;"></div>
                      </div>
                      
                      <div class="mb-3">
                        <label class="form-label small fw-bold text-success">
                          <i class="bi bi-circle-fill me-1" style="font-size: 8px;"></i>Destination:
                        </label>
                        <div id="editDestinationLocationText" class="text-muted small">Click "Set Destination" to select on map</div>
                        <div id="editDestinationLocationAddress" class="text-success small fw-bold" style="display: none;"></div>
                      </div>
                    </div>
                    
                    <div class="mb-3">
                      <label class="form-label fw-bold">Route Information</label>
                      <div class="route-summary p-3 bg-light rounded" id="editRouteSummary">
                        <div class="text-muted text-center">
                          <i class="bi bi-map display-6"></i>
                          <p class="mt-2 mb-0">Set pickup and destination on map to calculate route</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-md-8">
                <div class="card h-100">
                  <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Route Planning</h6>
                    <div class="btn-group btn-group-sm">
                      <button class="btn btn-outline-primary" id="editSetPickupBtn">
                        <i class="bi bi-geo-alt me-1"></i>Set Pickup
                      </button>
                      <button class="btn btn-outline-success" id="editSetDestinationBtn">
                        <i class="bi bi-geo-alt-fill me-1"></i>Set Destination
                      </button>
                      <button class="btn btn-warning" id="editCalculateRouteBtn" style="display: none;">
                        <i class="bi bi-map me-1"></i>Calculate Route
                      </button>
                    </div>
                  </div>
                  <div class="card-body p-0">
                    <div id="editDispatchMap" style="height: 400px;"></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="saveDispatchRoute">
              <i class="bi bi-check-circle me-1"></i>Save Route
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Warehouse Overview & Quick Actions -->
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Orders</h5>
            <button class="btn btn-sm btn-outline-primary">View All</button>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Pick Time</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td><strong>#ORD-2024-001</strong></td>
                    <td>TechCorp Inc.</td>
                    <td>15 items</td>
                    <td><span class="badge bg-danger">High</span></td>
                    <td><span class="badge bg-warning">Picking</span></td>
                    <td>1.2 hrs</td>
                  </tr>
                  <tr>
                    <td><strong>#ORD-2024-002</strong></td>
                    <td>Global Retail</td>
                    <td>8 items</td>
                    <td><span class="badge bg-warning">Medium</span></td>
                    <td><span class="badge bg-info">Packaging</span></td>
                    <td>0.8 hrs</td>
                  </tr>
                  <tr>
                    <td><strong>#ORD-2024-003</strong></td>
                    <td>E-Commerce Plus</td>
                    <td>23 items</td>
                    <td><span class="badge bg-success">Low</span></td>
                    <td><span class="badge bg-secondary">Pending</span></td>
                    <td>-</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Inventory Chart -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Inventory Levels</h5>
          </div>
          <div class="card-body">
            <canvas id="inventoryChart" height="100"></canvas>
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
              <button class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Order
              </button>
              <button class="btn btn-outline-primary">
                <i class="bi bi-box-arrow-in-down me-2"></i>Receive Stock
              </button>
              <button class="btn btn-outline-primary">
                <i class="bi bi-geo-alt-fill me-2"></i>Update Location
              </button>
              <button class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-text me-2"></i>Generate Report
              </button>
            </div>
          </div>
        </div>

        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Storage Utilization</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone A - Electronics</span>
                <span class="small text-muted">92%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-danger" style="width: 92%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone B - Clothing</span>
                <span class="small text-muted">78%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: 78%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone C - Furniture</span>
                <span class="small text-muted">65%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: 65%"></div>
              </div>
            </div>
            <div>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone D - Books</span>
                <span class="small text-muted">45%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 45%"></div>
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

   <!-- Sidebar toggle functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
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

  // Item picking functionality
  function incrementPicked(itemId) {
    const card = document.querySelector(`[data-item-id="${itemId}"]`);
    const pickedInput = card.querySelector('.picked-input');
    const pickedDisplay = card.querySelector('.picked-quantity');
    const maxQuantity = parseInt(pickedInput.getAttribute('max'));
    const requestedQuantity = parseInt(pickedInput.getAttribute('data-requested-quantity')) || 0;
    const currentValue = parseInt(pickedInput.value) || 0;
    
    // Limit increment to the requested quantity, but don't exceed available quantity
    const maxAllowed = Math.min(requestedQuantity, maxQuantity);
    
    if (currentValue < maxAllowed) {
      const newValue = currentValue + 1;
      pickedInput.value = newValue;
      pickedDisplay.textContent = newValue;
      updatePickedStatus(card);
      updatePickedQuantity(itemId, newValue);
    }
  }

  function decrementPicked(itemId) {
    const card = document.querySelector(`[data-item-id="${itemId}"]`);
    const pickedInput = card.querySelector('.picked-input');
    const pickedDisplay = card.querySelector('.picked-quantity');
    const currentValue = parseInt(pickedInput.value) || 0;
    
    if (currentValue > 0) {
      const newValue = currentValue - 1;
      pickedInput.value = newValue;
      pickedDisplay.textContent = newValue;
      updatePickedStatus(card);
      updatePickedQuantity(itemId, newValue);
    }
  }

  function pickAll(itemId, requestedQuantity, maxQuantity) {
    const card = document.querySelector(`[data-item-id="${itemId}"]`);
    const pickedInput = card.querySelector('.picked-input');
    const pickedDisplay = card.querySelector('.picked-quantity');
    
    // Use requested quantity, but don't exceed available quantity
    const quantityToPick = Math.min(requestedQuantity, maxQuantity);
    
    pickedInput.value = quantityToPick;
    pickedDisplay.textContent = quantityToPick;
    updatePickedStatus(card);
    updatePickedQuantity(itemId, quantityToPick);
  }

  function clearPicked(itemId) {
    const card = document.querySelector(`[data-item-id="${itemId}"]`);
    const pickedInput = card.querySelector('.picked-input');
    const pickedDisplay = card.querySelector('.picked-quantity');
    
    pickedInput.value = 0;
    pickedDisplay.textContent = 0;
    updatePickedStatus(card);
    updatePickedQuantity(itemId, 0);
  }

  function updatePickedQuantity(itemId, quantity) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    return fetch(`/inventory-items/${itemId}/picked`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            picked_quantity: quantity
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Picked quantity updated successfully');
        } else {
            console.error('Error updating picked quantity:', data.message);
        }
        return data;
    })
    .catch(error => {
        console.error('Error updating picked quantity:', error);
        throw error;
    });
  }
  
  function updatePickedStatus(card) {
    const pickedInput = card.querySelector('.picked-input');
    const pickedCount = parseInt(pickedInput.value) || 0;
    const maxQuantity = parseInt(pickedInput.getAttribute('max'));
    const requestedQuantity = parseInt(pickedInput.getAttribute('data-requested-quantity')) || 0;
    const pickAllBtn = card.querySelector('.pick-all-btn');
    
    if (pickedCount >= requestedQuantity && pickedCount > 0) {
      pickAllBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Request Complete';
      pickAllBtn.classList.remove('btn-warning');
      pickAllBtn.classList.add('btn-success');
      card.style.borderColor = '#198754';
      card.style.backgroundColor = '#f8fff9';
    } else if (pickedCount > 0) {
      const remaining = requestedQuantity - pickedCount;
      pickAllBtn.innerHTML = `<i class="bi bi-plus-circle me-1"></i>Pick Remaining (${remaining})`;
      pickAllBtn.classList.remove('btn-success');
      pickAllBtn.classList.add('btn-warning');
      card.style.borderColor = '#ffc107';
      card.style.backgroundColor = '#fffdf0';
    } else {
      pickAllBtn.innerHTML = `<i class="bi bi-cart-plus me-1"></i>Pick Requested (${requestedQuantity})`;
      pickAllBtn.classList.remove('btn-warning');
      pickAllBtn.classList.add('btn-success');
      card.style.borderColor = '';
      card.style.backgroundColor = '';
    }
    
    updateTotalItemsToPick();
  }

  function updateTotalItemsToPick() {
    const pickedInputs = document.querySelectorAll('.picked-input');
    let totalPicked = 0;
    
    pickedInputs.forEach(input => {
      totalPicked += parseInt(input.value) || 0;
    });
    
    const totalElement = document.getElementById('totalItemsToPick');
    if (totalElement) {
      totalElement.textContent = totalPicked;
    }
  }

  // Add event listeners for picking functionality
  document.addEventListener('DOMContentLoaded', function() {
    // Increment buttons
    document.querySelectorAll('.increment-btn').forEach(button => {
      button.addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        incrementPicked(itemId);
      });
    });

    // Decrement buttons
    document.querySelectorAll('.decrement-btn').forEach(button => {
      button.addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        decrementPicked(itemId);
      });
    });

    // Pick All buttons
    document.querySelectorAll('.pick-all-btn').forEach(button => {
      button.addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        const maxQuantity = parseInt(this.getAttribute('data-max-quantity'));
        const requestedQuantity = parseInt(this.getAttribute('data-requested-quantity'));
        pickAll(itemId, requestedQuantity, maxQuantity);
      });
    });

    // Clear buttons
    document.querySelectorAll('.clear-btn').forEach(button => {
      button.addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        clearPicked(itemId);
      });
    });

    // Input field changes
    document.querySelectorAll('.picked-input').forEach(input => {
      input.addEventListener('change', function() {
        const itemId = this.getAttribute('data-item-id');
        const value = parseInt(this.value) || 0;
        const maxValue = parseInt(this.getAttribute('max'));
        const requestedQuantity = parseInt(this.getAttribute('data-requested-quantity')) || 0;
        
        // Limit to requested quantity, but don't exceed available quantity
        const maxAllowed = Math.min(requestedQuantity, maxValue);
        
        if (value > maxAllowed) {
          this.value = maxAllowed;
        }
        
        // Update display
        const card = this.closest('.card');
        const pickedDisplay = card.querySelector('.picked-quantity');
        pickedDisplay.textContent = this.value;
        
        updatePickedStatus(card);
        updatePickedQuantity(itemId, parseInt(this.value));
      });
    });

    // Save Progress button
    const saveProgressBtn = document.getElementById('saveProgressBtn');
    if (saveProgressBtn) {
      saveProgressBtn.addEventListener('click', function() {
        savePickingProgress();
      });
    }

    // Complete Pick Session button
    const completePickSessionBtn = document.getElementById('completePickSessionBtn');
    if (completePickSessionBtn) {
      completePickSessionBtn.addEventListener('click', function() {
        completePickSession();
      });
    }

    // Initialize chart
    initializeInventoryChart();
    
    // Update initial total
    updateTotalItemsToPick();
    
    // Initialize dispatch event listeners for existing items (from session)
    addDispatchEventListeners();
    
    // Main dispatch map removed - using individual item route planning instead
    
    // Initialize edit dispatch functionality
    initializeEditDispatch();
    
    // Initialize schedule dispatch functionality
    initializeScheduleDispatch();
    
    // Load saved routes from storage
    loadDispatchRoutesFromStorage();
  });

  // Save picking progress function
  function savePickingProgress() {
    const pickedItems = [];
    const pickedInputs = document.querySelectorAll('.picked-input');
    
    pickedInputs.forEach(input => {
      const itemId = input.getAttribute('data-item-id');
      const quantity = parseInt(input.value) || 0;
      
      if (quantity > 0) {
        pickedItems.push({
          item_id: itemId,
          picked_quantity: quantity
        });
      }
    });

    if (pickedItems.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Items Picked',
        text: 'Please pick some items before saving progress.',
        confirmButtonText: 'OK'
      });
      return;
    }

    // Show loading state
    const saveBtn = document.getElementById('saveProgressBtn');
    const originalText = saveBtn.innerHTML;
    saveBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Saving...';
    saveBtn.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/picking-dispatch/save-progress', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        picked_items: pickedItems
      })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Progress Saved!',
          text: 'Your picking progress has been saved successfully.',
          confirmButtonText: 'OK',
          timer: 3000,
          timerProgressBar: true
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Save Failed',
          text: 'Error saving progress: ' + (data.message || 'Unknown error'),
          confirmButtonText: 'OK'
        });
      }
    })
    .catch(error => {
      console.error('Error saving progress:', error);
      Swal.fire({
        icon: 'error',
        title: 'Network Error',
        text: 'Error saving progress. Please check your connection and try again.',
        confirmButtonText: 'OK'
      });
    })
    .finally(() => {
      // Restore button state
      saveBtn.innerHTML = originalText;
      saveBtn.disabled = false;
    });
  }

  // Complete pick session function
  function completePickSession() {
    const pickedItems = [];
    const pickedInputs = document.querySelectorAll('.picked-input');
    let totalPicked = 0;
    
    pickedInputs.forEach(input => {
      const itemId = input.getAttribute('data-item-id');
      const quantity = parseInt(input.value) || 0;
      totalPicked += quantity;
      
      if (quantity > 0) {
        pickedItems.push({
          item_id: itemId,
          picked_quantity: quantity
        });
      }
    });

    if (pickedItems.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Items Picked',
        text: 'Please pick some items before completing the session.',
        confirmButtonText: 'OK'
      });
      return;
    }

    // Confirmation dialog with SweetAlert2
    Swal.fire({
      title: 'Complete Pick Session?',
      html: `
        <div class="text-start">
          <p><strong>Total items picked:</strong> ${totalPicked}</p>
          <p class="text-warning"><i class="bi bi-exclamation-triangle me-2"></i><strong>Warning:</strong> This action cannot be undone.</p>
          <p>Items will be removed from inventory and marked as completed.</p>
        </div>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#dc3545',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, Complete Session',
      cancelButtonText: 'Cancel',
      reverseButtons: true
    }).then((result) => {
      if (!result.isConfirmed) {
        return;
      }

      // Continue with completion process
      completeSessionProcess(pickedItems, totalPicked);
    });
  }

  // Separate function for the actual completion process
  function completeSessionProcess(pickedItems, totalPicked) {
    // Show loading with SweetAlert2
    Swal.fire({
      title: 'Completing Session...',
      html: `
        <div class="text-center">
          <div class="spinner-border text-primary mb-3" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p>Processing ${totalPicked} picked items...</p>
          <p class="text-muted small">Please wait while we update the inventory.</p>
        </div>
      `,
      allowOutsideClick: false,
      allowEscapeKey: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });

    // Show loading state on button too
    const completeBtn = document.getElementById('completePickSessionBtn');
    const originalText = completeBtn.innerHTML;
    completeBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Completing...';
    completeBtn.disabled = true;

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    fetch('/picking-dispatch/complete-session', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({
        picked_items: pickedItems,
        session_completed_at: new Date().toISOString()
      })
    })
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Session Completed!',
          html: `
            <div class="text-start">
              <p><strong>Pick session completed successfully!</strong></p>
              <p><i class="bi bi-check-circle text-success me-2"></i>Total items picked: <strong>${data.data.total_items_picked}</strong></p>
              <p><i class="bi bi-box text-info me-2"></i>Items processed: <strong>${data.data.items_processed}</strong></p>
              <p class="text-success mt-3">Items are now ready for dispatch.</p>
            </div>
          `,
          showCancelButton: true,
          confirmButtonColor: '#0d6efd',
          cancelButtonColor: '#6c757d',
          confirmButtonText: '<i class="bi bi-truck me-2"></i>Go to Dispatch',
          cancelButtonText: '<i class="bi bi-arrow-clockwise me-2"></i>Reload Page',
          reverseButtons: true
        }).then((result) => {
          if (result.isConfirmed) {
            // Populate dispatch section with completed items
            populateDispatchSection(data.data.items);
            // Scroll to dispatch section
            document.querySelector('#dispatch-section')?.scrollIntoView({ behavior: 'smooth' });
          } else {
            // Reload page to reset the picking interface
            window.location.reload();
          }
        });
      } else {
        console.error('Server error:', data);
        let errorHtml = `<p>Error completing session: <strong>${data.message || 'Unknown error'}</strong></p>`;
        if (data.debug) {
          console.error('Debug info:', data.debug);
          errorHtml += '<p class="text-muted small mt-2">Check browser console for technical details.</p>';
        }
        
        Swal.fire({
          icon: 'error',
          title: 'Session Failed',
          html: errorHtml,
          confirmButtonText: 'OK'
        });
      }
    })
    .catch(error => {
      console.error('Error completing session:', error);
      let errorHtml = '<p>Error completing pick session. Please try again.</p>';
      if (error.message.includes('HTTP 500')) {
        errorHtml += '<p class="text-muted small mt-2">Server error occurred. Check the browser console and server logs for details.</p>';
      }
      
      Swal.fire({
        icon: 'error',
        title: 'Network Error',
        html: errorHtml,
        confirmButtonText: 'OK'
      });
    })
    .finally(() => {
      // Restore button state
      completeBtn.innerHTML = originalText;
      completeBtn.disabled = false;
    });
  }

  // Function to populate dispatch section with picked items
  function populateDispatchSection(completedItems) {
    const dispatchContainer = document.getElementById('dispatchItemsContainer');
    const noItemsMessage = document.getElementById('noDispatchItems');
    
    // Hide the "no items" message
    if (noItemsMessage) {
      noItemsMessage.style.display = 'none';
    }
    
    // Clear existing dynamic items (keep the no items message)
    const existingItems = dispatchContainer.querySelectorAll('.dispatch-item');
    existingItems.forEach(item => item.remove());
    
    // Add each completed item to dispatch section
    completedItems.forEach((item, index) => {
      const priority = getPriorityLevel(item.picked_quantity);
      const destination = getDestination(item.item_name);
      
      const itemHtml = `
        <div class="col-12 dispatch-item">
          <div class="card border-start border-4 border-success">
            <div class="card-body p-3">
              <div class="row align-items-center">
                <div class="col-md-3">
                  <h6 class="mb-1 fw-semibold">${item.item_name}</h6>
                  <small class="text-muted">Qty: ${item.picked_quantity} • Ready for dispatch</small>
                </div>
                <div class="col-md-2">
                  <span class="badge ${priority.class}">${priority.level}</span>
                </div>
                <div class="col-md-3">
                  <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt me-2 text-muted"></i>
                    <span>${destination}</span>
                  </div>
                </div>
                <div class="col-md-2">
                  <span class="badge bg-success">Ready</span>
                </div>
                <div class="col-md-2">
                  <div class="d-flex gap-1">
                    <button class="btn btn-sm btn-outline-primary edit-dispatch-btn" 
                            data-item-id="${item.item_id}" 
                            data-item-name="${item.item_name}"
                            title="Edit Destination">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-success dispatch-now-btn" 
                            data-item-id="${item.item_id}" 
                            data-item-name="${item.item_name}"
                            data-quantity="${item.picked_quantity}"
                            title="Dispatch Now">
                      <i class="bi bi-truck"></i>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
      
      dispatchContainer.insertAdjacentHTML('beforeend', itemHtml);
    });
    
    // Add event listeners for new dispatch buttons
    addDispatchEventListeners();
    
    // Update dispatch section header to show count
    const dispatchHeader = document.querySelector('#dispatch-section h5');
    if (dispatchHeader) {
      dispatchHeader.textContent = `Dispatch Configuration (${completedItems.length} items ready)`;
    }
  }
  
  // Helper function to determine priority based on quantity
  function getPriorityLevel(quantity) {
    if (quantity >= 10) {
      return { level: 'HIGH', class: 'bg-danger' };
    } else if (quantity >= 5) {
      return { level: 'MEDIUM', class: 'bg-warning' };
    } else {
      return { level: 'LOW', class: 'bg-success' };
    }
  }
  
  // Helper function to determine destination based on item name
  function getDestination(itemName) {
    const destinations = {
      // IT Equipment
      'laptop': 'IT Department', 'computer': 'IT Department', 'monitor': 'IT Department',
      'mouse': 'IT Department', 'keyboard': 'IT Department', 'printer': 'IT Department',
      'server': 'IT Department', 'router': 'IT Department', 'switch': 'IT Department',
      
      // Office Supplies
      'chair': 'Admin Office', 'desk': 'Admin Office', 'paper': 'Admin Office',
      'pen': 'Admin Office', 'folder': 'Admin Office', 'stapler': 'Admin Office',
      
      // Vehicles & Logistics
      'truck': 'Logistics Department', 'vehicle': 'Logistics Department',
      'van': 'Logistics Department', 'forklift': 'Logistics Department',
      
      // Tools & Maintenance
      'tool': 'Maintenance Department', 'wrench': 'Maintenance Department',
      'hammer': 'Maintenance Department', 'drill': 'Maintenance Department',
      
      // General Equipment
      'equipment': 'Operations Department', 'machine': 'Operations Department',
      
      // Medical/Safety
      'medical': 'Medical Department', 'safety': 'Safety Department',
      'first aid': 'Medical Department', 'helmet': 'Safety Department'
    };
    
    const itemLower = itemName.toLowerCase();
    
    // Check for keyword matches
    for (const [keyword, destination] of Object.entries(destinations)) {
      if (itemLower.includes(keyword)) {
        return destination;
      }
    }
    
    // Special handling for unclear items
    if (itemName.length <= 10) {
      return 'Admin Office'; // Short names likely office supplies
    }
    
    return 'Warehouse'; // Default destination for unknown items
  }
  
  // Add event listeners for dispatch buttons
  function addDispatchEventListeners() {
    // Edit button event listeners are now handled in initializeEditDispatch()
    
    // Dispatch now buttons
    document.querySelectorAll('.dispatch-now-btn').forEach(button => {
      button.addEventListener('click', function() {
        const itemId = this.getAttribute('data-item-id');
        const itemName = this.getAttribute('data-item-name');
        const quantity = this.getAttribute('data-quantity');
        dispatchItemNow(itemId, itemName, quantity);
      });
    });
  }
  
  // Old edit function removed - now using modal-based editing
  
  // Function to dispatch item immediately
  function dispatchItemNow(itemId, itemName, quantity) {
    Swal.fire({
      title: 'Dispatch Item?',
      html: `
        <div class="text-start">
          <p><strong>Item:</strong> ${itemName}</p>
          <p><strong>Quantity:</strong> ${quantity}</p>
          <p class="text-warning"><i class="bi bi-exclamation-triangle me-2"></i>This will mark the item as dispatched.</p>
        </div>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#198754',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="bi bi-truck me-2"></i>Dispatch Now',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading
        Swal.fire({
          title: 'Dispatching Item...',
          text: 'Please wait while we process the dispatch.',
          allowOutsideClick: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        
        // Call backend to remove item from session
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('/picking-dispatch/remove-from-dispatch', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            item_id: itemId
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            // Remove item from dispatch list
            const itemCard = document.querySelector(`[data-item-id="${itemId}"]`).closest('.dispatch-item');
            itemCard.remove();
            
            // Remove route data from storage
            if (dispatchRoutes[itemId]) {
              delete dispatchRoutes[itemId];
              saveDispatchRoutesToStorage();
            }
            
            // Check if no items left
            if (data.remaining_count === 0) {
              document.getElementById('noDispatchItems').style.display = 'block';
              const dispatchHeader = document.querySelector('#dispatch-section h5');
              if (dispatchHeader) {
                dispatchHeader.textContent = 'Dispatch Configuration';
              }
            } else {
              const dispatchHeader = document.querySelector('#dispatch-section h5');
              if (dispatchHeader) {
                dispatchHeader.textContent = `Dispatch Configuration (${data.remaining_count} items ready)`;
              }
            }
            
            Swal.fire({
              icon: 'success',
              title: 'Item Dispatched!',
              text: `${itemName} has been successfully dispatched.`,
              timer: 3000,
              timerProgressBar: true
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Dispatch Failed',
              text: data.message || 'Failed to dispatch item',
              confirmButtonText: 'OK'
            });
          }
        })
        .catch(error => {
          console.error('Error dispatching item:', error);
          Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Failed to dispatch item. Please try again.',
            confirmButtonText: 'OK'
          });
        });
      }
    });
  }

  // Initialize dispatch map with location picking
  let dispatchMap;
  let pickupMarker;
  let destinationMarker;
  let routeLine;
  let pickupLocation = null;
  let destinationLocation = null;
  let routeCoordinates = null; // Store actual route coordinates
  let routeData = null; // Store complete route data
  let isSettingPickup = false;
  let isSettingDestination = false;

  function initializeDispatchMap() {
    // Initialize map centered on Manila, Philippines (you can change this)
    dispatchMap = L.map('dispatchMap').setView([14.5995, 120.9842], 11);
    
    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(dispatchMap);
    
    // Add click event listener for setting locations
    dispatchMap.on('click', function(e) {
      if (isSettingPickup) {
        setPickupLocation(e.latlng);
      } else if (isSettingDestination) {
        setDestinationLocation(e.latlng);
      }
    });
    
    // Add button event listeners
    document.getElementById('setPickupLocationBtn').addEventListener('click', function() {
      startSettingPickup();
    });
    
    document.getElementById('setDestinationBtn').addEventListener('click', function() {
      startSettingDestination();
    });
    
    document.getElementById('calculateRouteBtn').addEventListener('click', function() {
      calculateRoute();
    });
    
    // Add expand map button listener
    document.getElementById('expandMapBtn').addEventListener('click', function() {
      openFullscreenMap();
    });
  }
  
  function startSettingPickup() {
    isSettingPickup = true;
    isSettingDestination = false;
    
    // Update button states
    document.getElementById('setPickupLocationBtn').innerHTML = '<i class="bi bi-cursor me-1"></i>Click on Map';
    document.getElementById('setPickupLocationBtn').classList.add('active');
    document.getElementById('setDestinationBtn').classList.remove('active');
    
    // Change cursor
    document.getElementById('dispatchMap').style.cursor = 'crosshair';
    
    Swal.fire({
      icon: 'info',
      title: 'Set Pickup Location',
      text: 'Click anywhere on the map to set the pickup location.',
      timer: 3000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  function startSettingDestination() {
    isSettingPickup = false;
    isSettingDestination = true;
    
    // Update button states
    document.getElementById('setDestinationBtn').innerHTML = '<i class="bi bi-cursor me-1"></i>Click on Map';
    document.getElementById('setDestinationBtn').classList.add('active');
    document.getElementById('setPickupLocationBtn').classList.remove('active');
    
    // Change cursor
    document.getElementById('dispatchMap').style.cursor = 'crosshair';
    
    Swal.fire({
      icon: 'info',
      title: 'Set Destination',
      text: 'Click anywhere on the map to set the destination location.',
      timer: 3000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  function setPickupLocation(latlng) {
    pickupLocation = latlng;
    
    // Remove existing pickup marker
    if (pickupMarker) {
      dispatchMap.removeLayer(pickupMarker);
    }
    
    // Add new pickup marker (blue)
    pickupMarker = L.marker([latlng.lat, latlng.lng], {
      icon: L.divIcon({
        className: 'custom-marker pickup-marker',
        html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
      })
    }).addTo(dispatchMap);
    
    // Update UI with coordinates
    document.getElementById('pickupLocationText').innerHTML = `
      <strong>Lat:</strong> ${latlng.lat.toFixed(6)}<br>
      <strong>Lng:</strong> ${latlng.lng.toFixed(6)}
    `;
    
    // Get address from coordinates
    getAddressFromCoordinates(latlng.lat, latlng.lng, 'pickup');
    
    // Reset button
    document.getElementById('setPickupLocationBtn').innerHTML = '<i class="bi bi-geo-alt me-1"></i>Set Pickup';
    document.getElementById('setPickupLocationBtn').classList.remove('active');
    document.getElementById('dispatchMap').style.cursor = '';
    
    isSettingPickup = false;
    
    // Show calculate route button if both locations are set
    checkBothLocationsSet();
    
    Swal.fire({
      icon: 'success',
      title: 'Pickup Location Set!',
      text: 'Pickup location has been marked on the map.',
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  function setDestinationLocation(latlng) {
    destinationLocation = latlng;
    
    // Remove existing destination marker
    if (destinationMarker) {
      dispatchMap.removeLayer(destinationMarker);
    }
    
    // Add new destination marker (green)
    destinationMarker = L.marker([latlng.lat, latlng.lng], {
      icon: L.divIcon({
        className: 'custom-marker destination-marker',
        html: '<div style="background-color: #198754; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
      })
    }).addTo(dispatchMap);
    
    // Update UI with coordinates
    document.getElementById('destinationLocationText').innerHTML = `
      <strong>Lat:</strong> ${latlng.lat.toFixed(6)}<br>
      <strong>Lng:</strong> ${latlng.lng.toFixed(6)}
    `;
    
    // Get address from coordinates
    getAddressFromCoordinates(latlng.lat, latlng.lng, 'destination');
    
    // Reset button
    document.getElementById('setDestinationBtn').innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i>Set Destination';
    document.getElementById('setDestinationBtn').classList.remove('active');
    document.getElementById('dispatchMap').style.cursor = '';
    
    isSettingDestination = false;
    
    // Show calculate route button if both locations are set
    checkBothLocationsSet();
    
    Swal.fire({
      icon: 'success',
      title: 'Destination Set!',
      text: 'Destination has been marked on the map.',
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  function checkBothLocationsSet() {
    if (pickupLocation && destinationLocation) {
      document.getElementById('calculateRouteBtn').style.display = 'block';
    }
  }
  
  function calculateRoute() {
    if (!pickupLocation || !destinationLocation) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Locations',
        text: 'Please set both pickup and destination locations first.',
        confirmButtonText: 'OK'
      });
      return;
    }
    
    // Show loading
    Swal.fire({
      title: 'Calculating Route...',
      text: 'Please wait while we calculate the optimal route.',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Get actual road route using OSRM (Open Source Routing Machine)
    const routingUrl = `https://router.project-osrm.org/route/v1/driving/${pickupLocation.lng},${pickupLocation.lat};${destinationLocation.lng},${destinationLocation.lat}?overview=full&geometries=geojson`;
    
    console.log('Routing URL:', routingUrl);
    
    // Add timeout to the fetch request
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout
    
    fetch(routingUrl, { 
      signal: controller.signal,
      headers: {
        'Accept': 'application/json',
      }
    })
      .then(response => {
        clearTimeout(timeoutId);
        console.log('Response status:', response.status);
        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('Routing response:', data);
        if (data.routes && data.routes.length > 0) {
          const route = data.routes[0];
          const coordinates = route.geometry.coordinates;
          
          // Convert coordinates to Leaflet format [lat, lng]
          const routeCoords = coordinates.map(coord => [coord[1], coord[0]]);
          
          // Store route data for later use
          routeCoordinates = routeCoords;
          routeData = {
            distance: route.distance / 1000,
            duration: Math.round(route.duration / 60),
            coordinates: routeCoords
          };
          
          // Remove existing route line
          if (routeLine) {
            dispatchMap.removeLayer(routeLine);
          }
          
          // Draw actual road route
          routeLine = L.polyline(routeCoords, {
            color: '#ffc107',
            weight: 4,
            opacity: 0.8
          }).addTo(dispatchMap);
          
          // Fit map to show the route
          const group = new L.featureGroup([pickupMarker, destinationMarker, routeLine]);
          dispatchMap.fitBounds(group.getBounds().pad(0.1));
          
          // Update route info with actual data
          const distance = (route.distance / 1000); // Convert meters to kilometers
          const estimatedTime = Math.round(route.duration / 60); // Convert seconds to minutes
          
          document.getElementById('routeDistance').textContent = distance.toFixed(1) + ' km';
          document.getElementById('routeTime').textContent = estimatedTime + ' min';
          document.getElementById('routeInfo').style.display = 'block';
          
        } else {
          // Fallback to straight line if routing fails
          drawStraightLineRoute();
        }
      })
      .catch(error => {
        clearTimeout(timeoutId);
        console.error('Routing error:', error);
        
        // Close loading dialog first
        Swal.close();
        
        // Show specific error message based on error type
        let errorMessage = 'Unable to calculate road route. ';
        if (error.name === 'AbortError') {
          errorMessage += 'Request timed out after 10 seconds.';
        } else if (error.message.includes('HTTP error')) {
          errorMessage += 'Routing service is temporarily unavailable.';
        } else if (error.message.includes('Failed to fetch')) {
          errorMessage += 'Network connection issue.';
        } else {
          errorMessage += 'Please try again.';
        }
        
        // Immediately fall back to straight line with informative message
        Swal.fire({
          icon: 'info',
          title: 'Using Direct Route',
          html: `
            <div class="text-start">
              <p>${errorMessage}</p>
              <p class="text-info mt-2"><strong>Showing direct distance instead:</strong></p>
              <ul class="text-muted small">
                <li>Straight-line distance between points</li>
                <li>Estimated travel time (may vary)</li>
                <li>Route may cross obstacles</li>
              </ul>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: 'Continue with Direct Route',
          cancelButtonText: 'Try Again',
          confirmButtonColor: '#ffc107',
          cancelButtonColor: '#6c757d'
        }).then((result) => {
          if (result.isConfirmed) {
            drawStraightLineRoute();
          } else if (result.isDismissed) {
            // User wants to try again
            calculateRoute();
          }
        });
      });
  }
  
  function drawStraightLineRoute() {
    // Fallback straight-line route
    const distance = calculateDistance(pickupLocation, destinationLocation);
    const estimatedTime = Math.round(distance * 2);
    
    if (routeLine) {
      dispatchMap.removeLayer(routeLine);
    }
    
    routeLine = L.polyline([
      [pickupLocation.lat, pickupLocation.lng],
      [destinationLocation.lat, destinationLocation.lng]
    ], {
      color: '#ffc107',
      weight: 4,
      opacity: 0.8,
      dashArray: '10, 5'
    }).addTo(dispatchMap);
    
    const group = new L.featureGroup([pickupMarker, destinationMarker, routeLine]);
    dispatchMap.fitBounds(group.getBounds().pad(0.1));
    
    document.getElementById('routeDistance').textContent = distance.toFixed(1) + ' km';
    document.getElementById('routeTime').textContent = estimatedTime + ' min';
    document.getElementById('routeInfo').style.display = 'block';
    
    setTimeout(() => {
      Swal.fire({
        icon: 'success',
        title: 'Route Calculated!',
        html: `
          <div class="text-start">
            <p><strong>Distance:</strong> ${distance.toFixed(1)} km</p>
            <p><strong>Estimated Time:</strong> ${estimatedTime} minutes</p>
            <p class="text-muted small">Route has been drawn on the map.</p>
          </div>
        `,
        confirmButtonText: 'OK'
      });
    }, 1000);
  }
  
  function calculateDistance(pos1, pos2) {
    const R = 6371; // Earth's radius in kilometers
    const dLat = (pos2.lat - pos1.lat) * Math.PI / 180;
    const dLon = (pos2.lng - pos1.lng) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(pos1.lat * Math.PI / 180) * Math.cos(pos2.lat * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }
  
  // Reverse geocoding function to get address from coordinates
  function getAddressFromCoordinates(lat, lng, locationType) {
    // Show loading state
    const addressElement = document.getElementById(`${locationType}LocationAddress`);
    
    // Check if element exists before trying to modify it
    if (!addressElement) {
      console.log(`Address element not found for location type: ${locationType}`);
      return;
    }
    
    addressElement.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Getting address...';
    addressElement.style.display = 'block';
    
    // Use Nominatim (OpenStreetMap) reverse geocoding service
    const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`;
    
    fetch(url)
      .then(response => response.json())
      .then(data => {
        if (data && data.display_name) {
          // Format the address nicely
          const address = formatAddress(data);
          addressElement.innerHTML = `<i class="bi bi-geo-alt-fill me-1"></i>${address}`;
          
          // Add tooltip with full address
          addressElement.title = data.display_name;
          
          // Store address for later use
          if (locationType === 'pickup') {
            pickupLocation.address = address;
            pickupLocation.fullAddress = data.display_name;
          } else if (locationType === 'destination') {
            destinationLocation.address = address;
            destinationLocation.fullAddress = data.display_name;
          } else if (locationType === 'fullscreenPickup') {
            pickupLocation.address = address;
            pickupLocation.fullAddress = data.display_name;
          } else if (locationType === 'fullscreenDestination') {
            destinationLocation.address = address;
            destinationLocation.fullAddress = data.display_name;
          } else if (locationType === 'editPickup') {
            editPickupLocation.address = address;
            editPickupLocation.fullAddress = data.display_name;
          } else if (locationType === 'editDestination') {
            editDestinationLocation.address = address;
            editDestinationLocation.fullAddress = data.display_name;
          }
        } else {
          addressElement.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Address not found';
        }
      })
      .catch(error => {
        console.error('Geocoding error:', error);
        addressElement.innerHTML = '<i class="bi bi-exclamation-triangle me-1"></i>Unable to get address';
      });
  }
  
  // Format address for display
  function formatAddress(geocodeData) {
    const address = geocodeData.address || {};
    let formattedAddress = '';
    
    // Build address from components
    const components = [];
    
    // Add house number and road
    if (address.house_number && address.road) {
      components.push(`${address.house_number} ${address.road}`);
    } else if (address.road) {
      components.push(address.road);
    }
    
    // Add neighborhood or suburb
    if (address.neighbourhood) {
      components.push(address.neighbourhood);
    } else if (address.suburb) {
      components.push(address.suburb);
    }
    
    // Add city
    if (address.city) {
      components.push(address.city);
    } else if (address.town) {
      components.push(address.town);
    } else if (address.municipality) {
      components.push(address.municipality);
    }
    
    // Add state/province
    if (address.state) {
      components.push(address.state);
    }
    
    // Join components with commas, limit to first 3 for brevity
    formattedAddress = components.slice(0, 3).join(', ');
    
    // Fallback to display_name if no components found
    if (!formattedAddress && geocodeData.display_name) {
      const parts = geocodeData.display_name.split(',');
      formattedAddress = parts.slice(0, 3).join(',').trim();
    }
    
    return formattedAddress || 'Unknown Location';
  }
  
  // Alternative routing function using different service
  function tryAlternativeRouting() {
    return new Promise((resolve) => {
      // Try OpenRouteService as alternative
      const orsUrl = `https://api.openrouteservice.org/v2/directions/driving-car?start=${pickupLocation.lng},${pickupLocation.lat}&end=${destinationLocation.lng},${destinationLocation.lat}`;
      
      console.log('Trying OpenRouteService:', orsUrl);
      
      fetch(orsUrl, {
        headers: {
          'Accept': 'application/json',
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`ORS HTTP error! status: ${response.status}`);
        }
        return response.json();
      })
      .then(data => {
        console.log('ORS Response:', data);
        if (data.features && data.features.length > 0) {
          const route = data.features[0];
          const coordinates = route.geometry.coordinates;
          
          // Convert coordinates to Leaflet format [lat, lng]
          const routeCoords = coordinates.map(coord => [coord[1], coord[0]]);
          
          // Store route data
          routeCoordinates = routeCoords;
          routeData = {
            distance: route.properties.segments[0].distance / 1000,
            duration: Math.round(route.properties.segments[0].duration / 60),
            coordinates: routeCoords
          };
          
          // Remove existing route line
          if (routeLine) {
            dispatchMap.removeLayer(routeLine);
          }
          
          // Draw route
          routeLine = L.polyline(routeCoords, {
            color: '#ffc107',
            weight: 4,
            opacity: 0.8
          }).addTo(dispatchMap);
          
          // Fit map and update UI
          const group = new L.featureGroup([pickupMarker, destinationMarker, routeLine]);
          dispatchMap.fitBounds(group.getBounds().pad(0.1));
          
          document.getElementById('routeDistance').textContent = routeData.distance.toFixed(1) + ' km';
          document.getElementById('routeTime').textContent = routeData.duration + ' min';
          document.getElementById('routeInfo').style.display = 'block';
          
          Swal.fire({
            icon: 'success',
            title: 'Route Calculated!',
            html: `
              <div class="text-start">
                <p><strong>Distance:</strong> ${routeData.distance.toFixed(1)} km</p>
                <p><strong>Estimated Time:</strong> ${routeData.duration} minutes</p>
                <p class="text-info small">Route calculated using alternative service.</p>
              </div>
            `,
            confirmButtonText: 'OK'
          });
          
          resolve(true);
        } else {
          resolve(false);
        }
      })
      .catch(error => {
        console.error('Alternative routing failed:', error);
        resolve(false);
      });
    });
  }
  
  // Fullscreen map functionality
  let fullscreenMap;
  let fullscreenPickupMarker;
  let fullscreenDestinationMarker;
  let fullscreenRouteLine;
  let isFullscreenSettingPickup = false;
  let isFullscreenSettingDestination = false;
  
  function openFullscreenMap() {
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('fullscreenMapModal'));
    modal.show();
    
    // Initialize fullscreen map after modal is shown
    setTimeout(() => {
      initializeFullscreenMap();
    }, 300);
  }
  
  function initializeFullscreenMap() {
    // Initialize fullscreen map
    fullscreenMap = L.map('fullscreenDispatchMap').setView([14.5995, 120.9842], 11);
    
    // Add tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(fullscreenMap);
    
    // Copy existing markers and route from main map
    if (pickupLocation) {
      fullscreenPickupMarker = L.marker([pickupLocation.lat, pickupLocation.lng], {
        icon: L.divIcon({
          className: 'custom-marker pickup-marker',
          html: '<div style="background-color: #0d6efd; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
          iconSize: [25, 25],
          iconAnchor: [12.5, 12.5]
        })
      }).addTo(fullscreenMap);
      
      // Update fullscreen UI
      document.getElementById('fullscreenPickupLocationText').innerHTML = `
        <strong>Lat:</strong> ${pickupLocation.lat.toFixed(6)}<br>
        <strong>Lng:</strong> ${pickupLocation.lng.toFixed(6)}
      `;
      
      if (pickupLocation.address) {
        document.getElementById('fullscreenPickupLocationAddress').innerHTML = `<i class="bi bi-geo-alt-fill me-1"></i>${pickupLocation.address}`;
        document.getElementById('fullscreenPickupLocationAddress').style.display = 'block';
      }
    }
    
    if (destinationLocation) {
      fullscreenDestinationMarker = L.marker([destinationLocation.lat, destinationLocation.lng], {
        icon: L.divIcon({
          className: 'custom-marker destination-marker',
          html: '<div style="background-color: #198754; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
          iconSize: [25, 25],
          iconAnchor: [12.5, 12.5]
        })
      }).addTo(fullscreenMap);
      
      // Update fullscreen UI
      document.getElementById('fullscreenDestinationLocationText').innerHTML = `
        <strong>Lat:</strong> ${destinationLocation.lat.toFixed(6)}<br>
        <strong>Lng:</strong> ${destinationLocation.lng.toFixed(6)}
      `;
      
      if (destinationLocation.address) {
        document.getElementById('fullscreenDestinationLocationAddress').innerHTML = `<i class="bi bi-geo-alt-fill me-1"></i>${destinationLocation.address}`;
        document.getElementById('fullscreenDestinationLocationAddress').style.display = 'block';
      }
    }
    
    // Copy route if exists
    if (pickupLocation && destinationLocation) {
      if (routeData && routeData.coordinates) {
        // Use existing road route data
        fullscreenRouteLine = L.polyline(routeData.coordinates, {
          color: '#ffc107',
          weight: 5,
          opacity: 0.8
        }).addTo(fullscreenMap);
        
        // Show route info from stored data
        document.getElementById('fullscreenRouteDistance').textContent = routeData.distance.toFixed(1) + ' km';
        document.getElementById('fullscreenRouteTime').textContent = routeData.duration + ' min';
      } else {
        // Fallback to straight line
        fullscreenRouteLine = L.polyline([
          [pickupLocation.lat, pickupLocation.lng],
          [destinationLocation.lat, destinationLocation.lng]
        ], {
          color: '#ffc107',
          weight: 5,
          opacity: 0.8,
          dashArray: '10, 5'
        }).addTo(fullscreenMap);
        
        // Show estimated info
        const distance = calculateDistance(pickupLocation, destinationLocation);
        const estimatedTime = Math.round(distance * 2);
        document.getElementById('fullscreenRouteDistance').textContent = distance.toFixed(1) + ' km';
        document.getElementById('fullscreenRouteTime').textContent = estimatedTime + ' min';
      }
      
      document.getElementById('fullscreenRouteInfo').style.display = 'block';
      document.getElementById('fullscreenCalculateRouteBtn').style.display = 'inline-block';
      
      // Fit bounds
      const group = new L.featureGroup([fullscreenPickupMarker, fullscreenDestinationMarker, fullscreenRouteLine]);
      fullscreenMap.fitBounds(group.getBounds().pad(0.1));
    }
    
    // Add click event listener
    fullscreenMap.on('click', function(e) {
      if (isFullscreenSettingPickup) {
        setFullscreenPickupLocation(e.latlng);
      } else if (isFullscreenSettingDestination) {
        setFullscreenDestinationLocation(e.latlng);
      }
    });
    
    // Add button event listeners
    document.getElementById('fullscreenSetPickupBtn').addEventListener('click', function() {
      startFullscreenSettingPickup();
    });
    
    document.getElementById('fullscreenSetDestinationBtn').addEventListener('click', function() {
      startFullscreenSettingDestination();
    });
    
    document.getElementById('fullscreenCalculateRouteBtn').addEventListener('click', function() {
      calculateFullscreenRoute();
    });
    
    document.getElementById('applyRouteBtn').addEventListener('click', function() {
      applyRouteToMainMap();
    });
  }
  
  function startFullscreenSettingPickup() {
    isFullscreenSettingPickup = true;
    isFullscreenSettingDestination = false;
    
    document.getElementById('fullscreenSetPickupBtn').innerHTML = '<i class="bi bi-cursor me-1"></i>Click on Map';
    document.getElementById('fullscreenSetPickupBtn').classList.add('active');
    document.getElementById('fullscreenSetDestinationBtn').classList.remove('active');
    
    document.getElementById('fullscreenDispatchMap').style.cursor = 'crosshair';
  }
  
  function startFullscreenSettingDestination() {
    isFullscreenSettingPickup = false;
    isFullscreenSettingDestination = true;
    
    document.getElementById('fullscreenSetDestinationBtn').innerHTML = '<i class="bi bi-cursor me-1"></i>Click on Map';
    document.getElementById('fullscreenSetDestinationBtn').classList.add('active');
    document.getElementById('fullscreenSetPickupBtn').classList.remove('active');
    
    document.getElementById('fullscreenDispatchMap').style.cursor = 'crosshair';
  }
  
  function setFullscreenPickupLocation(latlng) {
    pickupLocation = latlng;
    
    if (fullscreenPickupMarker) {
      fullscreenMap.removeLayer(fullscreenPickupMarker);
    }
    
    fullscreenPickupMarker = L.marker([latlng.lat, latlng.lng], {
      icon: L.divIcon({
        className: 'custom-marker pickup-marker',
        html: '<div style="background-color: #0d6efd; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [25, 25],
        iconAnchor: [12.5, 12.5]
      })
    }).addTo(fullscreenMap);
    
    document.getElementById('fullscreenPickupLocationText').innerHTML = `
      <strong>Lat:</strong> ${latlng.lat.toFixed(6)}<br>
      <strong>Lng:</strong> ${latlng.lng.toFixed(6)}
    `;
    
    getAddressFromCoordinates(latlng.lat, latlng.lng, 'fullscreenPickup');
    
    document.getElementById('fullscreenSetPickupBtn').innerHTML = '<i class="bi bi-geo-alt me-1"></i>Set Pickup';
    document.getElementById('fullscreenSetPickupBtn').classList.remove('active');
    document.getElementById('fullscreenDispatchMap').style.cursor = '';
    
    isFullscreenSettingPickup = false;
    checkFullscreenBothLocationsSet();
  }
  
  function setFullscreenDestinationLocation(latlng) {
    destinationLocation = latlng;
    
    if (fullscreenDestinationMarker) {
      fullscreenMap.removeLayer(fullscreenDestinationMarker);
    }
    
    fullscreenDestinationMarker = L.marker([latlng.lat, latlng.lng], {
      icon: L.divIcon({
        className: 'custom-marker destination-marker',
        html: '<div style="background-color: #198754; width: 25px; height: 25px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [25, 25],
        iconAnchor: [12.5, 12.5]
      })
    }).addTo(fullscreenMap);
    
    document.getElementById('fullscreenDestinationLocationText').innerHTML = `
      <strong>Lat:</strong> ${latlng.lat.toFixed(6)}<br>
      <strong>Lng:</strong> ${latlng.lng.toFixed(6)}
    `;
    
    getAddressFromCoordinates(latlng.lat, latlng.lng, 'fullscreenDestination');
    
    document.getElementById('fullscreenSetDestinationBtn').innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i>Set Destination';
    document.getElementById('fullscreenSetDestinationBtn').classList.remove('active');
    document.getElementById('fullscreenDispatchMap').style.cursor = '';
    
    isFullscreenSettingDestination = false;
    checkFullscreenBothLocationsSet();
  }
  
  function checkFullscreenBothLocationsSet() {
    if (pickupLocation && destinationLocation) {
      document.getElementById('fullscreenCalculateRouteBtn').style.display = 'inline-block';
    }
  }
  
  function calculateFullscreenRoute() {
    if (!pickupLocation || !destinationLocation) return;
    
    // Show loading
    Swal.fire({
      title: 'Calculating Route...',
      text: 'Finding the best road route...',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Get actual road route using OSRM
    const routingUrl = `https://router.project-osrm.org/route/v1/driving/${pickupLocation.lng},${pickupLocation.lat};${destinationLocation.lng},${destinationLocation.lat}?overview=full&geometries=geojson`;
    
    fetch(routingUrl)
      .then(response => response.json())
      .then(data => {
        if (data.routes && data.routes.length > 0) {
          const route = data.routes[0];
          const coordinates = route.geometry.coordinates;
          
          // Convert coordinates to Leaflet format [lat, lng]
          const routeCoords = coordinates.map(coord => [coord[1], coord[0]]);
          
          // Store route data for transfer to main map
          routeCoordinates = routeCoords;
          routeData = {
            distance: route.distance / 1000,
            duration: Math.round(route.duration / 60),
            coordinates: routeCoords
          };
          
          // Remove existing route line
          if (fullscreenRouteLine) {
            fullscreenMap.removeLayer(fullscreenRouteLine);
          }
          
          // Draw actual road route
          fullscreenRouteLine = L.polyline(routeCoords, {
            color: '#ffc107',
            weight: 5,
            opacity: 0.8
          }).addTo(fullscreenMap);
          
          // Fit map to show the route
          const group = new L.featureGroup([fullscreenPickupMarker, fullscreenDestinationMarker, fullscreenRouteLine]);
          fullscreenMap.fitBounds(group.getBounds().pad(0.1));
          
          // Update route info with actual data
          const distance = (route.distance / 1000); // Convert meters to kilometers
          const estimatedTime = Math.round(route.duration / 60); // Convert seconds to minutes
          
          document.getElementById('fullscreenRouteDistance').textContent = distance.toFixed(1) + ' km';
          document.getElementById('fullscreenRouteTime').textContent = estimatedTime + ' min';
          document.getElementById('fullscreenRouteInfo').style.display = 'block';
          
          Swal.fire({
            icon: 'success',
            title: 'Route Found!',
            html: `
              <div class="text-start">
                <p><strong>Distance:</strong> ${distance.toFixed(1)} km</p>
                <p><strong>Estimated Time:</strong> ${estimatedTime} minutes</p>
                <p class="text-success small">Route follows actual roads and traffic patterns.</p>
              </div>
            `,
            confirmButtonText: 'OK'
          });
          
        } else {
          // Fallback to straight line if routing fails
          drawFullscreenStraightLineRoute();
        }
      })
      .catch(error => {
        console.error('Routing error:', error);
        // Fallback to straight line if routing fails
        drawFullscreenStraightLineRoute();
      });
  }
  
  function drawFullscreenStraightLineRoute() {
    // Fallback straight-line route for fullscreen
    if (fullscreenRouteLine) {
      fullscreenMap.removeLayer(fullscreenRouteLine);
    }
    
    fullscreenRouteLine = L.polyline([
      [pickupLocation.lat, pickupLocation.lng],
      [destinationLocation.lat, destinationLocation.lng]
    ], {
      color: '#ffc107',
      weight: 5,
      opacity: 0.8,
      dashArray: '10, 5'
    }).addTo(fullscreenMap);
    
    const group = new L.featureGroup([fullscreenPickupMarker, fullscreenDestinationMarker, fullscreenRouteLine]);
    fullscreenMap.fitBounds(group.getBounds().pad(0.1));
    
    const distance = calculateDistance(pickupLocation, destinationLocation);
    const estimatedTime = Math.round(distance * 2);
    
    document.getElementById('fullscreenRouteDistance').textContent = distance.toFixed(1) + ' km';
    document.getElementById('fullscreenRouteTime').textContent = estimatedTime + ' min';
    document.getElementById('fullscreenRouteInfo').style.display = 'block';
    
    Swal.fire({
      icon: 'warning',
      title: 'Straight Line Route',
      text: 'Could not find road route. Showing direct distance.',
      confirmButtonText: 'OK'
    });
  }
  
  function applyRouteToMainMap() {
    // Update main map with fullscreen selections
    if (pickupLocation && pickupMarker) {
      dispatchMap.removeLayer(pickupMarker);
    }
    if (destinationLocation && destinationMarker) {
      dispatchMap.removeLayer(destinationMarker);
    }
    if (routeLine) {
      dispatchMap.removeLayer(routeLine);
    }
    
    // Add markers to main map
    if (pickupLocation) {
      pickupMarker = L.marker([pickupLocation.lat, pickupLocation.lng], {
        icon: L.divIcon({
          className: 'custom-marker pickup-marker',
          html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
          iconSize: [20, 20],
          iconAnchor: [10, 10]
        })
      }).addTo(dispatchMap);
      
      document.getElementById('pickupLocationText').innerHTML = `
        <strong>Lat:</strong> ${pickupLocation.lat.toFixed(6)}<br>
        <strong>Lng:</strong> ${pickupLocation.lng.toFixed(6)}
      `;
      
      if (pickupLocation.address) {
        document.getElementById('pickupLocationAddress').innerHTML = `<i class="bi bi-geo-alt-fill me-1"></i>${pickupLocation.address}`;
        document.getElementById('pickupLocationAddress').style.display = 'block';
      }
    }
    
    if (destinationLocation) {
      destinationMarker = L.marker([destinationLocation.lat, destinationLocation.lng], {
        icon: L.divIcon({
          className: 'custom-marker destination-marker',
          html: '<div style="background-color: #198754; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
          iconSize: [20, 20],
          iconAnchor: [10, 10]
        })
      }).addTo(dispatchMap);
      
      document.getElementById('destinationLocationText').innerHTML = `
        <strong>Lat:</strong> ${destinationLocation.lat.toFixed(6)}<br>
        <strong>Lng:</strong> ${destinationLocation.lng.toFixed(6)}
      `;
      
      if (destinationLocation.address) {
        document.getElementById('destinationLocationAddress').innerHTML = `<i class="bi bi-geo-alt-fill me-1"></i>${destinationLocation.address}`;
        document.getElementById('destinationLocationAddress').style.display = 'block';
      }
    }
    
    // Add route to main map using stored route data
    if (pickupLocation && destinationLocation && routeData) {
      // Use the actual road route coordinates
      routeLine = L.polyline(routeData.coordinates, {
        color: '#ffc107',
        weight: 4,
        opacity: 0.8
        // No dashArray = solid line for road route
      }).addTo(dispatchMap);
      
      const group = new L.featureGroup([pickupMarker, destinationMarker, routeLine]);
      dispatchMap.fitBounds(group.getBounds().pad(0.1));
      
      // Use actual route data
      document.getElementById('routeDistance').textContent = routeData.distance.toFixed(1) + ' km';
      document.getElementById('routeTime').textContent = routeData.duration + ' min';
      document.getElementById('routeInfo').style.display = 'block';
      document.getElementById('calculateRouteBtn').style.display = 'block';
    } else if (pickupLocation && destinationLocation) {
      // Fallback to straight line if no route data
      routeLine = L.polyline([
        [pickupLocation.lat, pickupLocation.lng],
        [destinationLocation.lat, destinationLocation.lng]
      ], {
        color: '#ffc107',
        weight: 4,
        opacity: 0.8,
        dashArray: '10, 5'
      }).addTo(dispatchMap);
      
      const group = new L.featureGroup([pickupMarker, destinationMarker, routeLine]);
      dispatchMap.fitBounds(group.getBounds().pad(0.1));
      
      const distance = calculateDistance(pickupLocation, destinationLocation);
      const estimatedTime = Math.round(distance * 2);
      
      document.getElementById('routeDistance').textContent = distance.toFixed(1) + ' km';
      document.getElementById('routeTime').textContent = estimatedTime + ' min';
      document.getElementById('routeInfo').style.display = 'block';
      document.getElementById('calculateRouteBtn').style.display = 'block';
    }
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('fullscreenMapModal'));
    modal.hide();
    
    Swal.fire({
      icon: 'success',
      title: 'Route Applied!',
      text: 'The route has been applied to the main map.',
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  // Edit dispatch functionality
  let editDispatchMap;
  let editPickupMarker;
  let editDestinationMarker;
  let editRouteLine;
  let editPickupLocation = null;
  let editDestinationLocation = null;
  let editRouteData = null;
  let currentEditItemId = null;
  let isEditSettingPickup = false;
  let isEditSettingDestination = false;
  
  // Store dispatch routes for each item
  let dispatchRoutes = {};
  
  function initializeEditDispatch() {
    // Add event listeners for edit buttons using event delegation
    document.addEventListener('click', function(e) {
      if (e.target.closest('.edit-dispatch-btn')) {
        e.preventDefault();
        e.stopPropagation();
        
        const btn = e.target.closest('.edit-dispatch-btn');
        const itemId = btn.getAttribute('data-item-id');
        const itemName = btn.getAttribute('data-item-name');
        const destination = btn.getAttribute('data-destination');
        
        console.log('Opening edit modal for:', itemId, itemName, destination);
        openEditDispatchModal(itemId, itemName, destination);
      }
    });
    
    // Custom destination dropdown handler
    document.getElementById('editDestinationSelect').addEventListener('change', function() {
      const customGroup = document.getElementById('customDestinationGroup');
      if (this.value === 'Custom') {
        customGroup.style.display = 'block';
      } else {
        customGroup.style.display = 'none';
      }
    });
    
    // Save route button
    document.getElementById('saveDispatchRoute').addEventListener('click', saveDispatchRouteData);
  }
  
  function openEditDispatchModal(itemId, itemName, destination) {
    currentEditItemId = itemId;
    
    // Reset previous state
    editPickupLocation = null;
    editDestinationLocation = null;
    editRouteData = null;
    
    // Clear previous map if exists
    if (editDispatchMap) {
      editDispatchMap.remove();
      editDispatchMap = null;
    }
    
    // Set item details
    document.getElementById('editItemName').textContent = itemName;
    document.getElementById('editDestinationSelect').value = destination;
    
    // Reset custom destination
    document.getElementById('customDestinationGroup').style.display = 'none';
    document.getElementById('customDestinationInput').value = '';
    
    // Reset location displays
    document.getElementById('editPickupLocationText').innerHTML = 'Click "Set Pickup" to select on map';
    document.getElementById('editPickupLocationAddress').style.display = 'none';
    document.getElementById('editDestinationLocationText').innerHTML = 'Click "Set Destination" to select on map';
    document.getElementById('editDestinationLocationAddress').style.display = 'none';
    
    // Reset route summary
    document.getElementById('editRouteSummary').innerHTML = `
      <div class="text-muted text-center">
        <i class="bi bi-map display-6"></i>
        <p class="mt-2 mb-0">Set pickup and destination on map to calculate route</p>
      </div>
    `;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('editDispatchModal'));
    modal.show();
    
    // Initialize map after modal is shown
    setTimeout(() => {
      initializeEditDispatchMap();
      
      // Load existing route data if available
      if (dispatchRoutes[itemId]) {
        loadExistingRouteData(dispatchRoutes[itemId]);
      }
    }, 500);
  }
  
  function initializeEditDispatchMap() {
    // Initialize edit map
    editDispatchMap = L.map('editDispatchMap').setView([14.5995, 120.9842], 11);
    
    // Add tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© OpenStreetMap contributors'
    }).addTo(editDispatchMap);
    
    // Add click event listener
    editDispatchMap.on('click', function(e) {
      if (isEditSettingPickup) {
        setEditPickupLocation(e.latlng);
      } else if (isEditSettingDestination) {
        setEditDestinationLocation(e.latlng);
      }
    });
    
    // Add button event listeners
    document.getElementById('editSetPickupBtn').addEventListener('click', startEditSettingPickup);
    document.getElementById('editSetDestinationBtn').addEventListener('click', startEditSettingDestination);
    document.getElementById('editCalculateRouteBtn').addEventListener('click', calculateEditRoute);
  }
  
  function setEditPickupLocation(latlng) {
    // Remove existing pickup marker
    if (editPickupMarker) {
      editDispatchMap.removeLayer(editPickupMarker);
    }
    
    // Create pickup location object with name
    editPickupLocation = {
      lat: latlng.lat,
      lng: latlng.lng,
      name: 'Warehouse Location', // Default name, can be customized
      address: `Lat: ${latlng.lat.toFixed(6)}, Lng: ${latlng.lng.toFixed(6)}`
    };
    
    // Add new pickup marker
    editPickupMarker = L.marker([latlng.lat, latlng.lng], {
      icon: L.divIcon({
        className: 'custom-marker pickup-marker',
        html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
      })
    }).addTo(editDispatchMap);
    
    // Update UI
    document.getElementById('editPickupLocationText').innerHTML = `
      <i class="bi bi-geo-alt-fill text-primary me-1"></i>
      <span class="fw-semibold">${editPickupLocation.name}</span>
    `;
    document.getElementById('editPickupLocationAddress').style.display = 'block';
    document.getElementById('editPickupLocationAddress').innerHTML = `
      <small class="text-muted">
        <i class="bi bi-geo me-1"></i>
        ${editPickupLocation.address}
      </small>
    `;
    
    // Reset button
    document.getElementById('editSetPickupBtn').innerHTML = '<i class="bi bi-geo-alt me-1"></i>Set Pickup';
    document.getElementById('editSetPickupBtn').classList.remove('active');
    isEditSettingPickup = false;
    
    // Show calculate button if both locations are set
    if (editPickupLocation && editDestinationLocation) {
      document.getElementById('editCalculateRouteBtn').style.display = 'inline-block';
    }
  }
  
  function setEditDestinationLocation(latlng) {
    // Remove existing destination marker
    if (editDestinationMarker) {
      editDispatchMap.removeLayer(editDestinationMarker);
    }
    
    // Get destination name from dropdown
    const destinationSelect = document.getElementById('editDestinationSelect');
    const destinationName = destinationSelect.value === 'Custom' 
      ? document.getElementById('customDestinationInput').value 
      : destinationSelect.options[destinationSelect.selectedIndex].text;
    
    // Create destination location object with name
    editDestinationLocation = {
      lat: latlng.lat,
      lng: latlng.lng,
      name: destinationName || 'Destination Location',
      address: `Lat: ${latlng.lat.toFixed(6)}, Lng: ${latlng.lng.toFixed(6)}`
    };
    
    // Add new destination marker
    editDestinationMarker = L.marker([latlng.lat, latlng.lng], {
      icon: L.divIcon({
        className: 'custom-marker destination-marker',
        html: '<div style="background-color: #198754; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
      })
    }).addTo(editDispatchMap);
    
    // Update UI
    document.getElementById('editDestinationLocationText').innerHTML = `
      <i class="bi bi-geo-alt-fill text-success me-1"></i>
      <span class="fw-semibold">${editDestinationLocation.name}</span>
    `;
    document.getElementById('editDestinationLocationAddress').style.display = 'block';
    document.getElementById('editDestinationLocationAddress').innerHTML = `
      <small class="text-muted">
        <i class="bi bi-geo me-1"></i>
        ${editDestinationLocation.address}
      </small>
    `;
    
    // Reset button
    document.getElementById('editSetDestinationBtn').innerHTML = '<i class="bi bi-geo-alt me-1"></i>Set Destination';
    document.getElementById('editSetDestinationBtn').classList.remove('active');
    isEditSettingDestination = false;
    
    // Show calculate button if both locations are set
    if (editPickupLocation && editDestinationLocation) {
      document.getElementById('editCalculateRouteBtn').style.display = 'inline-block';
    }
  }
  
  function startEditSettingPickup() {
    isEditSettingPickup = true;
    isEditSettingDestination = false;
    
    document.getElementById('editSetPickupBtn').innerHTML = '<i class="bi bi-cursor me-1"></i>Click on Map';
    document.getElementById('editSetPickupBtn').classList.add('active');
    document.getElementById('editSetDestinationBtn').classList.remove('active');
    
    document.getElementById('editDispatchMap').style.cursor = 'crosshair';
    
    Swal.fire({
      icon: 'info',
      title: 'Set Pickup Location',
      text: 'Click anywhere on the map to set the pickup location.',
      timer: 3000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  function startEditSettingDestination() {
    isEditSettingPickup = false;
    isEditSettingDestination = true;
    
    document.getElementById('editSetDestinationBtn').innerHTML = '<i class="bi bi-cursor me-1"></i>Click on Map';
    document.getElementById('editSetDestinationBtn').classList.add('active');
    document.getElementById('editSetPickupBtn').classList.remove('active');
    
    document.getElementById('editDispatchMap').style.cursor = 'crosshair';
    
    Swal.fire({
      icon: 'info',
      title: 'Set Destination',
      text: 'Click anywhere on the map to set the destination location.',
      timer: 3000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  function setEditPickupLocation(latlng) {
    editPickupLocation = latlng;
    
    if (editPickupMarker) {
      editDispatchMap.removeLayer(editPickupMarker);
    }
    
    editPickupMarker = L.marker([latlng.lat, latlng.lng], {
      icon: L.divIcon({
        className: 'custom-marker pickup-marker',
        html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
      })
    }).addTo(editDispatchMap);
    
    // Update UI with coordinates
    document.getElementById('editPickupLocationText').innerHTML = `
      <strong>Lat:</strong> ${latlng.lat.toFixed(6)}<br>
      <strong>Lng:</strong> ${latlng.lng.toFixed(6)}
    `;
    
    // Reset button
    document.getElementById('editSetPickupBtn').innerHTML = '<i class="bi bi-geo-alt me-1"></i>Set Pickup';
    document.getElementById('editSetPickupBtn').classList.remove('active');
    document.getElementById('editDispatchMap').style.cursor = '';
    
    isEditSettingPickup = false;
    checkEditBothLocationsSet();
    
    // Get address
    getAddressFromCoordinates(latlng.lat, latlng.lng, 'editPickup');
  }
  
  function setEditDestinationLocation(latlng) {
    editDestinationLocation = latlng;
    
    if (editDestinationMarker) {
      editDispatchMap.removeLayer(editDestinationMarker);
    }
    
    editDestinationMarker = L.marker([latlng.lat, latlng.lng], {
      icon: L.divIcon({
        className: 'custom-marker destination-marker',
        html: '<div style="background-color: #198754; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
        iconSize: [20, 20],
        iconAnchor: [10, 10]
      })
    }).addTo(editDispatchMap);
    
    // Update UI with coordinates
    document.getElementById('editDestinationLocationText').innerHTML = `
      <strong>Lat:</strong> ${latlng.lat.toFixed(6)}<br>
      <strong>Lng:</strong> ${latlng.lng.toFixed(6)}
    `;
    
    // Reset button
    document.getElementById('editSetDestinationBtn').innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i>Set Destination';
    document.getElementById('editSetDestinationBtn').classList.remove('active');
    document.getElementById('editDispatchMap').style.cursor = '';
    
    isEditSettingDestination = false;
    checkEditBothLocationsSet();
    
    // Get address
    getAddressFromCoordinates(latlng.lat, latlng.lng, 'editDestination');
  }
  
  function checkEditBothLocationsSet() {
    if (editPickupLocation && editDestinationLocation) {
      document.getElementById('editCalculateRouteBtn').style.display = 'inline-block';
    }
  }
  
  function calculateEditRoute() {
    if (!editPickupLocation || !editDestinationLocation) return;
    
    // Show loading
    Swal.fire({
      title: 'Calculating Route...',
      text: 'Finding the best road route...',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Use same routing logic as main map
    const routingUrl = `https://router.project-osrm.org/route/v1/driving/${editPickupLocation.lng},${editPickupLocation.lat};${editDestinationLocation.lng},${editDestinationLocation.lat}?overview=full&geometries=geojson`;
    
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 5000);
    
    fetch(routingUrl, { 
      signal: controller.signal,
      headers: { 'Accept': 'application/json' }
    })
    .then(response => {
      clearTimeout(timeoutId);
      if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
      return response.json();
    })
    .then(data => {
      if (data.routes && data.routes.length > 0) {
        const route = data.routes[0];
        const coordinates = route.geometry.coordinates;
        const routeCoords = coordinates.map(coord => [coord[1], coord[0]]);
        
        // Store route data
        editRouteData = {
          distance: route.distance / 1000,
          duration: Math.round(route.duration / 60),
          coordinates: routeCoords,
          pickup: editPickupLocation,
          destination: editDestinationLocation
        };
        
        // Draw route
        if (editRouteLine) {
          editDispatchMap.removeLayer(editRouteLine);
        }
        
        editRouteLine = L.polyline(routeCoords, {
          color: '#ffc107',
          weight: 4,
          opacity: 0.8
        }).addTo(editDispatchMap);
        
        // Fit bounds
        const group = new L.featureGroup([editPickupMarker, editDestinationMarker, editRouteLine]);
        editDispatchMap.fitBounds(group.getBounds().pad(0.1));
        
        // Update route summary
        updateEditRouteSummary();
        
        Swal.fire({
          icon: 'success',
          title: 'Route Calculated!',
          text: `Distance: ${editRouteData.distance.toFixed(1)} km, Time: ${editRouteData.duration} min`,
          timer: 2000,
          timerProgressBar: true,
          showConfirmButton: false
        });
      } else {
        drawEditStraightLineRoute();
      }
    })
    .catch(error => {
      clearTimeout(timeoutId);
      console.error('Edit routing error:', error);
      Swal.close();
      drawEditStraightLineRoute();
    });
  }
  
  function drawEditStraightLineRoute() {
    const distance = calculateDistance(editPickupLocation, editDestinationLocation);
    const estimatedTime = Math.round(distance * 2);
    
    editRouteData = {
      distance: distance,
      duration: estimatedTime,
      coordinates: [[editPickupLocation.lat, editPickupLocation.lng], [editDestinationLocation.lat, editDestinationLocation.lng]],
      pickup: editPickupLocation,
      destination: editDestinationLocation,
      isStraightLine: true
    };
    
    if (editRouteLine) {
      editDispatchMap.removeLayer(editRouteLine);
    }
    
    editRouteLine = L.polyline(editRouteData.coordinates, {
      color: '#ffc107',
      weight: 4,
      opacity: 0.8,
      dashArray: '10, 5'
    }).addTo(editDispatchMap);
    
    const group = new L.featureGroup([editPickupMarker, editDestinationMarker, editRouteLine]);
    editDispatchMap.fitBounds(group.getBounds().pad(0.1));
    
    updateEditRouteSummary();
    
    Swal.fire({
      icon: 'info',
      title: 'Direct Route',
      text: 'Using straight-line distance. Road routing unavailable.',
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  function updateEditRouteSummary() {
    if (!editRouteData) return;
    
    const routeType = editRouteData.isStraightLine ? 'Direct Route' : 'Road Route';
    const routeIcon = editRouteData.isStraightLine ? 'bi-arrow-right' : 'bi-map';
    
    document.getElementById('editRouteSummary').innerHTML = `
      <div class="d-flex align-items-center mb-2">
        <i class="bi ${routeIcon} me-2 text-primary"></i>
        <span class="fw-semibold">${routeType}</span>
      </div>
      <div class="row g-2 text-sm">
        <div class="col-6">
          <strong>Distance:</strong><br>
          <span class="text-primary">${editRouteData.distance.toFixed(1)} km</span>
        </div>
        <div class="col-6">
          <strong>Est. Time:</strong><br>
          <span class="text-success">${editRouteData.duration} min</span>
        </div>
      </div>
    `;
  }
  
  function saveDispatchRouteData() {
    const destination = document.getElementById('editDestinationSelect').value === 'Custom' 
      ? document.getElementById('customDestinationInput').value 
      : document.getElementById('editDestinationSelect').value;
    
    if (!destination) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Destination',
        text: 'Please select or enter a destination.',
      });
      return;
    }
    
    console.log('Saving route data for item:', currentEditItemId);
    console.log('Destination:', destination);
    console.log('Route data:', editRouteData);
    
    // Store route data for this item
    dispatchRoutes[currentEditItemId] = {
      destination: destination,
      routeData: editRouteData
    };
    
    // Save to localStorage for persistence
    saveDispatchRoutesToStorage();
    
    // Save to backend (you can implement this)
    saveRouteToBackend(currentEditItemId, destination, editRouteData);
    
    // Update the display in the main list
    console.log('About to update dispatch item display...');
    updateDispatchItemDisplay(currentEditItemId, destination, editRouteData);
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('editDispatchModal'));
    modal.hide();
    
    Swal.fire({
      icon: 'success',
      title: 'Route Saved!',
      text: 'Dispatch route has been updated and will persist after refresh.',
      timer: 2000,
      timerProgressBar: true,
      showConfirmButton: false
    });
  }
  
  // Save dispatch routes to localStorage
  function saveDispatchRoutesToStorage() {
    try {
      localStorage.setItem('dispatchRoutes', JSON.stringify(dispatchRoutes));
      console.log('Dispatch routes saved to localStorage');
    } catch (error) {
      console.error('Error saving dispatch routes to localStorage:', error);
    }
  }
  
  // Load dispatch routes from localStorage
  function loadDispatchRoutesFromStorage() {
    try {
      const savedRoutes = localStorage.getItem('dispatchRoutes');
      if (savedRoutes) {
        dispatchRoutes = JSON.parse(savedRoutes);
        console.log('Dispatch routes loaded from localStorage:', dispatchRoutes);
        
        // Apply saved routes to the UI
        applySavedRoutesToUI();
      }
    } catch (error) {
      console.error('Error loading dispatch routes from localStorage:', error);
      dispatchRoutes = {};
    }
  }
  
  // Apply saved routes to the UI after page load
  function applySavedRoutesToUI() {
    Object.keys(dispatchRoutes).forEach(itemId => {
      const routeInfo = dispatchRoutes[itemId];
      if (routeInfo && routeInfo.routeData) {
        updateDispatchItemDisplay(itemId, routeInfo.destination, routeInfo.routeData);
      }
    });
  }
  
  // Save route to backend (implement this with your Laravel backend)
  function saveRouteToBackend(itemId, destination, routeData) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
      console.log('CSRF token not found, skipping backend save');
      return;
    }
    
    const routePayload = {
      item_id: itemId,
      destination: destination,
      route_data: {
        distance: routeData.distance,
        duration: routeData.duration,
        coordinates: routeData.coordinates,
        pickup: routeData.pickup,
        destination_coords: routeData.destination,
        is_straight_line: routeData.isStraightLine || false
      }
    };
    
    fetch('/picking-dispatch/save-route', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(routePayload)
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        console.log('Route saved to backend successfully');
      } else {
        console.error('Failed to save route to backend:', data.message);
      }
    })
    .catch(error => {
      console.error('Error saving route to backend:', error);
    });
  }
  
  function updateDispatchItemDisplay(itemId, destination, routeData) {
    console.log('Updating dispatch item display for:', itemId, destination, routeData);
    
    // Try multiple selectors to find the dispatch item
    let dispatchItem = null;
    
    // Method 1: Find by button with data-item-id
    const itemButton = document.querySelector(`[data-item-id="${itemId}"]`);
    if (itemButton) {
      dispatchItem = itemButton.closest('.dispatch-item');
      console.log('Found dispatch item via button:', dispatchItem);
    }
    
    // Method 2: Find dispatch item directly
    if (!dispatchItem) {
      dispatchItem = document.querySelector(`.dispatch-item [data-item-id="${itemId}"]`)?.closest('.dispatch-item');
      console.log('Found dispatch item via direct search:', dispatchItem);
    }
    
    // Method 3: Find by route preview ID
    if (!dispatchItem) {
      const routeElement = document.querySelector(`#routePreview_${itemId}`);
      if (routeElement) {
        dispatchItem = routeElement.closest('.dispatch-item');
        console.log('Found dispatch item via route preview:', dispatchItem);
      }
    }
    
    if (!dispatchItem) {
      console.error(`No dispatch item found for item ID: ${itemId}`);
      console.log('Available dispatch items:', document.querySelectorAll('.dispatch-item'));
      return;
    }
    
    // Update destination text
    const destinationSpan = dispatchItem.querySelector('.fw-medium');
    if (destinationSpan) {
      destinationSpan.textContent = destination;
      console.log('Updated destination text to:', destination);
    } else {
      console.log('Destination span not found. Available elements:', dispatchItem.querySelectorAll('*'));
    }
    
    // Update route preview
    const routePreview = dispatchItem.querySelector(`#routePreview_${itemId} .route-text`);
    if (routePreview && routeData) {
      const routeType = routeData.isStraightLine ? 'Direct' : 'Road';
      const routeText = `${routeType} route: ${routeData.distance.toFixed(1)} km, ${routeData.duration} min`;
      routePreview.textContent = routeText;
      routePreview.parentElement.classList.remove('text-muted');
      routePreview.parentElement.classList.add('text-success');
      console.log('Updated route preview to:', routeText);
    } else {
      console.log('Route preview not found or no route data');
      console.log('Route preview element:', routePreview);
      console.log('Route data:', routeData);
      
      // Try alternative selector
      const altRoutePreview = dispatchItem.querySelector('.route-text');
      if (altRoutePreview && routeData) {
        const routeType = routeData.isStraightLine ? 'Direct' : 'Road';
        const routeText = `${routeType} route: ${routeData.distance.toFixed(1)} km, ${routeData.duration} min`;
        altRoutePreview.textContent = routeText;
        altRoutePreview.parentElement.classList.remove('text-muted');
        altRoutePreview.parentElement.classList.add('text-success');
        console.log('Updated route preview via alternative selector:', routeText);
      }
    }
  }
  
  function loadExistingRouteData(savedRoute) {
    console.log('Loading existing route data:', savedRoute);
    
    if (savedRoute.routeData) {
      editRouteData = savedRoute.routeData;
      editPickupLocation = savedRoute.routeData.pickup;
      editDestinationLocation = savedRoute.routeData.destination;
      
      // Update destination dropdown if it's a custom destination
      if (savedRoute.destination && savedRoute.destination !== document.getElementById('editDestinationSelect').value) {
        // Check if it's a custom destination
        const destinationSelect = document.getElementById('editDestinationSelect');
        const existingOption = Array.from(destinationSelect.options).find(option => option.value === savedRoute.destination);
        
        if (!existingOption) {
          // It's a custom destination
          destinationSelect.value = 'Custom';
          document.getElementById('customDestinationGroup').style.display = 'block';
          document.getElementById('customDestinationInput').value = savedRoute.destination;
        } else {
          destinationSelect.value = savedRoute.destination;
        }
      }
      
      // Update pickup location display
      if (editPickupLocation) {
        const pickupLocationName = editPickupLocation.name || editPickupLocation.address || 'Warehouse Location';
        document.getElementById('editPickupLocationText').innerHTML = `
          <i class="bi bi-geo-alt-fill text-primary me-1"></i>
          <span class="fw-semibold">${pickupLocationName}</span>
        `;
        document.getElementById('editPickupLocationAddress').style.display = 'block';
        document.getElementById('editPickupLocationAddress').innerHTML = `
          <small class="text-muted">
            <i class="bi bi-geo me-1"></i>
            Lat: ${editPickupLocation.lat.toFixed(6)}, Lng: ${editPickupLocation.lng.toFixed(6)}
          </small>
        `;
        
        // Add pickup marker
        editPickupMarker = L.marker([editPickupLocation.lat, editPickupLocation.lng], {
          icon: L.divIcon({
            className: 'custom-marker pickup-marker',
            html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
          })
        }).addTo(editDispatchMap);
      }
      
      // Update destination location display
      if (editDestinationLocation) {
        const destinationLocationName = editDestinationLocation.name || editDestinationLocation.address || savedRoute.destination || 'Destination Location';
        document.getElementById('editDestinationLocationText').innerHTML = `
          <i class="bi bi-geo-alt-fill text-success me-1"></i>
          <span class="fw-semibold">${destinationLocationName}</span>
        `;
        document.getElementById('editDestinationLocationAddress').style.display = 'block';
        document.getElementById('editDestinationLocationAddress').innerHTML = `
          <small class="text-muted">
            <i class="bi bi-geo me-1"></i>
            Lat: ${editDestinationLocation.lat.toFixed(6)}, Lng: ${editDestinationLocation.lng.toFixed(6)}
          </small>
        `;
        
        // Add destination marker
        editDestinationMarker = L.marker([editDestinationLocation.lat, editDestinationLocation.lng], {
          icon: L.divIcon({
            className: 'custom-marker destination-marker',
            html: '<div style="background-color: #198754; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);"></div>',
            iconSize: [20, 20],
            iconAnchor: [10, 10]
          })
        }).addTo(editDispatchMap);
      }
      
      // Add route line
      if (editRouteData.coordinates) {
        const dashArray = editRouteData.isStraightLine ? '10, 5' : null;
        editRouteLine = L.polyline(editRouteData.coordinates, {
          color: '#ffc107',
          weight: 4,
          opacity: 0.8,
          dashArray: dashArray
        }).addTo(editDispatchMap);
        
        // Fit bounds
        const group = new L.featureGroup([editPickupMarker, editDestinationMarker, editRouteLine]);
        editDispatchMap.fitBounds(group.getBounds().pad(0.1));
      }
      
      updateEditRouteSummary();
      document.getElementById('editCalculateRouteBtn').style.display = 'inline-block';
      
      console.log('Existing route data loaded successfully');
    } else {
      console.log('No existing route data found');
    }
  }
  
  // Schedule Dispatch functionality
  function initializeScheduleDispatch() {
    // Schedule Dispatch button
    document.getElementById('scheduleDispatchBtn').addEventListener('click', openScheduleDispatchModal);
    
    // Bulk Dispatch button
    document.getElementById('bulkDispatchBtn').addEventListener('click', openBulkDispatchModal);
    
    // Confirm Schedule Dispatch button
    document.getElementById('confirmScheduleDispatch').addEventListener('click', confirmScheduleDispatch);
  }
  
  function openScheduleDispatchModal() {
    // Get all dispatch items
    const dispatchItems = document.querySelectorAll('.dispatch-item');
    
    if (dispatchItems.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Items to Dispatch',
        text: 'There are no items ready for dispatch. Complete picking first.',
        confirmButtonText: 'OK'
      });
      return;
    }
    
    // Set default date/time to current + 1 hour
    const now = new Date();
    now.setHours(now.getHours() + 1);
    const defaultDateTime = now.toISOString().slice(0, 16);
    document.getElementById('scheduleDateTime').value = defaultDateTime;
    
    // Populate items list
    populateScheduleItemsList();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('scheduleDispatchModal'));
    modal.show();
  }
  
  function populateScheduleItemsList() {
    const itemsList = document.getElementById('scheduleItemsList');
    const dispatchItems = document.querySelectorAll('.dispatch-item');
    let totalItems = 0;
    let totalEstimatedTime = 0;
    
    let itemsHtml = '';
    
    dispatchItems.forEach((item, index) => {
      const itemName = item.querySelector('.fw-semibold').textContent;
      const quantity = item.querySelector('small').textContent.match(/Qty: (\d+)/)[1];
      const destination = item.querySelector('.destination-info .fw-semibold').textContent;
      const routeInfo = item.querySelector('.route-text').textContent;
      
      // Extract time from route info if available
      const timeMatch = routeInfo.match(/(\d+) min/);
      const itemTime = timeMatch ? parseInt(timeMatch[1]) : 15; // Default 15 min
      
      totalItems++;
      totalEstimatedTime += itemTime;
      
      itemsHtml += `
        <div class="d-flex align-items-center justify-content-between p-2 border rounded mb-2">
          <div class="flex-grow-1">
            <div class="fw-semibold">${itemName}</div>
            <small class="text-muted">Qty: ${quantity} → ${destination}</small>
            <div class="small text-success">${routeInfo}</div>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" checked data-item-index="${index}">
          </div>
        </div>
      `;
    });
    
    itemsList.innerHTML = itemsHtml;
    document.getElementById('scheduleTotalItems').textContent = totalItems;
    document.getElementById('scheduleEstimatedTime').textContent = `${totalEstimatedTime} min`;
  }
  
  function confirmScheduleDispatch() {
    const dateTime = document.getElementById('scheduleDateTime').value;
    const priority = document.getElementById('schedulePriority').value;
    const driver = document.getElementById('scheduleDriver').value;
    const instructions = document.getElementById('scheduleInstructions').value;
    
    // Validation
    if (!dateTime) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Date/Time',
        text: 'Please select a dispatch date and time.',
      });
      return;
    }
    
    if (!driver) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Driver',
        text: 'Please select a driver/vehicle.',
      });
      return;
    }
    
    // Get selected items
    const selectedItems = [];
    const checkboxes = document.querySelectorAll('#scheduleItemsList input[type="checkbox"]:checked');
    
    if (checkboxes.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Items Selected',
        text: 'Please select at least one item to dispatch.',
      });
      return;
    }
    
    checkboxes.forEach(checkbox => {
      const itemIndex = checkbox.getAttribute('data-item-index');
      selectedItems.push(itemIndex);
    });
    
    // Show confirmation
    const scheduledDate = new Date(dateTime);
    const formattedDate = scheduledDate.toLocaleDateString();
    const formattedTime = scheduledDate.toLocaleTimeString();
    const driverName = document.getElementById('scheduleDriver').selectedOptions[0].text;
    
    Swal.fire({
      title: 'Confirm Dispatch Schedule',
      html: `
        <div class="text-start">
          <p><strong>Date:</strong> ${formattedDate}</p>
          <p><strong>Time:</strong> ${formattedTime}</p>
          <p><strong>Driver:</strong> ${driverName}</p>
          <p><strong>Priority:</strong> ${priority.charAt(0).toUpperCase() + priority.slice(1)}</p>
          <p><strong>Items:</strong> ${selectedItems.length} items selected</p>
          ${instructions ? `<p><strong>Instructions:</strong> ${instructions}</p>` : ''}
        </div>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#0d6efd',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="bi bi-calendar-check me-2"></i>Schedule Dispatch',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        processScheduleDispatch(selectedItems, {
          dateTime,
          priority,
          driver,
          instructions
        });
      }
    });
  }
  
  function processScheduleDispatch(selectedItems, scheduleData) {
    // Show loading
    Swal.fire({
      title: 'Scheduling Dispatch...',
      text: 'Please wait while we schedule your dispatch.',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Simulate API call (replace with actual backend call)
    setTimeout(() => {
      // Close modal
      const modal = bootstrap.Modal.getInstance(document.getElementById('scheduleDispatchModal'));
      modal.hide();
      
      // Show success
      Swal.fire({
        icon: 'success',
        title: 'Dispatch Scheduled!',
        html: `
          <div class="text-start">
            <p>Your dispatch has been successfully scheduled.</p>
            <p><strong>Schedule ID:</strong> DSP-${Date.now()}</p>
            <p><strong>Items:</strong> ${selectedItems.length} items</p>
            <p><strong>Date:</strong> ${new Date(scheduleData.dateTime).toLocaleDateString()}</p>
            <p><strong>Time:</strong> ${new Date(scheduleData.dateTime).toLocaleTimeString()}</p>
          </div>
        `,
        confirmButtonText: 'OK'
      });
      
      // Here you would typically:
      // 1. Send data to backend
      // 2. Update dispatch status
      // 3. Remove items from ready list
      // 4. Add to scheduled dispatches
      
    }, 2000);
  }
  
  function openBulkDispatchModal() {
    const dispatchItems = document.querySelectorAll('.dispatch-item');
    
    if (dispatchItems.length === 0) {
      Swal.fire({
        icon: 'warning',
        title: 'No Items to Dispatch',
        text: 'There are no items ready for dispatch.',
        confirmButtonText: 'OK'
      });
      return;
    }
    
    Swal.fire({
      title: 'Bulk Dispatch All Items?',
      html: `
        <div class="text-start">
          <p>This will immediately dispatch all <strong>${dispatchItems.length} items</strong> that are ready.</p>
          <p class="text-warning"><i class="bi bi-exclamation-triangle me-2"></i>This action cannot be undone.</p>
          <p>Are you sure you want to proceed?</p>
        </div>
      `,
      icon: 'question',
      showCancelButton: true,
      confirmButtonColor: '#198754',
      cancelButtonColor: '#6c757d',
      confirmButtonText: '<i class="bi bi-truck me-2"></i>Dispatch All Now',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        processBulkDispatch();
      }
    });
  }
  
  function processBulkDispatch() {
    // Show loading
    Swal.fire({
      title: 'Processing Bulk Dispatch...',
      text: 'Dispatching all items...',
      allowOutsideClick: false,
      showConfirmButton: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Simulate processing
    setTimeout(() => {
      const dispatchItems = document.querySelectorAll('.dispatch-item');
      const itemCount = dispatchItems.length;
      
      // Remove all dispatch items (simulate dispatch)
      dispatchItems.forEach(item => {
        item.remove();
      });
      
      // Show no items message
      document.getElementById('noDispatchItems').style.display = 'block';
      
      Swal.fire({
        icon: 'success',
        title: 'Bulk Dispatch Complete!',
        html: `
          <div class="text-start">
            <p><strong>${itemCount} items</strong> have been successfully dispatched.</p>
            <p><strong>Dispatch ID:</strong> BULK-${Date.now()}</p>
            <p>All items have been removed from the dispatch queue.</p>
          </div>
        `,
        confirmButtonText: 'OK'
      });
    }, 3000);
  }

  // Initialize inventory chart
  function initializeInventoryChart() {
    const ctx = document.getElementById('inventoryChart');
    if (ctx) {
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: ['Electronics', 'Furniture', 'Office Supplies', 'Equipment', 'Accessories'],
          datasets: [{
            label: 'Available Stock',
            data: [45, 23, 67, 34, 28],
            backgroundColor: 'rgba(54, 162, 235, 0.2)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }, {
            label: 'Picked Items',
            data: [12, 8, 23, 15, 9],
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    }
  }
  
  </script>
  </body>
  </html>