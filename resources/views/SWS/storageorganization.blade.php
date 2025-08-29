<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Vehicle Storage Organization - Smart Warehousing</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <!-- Storage Layout Custom Styles -->
  <style>
    /* Clean UI Improvements */
    .storage-grid {
      padding: 1.5rem 0;
    }
    
    .storage-bin {
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 1rem;
      min-height: 140px;
      transition: all 0.2s ease;
      cursor: pointer;
      position: relative;
      background: #ffffff;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .storage-bin:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      border-color: #007bff;
    }
    
    .storage-bin.available {
      border-left: 4px solid #28a745;
    }
    
    .storage-bin.occupied {
      border-left: 4px solid #007bff;
      background: #f8f9ff;
    }
    
    .storage-bin.maintenance {
      border-left: 4px solid #ffc107;
      background: #fffdf5;
    }
    
    .storage-bin.reserved {
      border-left: 4px solid #6c757d;
      background: #f8f9fa;
    }
    
    .bin-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.75rem;
    }
    
    .bin-id {
      font-weight: 600;
      font-size: 0.875rem;
      color: #495057;
      letter-spacing: 0.5px;
    }
    
    .capacity-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
    }
    
    .capacity-text {
      font-size: 0.75rem;
      color: #6c757d;
      font-weight: 500;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    
    .capacity-value {
      font-size: 0.8rem;
      font-weight: 600;
      color: #495057;
    }
    
    .items-list {
      margin-top: 0.75rem;
      font-size: 0.75rem;
    }
    
    .items-list strong {
      color: #495057;
      font-size: 0.75rem;
      font-weight: 600;
    }
    
    .items-list small {
      color: #666;
      line-height: 1.4;
    }
    
    .storage-bin .progress {
      background-color: rgba(255,255,255,0.7);
      border-radius: 2px;
    }
    
    .storage-bin .progress-bar {
      border-radius: 2px;
    }
    
    /* Legend styling */
    .badge {
      font-size: 12px;
      padding: 4px 8px;
    }
    
    /* Zone styling */
    .storage-zone {
      border: 1px solid #e9ecef;
      border-radius: 8px;
      padding: 1.5rem;
      background: #ffffff;
      margin-bottom: 1.5rem;
    }
    
    .zone-header {
      margin-bottom: 1.25rem;
      padding: 1rem;
      background: #ffffff;
      border-radius: 6px;
      border: 1px solid #e9ecef;
      box-shadow: 0 1px 2px rgba(0,0,0,0.04);
    }
    
    .zone-indicator {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      margin-right: 0.75rem;
    }
    
    .zone-title {
      color: #495057;
      font-weight: 600;
      font-size: 1rem;
    }
    
    .zone-stats {
      display: flex;
      gap: 0.5rem;
      align-items: center;
    }
    
    .zone-stats .badge {
      font-size: 11px;
      padding: 6px 10px;
    }
    
    .zone-bins {
      padding: 10px 0;
    }

    /* Clean card styling */
    .card {
      border: 1px solid #e9ecef;
      box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }
    
    .card-header {
      background: #ffffff;
      border-bottom: 1px solid #e9ecef;
      padding: 1rem 1.25rem;
    }
    
    .card-title {
      font-weight: 600;
      color: #495057;
      margin-bottom: 0;
    }
    
    /* Button improvements */
    .btn {
      border-radius: 6px;
      font-weight: 500;
      padding: 0.5rem 1rem;
      transition: all 0.2s ease;
    }
    
    .btn-sm {
      padding: 0.375rem 0.75rem;
      font-size: 0.8rem;
    }
    
    /* Table styling */
    .table th {
      font-weight: 600;
      color: #495057;
      border-bottom: 2px solid #e9ecef;
      padding: 0.75rem;
    }
    
    .table td {
      padding: 0.75rem;
      vertical-align: middle;
    }
    
    /* Badge improvements */
    .badge {
      font-weight: 500;
      padding: 0.375rem 0.75rem;
      border-radius: 4px;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
      .storage-bin {
        min-height: 120px;
        padding: 0.875rem;
      }
      
      .zone-header {
        padding: 0.75rem;
      }
      
      .card-header {
        padding: 0.875rem 1rem;
      }
    }

    @media (max-width: 768px) {
      .storage-bin {
        min-height: 140px;
        padding: 12px;
      }
      
      .bin-id {
        font-size: 14px;
      }
      
      .capacity-value {
        font-size: 12px;
      }
      
      .zone-title {
        font-size: 16px;
      }
      
      .zone-stats {
        margin-top: 10px;
      }
      
      .zone-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
      }
      
      .table-responsive {
        font-size: 0.875rem;
      }
      
      .card-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 0.5rem;
      }
    }
    
    @media (max-width: 576px) {
      .storage-bin {
        min-height: 120px;
        padding: 10px;
      }
      
      .zone-stats .badge {
        font-size: 10px;
        padding: 4px 8px;
      }
      
      .items-list {
        font-size: 11px;
      }
    }
  </style>

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
              <a href="{{ url('/storage-organization') }}" class="nav-link text-dark small active">
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
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="btn btn-link nav-link text-danger p-0">
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
            <h2 class="fw-bold mb-1">Storage Organization</h2>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name ?? 'User' }}! Optimize warehouse layout and storage efficiency.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/smartwarehousing') }}" class="text-decoration-none">Smart Warehousing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Storage Organization</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
      <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0 h-100">
          <div class="card-body d-flex align-items-center">
            <div class="flex-shrink-0">
              <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-boxes fs-5"></i>
              </div>
            </div>
            <div class="flex-grow-1 ms-3">
              <div class="fw-semibold text-dark">Total Items</div>
              <div class="fs-4 fw-bold text-primary">{{ $unorganizedItems->count() + $organizedItems->count() }}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-success bg-opacity-10 text-success me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-check-circle fs-5"></i>
              </div>
              <div class="flex-grow-1">
                <h3 class="fw-bold mb-0">{{ $organizedItems->count() }}</h3>
                <p class="text-muted mb-0 small">Properly Organized</p>
                <small class="text-success"><i class="bi bi-check"></i> Ready for dispatch</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-info bg-opacity-10 text-info me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-grid-3x3 fs-5"></i>
              </div>
              <div class="flex-grow-1">
                <h3 class="fw-bold mb-0">2</h3>
                <p class="text-muted mb-0 small">Active Zones</p>
                <small class="text-info"><i class="bi bi-info-circle"></i> Storage areas</small>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="card stat-card shadow-sm border-0 h-100">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <i class="bi bi-box fs-5"></i>
              </div>
              <div class="flex-grow-1">
                @php
                  $usedBins = collect($storageBins)->where('status', 'occupied')->count();
                @endphp
                <h3 class="fw-bold mb-0">{{ $usedBins }}</h3>
                <p class="text-muted mb-0 small">Used Bins</p>
                <small class="text-warning"><i class="bi bi-boxes"></i> Storage bins</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Storage Layout Section -->
    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Storage Management</h5>
          </div>
          <div class="card-body p-0">
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs nav-fill border-0" id="storageManagementTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold" id="items-awaiting-tab" data-bs-toggle="tab" data-bs-target="#items-awaiting" type="button" role="tab" aria-controls="items-awaiting" aria-selected="true" style="color: #495057 !important;">
                  <i class="bi bi-box-seam me-2"></i>Items Awaiting Storage Organization
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="storage-zones-tab" data-bs-toggle="tab" data-bs-target="#storage-zones" type="button" role="tab" aria-controls="storage-zones" aria-selected="false" style="color: #495057 !important;">
                  <i class="bi bi-grid-3x3 me-2"></i>Storage Zones
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold" id="inventory-locations-tab" data-bs-toggle="tab" data-bs-target="#inventory-locations" type="button" role="tab" aria-controls="inventory-locations" aria-selected="false" style="color: #495057 !important;">
                  <i class="bi bi-geo-alt me-2"></i>Inventory Locations
                </button>
              </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="storageManagementTabContent">
              <!-- Items Awaiting Storage Organization Tab -->
              <div class="tab-pane fade show active" id="items-awaiting" role="tabpanel" aria-labelledby="items-awaiting-tab">
                <div class="p-4">
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead class="table-light">
                        <tr>
                          <th>Item Code</th>
                          <th>Item Name</th>
                          <th>Category</th>
                          <th>Current Location</th>
                          <th>Quantity</th>
                          <th>Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        @forelse($unorganizedItems as $item)
                        <tr>
                          <td><strong>{{ $item->receipt->receipt_number ?? 'N/A' }}</strong></td>
                          <td>{{ $item->item_name }}</td>
                          <td><span class="badge bg-info">{{ $item->description ?? 'General' }}</span></td>
                          <td>
                            <span class="badge bg-warning">{{ ucfirst(str_replace('_', ' ', $item->storage_location ?? 'receiving_area')) }}</span>
                          </td>
                          <td>{{ $item->quantity }} {{ $item->unit }}</td>
                          <td>
                            <button class="btn btn-sm btn-primary assign-location-btn" data-item-id="{{ $item->id }}" data-item-name="{{ $item->item_name }}">
                              <i class="bi bi-geo-alt"></i> Assign Location
                            </button>
                          </td>
                        </tr>
                        @empty
                        <tr>
                          <td colspan="6" class="text-center text-muted">
                            <div class="py-4">
                              <i class="bi bi-check-circle fs-1 text-success"></i>
                              <p class="mt-2">All items are properly organized!</p>
                              <a href="{{ url('/inventory-receipt') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Add More Items
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

              <!-- Storage Zones Tab -->
              <div class="tab-pane fade" id="storage-zones" role="tabpanel" aria-labelledby="storage-zones-tab">
                <div class="p-4">
                  <div class="storage-grid">
              @php
                $zones = [
                  'A' => [
                    'name' => 'Zone A - Vehicle Parts & Components',
                    'color' => 'primary',
                    'bins' => ['A1-1', 'A1-2', 'A1-3', 'A2-1', 'A2-2', 'A2-3']
                  ],
                  'B' => [
                    'name' => 'Zone B - Tools & Equipment',
                    'color' => 'success',
                    'bins' => ['B1-1', 'B1-2', 'B1-3', 'B2-1', 'B2-2', 'B2-3']
                  ]
                ];
              @endphp

              @foreach($zones as $zoneId => $zone)
              <!-- Zone {{ $zoneId }} -->
              <div class="storage-zone mb-4">
                <div class="zone-header">
                  <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                      <div class="zone-indicator bg-{{ $zone['color'] }}"></div>
                      <h5 class="zone-title mb-0">{{ $zone['name'] }}</h5>
                    </div>
                    <div class="zone-stats">
                      @php
                        $zoneBins = collect($zone['bins'])->map(function($binId) use ($storageBins) {
                          return $storageBins[$binId] ?? ['used_capacity' => 0, 'max_capacity' => 100, 'status' => 'available'];
                        });
                        $totalUsed = $zoneBins->sum('used_capacity');
                        $totalCapacity = $zoneBins->sum('max_capacity');
                        $zoneUtilization = $totalCapacity > 0 ? round(($totalUsed / $totalCapacity) * 100) : 0;
                        $occupiedBins = $zoneBins->where('status', 'occupied')->count();
                        $totalBins = $zoneBins->count();
                      @endphp
                      <span class="badge bg-{{ $zone['color'] }} me-2">{{ $occupiedBins }}/{{ $totalBins }} Bins Occupied</span>
                      <span class="badge bg-light text-dark">{{ $zoneUtilization }}% Utilized</span>
                    </div>
                  </div>
                </div>
                
                <div class="zone-bins">
                  <div class="row g-3">
                    @foreach($zone['bins'] as $binId)
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                      @php
                        $binData = $storageBins[$binId] ?? ['status' => 'available', 'used_capacity' => 0, 'max_capacity' => 100, 'items' => collect(), 'usage_percentage' => 0];
                        $statusClass = $binData['status'];
                        $iconClass = match($binData['status']) {
                          'available' => 'bi-geo-alt text-success',
                          'occupied' => 'bi-check-circle text-primary',
                          'maintenance' => 'bi-gear text-warning',
                          'reserved' => 'bi-clock text-secondary',
                          default => 'bi-geo-alt text-success'
                        };
                        $progressColor = match(true) {
                          $binData['usage_percentage'] >= 80 => 'bg-danger',
                          $binData['usage_percentage'] >= 60 => 'bg-warning',
                          $binData['usage_percentage'] >= 30 => 'bg-primary',
                          $binData['usage_percentage'] > 0 => 'bg-info',
                          default => 'bg-success'
                        };
                      @endphp
                      <div class="storage-bin {{ $statusClass }} h-100">
                        <div class="bin-header">
                          <span class="bin-id">{{ $binId }}</span>
                          <i class="bi {{ $iconClass }} fs-5"></i>
                        </div>
                        <div class="capacity-info">
                          <span class="capacity-text">Capacity</span>
                          <span class="capacity-value">{{ $binData['used_capacity'] }}/{{ $binData['max_capacity'] }}</span>
                        </div>
                        <div class="progress mb-2" style="height: 6px;">
                          <div class="progress-bar {{ $progressColor }}" style="width: {{ $binData['usage_percentage'] }}%"></div>
                        </div>
                        <div class="items-list">
                          <strong class="d-block mb-1">Items:</strong>
                          @if($binData['items']->count() > 0)
                            <small class="text-break">{{ $binData['items']->pluck('item_name')->take(2)->implode(', ') }}{{ $binData['items']->count() > 2 ? ' +' . ($binData['items']->count() - 2) . ' more' : '' }}</small>
                          @elseif($binData['status'] === 'maintenance')
                            <small class="text-muted fst-italic">Under Maintenance</small>
                          @elseif($binData['status'] === 'reserved')
                            <small class="text-muted fst-italic">Reserved</small>
                          @else
                            <small class="text-muted fst-italic">Empty</small>
                          @endif
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                </div>
              </div>
              @endforeach
                  </div>
                </div>
              </div>

              <!-- Inventory Locations Tab -->
              <div class="tab-pane fade" id="inventory-locations" role="tabpanel" aria-labelledby="inventory-locations-tab">
                <div class="p-4">
                  <!-- Search and Filter Controls -->
                  <div class="row g-3 mb-4">
                    <div class="col-md-8">
                      <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                          <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" id="inventorySearch" placeholder="Search inventory items...">
                      </div>
                    </div>
                    <div class="col-md-4">
                      <select class="form-select" id="zoneFilter">
                        <option value="">All Zones</option>
                        <option value="A">Zone A</option>
                        <option value="B">Zone B</option>
                      </select>
                    </div>
                  </div>

                  <!-- Inventory Items List -->
                  <div class="inventory-items-container">
                    @foreach($organizedItems as $item)
                    <div class="inventory-item-card mb-3 p-3 border rounded-3 bg-white shadow-sm" data-zone="{{ substr($item->storage_location, 0, 1) }}" data-item-name="{{ strtolower($item->item_name) }}">
                      <div class="row align-items-center">
                        <div class="col-md-1">
                          <div class="item-location-icon bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                            <i class="bi bi-geo-alt"></i>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="item-details">
                            <h6 class="item-name mb-1 fw-semibold">{{ $item->item_name }}</h6>
                            <div class="item-meta d-flex flex-wrap gap-2 align-items-center">
                              <span class="badge bg-light text-dark">Part: {{ $item->receipt->receipt_number ?? 'N/A' }}</span>
                              <span class="badge bg-primary">{{ substr($item->storage_location, 0, 1) ?? 'N/A' }}</span>
                              <small class="text-muted">Stock: {{ $item->quantity }} ({{ $item->unit }})</small>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-3 text-end">
                          <div class="item-status mb-2">
                            @php
                              $statusClass = 'success';
                              $statusText = 'optimal';
                              if($item->quantity < 10) {
                                $statusClass = 'warning';
                                $statusText = 'low';
                              } elseif($item->quantity < 5) {
                                $statusClass = 'danger';
                                $statusText = 'critical';
                              }
                            @endphp
                            <span class="badge bg-{{ $statusClass }} rounded-pill">{{ $statusText }}</span>
                          </div>
                          <div class="item-actions">
                            <button class="btn btn-sm btn-outline-primary relocate-btn" 
                                    data-item-id="{{ $item->id }}" 
                                    data-item-name="{{ $item->item_name }}"
                                    data-current-location="{{ $item->storage_location }}">
                              <i class="bi bi-arrows-move"></i>
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endforeach

                    @if($organizedItems->count() === 0)
                    <div class="text-center py-5">
                      <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                      <h5 class="text-muted">No items found</h5>
                      <p class="text-muted">Items will appear here once they are assigned to storage locations</p>
                    </div>
                    @endif
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Warehouse Overview & Quick Actions -->
    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Inventory Chart -->
        <div class="card shadow-sm border-0">
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
                <span class="small">Zone A - Vehicle Parts</span>
                <span class="small text-muted">85%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-warning" style="width: 85%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone B - Tools & Equipment</span>
                <span class="small text-muted">72%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" style="width: 72%"></div>
              </div>
            </div>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone C - Safety Equipment</span>
                <span class="small text-muted">58%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-info" style="width: 58%"></div>
              </div>
            </div>
            <div>
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="small">Zone D - Maintenance Supplies</span>
                <span class="small text-muted">43%</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-primary" style="width: 43%"></div>
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

      // Storage Organization functionality
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      // Assign Location functionality
      document.querySelectorAll('.assign-location-btn').forEach(button => {
        button.addEventListener('click', function() {
          const itemId = this.dataset.itemId;
          const itemName = this.dataset.itemName;
          showLocationAssignmentModal(itemId, itemName);
        });
      });
      
      // Show Location Assignment Modal
      function showLocationAssignmentModal(itemId, itemName) {
        const modalHtml = `
          <div class="modal fade" id="locationModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Assign Storage Location</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p><strong>Item:</strong> ${itemName}</p>
                  <form id="locationForm">
                    <div class="mb-3">
                      <label class="form-label">Zone</label>
                      <select class="form-select" name="zone" required>
                        <option value="">Select Zone</option>
                        <option value="A">Zone A - Vehicle Parts & Components</option>
                        <option value="B">Zone B - Tools & Equipment</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">Bin</label>
                      <select class="form-select" name="bin" required>
                        <option value="">Select Bin</option>
                      </select>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" onclick="assignLocation(${itemId})">
                    <i class="bi bi-geo-alt"></i> Assign Location
                  </button>
                </div>
              </div>
            </div>
          </div>
        `;
        
        // Remove existing modal
        const existingModal = document.getElementById('locationModal');
        if (existingModal) {
          existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Set up zone change handler for bins
        const zoneSelect = document.querySelector('#locationModal select[name="zone"]');
        const binSelect = document.querySelector('#locationModal select[name="bin"]');
        
        zoneSelect.addEventListener('change', function() {
          const zone = this.value;
          binSelect.innerHTML = '<option value="">Select Bin</option>';
          
          if (zone) {
            const bins = zone === 'A' ? ['1-1', '1-2', '1-3', '2-1', '2-2', '2-3'] : ['1-1', '1-2', '1-3', '2-1', '2-2', '2-3'];
            bins.forEach(bin => {
              const binId = zone + bin;
              binSelect.innerHTML += `<option value="${bin}">${binId}</option>`;
            });
          }
        });
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('locationModal'));
        modal.show();
      }
      
      // Assign Location function
      window.assignLocation = function(itemId) {
        const form = document.getElementById('locationForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.item_id = itemId;
        
        fetch('/api/storage/assign-location', {
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
            alert('Location assigned successfully!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('locationModal'));
            modal.hide();
            
            // Show next step message
            const nextStepMsg = `Item has been organized and assigned to ${data.item.zone}-${data.item.bin}.
            
Next steps:
1. Item is now available for picking and dispatch
2. Go to Picking & Dispatch to manage outbound operations
3. Stock levels will be monitored for replenishment`;
            
            if (confirm(nextStepMsg + '\n\nWould you like to go to Picking & Dispatch now?')) {
              window.location.href = '/picking-dispatch';
            } else {
              location.reload();
            }
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while assigning location.');
        });
      };

      // Search and Filter functionality for Inventory Locations tab
      const inventorySearch = document.getElementById('inventorySearch');
      const zoneFilter = document.getElementById('zoneFilter');
      const inventoryItems = document.querySelectorAll('.inventory-item-card');

      function filterInventoryItems() {
        const searchTerm = inventorySearch ? inventorySearch.value.toLowerCase() : '';
        const selectedZone = zoneFilter ? zoneFilter.value : '';

        inventoryItems.forEach(item => {
          const itemName = item.dataset.itemName || '';
          const itemZone = item.dataset.zone || '';
          
          const matchesSearch = itemName.includes(searchTerm);
          const matchesZone = !selectedZone || itemZone === selectedZone;
          
          if (matchesSearch && matchesZone) {
            item.style.display = 'block';
          } else {
            item.style.display = 'none';
          }
        });
      }

      if (inventorySearch) {
        inventorySearch.addEventListener('input', filterInventoryItems);
      }

      if (zoneFilter) {
        zoneFilter.addEventListener('change', filterInventoryItems);
      }

      // Relocate functionality
      document.querySelectorAll('.relocate-btn').forEach(button => {
        button.addEventListener('click', function() {
          const itemId = this.dataset.itemId;
          const itemName = this.dataset.itemName || 'Item';
          const currentLocation = this.dataset.currentLocation || 'Unknown';
          showRelocateModal(itemId, itemName, currentLocation);
        });
      });
      
      // Show Relocate Modal
      function showRelocateModal(itemId, itemName, currentLocation) {
        const modalHtml = `
          <div class="modal fade" id="relocateModal" tabindex="-1">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Relocate Item</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p><strong>Item:</strong> ${itemName}</p>
                  <p><strong>Current Location:</strong> <span class="badge bg-secondary">${currentLocation}</span></p>
                  <form id="relocateForm">
                    <div class="mb-3">
                      <label class="form-label">New Zone</label>
                      <select class="form-select" name="zone" required>
                        <option value="">Select Zone</option>
                        <option value="A">Zone A - Vehicle Parts & Components</option>
                        <option value="B">Zone B - Tools & Equipment</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <label class="form-label">New Bin</label>
                      <select class="form-select" name="bin" required>
                        <option value="">Select Bin</option>
                      </select>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-warning" onclick="relocateItem(${itemId})">
                    <i class="bi bi-arrows-move"></i> Relocate Item
                  </button>
                </div>
              </div>
            </div>
          </div>
        `;
        
        // Remove existing modal
        const existingModal = document.getElementById('relocateModal');
        if (existingModal) {
          existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Set up zone change handler for bins
        const zoneSelect = document.querySelector('#relocateModal select[name="zone"]');
        const binSelect = document.querySelector('#relocateModal select[name="bin"]');
        
        zoneSelect.addEventListener('change', function() {
          const zone = this.value;
          binSelect.innerHTML = '<option value="">Select Bin</option>';
          
          if (zone) {
            const bins = zone === 'A' ? ['1-1', '1-2', '1-3', '2-1', '2-2', '2-3'] : ['1-1', '1-2', '1-3', '2-1', '2-2', '2-3'];
            bins.forEach(bin => {
              const binId = zone + bin;
              binSelect.innerHTML += `<option value="${bin}">${binId}</option>`;
            });
          }
        });
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('relocateModal'));
        modal.show();
      }
      
      // Relocate Item function
      window.relocateItem = function(itemId) {
        const form = document.getElementById('relocateForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        data.item_id = itemId;
        
        fetch('/api/storage/relocate-item', {
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
            alert(`Item relocated successfully from ${data.item.old_location} to ${data.item.new_location}!`);
            const modal = bootstrap.Modal.getInstance(document.getElementById('relocateModal'));
            modal.hide();
            location.reload();
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while relocating item.');
        });
      };

      // Initialize Inventory Levels Chart
      const inventoryChartCtx = document.getElementById('inventoryChart');
      if (inventoryChartCtx) {
        // Prepare data from backend
        const inventoryData = {
          @php
            $inventoryStats = [];
            foreach($organizedItems as $item) {
              $category = $item->description ?? 'General';
              if (!isset($inventoryStats[$category])) {
                $inventoryStats[$category] = ['quantity' => 0, 'items' => 0];
              }
              $inventoryStats[$category]['quantity'] += $item->quantity;
              $inventoryStats[$category]['items']++;
            }
          @endphp
          labels: {!! json_encode(array_keys($inventoryStats)) !!},
          datasets: [{
            label: 'Stock Quantity',
            data: {!! json_encode(array_column($inventoryStats, 'quantity')) !!},
            backgroundColor: [
              'rgba(54, 162, 235, 0.8)',
              'rgba(255, 99, 132, 0.8)', 
              'rgba(255, 205, 86, 0.8)',
              'rgba(75, 192, 192, 0.8)',
              'rgba(153, 102, 255, 0.8)',
              'rgba(255, 159, 64, 0.8)'
            ],
            borderColor: [
              'rgba(54, 162, 235, 1)',
              'rgba(255, 99, 132, 1)',
              'rgba(255, 205, 86, 1)', 
              'rgba(75, 192, 192, 1)',
              'rgba(153, 102, 255, 1)',
              'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 2
          }]
        };

        new Chart(inventoryChartCtx, {
          type: 'doughnut',
          data: inventoryData,
          options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
              legend: {
                position: 'bottom',
                labels: {
                  padding: 20,
                  font: {
                    size: 12
                  }
                }
              },
              tooltip: {
                callbacks: {
                  label: function(context) {
                    const label = context.label || '';
                    const value = context.parsed || 0;
                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                    const percentage = ((value / total) * 100).toFixed(1);
                    return `${label}: ${value} items (${percentage}%)`;
                  }
                }
              }
            },
            cutout: '60%',
            elements: {
              arc: {
                borderWidth: 2
              }
            }
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