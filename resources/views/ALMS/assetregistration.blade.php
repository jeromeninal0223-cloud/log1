<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Physical Asset Registration - ALMS</title>
  <meta name="csrf-token" content="{{ csrf_token() }}" />

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
          <i class="bi bi-building fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Physical Asset Registration</h2>
          <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Register and manage physical assets by category.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/alms') }}" class="text-decoration-none">Asset Lifecycle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Assets Registration</li>
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
              <p class="text-muted mb-0 small">Active Assets</p>
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
              <p class="text-muted mb-0 small">Total Assets</p>
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
              <p class="text-muted mb-0 small">Assets Availability</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +2%</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="row g-4">
    <!-- Main Column - Vehicle List -->
    <div class="col-12">
      <!-- Vehicle Fleet Management -->
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0 text-dark fw-semibold">Asset Management</h5>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newVehicleModal">
              <i class="bi bi-plus-circle me-1"></i>Register Asset
            </button>
          </div>
        </div>
        <div class="card-body p-4">
          <!-- Enhanced Search and Filter Bar -->
          <div class="bg-light rounded-3 p-3 mb-4">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label small fw-semibold text-muted mb-1">Search Assets</label>
                <div class="input-group">
                  <span class="input-group-text bg-white border-end-0">
                    <i class="bi bi-search text-muted"></i>
                  </span>
                  <input type="text" class="form-control border-start-0 ps-0" id="searchAssets" 
                         placeholder="Search by asset ID, name, description...">
                </div>
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Asset Category</label>
                <select class="form-select" id="filterCategory">
                  <option value="">All Categories</option>
                  <option value="buildings">🏢 Buildings & Structures</option>
                  <option value="vehicles">🚚 Vehicles</option>
                  <option value="machinery">⚙️ Machinery & Equipment</option>
                  <option value="furniture">🪑 Furniture & Fixtures</option>
                  <option value="it_equipment">💻 IT Equipment</option>
                  <option value="tools">🧰 Tools & Instruments</option>
                  <option value="office_equipment">🌐 Office Equipment</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label small fw-semibold text-muted mb-1">Condition</label>
                <select class="form-select" id="filterCondition">
                  <option value="">All Conditions</option>
                  <option value="Excellent">🟢 Excellent</option>
                  <option value="Good">🔵 Good</option>
                  <option value="Fair">🟡 Fair</option>
                  <option value="Poor">🔴 Poor</option>
                </select>
              </div>
              <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-outline-secondary w-100" type="button" title="Clear Filters" id="clearFilters">
                  <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </button>
              </div>
            </div>
          </div>
          
          <!-- Dynamic Asset List Table -->
          <div class="table-responsive rounded-3 shadow-sm">
            <table class="table table-hover mb-0" id="assetTable">
              <thead>
                <tr class="bg-primary bg-opacity-10">
                  <th class="border-0 py-3 fw-semibold text-primary">Asset</th>
                  <th class="border-0 py-3 fw-semibold text-primary">Category</th>
                  <th class="border-0 py-3 fw-semibold text-primary">Details</th>
                  <th class="border-0 py-3 fw-semibold text-primary">Condition</th>
                  <th class="border-0 py-3 fw-semibold text-primary">Date Added</th>
                  <th class="border-0 py-3 fw-semibold text-primary text-center">Actions</th>
                </tr>
              </thead>
              <tbody>
                @if(isset($assets) && $assets->count())
                @foreach($assets as $asset)
                <tr class="border-0" style="border-bottom: 1px solid #f0f0f0 !important;">
                  <!-- Asset Image & ID -->
                  <td class="py-4" style="width: 160px;">
                    <div class="d-flex align-items-center">
                      <div class="position-relative">
                        @if($asset->image_path && $asset->image_exists)
                          <img src="{{ asset('storage/' . $asset->image_path) }}" 
                               alt="Asset Image" 
                               class="rounded-3 shadow-sm" 
                               style="width: 90px; height: 65px; object-fit: cover;">
                        @else
                          <div class="bg-light rounded-3 d-flex align-items-center justify-content-center shadow-sm" 
                               style="width: 90px; height: 65px;" 
                               title="No image uploaded">
                            @php
                              $categoryIcons = [
                                'buildings' => 'bi-building',
                                'vehicles' => 'bi-truck',
                                'machinery' => 'bi-gear-wide-connected',
                                'furniture' => 'bi-chair',
                                'it_equipment' => 'bi-laptop',
                                'tools' => 'bi-tools',
                                'office_equipment' => 'bi-printer'
                              ];
                              $icon = $categoryIcons[$asset->asset_category] ?? 'bi-box';
                            @endphp
                            <i class="{{ $icon }} text-muted" style="font-size: 1.5rem;"></i>
                          </div>
                        @endif
                        <span class="position-absolute badge rounded-pill bg-primary text-white" 
                              style="top: -8px; left: -8px; font-size: 0.65rem; padding: 4px 8px; font-weight: 600;">
                          {{ $asset->asset_id ?? str_pad($asset->id, 3, '0', STR_PAD_LEFT) }}
                        </span>
                      </div>
                      <div class="ms-3">
                        <div class="fw-bold text-dark" style="font-size: 0.9rem;">
                          @php
                            $assetName = '';
                            switch($asset->asset_category) {
                              case 'buildings':
                                $assetName = $asset->building_name;
                                break;
                              case 'vehicles':
                                $assetName = $asset->plate_number;
                                break;
                              case 'machinery':
                                $assetName = $asset->equipment_name;
                                break;
                              case 'furniture':
                                $assetName = $asset->item_description;
                                break;
                              case 'it_equipment':
                                $assetName = $asset->item_name;
                                break;
                              case 'tools':
                                $assetName = $asset->tool_name;
                                break;
                              case 'office_equipment':
                                $assetName = $asset->equipment_name;
                                break;
                              default:
                                $assetName = $asset->plate_number ?? 'Asset #' . $asset->id;
                            }
                          @endphp
                          {{ $assetName }}
                        </div>
                        <small class="text-muted">ID: {{ $asset->asset_id ?? $asset->id }}</small>
                      </div>
                    </div>
                  </td>
                  
                  <!-- Category -->
                  <td class="py-4" style="width: 150px;">
                    @php
                      $categoryLabels = [
                        'buildings' => ['🏢 Buildings', '#6f42c1'],
                        'vehicles' => ['🚚 Vehicles', '#0d6efd'],
                        'machinery' => ['⚙️ Machinery', '#fd7e14'],
                        'furniture' => ['🪑 Furniture', '#20c997'],
                        'it_equipment' => ['💻 IT Equipment', '#6610f2'],
                        'tools' => ['🧰 Tools', '#dc3545'],
                        'office_equipment' => ['🌐 Office Equip', '#198754']
                      ];
                      $categoryInfo = $categoryLabels[$asset->asset_category] ?? ['📦 Other', '#6c757d'];
                    @endphp
                    <span class="badge rounded-pill px-3 py-2" 
                          style="background-color: {{ $categoryInfo[1] }}; color: white; font-size: 0.75rem; font-weight: 500;">
                      {{ $categoryInfo[0] }}
                    </span>
                  </td>
                  
                  <!-- Details -->
                  <td class="py-4" style="width: 250px;">
                    <div>
                      @if($asset->asset_category === 'buildings')
                        <div class="fw-semibold text-dark mb-1">{{ $asset->location_address }}</div>
                        @if($asset->floor_area)
                          <small class="text-muted">{{ number_format($asset->floor_area) }} sqm</small>
                        @endif
                      @elseif($asset->asset_category === 'vehicles')
                        <div class="fw-semibold text-dark mb-1">{{ $asset->vehicle_type }}</div>
                        <small class="text-muted">{{ $asset->brand_model ?? ($asset->brand . ' ' . $asset->model) }}</small>
                      @elseif($asset->asset_category === 'machinery')
                        <div class="fw-semibold text-dark mb-1">{{ $asset->brand_model }}</div>
                        @if($asset->serial_number)
                          <small class="text-muted">SN: {{ $asset->serial_number }}</small>
                        @endif
                      @elseif($asset->asset_category === 'furniture')
                        <div class="fw-semibold text-dark mb-1">{{ $asset->brand_model }}</div>
                        @if($asset->quantity)
                          <small class="text-muted">Qty: {{ $asset->quantity }}</small>
                        @endif
                      @elseif($asset->asset_category === 'it_equipment')
                        <div class="fw-semibold text-dark mb-1">{{ $asset->brand_model }}</div>
                        @if($asset->serial_number)
                          <small class="text-muted">SN: {{ $asset->serial_number }}</small>
                        @endif
                      @elseif($asset->asset_category === 'tools')
                        <div class="fw-semibold text-dark mb-1">{{ $asset->brand_model }}</div>
                        @if($asset->quantity)
                          <small class="text-muted">Qty: {{ $asset->quantity }}</small>
                        @endif
                      @elseif($asset->asset_category === 'office_equipment')
                        <div class="fw-semibold text-dark mb-1">{{ $asset->brand_model }}</div>
                        @if($asset->serial_number)
                          <small class="text-muted">SN: {{ $asset->serial_number }}</small>
                        @endif
                      @else
                        <div class="fw-semibold text-dark mb-1">{{ $asset->brand ?? 'N/A' }}</div>
                        <small class="text-muted">{{ $asset->model ?? 'N/A' }}</small>
                      @endif
                    </div>
                  </td>
                  
                  <!-- Condition -->
                  <td class="py-4" style="width: 120px;">
                    @if($asset->condition)
                      <span class="badge rounded-pill px-3 py-2 
                        {{ $asset->condition === 'Excellent' ? 'bg-success' : 
                           ($asset->condition === 'Good' ? 'bg-primary' : 
                           ($asset->condition === 'Fair' ? 'bg-warning' : 'bg-danger')) }}" 
                            style="font-size: 0.75rem; font-weight: 600;">
                        {{ $asset->condition }}
                      </span>
                    @else
                      <span class="text-muted small">Not specified</span>
                    @endif
                  </td>
                  
                  <!-- Date Added -->
                  <td class="py-4" style="width: 140px;">
                    <div class="d-flex align-items-center text-muted mb-1">
                      <i class="bi bi-calendar3 me-2" style="font-size: 0.9rem;"></i>
                      <span style="font-size: 0.9rem; font-weight: 500;">
                        {{ \Carbon\Carbon::parse($asset->date_acquired ?? $asset->created_at)->format('M d, Y') }}
                      </span>
                    </div>
                    <div class="text-muted" style="font-size: 0.8rem; margin-left: 22px;">
                      {{ \Carbon\Carbon::parse($asset->date_acquired ?? $asset->created_at)->diffForHumans() }}
                    </div>
                  </td>
                  
                  <!-- Actions -->
                  <td class="py-4 text-center" style="width: 140px;">
                    <div class="btn-group btn-group-sm" role="group">
                      <button class="btn btn-outline-primary view-asset-btn rounded-2 me-1" 
                              data-id="{{ $asset->id }}" title="View Details"
                              style="padding: 6px 10px; border-width: 1.5px;">
                        <i class="bi bi-eye" style="font-size: 0.9rem;"></i>
                      </button>
                      <button class="btn btn-outline-secondary edit-asset-btn rounded-2 me-1" 
                              data-id="{{ $asset->id }}" title="Edit Asset"
                              style="padding: 6px 10px; border-width: 1.5px;">
                        <i class="bi bi-pencil" style="font-size: 0.9rem;"></i>
                      </button>
                      <button class="btn btn-outline-info rounded-2" title="Asset History"
                              style="padding: 6px 10px; border-width: 1.5px;">
                        <i class="bi bi-clock-history" style="font-size: 0.9rem;"></i>
                      </button>
                    </div>
                  </td>
                </tr>
                @endforeach
                @else
                <tr>
                  <td colspan="6" class="text-center py-5">
                    <div class="empty-state">
                      <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-building fs-1 text-muted"></i>
                      </div>
                      <h6 class="text-muted mb-2">No physical assets registered yet</h6>
                      <p class="text-muted small mb-3">Start managing your assets by registering your first physical asset</p>
                      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newVehicleModal">
                        <i class="bi bi-plus-circle me-2"></i>Register First Asset
                      </button>
                    </div>
                  </td>
                </tr>
                @endif
              </tbody>
            </table>
          </div>
          
          @if(isset($assets) && $assets->hasPages())
            <div class="d-flex justify-content-center mt-3">
              {{ $assets->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- New Vehicle Registration Modal -->
  <div class="modal fade" id="newVehicleModal" tabindex="-1" aria-labelledby="newVehicleModalLabel" aria-hidden="true" style="z-index: 1056;">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newVehicleModalLabel">
            <i class="bi bi-plus-circle me-2"></i>Physical Asset Registration
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="assetForm" method="POST" action="{{ url('/alms/assetregistration') }}" enctype="multipart/form-data">
            @csrf
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
            
            <!-- Category Selection -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <label class="form-label fw-bold">Asset Category <span class="text-danger">*</span></label>
                <select id="assetCategory" name="asset_category" class="form-select" required>
                  <option value="">Select Asset Category</option>
                  <option value="buildings">🏢 Buildings & Structures</option>
                  <option value="vehicles">🚚 Vehicles</option>
                  <option value="machinery">⚙️ Machinery & Equipment</option>
                  <option value="furniture">🪑 Furniture & Fixtures</option>
                  <option value="it_equipment">💻 IT Equipment</option>
                  <option value="tools">🧰 Tools & Instruments</option>
                  <option value="office_equipment">🌐 Office Equipment</option>
                </select>
              </div>
            </div>

            <!-- Dynamic Form Fields Container -->
            <div id="dynamicFormFields">
              <div class="text-center text-muted py-4">
                <i class="bi bi-arrow-up fs-1 mb-2 d-block"></i>
                <p>Please select an asset category above to display the appropriate form fields.</p>
              </div>
            </div>

            <!-- Common Asset Image Upload Field -->
            <div id="assetImageField" style="display: none;">
              <div class="row g-3 mt-3">
                <div class="col-12">
                  <hr class="my-3">
                  <label class="form-label">Asset Image (Optional)</label>
                  <input type="file" name="asset_image" class="form-control" accept="image/*">
                  <small class="text-muted">Upload a photo of the asset. Supported formats: JPG, PNG, GIF, WEBP. Max size: 2MB</small>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancel
          </button>
          <button type="reset" form="assetForm" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-clockwise me-1"></i>Reset Form
          </button>
          <button type="submit" form="assetForm" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Register Physical Asset
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Dynamic View/Edit Asset Modal -->
  <div class="modal fade" id="assetModal" tabindex="-1" aria-labelledby="assetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="assetModalLabel">Asset Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- View Mode -->
          <div id="viewMode" style="display: none;">
            <div class="row g-4">
              <!-- Asset Image -->
              <div class="col-md-4">
                <div class="text-center">
                  <div id="viewImageContainer" class="mb-3">
                    <img id="viewImage" src="" alt="Asset Image" class="img-thumbnail rounded-3" style="max-width: 100%; max-height: 250px; object-fit: cover;">
                  </div>
                  <div id="viewNoImageContainer" class="bg-light rounded-3 d-flex align-items-center justify-content-center" style="height: 250px; display: none !important;">
                    <div class="text-center">
                      <i id="viewCategoryIcon" class="bi bi-building text-muted mb-2" style="font-size: 3rem;"></i>
                      <p class="text-muted small">No image uploaded</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Asset Details -->
              <div class="col-md-8">
                <div id="viewDetailsContainer">
                  <!-- Dynamic content will be populated here -->
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Mode -->
          <div id="editMode" style="display: none;">
            <form id="assetEditForm" enctype="multipart/form-data" method="POST" onsubmit="return false;">
              @csrf
              @method('PUT')
              <input type="hidden" id="editId">
              <input type="hidden" id="editCategory">
              
              <!-- Dynamic edit form will be populated here -->
              <div id="editFormContainer">
                <!-- Dynamic content will be populated here -->
              </div>
              
              <!-- Common Image Upload Section -->
              <div class="row g-3 mt-3">
                <div class="col-12">
                  <hr class="my-3">
                  <label class="form-label">Asset Image</label>
                  <div id="currentImageDisplay" class="mb-2" style="display: none;">
                    <div class="d-flex align-items-center gap-2">
                      <img id="currentImage" src="" alt="Current Asset Image" class="img-thumbnail" style="max-width: 150px; max-height: 100px; object-fit: cover;">
                      <div>
                        <small class="text-muted d-block">Current Image</small>
                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeCurrentImage">
                          <i class="bi bi-trash"></i> Remove Current
                        </button>
                      </div>
                    </div>
                  </div>
                  <input type="file" class="form-control" id="editAssetImage" name="asset_image" accept="image/*">
                  <div class="form-text">Upload a new photo to replace the current one (optional). Max size: 2MB</div>
                  <div id="editImagePreview" class="mt-2" style="display: none;">
                    <img id="editPreviewImg" src="" alt="New Asset Preview" class="img-thumbnail" style="max-width: 150px; max-height: 100px; object-fit: cover;">
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeEditImage">
                      <i class="bi bi-trash"></i> Remove New
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-outline-primary" id="editAssetBtn" style="display: none;">
            <i class="bi bi-pencil"></i> Edit
          </button>
          <button type="button" class="btn btn-primary" id="saveAssetBtn" style="display: none;">
            <i class="bi bi-check"></i> Save Changes
          </button>
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

      // Add loading animation to specific action buttons only
      document.querySelectorAll('.btn:not([data-bs-toggle]):not([data-bs-dismiss]):not(.btn-close)').forEach(btn => {
        btn.addEventListener('click', function(e) {
          if (!this.classList.contains('loading') && !this.hasAttribute('data-bs-toggle') && !this.hasAttribute('data-bs-dismiss')) {
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

      // Dynamic Asset action handlers
      const assetModal = new bootstrap.Modal(document.getElementById('assetModal'));
      
      // Helper functions for dynamic asset handling
      function getAssetName(asset) {
        switch(asset.asset_category) {
          case 'buildings': return asset.building_name || 'Building Asset';
          case 'vehicles': return asset.plate_number || 'Vehicle Asset';
          case 'machinery': return asset.equipment_name || 'Machinery Asset';
          case 'furniture': return asset.item_description || 'Furniture Asset';
          case 'it_equipment': return asset.item_name || 'IT Equipment Asset';
          case 'tools': return asset.tool_name || 'Tools Asset';
          case 'office_equipment': return asset.equipment_name || 'Office Equipment Asset';
          default: return asset.plate_number || `Asset #${asset.id}`;
        }
      }
      
      function getCategoryIcon(category) {
        const icons = {
          'buildings': 'bi bi-building',
          'vehicles': 'bi bi-truck',
          'machinery': 'bi bi-gear-wide-connected',
          'furniture': 'bi bi-chair',
          'it_equipment': 'bi bi-laptop',
          'tools': 'bi bi-tools',
          'office_equipment': 'bi bi-printer'
        };
        return icons[category] || 'bi bi-box';
      }
      
      function generateViewDetails(asset) {
        let html = '<div class="row g-3">';
        
        // Asset ID and Category
        html += `
          <div class="col-md-6">
            <strong>Asset ID:</strong>
            <p class="mb-2">${asset.asset_id || asset.id}</p>
          </div>
          <div class="col-md-6">
            <strong>Category:</strong>
            <p class="mb-2">${getCategoryLabel(asset.asset_category)}</p>
          </div>
        `;
        
        // Category-specific fields
        switch(asset.asset_category) {
          case 'buildings':
            html += `
              <div class="col-md-12"><strong>Building Name:</strong><p class="mb-2">${asset.building_name || 'N/A'}</p></div>
              <div class="col-md-12"><strong>Location Address:</strong><p class="mb-2">${asset.location_address || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Floor Area:</strong><p class="mb-2">${asset.floor_area ? asset.floor_area + ' sqm' : 'N/A'}</p></div>
              <div class="col-md-6"><strong>Current Use:</strong><p class="mb-2">${asset.current_use || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Responsible Department:</strong><p class="mb-2">${asset.responsible_department || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Custodian:</strong><p class="mb-2">${asset.custodian || 'N/A'}</p></div>
            `;
            break;
          case 'vehicles':
            html += `
              <div class="col-md-6"><strong>Plate Number:</strong><p class="mb-2">${asset.plate_number || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Vehicle Type:</strong><p class="mb-2">${asset.vehicle_type || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Brand/Model:</strong><p class="mb-2">${asset.brand_model || (asset.brand + ' ' + asset.model) || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Engine/Chassis:</strong><p class="mb-2">${asset.engine_chassis || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Assigned Driver:</strong><p class="mb-2">${asset.assigned_driver || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Department/Location:</strong><p class="mb-2">${asset.department_location || 'N/A'}</p></div>
            `;
            break;
          case 'machinery':
            html += `
              <div class="col-md-12"><strong>Equipment Name:</strong><p class="mb-2">${asset.equipment_name || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Brand/Model:</strong><p class="mb-2">${asset.brand_model || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Serial Number:</strong><p class="mb-2">${asset.serial_number || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Location/Site:</strong><p class="mb-2">${asset.location_site || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Assigned To:</strong><p class="mb-2">${asset.assigned_to || 'N/A'}</p></div>
            `;
            break;
          case 'furniture':
            html += `
              <div class="col-md-12"><strong>Item Description:</strong><p class="mb-2">${asset.item_description || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Brand/Model:</strong><p class="mb-2">${asset.brand_model || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Quantity:</strong><p class="mb-2">${asset.quantity || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Unit Cost:</strong><p class="mb-2">${asset.unit_cost ? '₱' + parseFloat(asset.unit_cost).toLocaleString() : 'N/A'}</p></div>
              <div class="col-md-6"><strong>Total Cost:</strong><p class="mb-2">${asset.total_cost ? '₱' + parseFloat(asset.total_cost).toLocaleString() : 'N/A'}</p></div>
            `;
            break;
          case 'it_equipment':
            html += `
              <div class="col-md-12"><strong>Item Name:</strong><p class="mb-2">${asset.item_name || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Brand/Model:</strong><p class="mb-2">${asset.brand_model || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Serial Number:</strong><p class="mb-2">${asset.serial_number || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Department:</strong><p class="mb-2">${asset.department || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Assigned To:</strong><p class="mb-2">${asset.assigned_to || 'N/A'}</p></div>
            `;
            break;
          case 'tools':
            html += `
              <div class="col-md-12"><strong>Tool Name:</strong><p class="mb-2">${asset.tool_name || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Brand/Model:</strong><p class="mb-2">${asset.brand_model || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Serial Number:</strong><p class="mb-2">${asset.serial_number || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Quantity:</strong><p class="mb-2">${asset.quantity || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Custodian:</strong><p class="mb-2">${asset.custodian || 'N/A'}</p></div>
            `;
            break;
          case 'office_equipment':
            html += `
              <div class="col-md-12"><strong>Equipment Name:</strong><p class="mb-2">${asset.equipment_name || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Brand/Model:</strong><p class="mb-2">${asset.brand_model || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Serial Number:</strong><p class="mb-2">${asset.serial_number || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Location:</strong><p class="mb-2">${asset.location || 'N/A'}</p></div>
              <div class="col-md-6"><strong>Assigned To:</strong><p class="mb-2">${asset.assigned_to || 'N/A'}</p></div>
            `;
            break;
        }
        
        // Common fields
        html += `
          <div class="col-md-6">
            <strong>Condition:</strong>
            <p class="mb-2">
              ${asset.condition ? `<span class="badge ${getConditionBadge(asset.condition)}">${asset.condition}</span>` : 'N/A'}
            </p>
          </div>
          <div class="col-md-6">
            <strong>Date Acquired:</strong>
            <p class="mb-2">${asset.date_acquired ? new Date(asset.date_acquired).toLocaleDateString() : 'N/A'}</p>
          </div>
        `;
        
        if (asset.acquisition_cost) {
          html += `
            <div class="col-md-6">
              <strong>Acquisition Cost:</strong>
              <p class="mb-2">₱${parseFloat(asset.acquisition_cost).toLocaleString()}</p>
            </div>
          `;
        }
        
        if (asset.remarks) {
          html += `
            <div class="col-12">
              <strong>Remarks:</strong>
              <p class="mb-2">${asset.remarks}</p>
            </div>
          `;
        }
        
        html += '</div>';
        return html;
      }
      
      function getCategoryLabel(category) {
        const labels = {
          'buildings': '🏢 Buildings & Structures',
          'vehicles': '🚚 Vehicles',
          'machinery': '⚙️ Machinery & Equipment',
          'furniture': '🪑 Furniture & Fixtures',
          'it_equipment': '💻 IT Equipment',
          'tools': '🧰 Tools & Instruments',
          'office_equipment': '🌐 Office Equipment'
        };
        return labels[category] || 'Other';
      }
      
      function getConditionBadge(condition) {
        const badges = {
          'Excellent': 'bg-success',
          'Good': 'bg-primary',
          'Fair': 'bg-warning',
          'Poor': 'bg-danger'
        };
        return badges[condition] || 'bg-secondary';
      }
      
      function generateEditForm(asset) {
        let html = '<div class="row g-3">';
        
        // Asset ID (always first)
        html += `
          <div class="col-md-6">
            <label class="form-label">Asset ID <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="asset_id" value="${asset.asset_id || ''}" required>
          </div>
        `;
        
        // Category-specific fields
        switch(asset.asset_category) {
          case 'buildings':
            html += `
              <div class="col-md-6">
                <label class="form-label">Building Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="building_name" value="${asset.building_name || ''}" required>
              </div>
              <div class="col-12">
                <label class="form-label">Location Address <span class="text-danger">*</span></label>
                <textarea class="form-control" name="location_address" rows="2" required>${asset.location_address || ''}</textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Floor Area (sqm)</label>
                <input type="number" class="form-control" name="floor_area" value="${asset.floor_area || ''}" step="0.01">
              </div>
              <div class="col-md-6">
                <label class="form-label">Current Use</label>
                <input type="text" class="form-control" name="current_use" value="${asset.current_use || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Responsible Department</label>
                <input type="text" class="form-control" name="responsible_department" value="${asset.responsible_department || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Custodian</label>
                <input type="text" class="form-control" name="custodian" value="${asset.custodian || ''}">
              </div>
            `;
            break;
          case 'vehicles':
            html += `
              <div class="col-md-6">
                <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                <select class="form-select" name="vehicle_type" required>
                  <option value="">Select Type</option>
                  <option value="Car" ${asset.vehicle_type === 'Car' ? 'selected' : ''}>Car</option>
                  <option value="Van" ${asset.vehicle_type === 'Van' ? 'selected' : ''}>Van</option>
                  <option value="Bus" ${asset.vehicle_type === 'Bus' ? 'selected' : ''}>Bus</option>
                  <option value="Truck" ${asset.vehicle_type === 'Truck' ? 'selected' : ''}>Truck</option>
                  <option value="Motorcycle" ${asset.vehicle_type === 'Motorcycle' ? 'selected' : ''}>Motorcycle</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Plate Number <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="plate_number" value="${asset.plate_number || ''}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Engine/Chassis</label>
                <input type="text" class="form-control" name="engine_chassis" value="${asset.engine_chassis || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Supplier</label>
                <input type="text" class="form-control" name="supplier" value="${asset.supplier || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned Driver</label>
                <input type="text" class="form-control" name="assigned_driver" value="${asset.assigned_driver || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department/Location</label>
                <input type="text" class="form-control" name="department_location" value="${asset.department_location || ''}">
              </div>
            `;
            break;
          case 'machinery':
            html += `
              <div class="col-md-6">
                <label class="form-label">Equipment Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="equipment_name" value="${asset.equipment_name || ''}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Brand/Model</label>
                <input type="text" class="form-control" name="brand_model" value="${asset.brand_model || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" name="serial_number" value="${asset.serial_number || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Location/Site</label>
                <input type="text" class="form-control" name="location_site" value="${asset.location_site || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned To</label>
                <input type="text" class="form-control" name="assigned_to" value="${asset.assigned_to || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Maintenance Frequency</label>
                <select class="form-select" name="maintenance_frequency">
                  <option value="">Select Frequency</option>
                  <option value="Weekly" ${asset.maintenance_frequency === 'Weekly' ? 'selected' : ''}>Weekly</option>
                  <option value="Monthly" ${asset.maintenance_frequency === 'Monthly' ? 'selected' : ''}>Monthly</option>
                  <option value="Quarterly" ${asset.maintenance_frequency === 'Quarterly' ? 'selected' : ''}>Quarterly</option>
                  <option value="Semi-Annual" ${asset.maintenance_frequency === 'Semi-Annual' ? 'selected' : ''}>Semi-Annual</option>
                  <option value="Annual" ${asset.maintenance_frequency === 'Annual' ? 'selected' : ''}>Annual</option>
                </select>
              </div>
            `;
            break;
          case 'furniture':
            html += `
              <div class="col-md-6">
                <label class="form-label">Item Description <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="item_description" value="${asset.item_description || ''}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Brand/Model</label>
                <input type="text" class="form-control" name="brand_model" value="${asset.brand_model || ''}">
              </div>
              <div class="col-md-4">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity" value="${asset.quantity || ''}" min="1">
              </div>
              <div class="col-md-4">
                <label class="form-label">Unit Cost (₱)</label>
                <input type="number" class="form-control" name="unit_cost" value="${asset.unit_cost || ''}" step="0.01">
              </div>
              <div class="col-md-4">
                <label class="form-label">Total Cost (₱)</label>
                <input type="number" class="form-control" name="total_cost" value="${asset.total_cost || ''}" step="0.01" readonly>
              </div>
              <div class="col-md-6">
                <label class="form-label">Location/Office</label>
                <input type="text" class="form-control" name="location_office" value="${asset.location_office || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned User</label>
                <input type="text" class="form-control" name="assigned_user" value="${asset.assigned_user || ''}">
              </div>
            `;
            break;
          case 'it_equipment':
            html += `
              <div class="col-md-6">
                <label class="form-label">Item Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="item_name" value="${asset.item_name || ''}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Brand/Model</label>
                <input type="text" class="form-control" name="brand_model" value="${asset.brand_model || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" name="serial_number" value="${asset.serial_number || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department</label>
                <input type="text" class="form-control" name="department" value="${asset.department || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned To</label>
                <input type="text" class="form-control" name="assigned_to" value="${asset.assigned_to || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Warranty Expiry</label>
                <input type="date" class="form-control" name="warranty_expiry" value="${asset.warranty_expiry || ''}">
              </div>
            `;
            break;
          case 'tools':
            html += `
              <div class="col-md-6">
                <label class="form-label">Tool Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="tool_name" value="${asset.tool_name || ''}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Brand/Model</label>
                <input type="text" class="form-control" name="brand_model" value="${asset.brand_model || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" name="serial_number" value="${asset.serial_number || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Quantity</label>
                <input type="number" class="form-control" name="quantity" value="${asset.quantity || ''}" min="1">
              </div>
              <div class="col-md-6">
                <label class="form-label">Custodian</label>
                <input type="text" class="form-control" name="custodian" value="${asset.custodian || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" name="location" value="${asset.location || ''}">
              </div>
            `;
            break;
          case 'office_equipment':
            html += `
              <div class="col-md-6">
                <label class="form-label">Equipment Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="equipment_name" value="${asset.equipment_name || ''}" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Brand/Model</label>
                <input type="text" class="form-control" name="brand_model" value="${asset.brand_model || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Serial Number</label>
                <input type="text" class="form-control" name="serial_number" value="${asset.serial_number || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Location</label>
                <input type="text" class="form-control" name="location" value="${asset.location || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Assigned To</label>
                <input type="text" class="form-control" name="assigned_to" value="${asset.assigned_to || ''}">
              </div>
              <div class="col-md-6">
                <label class="form-label">Maintenance Schedule</label>
                <select class="form-select" name="maintenance_schedule">
                  <option value="">Select Schedule</option>
                  <option value="Monthly" ${asset.maintenance_schedule === 'Monthly' ? 'selected' : ''}>Monthly</option>
                  <option value="Quarterly" ${asset.maintenance_schedule === 'Quarterly' ? 'selected' : ''}>Quarterly</option>
                  <option value="Semi-Annual" ${asset.maintenance_schedule === 'Semi-Annual' ? 'selected' : ''}>Semi-Annual</option>
                  <option value="Annual" ${asset.maintenance_schedule === 'Annual' ? 'selected' : ''}>Annual</option>
                </select>
              </div>
            `;
            break;
        }
        
        // Common fields
        html += `
          <div class="col-md-6">
            <label class="form-label">Date Acquired</label>
            <input type="date" class="form-control" name="date_acquired" value="${asset.date_acquired || ''}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Acquisition Cost (₱)</label>
            <input type="number" class="form-control" name="acquisition_cost" value="${asset.acquisition_cost || ''}" step="0.01">
          </div>
          <div class="col-md-6">
            <label class="form-label">Condition</label>
            <select class="form-select" name="condition">
              <option value="">Select Condition</option>
              <option value="Excellent" ${asset.condition === 'Excellent' ? 'selected' : ''}>Excellent</option>
              <option value="Good" ${asset.condition === 'Good' ? 'selected' : ''}>Good</option>
              <option value="Fair" ${asset.condition === 'Fair' ? 'selected' : ''}>Fair</option>
              <option value="Poor" ${asset.condition === 'Poor' ? 'selected' : ''}>Poor</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">Remarks</label>
            <textarea class="form-control" name="remarks" rows="3">${asset.remarks || ''}</textarea>
          </div>
        `;
        
        html += '</div>';
        return html;
      }
      
      // Dynamic view asset handler
      document.querySelectorAll('.view-asset-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
          e.preventDefault();
          const id = this.getAttribute('data-id');
          try {
            const res = await fetch(`{{ url('/alms/assets') }}/${id}`);
            if (!res.ok) return;
            const json = await res.json();
            const asset = json.data;
            
            // Show view mode, hide edit mode
            document.getElementById('viewMode').style.display = 'block';
            document.getElementById('editMode').style.display = 'none';
            document.getElementById('editAssetBtn').style.display = 'inline-block';
            document.getElementById('saveAssetBtn').style.display = 'none';
            
            // Set modal title
            const assetName = getAssetName(asset);
            document.getElementById('assetModalLabel').textContent = `${assetName} - Details`;
            
            // Handle image display
            const viewImageContainer = document.getElementById('viewImageContainer');
            const viewNoImageContainer = document.getElementById('viewNoImageContainer');
            const viewImage = document.getElementById('viewImage');
            const viewCategoryIcon = document.getElementById('viewCategoryIcon');
            
            if (asset.image_path) {
              viewImage.src = `{{ asset('storage') }}/${asset.image_path}`;
              viewImageContainer.style.display = 'block';
              viewNoImageContainer.style.display = 'none';
            } else {
              viewImageContainer.style.display = 'none';
              viewNoImageContainer.style.display = 'block';
              viewCategoryIcon.className = getCategoryIcon(asset.asset_category) + ' text-muted mb-2';
            }
            
            // Populate dynamic view details
            document.getElementById('viewDetailsContainer').innerHTML = generateViewDetails(asset);
            
            // Store asset data for edit mode
            document.getElementById('editId').value = asset.id;
            
            assetModal.show();
          } catch (err) {
            console.error(err);
          }
        });
      });

      // Dynamic edit asset handler
      document.querySelectorAll('.edit-asset-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
          e.preventDefault();
          const id = this.getAttribute('data-id');
          try {
            const res = await fetch(`{{ url('/alms/assets') }}/${id}`);
            if (!res.ok) return;
            const json = await res.json();
            const asset = json.data;
            
            // Show edit mode, hide view mode
            document.getElementById('viewMode').style.display = 'none';
            document.getElementById('editMode').style.display = 'block';
            document.getElementById('editAssetBtn').style.display = 'none';
            document.getElementById('saveAssetBtn').style.display = 'inline-block';
            
            // Set modal title
            const assetName = getAssetName(asset);
            document.getElementById('assetModalLabel').textContent = `Edit ${assetName}`;
            document.getElementById('editId').value = asset.id;
            document.getElementById('editCategory').value = asset.asset_category;
            
            // Generate dynamic edit form
            document.getElementById('editFormContainer').innerHTML = generateEditForm(asset);
            
            // Handle current image display
            const currentImageDisplay = document.getElementById('currentImageDisplay');
            const currentImage = document.getElementById('currentImage');
            if (asset.image_path) {
              currentImage.src = `{{ asset('storage') }}/${asset.image_path}`;
              currentImageDisplay.style.display = 'block';
            } else {
              currentImageDisplay.style.display = 'none';
            }
            
            // Reset edit image preview
            document.getElementById('editImagePreview').style.display = 'none';
            document.getElementById('editAssetImage').value = '';
            
            assetModal.show();
          } catch (err) {
            console.error(err);
          }
        });
      });

      // Edit Asset Button (from view mode to edit mode)
      document.getElementById('editAssetBtn')?.addEventListener('click', function() {
        document.getElementById('viewMode').style.display = 'none';
        document.getElementById('editMode').style.display = 'block';
        document.getElementById('editAssetBtn').style.display = 'none';
        document.getElementById('saveAssetBtn').style.display = 'inline-block';
        
        // Get asset data and populate edit form
        const id = document.getElementById('editId').value;
        fetch(`{{ url('/alms/assets') }}/${id}`)
          .then(res => res.json())
          .then(json => {
            const asset = json.data;
            const assetName = getAssetName(asset);
            document.getElementById('assetModalLabel').textContent = `Edit ${assetName}`;
            document.getElementById('editCategory').value = asset.asset_category;
            
            // Generate dynamic edit form
            document.getElementById('editFormContainer').innerHTML = generateEditForm(asset);
            
            // Handle current image display
            const currentImageDisplay = document.getElementById('currentImageDisplay');
            const currentImage = document.getElementById('currentImage');
            if (asset.image_path) {
              currentImage.src = `{{ asset('storage') }}/${asset.image_path}`;
              currentImageDisplay.style.display = 'block';
            } else {
              currentImageDisplay.style.display = 'none';
            }
            
            // Reset edit image preview
            document.getElementById('editImagePreview').style.display = 'none';
            document.getElementById('editAssetImage').value = '';
          })
          .catch(err => console.error(err));
      });

      // Dynamic save asset handler
      document.getElementById('saveAssetBtn')?.addEventListener('click', async function(e) {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        const category = document.getElementById('editCategory').value;
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Show loading state
        Swal.fire({
          title: 'Updating Asset...',
          text: 'Please wait while we save your changes',
          icon: 'info',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        
        try {
          // Create FormData from the dynamic form
          const formData = new FormData();
          formData.append('_method', 'PUT');
          formData.append('_token', csrfToken);
          formData.append('asset_category', category);
          
          // Get all form inputs from the dynamic form
          const formContainer = document.getElementById('editFormContainer');
          const inputs = formContainer.querySelectorAll('input, select, textarea');
          
          inputs.forEach(input => {
            if (input.type === 'file') return; // Skip file inputs
            if (input.name && input.value) {
              formData.append(input.name, input.value);
            }
          });
          
          // Handle image upload
          const editImageFile = document.getElementById('editAssetImage').files[0];
          if (editImageFile && editImageFile.size > 0) {
            formData.append('asset_image', editImageFile);
          }
          
          const res = await fetch(`{{ url('/alms/assets') }}/${id}`, {
            method: 'POST',
            headers: {
              'Accept': 'application/json'
            },
            body: formData
          });
          
          if (!res.ok) {
            const errorData = await res.json().catch(() => ({}));
            console.error('Update error:', errorData);
            
            Swal.fire({
              title: 'Update Failed!',
              text: errorData.message || 'Failed to save changes. Please try again.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
            return;
          }
          
          const result = await res.json();
          
          // Show success message
          Swal.fire({
            title: 'Success!',
            text: 'Asset updated successfully!',
            icon: 'success',
            timer: 2000,
            timerProgressBar: true,
            showConfirmButton: false
          }).then(() => {
            assetModal.hide();
            // Force a hard refresh to ensure updated data is displayed
            window.location.reload(true);
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

      // Image preview functionality
      const vehicleImageInput = document.getElementById('vehicleImage');
      const imagePreview = document.getElementById('imagePreview');
      const previewImg = document.getElementById('previewImg');
      const removeImageBtn = document.getElementById('removeImage');
      
      const editVehicleImageInput = document.getElementById('editVehicleImage');
      const editImagePreview = document.getElementById('editImagePreview');
      const editPreviewImg = document.getElementById('editPreviewImg');
      const removeEditImageBtn = document.getElementById('removeEditImage');
      const removeCurrentImageBtn = document.getElementById('removeCurrentImage');
      
      // Handle new vehicle image preview
      if (vehicleImageInput) {
        vehicleImageInput.addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
              showNotification('error', 'Please select a valid image file (JPEG, PNG, GIF, or WEBP)');
              this.value = '';
              imagePreview.style.display = 'none';
              return;
            }
            
            // Validate file size (2MB = 2048KB = 2097152 bytes)
            if (file.size > 2097152) {
              showNotification('error', 'Image file size must be less than 2MB');
              this.value = '';
              imagePreview.style.display = 'none';
              return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
              previewImg.src = e.target.result;
              imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
          } else {
            imagePreview.style.display = 'none';
            previewImg.src = '';
          }
        });
      }
      
      // Remove new vehicle image preview
      if (removeImageBtn) {
        removeImageBtn.addEventListener('click', function() {
          vehicleImageInput.value = '';
          imagePreview.style.display = 'none';
          previewImg.src = '';
        });
      }
      
      // Handle edit vehicle image preview
      if (editVehicleImageInput) {
        editVehicleImageInput.addEventListener('change', function(e) {
          const file = e.target.files[0];
          if (file) {
            // Validate file type
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
              showNotification('error', 'Please select a valid image file (JPEG, PNG, GIF, or WEBP)');
              this.value = '';
              editImagePreview.style.display = 'none';
              return;
            }
            
            // Validate file size (2MB = 2048KB = 2097152 bytes)
            if (file.size > 2097152) {
              showNotification('error', 'Image file size must be less than 2MB');
              this.value = '';
              editImagePreview.style.display = 'none';
              return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
              editPreviewImg.src = e.target.result;
              editImagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
          } else {
            editImagePreview.style.display = 'none';
            editPreviewImg.src = '';
          }
        });
      }
      
      // Remove edit image preview
      if (removeEditImageBtn) {
        removeEditImageBtn.addEventListener('click', function() {
          editVehicleImageInput.value = '';
          editImagePreview.style.display = 'none';
          editPreviewImg.src = '';
        });
      }
      
      // Remove current image (for edit modal)
      if (removeCurrentImageBtn) {
        removeCurrentImageBtn.addEventListener('click', function() {
          const currentImageDisplay = document.getElementById('currentImageDisplay');
          currentImageDisplay.style.display = 'none';
          // Add a hidden input to indicate image should be removed
          let removeImageInput = document.getElementById('removeCurrentImageFlag');
          if (!removeImageInput) {
            removeImageInput = document.createElement('input');
            removeImageInput.type = 'hidden';
            removeImageInput.id = 'removeCurrentImageFlag';
            removeImageInput.name = 'remove_current_image';
            removeImageInput.value = '1';
            document.getElementById('assetEditForm').appendChild(removeImageInput);
          }
        });
      }

      // Dynamic Asset Form handling
      const assetForm = document.getElementById('assetForm');
      const newAssetModal = new bootstrap.Modal(document.getElementById('newVehicleModal'));
      
      // Handle dynamic asset form submission
      if (assetForm) {
        assetForm.addEventListener('submit', async function(e) {
          e.preventDefault();
          
          // Get selected category
          const selectedCategory = document.getElementById('assetCategory').value;
          if (!selectedCategory) {
            showNotification('error', 'Please select an asset category first');
            return;
          }
          
          // Show loading state
          Swal.fire({
            title: 'Registering Asset...',
            text: 'Please wait while we save your asset information',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
          
          try {
            const formData = new FormData(assetForm);
            
            const response = await fetch("{{ url('/alms/assetregistration') }}", {
              method: 'POST',
              headers: {
                'Accept': 'application/json'
              },
              body: formData
            });

            if (!response.ok) {
              const errorData = await response.json().catch(() => ({}));
              
              Swal.fire({
                title: 'Registration Failed!',
                text: errorData.message || 'Failed to register asset. Please check your inputs and try again.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
              });
              return;
            }

            // Show success message
            Swal.fire({
              title: 'Success!',
              text: 'Physical asset registered successfully!',
              icon: 'success',
              timer: 2000,
              timerProgressBar: true,
              showConfirmButton: false
            }).then(() => {
              newAssetModal.hide();
              // Reload to fetch updated list from database
              window.location.href = "{{ url('/alms/assetregistration') }}";
            });
            
          } catch (ex) {
            console.error(ex);
            Swal.fire({
              title: 'Error!',
              text: 'An unexpected error occurred. Please try again.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
          }
        });
      }

      // Vehicle form handling (legacy support)
      const vehicleForm = document.getElementById('vehicleForm');
      const newVehicleModal = new bootstrap.Modal(document.getElementById('newVehicleModal'));
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
          const vehicleImage = document.getElementById('vehicleImage').files[0];
          
          // Basic validation
          if (!plateNumber || !vehicleType || !regDate) {
            showNotification('error', 'Please fill in all required fields (marked with *)');
            return;
          }
          
          // Show loading state
          Swal.fire({
            title: 'Registering Asset...',
            text: 'Please wait while we save your asset information',
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
            
            // Always use FormData for consistency
            const formData = new FormData(vehicleForm);
            
            const response = await fetch("{{ url('/alms/assetregistration') }}", {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
              },
              body: formData
            });

            if (!response.ok) {
              const errorData = await response.json().catch(() => ({}));
              
              Swal.fire({
                title: 'Registration Failed!',
                text: errorData.message || 'Failed to register asset. Please check your inputs and try again.',
                icon: 'error',
                confirmButtonColor: '#dc3545'
              });
              return;
            }

            // Show success message
            Swal.fire({
              title: 'Success!',
              text: 'Physical asset registered successfully!',
              icon: 'success',
              timer: 2000,
              timerProgressBar: true,
              showConfirmButton: false
            }).then(() => {
              newVehicleModal.hide();
              // Reload to fetch updated list from database
              window.location.href = "{{ url('/alms/assetregistration') }}";
            });
            
          } catch (ex) {
            console.error(ex);
            Swal.fire({
              title: 'Error!',
              text: 'An unexpected error occurred. Please try again.',
              icon: 'error',
              confirmButtonColor: '#dc3545'
            });
          }
        });
      }
      
      // Reset form when modal is hidden
      document.getElementById('newVehicleModal').addEventListener('hidden.bs.modal', function() {
        vehicleForm.reset();
        if (registrationDate) {
          registrationDate.value = today;
        }
        // Reset image preview
        if (imagePreview) {
          imagePreview.style.display = 'none';
        }
        if (previewImg) {
          previewImg.src = '';
        }
      });
      
      // Search and filter functionality
      const searchInput = document.getElementById('searchVehicles');
      const filterType = document.getElementById('filterType');
      const filterStatus = document.getElementById('filterStatus');
      const vehicleTable = document.getElementById('vehicleTable');
      
      function filterVehicles() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const typeFilter = filterType?.value || '';
        const statusFilter = filterStatus?.value || '';
        const rows = vehicleTable?.querySelectorAll('tbody tr') || [];
        
        rows.forEach(row => {
          const plateNumber = row.cells[1]?.textContent.toLowerCase() || '';
          const type = row.cells[2]?.textContent || '';
          const brandModel = row.cells[3]?.textContent.toLowerCase() || '';
          const status = row.cells[6]?.textContent || '';
          
          const matchesSearch = plateNumber.includes(searchTerm) || brandModel.includes(searchTerm);
          const matchesType = !typeFilter || type.includes(typeFilter);
          const matchesStatus = !statusFilter || status.includes(statusFilter);
          
          row.style.display = matchesSearch && matchesType && matchesStatus ? '' : 'none';
        });
      }
      
      searchInput?.addEventListener('input', filterVehicles);
      filterType?.addEventListener('change', filterVehicles);
      filterStatus?.addEventListener('change', filterVehicles);
      
      // Clear filters functionality
      const clearFiltersBtn = document.getElementById('clearFilters');
      if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
          if (searchInput) searchInput.value = '';
          if (filterType) filterType.value = '';
          if (filterStatus) filterStatus.value = '';
          filterVehicles();
          showNotification('info', 'Filters have been cleared', 'Filters Reset');
        });
      }
      
      // SweetAlert2 Notification function
      function showNotification(type, message, title = null) {
        const config = {
          text: message,
          timer: 3000,
          timerProgressBar: true,
          showConfirmButton: false,
          toast: true,
          position: 'top-end'
        };

        switch (type) {
          case 'success':
            config.icon = 'success';
            config.title = title || 'Success!';
            config.iconColor = '#28a745';
            break;
          case 'error':
            config.icon = 'error';
            config.title = title || 'Error!';
            config.iconColor = '#dc3545';
            config.timer = 4000;
            break;
          case 'warning':
            config.icon = 'warning';
            config.title = title || 'Warning!';
            config.iconColor = '#ffc107';
            break;
          case 'info':
            config.icon = 'info';
            config.title = title || 'Info';
            config.iconColor = '#17a2b8';
            break;
          default:
            config.icon = 'info';
            config.title = title || 'Notification';
        }

        Swal.fire(config);
      }

      // SweetAlert2 Confirmation function
      function showConfirmation(title, text, confirmText = 'Yes', cancelText = 'Cancel') {
        return Swal.fire({
          title: title,
          text: text,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#0d6efd',
          cancelButtonColor: '#6c757d',
          confirmButtonText: confirmText,
          cancelButtonText: cancelText,
          reverseButtons: true
        });
      }


      // Dynamic Asset Form Generation
      const assetCategorySelect = document.getElementById('assetCategory');
      const dynamicFormFields = document.getElementById('dynamicFormFields');

      // Form templates for each category
      const formTemplates = {
        buildings: `
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Asset ID / Tag No. <span class="text-danger">*</span></label>
              <input type="text" name="asset_id" class="form-control" placeholder="Enter asset ID" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Building Name / Description <span class="text-danger">*</span></label>
              <input type="text" name="building_name" class="form-control" placeholder="Enter building name" required>
            </div>
            <div class="col-12">
              <label class="form-label">Location / Address <span class="text-danger">*</span></label>
              <textarea name="location_address" class="form-control" rows="2" placeholder="Enter complete address" required></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Floor Area (sqm)</label>
              <input type="number" name="floor_area" class="form-control" placeholder="0" step="0.01" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date Constructed / Acquired</label>
              <input type="date" name="date_acquired" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Acquisition Cost (₱)</label>
              <input type="number" name="acquisition_cost" class="form-control" placeholder="0" step="0.01" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Condition</label>
              <select name="condition" class="form-select">
                <option value="">Select condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Current Use</label>
              <input type="text" name="current_use" class="form-control" placeholder="e.g., Office, Warehouse">
            </div>
            <div class="col-md-6">
              <label class="form-label">Responsible Department</label>
              <input type="text" name="responsible_department" class="form-control" placeholder="Enter department">
            </div>
            <div class="col-md-6">
              <label class="form-label">Custodian / Supervisor</label>
              <input type="text" name="custodian" class="form-control" placeholder="Enter custodian name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Inspection Date</label>
              <input type="date" name="last_inspection" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes or remarks"></textarea>
            </div>
          </div>
        `,
        vehicles: `
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Asset ID / Tag No. <span class="text-danger">*</span></label>
              <input type="text" name="asset_id" class="form-control" placeholder="Enter asset ID" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Vehicle Type / Model <span class="text-danger">*</span></label>
              <input type="text" name="vehicle_type" class="form-control" placeholder="e.g., Sedan, Truck, Van" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Plate Number <span class="text-danger">*</span></label>
              <input type="text" name="plate_number" class="form-control" placeholder="Enter plate number" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Engine / Chassis No.</label>
              <input type="text" name="engine_chassis" class="form-control" placeholder="Enter engine/chassis number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date Acquired</label>
              <input type="date" name="date_acquired" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Supplier / Vendor</label>
              <input type="text" name="supplier" class="form-control" placeholder="Enter supplier name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Acquisition Cost (₱)</label>
              <input type="number" name="acquisition_cost" class="form-control" placeholder="0" step="0.01" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Condition</label>
              <select name="condition" class="form-select">
                <option value="">Select condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Operational Status</label>
              <select name="operational_status" class="form-select">
                <option value="">Select status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Under Maintenance">Under Maintenance</option>
                <option value="Retired">Retired</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Assigned Driver / Custodian</label>
              <input type="text" name="assigned_driver" class="form-control" placeholder="Enter driver name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Department / Location</label>
              <input type="text" name="department_location" class="form-control" placeholder="Enter department/location">
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Maintenance</label>
              <input type="date" name="last_maintenance" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Next Maintenance Due</label>
              <input type="date" name="next_maintenance" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes or remarks"></textarea>
            </div>
          </div>
        `,
        machinery: `
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Asset ID / Tag No. <span class="text-danger">*</span></label>
              <input type="text" name="asset_id" class="form-control" placeholder="Enter asset ID" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Equipment Name / Type <span class="text-danger">*</span></label>
              <input type="text" name="equipment_name" class="form-control" placeholder="Enter equipment name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Brand / Model</label>
              <input type="text" name="brand_model" class="form-control" placeholder="Enter brand and model">
            </div>
            <div class="col-md-6">
              <label class="form-label">Serial Number</label>
              <input type="text" name="serial_number" class="form-control" placeholder="Enter serial number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date Acquired</label>
              <input type="date" name="date_acquired" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Acquisition Cost (₱)</label>
              <input type="number" name="acquisition_cost" class="form-control" placeholder="0" step="0.01" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Condition</label>
              <select name="condition" class="form-select">
                <option value="">Select condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location / Site</label>
              <input type="text" name="location_site" class="form-control" placeholder="Enter location">
            </div>
            <div class="col-md-6">
              <label class="form-label">Assigned To</label>
              <input type="text" name="assigned_to" class="form-control" placeholder="Enter assigned person">
            </div>
            <div class="col-md-6">
              <label class="form-label">Maintenance Frequency</label>
              <select name="maintenance_frequency" class="form-select">
                <option value="">Select frequency</option>
                <option value="Daily">Daily</option>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Annually">Annually</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Last Service Date</label>
              <input type="date" name="last_service" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Next Service Date</label>
              <input type="date" name="next_service" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes or remarks"></textarea>
            </div>
          </div>
        `,
        furniture: `
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Asset ID / Tag No. <span class="text-danger">*</span></label>
              <input type="text" name="asset_id" class="form-control" placeholder="Enter asset ID" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Item Description <span class="text-danger">*</span></label>
              <input type="text" name="item_description" class="form-control" placeholder="Enter item description" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Brand / Model</label>
              <input type="text" name="brand_model" class="form-control" placeholder="Enter brand and model">
            </div>
            <div class="col-md-6">
              <label class="form-label">Quantity</label>
              <input type="number" name="quantity" class="form-control" placeholder="1" min="1">
            </div>
            <div class="col-md-6">
              <label class="form-label">Unit Cost (₱)</label>
              <input type="number" name="unit_cost" class="form-control" placeholder="0" step="0.01" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Total Cost (₱)</label>
              <input type="number" name="total_cost" class="form-control" placeholder="0" step="0.01" min="0" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location / Office</label>
              <input type="text" name="location_office" class="form-control" placeholder="Enter location/office">
            </div>
            <div class="col-md-6">
              <label class="form-label">Condition</label>
              <select name="condition" class="form-select">
                <option value="">Select condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Assigned User / Department</label>
              <input type="text" name="assigned_user" class="form-control" placeholder="Enter assigned user/department">
            </div>
            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes or remarks"></textarea>
            </div>
          </div>
        `,
        it_equipment: `
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Asset ID / Tag No. <span class="text-danger">*</span></label>
              <input type="text" name="asset_id" class="form-control" placeholder="Enter asset ID" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Item Name / Description <span class="text-danger">*</span></label>
              <input type="text" name="item_name" class="form-control" placeholder="Enter item name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Serial Number</label>
              <input type="text" name="serial_number" class="form-control" placeholder="Enter serial number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Brand / Model</label>
              <input type="text" name="brand_model" class="form-control" placeholder="Enter brand and model">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date Acquired</label>
              <input type="date" name="date_acquired" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cost (₱)</label>
              <input type="number" name="cost" class="form-control" placeholder="0" step="0.01" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Condition</label>
              <select name="condition" class="form-select">
                <option value="">Select condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Assigned To</label>
              <input type="text" name="assigned_to" class="form-control" placeholder="Enter assigned person">
            </div>
            <div class="col-md-6">
              <label class="form-label">Department</label>
              <input type="text" name="department" class="form-control" placeholder="Enter department">
            </div>
            <div class="col-md-6">
              <label class="form-label">Warranty Expiry Date</label>
              <input type="date" name="warranty_expiry" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Status</label>
              <select name="status" class="form-select">
                <option value="">Select status</option>
                <option value="Active">Active</option>
                <option value="Inactive">Inactive</option>
                <option value="Under Repair">Under Repair</option>
                <option value="Retired">Retired</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes or remarks"></textarea>
            </div>
          </div>
        `,
        tools: `
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Asset ID / Tag No. <span class="text-danger">*</span></label>
              <input type="text" name="asset_id" class="form-control" placeholder="Enter asset ID" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Tool Name / Description <span class="text-danger">*</span></label>
              <input type="text" name="tool_name" class="form-control" placeholder="Enter tool name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Brand / Model</label>
              <input type="text" name="brand_model" class="form-control" placeholder="Enter brand and model">
            </div>
            <div class="col-md-6">
              <label class="form-label">Serial Number</label>
              <input type="text" name="serial_number" class="form-control" placeholder="Enter serial number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Quantity</label>
              <input type="number" name="quantity" class="form-control" placeholder="1" min="1">
            </div>
            <div class="col-md-6">
              <label class="form-label">Unit Cost (₱)</label>
              <input type="number" name="unit_cost" class="form-control" placeholder="0" step="0.01" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Total Cost (₱)</label>
              <input type="number" name="total_cost" class="form-control" placeholder="0" step="0.01" min="0" readonly>
            </div>
            <div class="col-md-6">
              <label class="form-label">Custodian</label>
              <input type="text" name="custodian" class="form-control" placeholder="Enter custodian name">
            </div>
            <div class="col-md-6">
              <label class="form-label">Condition</label>
              <select name="condition" class="form-select">
                <option value="">Select condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <input type="text" name="location" class="form-control" placeholder="Enter location">
            </div>
            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes or remarks"></textarea>
            </div>
          </div>
        `,
        office_equipment: `
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Asset ID / Tag No. <span class="text-danger">*</span></label>
              <input type="text" name="asset_id" class="form-control" placeholder="Enter asset ID" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Equipment Name <span class="text-danger">*</span></label>
              <input type="text" name="equipment_name" class="form-control" placeholder="Enter equipment name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Brand / Model</label>
              <input type="text" name="brand_model" class="form-control" placeholder="Enter brand and model">
            </div>
            <div class="col-md-6">
              <label class="form-label">Serial Number</label>
              <input type="text" name="serial_number" class="form-control" placeholder="Enter serial number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Date Acquired</label>
              <input type="date" name="date_acquired" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Cost (₱)</label>
              <input type="number" name="cost" class="form-control" placeholder="0" step="0.01" min="0">
            </div>
            <div class="col-md-6">
              <label class="form-label">Location</label>
              <input type="text" name="location" class="form-control" placeholder="Enter location">
            </div>
            <div class="col-md-6">
              <label class="form-label">Condition</label>
              <select name="condition" class="form-select">
                <option value="">Select condition</option>
                <option value="Excellent">Excellent</option>
                <option value="Good">Good</option>
                <option value="Fair">Fair</option>
                <option value="Poor">Poor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Assigned To</label>
              <input type="text" name="assigned_to" class="form-control" placeholder="Enter assigned person">
            </div>
            <div class="col-md-6">
              <label class="form-label">Maintenance Schedule</label>
              <select name="maintenance_schedule" class="form-select">
                <option value="">Select schedule</option>
                <option value="Weekly">Weekly</option>
                <option value="Monthly">Monthly</option>
                <option value="Quarterly">Quarterly</option>
                <option value="Annually">Annually</option>
                <option value="As Needed">As Needed</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control" rows="3" placeholder="Additional notes or remarks"></textarea>
            </div>
          </div>
        `
      };

      // Handle category selection change
      if (assetCategorySelect) {
        assetCategorySelect.addEventListener('change', function() {
          const selectedCategory = this.value;
          const assetImageField = document.getElementById('assetImageField');
          
          if (selectedCategory && formTemplates[selectedCategory]) {
            dynamicFormFields.innerHTML = formTemplates[selectedCategory];
            
            // Show the asset image upload field
            if (assetImageField) {
              assetImageField.style.display = 'block';
            }
            
            // Add automatic calculation for furniture and tools categories
            if (selectedCategory === 'furniture' || selectedCategory === 'tools') {
              setupCostCalculation();
            }
          } else {
            dynamicFormFields.innerHTML = `
              <div class="text-center text-muted py-4">
                <i class="bi bi-arrow-up fs-1 mb-2 d-block"></i>
                <p>Please select an asset category above to display the appropriate form fields.</p>
              </div>
            `;
            
            // Hide the asset image upload field
            if (assetImageField) {
              assetImageField.style.display = 'none';
            }
          }
        });
      }

      // Setup automatic cost calculation for furniture and tools
      function setupCostCalculation() {
        const quantityInput = document.querySelector('input[name="quantity"]');
        const unitCostInput = document.querySelector('input[name="unit_cost"]');
        const totalCostInput = document.querySelector('input[name="total_cost"]');

        function calculateTotal() {
          const quantity = parseFloat(quantityInput?.value) || 0;
          const unitCost = parseFloat(unitCostInput?.value) || 0;
          const total = quantity * unitCost;
          
          if (totalCostInput) {
            totalCostInput.value = total.toFixed(2);
          }
        }

        if (quantityInput && unitCostInput) {
          quantityInput.addEventListener('input', calculateTotal);
          unitCostInput.addEventListener('input', calculateTotal);
        }
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
