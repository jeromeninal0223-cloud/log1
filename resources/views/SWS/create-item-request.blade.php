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
  <title>Create Item Request - Smart Warehousing</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
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
            <i class="bi bi-plus-circle fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">Create Item Request</h2>
            <p class="text-muted mb-0">Submit a new request for warehouse items.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/picking-dispatch') }}" class="text-decoration-none">Picking & Dispatch</a></li>
            <li class="breadcrumb-item active" aria-current="page">Create Request</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Request Form -->
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Item Request Details</h5>
          </div>
          <div class="card-body">
            <form action="{{ route('item-requests.store') }}" method="POST">
              @csrf
              
              <div class="row g-3">
                <!-- Item Name -->
                <div class="col-12">
                  <label for="item_name" class="form-label">Select Item <span class="text-danger">*</span></label>
                  <select class="form-select @error('item_name') is-invalid @enderror" id="item_name" name="item_name" required>
                    <option value="">Choose an item from inventory...</option>
                  </select>
                  @error('item_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Quantity -->
                <div class="col-md-6">
                  <label for="requested_quantity" class="form-label">Requested Quantity <span class="text-danger">*</span></label>
                  <input type="number" class="form-control @error('requested_quantity') is-invalid @enderror" 
                         id="requested_quantity" name="requested_quantity" value="{{ old('requested_quantity') }}" 
                         min="1" required>
                  @error('requested_quantity')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Available Quantity (Display Only) -->
                <div class="col-md-6">
                  <label for="available_quantity_display" class="form-label">Available Quantity</label>
                  <input type="text" class="form-control" id="available_quantity_display" readonly 
                         placeholder="Select an item to see availability">
                  <input type="hidden" id="available_quantity" name="available_quantity">
                </div>

                <!-- Item Location (Display Only) -->
                <div class="col-12" id="location_section" style="display: none;">
                  <div class="alert alert-info mb-0">
                    <div class="d-flex align-items-center">
                      <i class="bi bi-geo-alt-fill me-2"></i>
                      <div>
                        <strong>Item Location:</strong>
                        <span id="item_location_display"></span>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Delivery Information -->
                <div class="col-12">
                  <h6 class="fw-semibold text-primary mt-4 mb-3">
                    <i class="bi bi-truck me-2"></i>Delivery Information
                  </h6>
                </div>

                <!-- Delivery Location -->
                <div class="col-md-6">
                  <label for="delivery_location" class="form-label">Delivery Location <span class="text-danger">*</span></label>
                  <select class="form-select @error('delivery_location') is-invalid @enderror" id="delivery_location" name="delivery_location" required>
                    <option value="">Select delivery location...</option>
                    <option value="office_building_a">Office Building A</option>
                    <option value="office_building_b">Office Building B</option>
                    <option value="warehouse_main">Main Warehouse</option>
                    <option value="warehouse_secondary">Secondary Warehouse</option>
                    <option value="production_floor">Production Floor</option>
                    <option value="maintenance_shop">Maintenance Shop</option>
                    <option value="security_office">Security Office</option>
                    <option value="admin_office">Administration Office</option>
                    <option value="hr_department">HR Department</option>
                    <option value="finance_department">Finance Department</option>
                    <option value="it_department">IT Department</option>
                    <option value="parking_area">Parking Area</option>
                    <option value="loading_dock">Loading Dock</option>
                    <option value="other">Other (specify below)</option>
                  </select>
                  @error('delivery_location')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Delivery Department -->
                <div class="col-md-6">
                  <label for="delivery_department" class="form-label">Department/Unit</label>
                  <input type="text" class="form-control @error('delivery_department') is-invalid @enderror" 
                         id="delivery_department" name="delivery_department" value="{{ old('delivery_department') }}" 
                         placeholder="e.g., Accounting, Operations, Project Team Alpha">
                  @error('delivery_department')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Priority -->
                <div class="col-md-6">
                  <label for="priority" class="form-label">Priority Level</label>
                  <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                    <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                    <option value="MEDIUM" {{ old('priority', 'MEDIUM') == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                    <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                  </select>
                  @error('priority')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Custom Location (shown when "Other" is selected) -->
                <div class="col-12" id="custom_location_section" style="display: none;">
                  <label for="custom_location" class="form-label">Specify Custom Location</label>
                  <input type="text" class="form-control" id="custom_location" name="custom_location" 
                         placeholder="Please specify the exact delivery location">
                </div>

                <!-- Delivery Instructions -->
                <div class="col-12">
                  <label for="delivery_instructions" class="form-label">Delivery Instructions</label>
                  <textarea class="form-control @error('delivery_instructions') is-invalid @enderror" 
                            id="delivery_instructions" name="delivery_instructions" rows="3" 
                            placeholder="Any special instructions for delivery (e.g., contact person, access requirements, specific room number)">{{ old('delivery_instructions') }}</textarea>
                  @error('delivery_instructions')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>

                <!-- Notes -->
                <div class="col-12">
                  <label for="notes" class="form-label">Additional Notes</label>
                  <textarea class="form-control @error('notes') is-invalid @enderror" 
                            id="notes" name="notes" rows="2" 
                            placeholder="Any additional information about this request">{{ old('notes') }}</textarea>
                  @error('notes')
                    <div class="invalid-feedback">{{ $message }}</div>
                  @enderror
                </div>
              </div>

              <!-- Form Actions -->
              <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ url('/picking-dispatch') }}" class="btn btn-outline-secondary">
                  <i class="bi bi-arrow-left me-1"></i>Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-plus-circle me-1"></i>Create Request
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Item Selection and Location Display -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const itemSelect = document.getElementById('item_name');
      const availableQtyDisplay = document.getElementById('available_quantity_display');
      const availableQtyHidden = document.getElementById('available_quantity');
      const locationSection = document.getElementById('location_section');
      const locationDisplay = document.getElementById('item_location_display');

      // Load inventory items
      loadInventoryItems();

      // Handle delivery location selection
      const deliveryLocationSelect = document.getElementById('delivery_location');
      const customLocationSection = document.getElementById('custom_location_section');
      
      deliveryLocationSelect.addEventListener('change', function() {
        if (this.value === 'other') {
          customLocationSection.style.display = 'block';
          document.getElementById('custom_location').required = true;
        } else {
          customLocationSection.style.display = 'none';
          document.getElementById('custom_location').required = false;
        }
      });

      // Handle item selection
      itemSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        
        if (selectedOption.value) {
          const quantity = selectedOption.dataset.quantity;
          const location = selectedOption.dataset.location;
          const zone = selectedOption.dataset.zone;
          
          // Update available quantity
          availableQtyDisplay.value = quantity;
          availableQtyHidden.value = quantity;
          
          // Format and display location
          let locationText = '';
          if (location && location !== 'receiving_area') {
            const zoneChar = location.charAt(0);
            const bin = location.substring(1);
            const zoneName = getZoneName(zoneChar);
            locationText = `${zoneName} - Bin ${bin}`;
          } else if (location === 'receiving_area') {
            locationText = 'Receiving Area';
          } else {
            locationText = 'No location set';
          }
          
          locationDisplay.textContent = locationText;
          locationSection.style.display = 'block';
        } else {
          // Reset fields
          availableQtyDisplay.value = '';
          availableQtyHidden.value = '';
          locationSection.style.display = 'none';
        }
      });

      function loadInventoryItems() {
        fetch('/api/inventory-items')
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              populateItemSelect(data.items);
            }
          })
          .catch(error => {
            console.error('Error loading inventory items:', error);
          });
      }

      function populateItemSelect(items) {
        // Clear existing options except the first one
        itemSelect.innerHTML = '<option value="">Choose an item from inventory...</option>';
        
        items.forEach(item => {
          const option = document.createElement('option');
          option.value = item.item_name;
          option.textContent = `${item.item_name} (${item.quantity} available)`;
          option.dataset.quantity = item.quantity;
          option.dataset.location = item.storage_location;
          option.dataset.itemId = item.id;
          itemSelect.appendChild(option);
        });
      }

      function getZoneName(zoneChar) {
        const zoneNames = {
          'A': 'Zone A - Vehicle Parts & Components',
          'B': 'Zone B - Tools & Equipment',
          'C': 'Zone C',
          'D': 'Zone D',
          'E': 'Zone E'
        };
        return zoneNames[zoneChar] || `Zone ${zoneChar}`;
      }
    });
  </script>

  <!-- Sidebar toggle functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Sidebar toggle elements
      const menuBtn = document.getElementById('menu-btn');
      const desktopToggle = document.getElementById('desktop-toggle');
      const sidebar = document.getElementById('sidebar');
      const overlay = document.getElementById('overlay');
      const mainContent = document.getElementById('main-content');

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
    });
  </script>
</body>
</html>
