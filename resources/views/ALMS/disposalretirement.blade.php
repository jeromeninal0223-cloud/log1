<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Smart Warehousing Dashboard</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <meta name="csrf-token" content="{{ csrf_token() }}" />

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
              <a href="{{ url('/alms/maintenance') }}" class="nav-link text-dark small">
                <i class="bi bi-arrow-repeat me-2"></i> Maintenance Schedule
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/alms/disposalretirement') }}" class="nav-link text-dark small active">
                <i class="bi bi-wrench-adjustable me-2"></i> Disposal/Retirement
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/alms/vehicle-requests') }}" class="nav-link text-dark small">
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
          <i class="bi bi-box-seam fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Disposal & Retirement</h2>
          <p class="text-muted mb-0">Welcome back, Sarah! Manage asset disposal and retirement processes.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/alms') }}" class="text-decoration-none">Asset Lifecycle Management</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disposal & Retirement</li>
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
            <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
              <i class="bi bi-exclamation-triangle"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">12</h3>
              <p class="text-muted mb-0 small">Pending Disposals</p>
              <small class="text-warning"><i class="bi bi-arrow-up"></i> +2 today</small>
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
              <h3 class="fw-bold mb-0">45</h3>
              <p class="text-muted mb-0 small">Assets Retired</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +8 this month</small>
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
              <i class="bi bi-recycle"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">$24,500</h3>
              <p class="text-muted mb-0 small">Recovery Value</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> +15%</small>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-danger bg-opacity-10 text-danger me-3">
              <i class="bi bi-clock-history"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">18</h3>
              <p class="text-muted mb-0 small">Overdue Reviews</p>
              <small class="text-danger"><i class="bi bi-arrow-up"></i> +3</small>
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
      <!-- Asset Disposal Request Form -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom bg-danger text-white">
          <h5 class="card-title mb-0">New Asset Disposal Request</h5>
        </div>
        <div class="card-body">
          <!-- Asset Disposal Form -->
          <form id="disposalForm">
            <div class="row g-3">
              <!-- Asset Information -->
              <div class="col-md-6">
                <label for="assetId" class="form-label">Select Asset <span class="text-danger">*</span></label>
                <select class="form-select" id="assetId" required>
                  <option value="">Choose an asset...</option>
                  <!-- Assets will be loaded dynamically -->
                </select>
              </div>
              <div class="col-md-6">
                <label for="assetName" class="form-label">Asset Name</label>
                <input type="text" class="form-control" id="assetName" placeholder="Asset name will appear here" readonly>
              </div>
              <div class="col-md-6">
                <label for="disposalReason" class="form-label">Disposal Reason</label>
                <select class="form-select" id="disposalReason" required>
                  <option value="">Select reason</option>
                  <option value="end_of_life">End of Useful Life</option>
                  <option value="damaged">Damaged Beyond Repair</option>
                  <option value="obsolete">Obsolete Technology</option>
                  <option value="cost_ineffective">Cost Ineffective to Maintain</option>
                  <option value="regulatory">Regulatory Compliance</option>
                  <option value="other">Other</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="disposalMethod" class="form-label">Disposal Method</label>
                <select class="form-select" id="disposalMethod" required>
                  <option value="">Select method</option>
                  <option value="sale">Sale</option>
                  <option value="donation">Donation</option>
                  <option value="recycle">Recycling</option>
                  <option value="scrap">Scrap</option>
                  <option value="destroy">Secure Destruction</option>
                  <option value="return">Return to Vendor</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="requestedBy" class="form-label">Requested By</label>
                <input type="text" class="form-control" id="requestedBy" value="{{ Auth::user()->name }}" readonly>
              </div>
              <div class="col-md-6">
                <label for="department" class="form-label">Department</label>
                <input type="text" class="form-control" id="department" placeholder="Department" required>
              </div>
              <div class="col-md-6">
                <label for="estimatedValue" class="form-label">Estimated Recovery Value</label>
                <input type="number" class="form-control" id="estimatedValue" placeholder="0.00" step="0.01">
              </div>
              <div class="col-md-6">
                <label for="urgency" class="form-label">Urgency Level</label>
                <select class="form-select" id="urgency" required>
                  <option value="low">Low</option>
                  <option value="medium" selected>Medium</option>
                  <option value="high">High</option>
                  <option value="critical">Critical</option>
                </select>
              </div>
              <div class="col-12">
                <label for="justification" class="form-label">Justification</label>
                <textarea class="form-control" id="justification" rows="3" placeholder="Provide detailed justification for disposal..." required></textarea>
              </div>
              <div class="col-12">
                <label for="additionalNotes" class="form-label">Additional Notes</label>
                <textarea class="form-control" id="additionalNotes" rows="2" placeholder="Any additional information..."></textarea>
              </div>
            </div>
            
            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-4">
              <button type="reset" class="btn btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i>Cancel
              </button>
              <div>
                <button type="button" class="btn btn-outline-primary me-2">
                  <i class="bi bi-file-earmark me-1"></i>Save as Draft
                </button>
                <button type="submit" class="btn btn-danger">
                  <i class="bi bi-trash me-1"></i>Submit Disposal Request
                </button>
              </div>
            </div>
          </form>

          <!-- Asset Disposal Form JavaScript -->
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              const assetIdSelect = document.getElementById('assetId');
              const assetNameInput = document.getElementById('assetName');
              const disposalForm = document.getElementById('disposalForm');
              let assetsData = {};

              // Load assets on page load
              loadAssets();

              async function loadAssets() {
                try {
                  const response = await fetch("{{ url('/api/assets') }}", {
                    headers: {
                      'Accept': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                  });

                  if (!response.ok) {
                    throw new Error('Failed to load assets');
                  }

                  const assets = await response.json();
                  
                  // Clear existing options except the first one
                  assetIdSelect.innerHTML = '<option value="">Choose an asset...</option>';
                  
                  // Populate dropdown and store asset data
                  assets.forEach(asset => {
                    const option = document.createElement('option');
                    option.value = asset.id;
                    option.textContent = `${asset.asset_id || '#ASSET-' + asset.id} - ${getAssetDisplayName(asset)}`;
                    assetIdSelect.appendChild(option);
                    
                    // Store full asset data for later use
                    assetsData[asset.id] = asset;
                  });

                } catch (error) {
                  console.error('Error loading assets:', error);
                  Swal.fire({
                    title: 'Error',
                    text: 'Failed to load assets. Please refresh the page.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                  });
                }
              }

              function getAssetDisplayName(asset) {
                // Return appropriate name based on asset type
                return asset.plate_number || 
                       asset.building_name || 
                       asset.equipment_name || 
                       asset.item_name || 
                       asset.tool_name || 
                       asset.item_description || 
                       'Asset';
              }

              // Asset selection change handler
              assetIdSelect.addEventListener('change', function() {
                const selectedAssetId = this.value;
                
                if (selectedAssetId && assetsData[selectedAssetId]) {
                  const asset = assetsData[selectedAssetId];
                  const displayName = getAssetDisplayName(asset);
                  
                  assetNameInput.value = displayName;
                  assetNameInput.classList.remove('is-invalid');
                  assetNameInput.classList.add('is-valid');
                  
                  // Auto-fill department if available
                  const departmentField = document.getElementById('department');
                  if (asset.department && departmentField) {
                    departmentField.value = asset.department || asset.responsible_department || '';
                  }
                } else {
                  assetNameInput.value = '';
                  assetNameInput.classList.remove('is-valid', 'is-invalid');
                }
              });

              // Form submission with SweetAlert
              disposalForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                // Validate required fields
                const requiredFields = ['assetId', 'disposalReason', 'disposalMethod', 'department', 'urgency', 'justification'];
                let isValid = true;
                
                requiredFields.forEach(fieldId => {
                  const field = document.getElementById(fieldId);
                  if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                  } else {
                    field.classList.remove('is-invalid');
                  }
                });

                if (!isValid) {
                  Swal.fire({
                    title: 'Missing Information',
                    text: 'Please fill in all required fields.',
                    icon: 'warning',
                    confirmButtonColor: '#ffc107'
                  });
                  return;
                }

                // Show loading
                Swal.fire({
                  title: 'Submitting Request...',
                  text: 'Please wait while we process your disposal request',
                  icon: 'info',
                  allowOutsideClick: false,
                  allowEscapeKey: false,
                  showConfirmButton: false,
                  didOpen: () => {
                    Swal.showLoading();
                  }
                });

                try {
                  // Prepare form data
                  const formData = {
                    asset_id: document.getElementById('assetId').value,
                    disposal_reason: document.getElementById('disposalReason').value,
                    disposal_method: document.getElementById('disposalMethod').value,
                    department: document.getElementById('department').value,
                    estimated_value: document.getElementById('estimatedValue').value || 0,
                    urgency: document.getElementById('urgency').value,
                    justification: document.getElementById('justification').value,
                    additional_notes: document.getElementById('additionalNotes').value,
                    requested_by: "{{ Auth::user()->name }}"
                  };

                  const response = await fetch("{{ url('/alms/disposal-requests') }}", {
                    method: 'POST',
                    headers: {
                      'Content-Type': 'application/json',
                      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                      'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                  });

                  if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));
                    throw new Error(errorData.message || 'Failed to submit disposal request');
                  }

                  const result = await response.json();

                  // Show success message
                  Swal.fire({
                    title: 'Success!',
                    text: `Disposal request submitted successfully! Request ID: ${result.request_id || 'DR-' + Date.now()}`,
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false
                  }).then(() => {
                    // Reset form
                    disposalForm.reset();
                    assetNameInput.value = '';
                    assetNameInput.classList.remove('is-valid', 'is-invalid');
                    
                    // Reload page to show updated data
                    window.location.reload();
                  });

                } catch (error) {
                  console.error('Disposal request error:', error);
                  Swal.fire({
                    title: 'Submission Failed!',
                    text: error.message || 'Failed to submit disposal request. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                  });
                }
              });
            });
          </script>
        </div>
      </div>
      
      <!-- Recent Disposal Requests -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Recent Disposal Requests</h5>
          <button class="btn btn-sm btn-outline-primary" onclick="window.location.reload()">
            <i class="bi bi-arrow-clockwise me-1"></i>Refresh
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-hover">
              <thead class="table-light">
                <tr>
                  <th>Request ID</th>
                  <th>Date</th>
                  <th>Asset ID</th>
                  <th>Asset Name</th>
                  <th>Reason</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @if($disposalRequests && $disposalRequests->count() > 0)
                  @foreach($disposalRequests as $request)
                  <tr>
                    <td><strong>{{ $request->request_id }}</strong></td>
                    <td>{{ $request->created_at->format('M d, Y') }}<br>
                        <small class="text-muted">{{ $request->created_at->format('g:i A') }}</small>
                    </td>
                    <td>{{ $request->asset->asset_id ?? '#ASSET-' . $request->asset_id }}</td>
                    <td>
                      @if($request->asset)
                        {{ $request->asset->plate_number ?? $request->asset->building_name ?? $request->asset->equipment_name ?? $request->asset->item_name ?? $request->asset->tool_name ?? 'Asset' }}
                      @else
                        <span class="text-muted">Asset not found</span>
                      @endif
                    </td>
                    <td>{{ ucfirst(str_replace('_', ' ', $request->disposal_reason)) }}</td>
                    <td>
                      @php
                        $statusClass = match($request->status) {
                          'pending' => 'bg-warning',
                          'approved' => 'bg-success',
                          'rejected' => 'bg-danger',
                          'in_progress' => 'bg-info',
                          'completed' => 'bg-dark',
                          default => 'bg-secondary'
                        };
                      @endphp
                      <span class="badge {{ $statusClass }}">{{ ucfirst($request->status) }}</span>
                    </td>
                    <td>
                      <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary view-request-btn" data-id="{{ $request->id }}" title="View Details">
                          <i class="bi bi-eye"></i>
                        </button>
                        @if($request->status === 'pending')
                        <button class="btn btn-outline-success approve-request-btn" data-id="{{ $request->id }}" title="Approve">
                          <i class="bi bi-check"></i>
                        </button>
                        <button class="btn btn-outline-danger reject-request-btn" data-id="{{ $request->id }}" title="Reject">
                          <i class="bi bi-x"></i>
                        </button>
                        @elseif($request->status === 'completed')
                        <button class="btn btn-outline-secondary" title="Download Certificate">
                          <i class="bi bi-file-earmark-pdf"></i>
                        </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @endforeach
                @else
                  <tr>
                    <td colspan="7" class="text-center py-4">
                      <div class="empty-state">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mt-2">No disposal requests yet</h6>
                        <p class="text-muted small">Submit your first disposal request using the form above</p>
                      </div>
                    </td>
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
      <!-- Asset Details Preview (Hidden by default, shown when viewing asset) -->
      <div class="card shadow-sm border-0 d-none" id="assetPreviewCard">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Asset Details</h5>
          <button class="btn btn-sm btn-close" id="closeAssetPreview"></button>
        </div>
        <div class="card-body">
          <h6 class="fw-bold">AST-001</h6>
          <p class="small text-muted">Acquired: 15 Jan 2022 | Last Maintenance: 20 Dec 2024</p>
          
          <div class="row g-2">
            <div class="col-6">
              <small class="text-muted">Asset Name</small>
              <p class="mb-1 fw-semibold">Dell Laptop OptiPlex 7090</p>
            </div>
            <div class="col-6">
              <small class="text-muted">Category</small>
              <p class="mb-1">IT Equipment</p>
            </div>
            <div class="col-6">
              <small class="text-muted">Current Value</small>
              <p class="mb-1">$1,200</p>
            </div>
            <div class="col-6">
              <small class="text-muted">Condition</small>
              <p class="mb-1"><span class="badge bg-warning">Fair</span></p>
            </div>
            <div class="col-6">
              <small class="text-muted">Location</small>
              <p class="mb-1">Office Floor 2</p>
            </div>
            <div class="col-6">
              <small class="text-muted">Assigned To</small>
              <p class="mb-1">John Doe</p>
            </div>
          </div>
          
          <hr class="my-3">
          
          <div class="d-grid gap-2">
            <button class="btn btn-sm btn-outline-danger">
              <i class="bi bi-trash me-1"></i>Request Disposal
            </button>
            <button class="btn btn-sm btn-outline-primary">
              <i class="bi bi-pencil me-1"></i>Update Details
            </button>
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
    });
  </script>
</body>
</html>
