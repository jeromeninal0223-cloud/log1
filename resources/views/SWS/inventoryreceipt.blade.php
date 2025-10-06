<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Inventory Receipt - Smart Warehousing</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    .stat-card {
      transition: transform 0.2s ease-in-out;
    }
    
    .stat-card:hover {
      transform: translateY(-2px);
    }
    
    .table th {
      font-weight: 600;
      font-size: 0.875rem;
    }
    
    .badge {
      font-size: 0.75rem;
    }
    
    .form-label {
      font-weight: 500;
      font-size: 0.875rem;
    }
    
    .alert-sm {
      padding: 0.5rem 0.75rem;
      font-size: 0.875rem;
    }
    
    .progress {
      border-radius: 10px;
    }
    
    .card-header {
      background-color: #f8f9fa;
      border-bottom: 1px solid #dee2e6;
    }
    
    .is-invalid {
      border-color: #dc3545 !important;
    }
    
    .is-invalid:focus {
      box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .alert {
      margin-bottom: 1rem;
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
              <a href="{{ url('/inventory-receipt') }}" class="nav-link text-dark small active">
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
        <div class="collapse " id="procurementSubmenu">
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
      <!-- ADD THIS: Project Logistics Tracker Dropdown -->
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
        <form id="logout-form" action="{{ url('/logout') }}" method="POST">
          @csrf
          <button type="submit" class="nav-link text-danger btn btn-link p-0" style="text-decoration: none;">
            <i class="bi bi-box-arrow-right me-2"></i> Logout
          </button>
        </form>
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
          <h2 class="fw-bold mb-1">Inventory Receipt Management</h2>
          <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name ?? 'User' }}! Manage incoming shipments and inventory receipt processes.</p>
        </div>
      </div>
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
          <li class="breadcrumb-item"><a href="{{ url('/smartwarehousing') }}" class="text-decoration-none">Smart Warehousing</a></li>
          <li class="breadcrumb-item active" aria-current="page">Inventory Receipt</li>
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
              <h3 class="fw-bold mb-0">{{ $todayReceipts }}</h3>
              <p class="text-muted mb-0 small">Items Received Today</p>
              <small class="text-info"><i class="bi bi-info-circle"></i> New items added</small>
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
              <h3 class="fw-bold mb-0">{{ $weekReceipts }}</h3>
              <p class="text-muted mb-0 small">This Week</p>
              <small class="text-success"><i class="bi bi-arrow-up"></i> Weekly total</small>
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
              <h3 class="fw-bold mb-0">{{ $recentReceipts->where('location', 'receiving_area')->count() }}</h3>
              <p class="text-muted mb-0 small">Awaiting Storage</p>
              <small class="text-warning"><i class="bi bi-arrow-right"></i> Need organization</small>
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
              <h3 class="fw-bold mb-0">₱{{ number_format($totalValue, 0) }}</h3>
              <p class="text-muted mb-0 small">Total Inventory Value</p>
              <small class="text-success"><i class="bi bi-graph-up"></i> Current worth</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="row g-4">
    <!-- Full Width Receipt Form -->
    <div class="col-12">
      <!-- Receipt Entry Form -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom bg-primary text-white">
          <h5 class="card-title mb-0">New Inventory Receipt</h5>
        </div>
        <div class="card-body">
          <!-- Manual Inventory Receipt Entry -->
          <form id="receiptForm">
            <!-- Receipt Header Information -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary mb-3"><i class="bi bi-info-circle me-2"></i>Receipt Information</h6>
              </div>
              <div class="col-md-4">
                <label for="receiptNumber" class="form-label">Receipt Number</label>
                <input type="text" class="form-control" id="receiptNumber" name="receiptNumber" placeholder="Auto-generated" readonly>
              </div>
              <div class="col-md-4">
                <label for="receiptDate" class="form-label">Receipt Date</label>
                <input type="date" class="form-control" id="receiptDate" name="receiptDate" required>
              </div>
              <div class="col-md-4">
                <label for="receivedBy" class="form-label">Received By</label>
                <input type="text" class="form-control" id="receivedBy" name="receivedBy" placeholder="Enter receiver name" required>
              </div>
            </div>
            
            <!-- Supplier & Order Information -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary mb-3"><i class="bi bi-building me-2"></i>Supplier & Order Details</h6>
              </div>
              <div class="col-md-4">
                <label for="supplier" class="form-label">Supplier *</label>
                <select class="form-select" id="supplier" name="supplier" required>
                  <option value="">Select Supplier</option>
                  @foreach($suppliers as $supplier)
                    <option value="{{ $supplier }}">{{ $supplier }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-4">
                <label for="purchaseOrder" class="form-label">Purchase Order #</label>
                <select class="form-select" id="purchaseOrder" name="purchaseOrder">
                  <option value="">Select Purchase Order (Optional)</option>
                </select>
                <small class="text-muted">Select a supplier first</small>
              </div>
              <div class="col-md-4">
                <label for="invoiceNumber" class="form-label">Invoice #</label>
                <select class="form-select" id="invoiceNumber" name="invoiceNumber">
                  <option value="">Select Invoice (Optional)</option>
                </select>
                <small class="text-muted">Select a supplier first</small>
              </div>
            </div>
            
            <!-- Delivery Information -->
            <div class="row g-3 mb-4">
              <div class="col-12">
                <h6 class="text-primary mb-3"><i class="bi bi-truck me-2"></i>Delivery Information</h6>
              </div>
              <div class="col-md-6">
                <label for="deliveryDate" class="form-label">Delivery Date *</label>
                <input type="date" class="form-control" id="deliveryDate" name="deliveryDate" required>
              </div>
              <div class="col-md-6">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Enter any special instructions or notes"></textarea>
              </div>
            </div>
            
            <hr class="my-4">
            
            <!-- Item Entry Section -->
            <h6 class="mb-3">Items Received</h6>
            <div class="table-responsive mb-3">
              <table class="table table-sm" id="itemsTable">
                <thead>
                  <tr>
                    <th>Item Name</th>
                    <th>Received Qty</th>
                    <th>Damaged Qty</th>
                    <th>Good Qty</th>
                    <th>Unit</th>
                    <th>Unit Price</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Location</th>
                    <th>Item Image</th>
                    <th>Damage Image</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="itemsTableBody">
                  <!-- Items will be added here dynamically -->
                </tbody>
              </table>
            </div>
            
            
            <!-- Add Item Form (Initially Hidden) -->
            <div id="addItemForm" style="display: none;" class="border rounded p-3 mb-3 bg-light">
              <h6 class="mb-3">Add New Item</h6>
              
              <!-- PO Item Selection -->
              <div class="row g-3 mb-3">
                <div class="col-12">
                  <label for="poItemSelect" class="form-label">Select from Purchase Order (Optional)</label>
                  <select class="form-select" id="poItemSelect">
                    <option value="">Select an item from PO or enter manually</option>
                  </select>
                  <small class="text-muted">Choose an item from the selected purchase order to auto-fill the form</small>
                </div>
              </div>
              
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="itemName" class="form-label">Item Name</label>
                  <input type="text" class="form-control" id="itemName" name="itemName">
                </div>
                <div class="col-md-3">
                  <label for="itemQty" class="form-label">Quantity</label>
                  <input type="number" class="form-control" id="itemQty" name="itemQty" min="0">
                </div>
                <div class="col-md-3">
                  <label for="itemUnit" class="form-label">Unit</label>
                  <select class="form-select" id="itemUnit" name="itemUnit">
                    <option value="">Select Unit</option>
                    <option value="pcs">Pieces</option>
                    <option value="kg">Kilograms</option>
                    <option value="l">Liters</option>
                    <option value="m">Meters</option>
                    <option value="box">Boxes</option>
                    <option value="pack">Packs</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label for="itemUnitPrice" class="form-label">Unit Price</label>
                  <input type="number" class="form-control" id="itemUnitPrice" name="itemUnitPrice" step="0.01" min="0">
                </div>
                <div class="col-md-3">
                  <label for="itemCondition" class="form-label">Condition</label>
                  <select class="form-select" id="itemCondition" name="itemCondition">
                    <option value="">Select Condition</option>
                    <option value="New">New</option>
                    <option value="Good">Good</option>
                    <option value="Fair">Fair</option>
                    <option value="Damaged">Damaged</option>
                    <option value="Expired">Expired</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="itemLocation" class="form-label">Storage Location</label>
                  <select class="form-select" id="itemLocation" name="itemLocation">
                    <option value="">Select Location</option>
                    <option value="A1-01">A1-01</option>
                    <option value="A1-02">A1-02</option>
                    <option value="B2-01">B2-01</option>
                    <option value="C3-01">C3-01</option>
                    <option value="Cold-01">Cold-01</option>
                    <option value="Haz-01">Haz-01</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="itemImage" class="form-label">Item Image</label>
                  <input type="file" class="form-control" id="itemImage" name="itemImage" accept="image/*">
                  <small class="text-muted">Upload an image of the item (JPG, PNG, GIF - Max 2MB)</small>
                  <div id="imagePreview" class="mt-2" style="display: none;">
                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 150px; max-height: 150px;">
                    <button type="button" class="btn btn-sm btn-outline-danger ms-2" id="removeImageBtn">
                      <i class="bi bi-trash"></i> Remove
                    </button>
                  </div>
                </div>
                <div class="col-12">
                  <button type="button" class="btn btn-success btn-sm me-2" id="saveItemBtn">
                    <i class="bi bi-check-circle me-1"></i>Save Item
                  </button>
                  <button type="button" class="btn btn-outline-secondary btn-sm" id="cancelItemBtn">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                  </button>
                </div>
              </div>
            </div>
            
            <!-- Receipt Summary -->
            <div class="card bg-light border-0 mb-3">
              <div class="card-body">
                <div class="row">
                  <div class="col-md-3">
                    <div class="text-center">
                      <h6 class="text-muted mb-1">Total Items</h6>
                      <h4 class="fw-bold text-primary" id="totalItems">0</h4>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="text-center">
                      <h6 class="text-muted mb-1">Total Quantity</h6>
                      <h4 class="fw-bold text-success" id="totalQuantity">0</h4>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="text-center">
                      <h6 class="text-muted mb-1">Total Value</h6>
                      <h4 class="fw-bold text-info" id="totalValue">₱0.00</h4>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <div class="text-center">
                      <h6 class="text-muted mb-1">Damaged Quantity</h6>
                      <h4 class="fw-bold text-danger" id="damagedItems">0</h4>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <!-- Form Actions -->
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <button type="reset" class="btn btn-outline-secondary me-2">
                  <i class="bi bi-x-circle me-1"></i>Clear Form
                </button>
                <button type="button" class="btn btn-outline-info" id="saveAsDraftBtn">
                  <i class="bi bi-save me-1"></i>Save as Draft
                </button>
              </div>
              <button type="submit" class="btn btn-primary btn-lg" id="submitReceiptBtn">
                <i class="bi bi-check-circle me-2"></i>Complete Receipt
              </button>
            </div>
          </form>

          <!-- Add Item Modal -->
          <div class="modal fade" id="addItemModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">Add New Item to Inventory</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <form id="addItemModalForm">
                    <div class="row g-3">
                      <div class="col-md-6">
                        <label for="modal_item_code" class="form-label">Item Code</label>
                        <input type="text" class="form-control" id="modal_item_code" name="item_code" required>
                      </div>
                      <div class="col-md-6">
                        <label for="modal_name" class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="modal_name" name="name" required>
                      </div>
                      <div class="col-12">
                        <label for="modal_description" class="form-label">Description</label>
                        <textarea class="form-control" id="modal_description" name="description" rows="2"></textarea>
                      </div>
                      <div class="col-md-6">
                        <label for="modal_category" class="form-label">Category</label>
                        <select class="form-select" id="modal_category" name="category" required>
                          <option value="">Select Category</option>
                          <option value="Electronics">Electronics</option>
                          <option value="Office Supplies">Office Supplies</option>
                          <option value="Vehicle Parts">Vehicle Parts</option>
                          <option value="Maintenance">Maintenance</option>
                          <option value="Safety Equipment">Safety Equipment</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="modal_supplier" class="form-label">Supplier</label>
                        <input type="text" class="form-control" id="modal_supplier" name="supplier">
                      </div>
                      <div class="col-md-4">
                        <label for="modal_quantity_received" class="form-label">Quantity Received</label>
                        <input type="number" class="form-control" id="modal_quantity_received" name="quantity_received" min="0" required>
                      </div>
                      <div class="col-md-4">
                        <label for="modal_unit_of_measure" class="form-label">Unit of Measure</label>
                        <select class="form-select" id="modal_unit_of_measure" name="unit_of_measure" required>
                          <option value="">Select Unit</option>
                          <option value="pcs">Pieces</option>
                          <option value="kg">Kilograms</option>
                          <option value="l">Liters</option>
                          <option value="m">Meters</option>
                          <option value="box">Boxes</option>
                          <option value="pack">Packs</option>
                        </select>
                      </div>
                      <div class="col-md-4">
                        <label for="modal_unit_price" class="form-label">Unit Price</label>
                        <input type="number" class="form-control" id="modal_unit_price" name="unit_price" step="0.01" min="0" required>
                      </div>
                      <div class="col-md-6">
                        <label for="modal_minimum_stock" class="form-label">Minimum Stock Level</label>
                        <input type="number" class="form-control" id="modal_minimum_stock" name="minimum_stock" min="0" required>
                      </div>
                      <div class="col-md-6">
                        <label for="modal_reorder_quantity" class="form-label">Reorder Quantity</label>
                        <input type="number" class="form-control" id="modal_reorder_quantity" name="reorder_quantity" min="0" required>
                      </div>
                      <div class="col-md-6">
                        <label for="modal_batch_number" class="form-label">Batch Number</label>
                        <input type="text" class="form-control" id="modal_batch_number" name="batch_number">
                      </div>
                      <div class="col-md-6">
                        <label for="modal_expiry_date" class="form-label">Expiry Date</label>
                        <input type="date" class="form-control" id="modal_expiry_date" name="expiry_date">
                      </div>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary" id="saveItemModalBtn">
                    <i class="bi bi-save"></i> Add to Inventory
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Enhanced JS for item management -->
          <script>
            document.addEventListener('DOMContentLoaded', function() {
              // Item editing functionality
              setupItemEditHandlers();
              
              // Image upload preview functionality
              const itemImageInput = document.getElementById('itemImage');
              const imagePreview = document.getElementById('imagePreview');
              const previewImg = document.getElementById('previewImg');
              const removeImageBtn = document.getElementById('removeImageBtn');
              
              if (itemImageInput) {
                itemImageInput.addEventListener('change', function(e) {
                  const file = e.target.files[0];
                  if (file) {
                    // Validate file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                      alert('File size must be less than 2MB');
                      e.target.value = '';
                      return;
                    }
                    
                    // Validate file type
                    if (!file.type.match('image.*')) {
                      alert('Please select a valid image file');
                      e.target.value = '';
                      return;
                    }
                    
                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                      previewImg.src = e.target.result;
                      imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                  } else {
                    imagePreview.style.display = 'none';
                  }
                });
              }
              
              if (removeImageBtn) {
                removeImageBtn.addEventListener('click', function() {
                  itemImageInput.value = '';
                  imagePreview.style.display = 'none';
                });
              }

              // Get CSRF token
              const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
              
              const addItemBtn = document.getElementById('addItemBtn');
              const addItemForm = document.getElementById('addItemForm');
              const saveItemBtn = document.getElementById('saveItemBtn');
              const cancelItemBtn = document.getElementById('cancelItemBtn');
              const itemsTableBody = document.getElementById('itemsTableBody');

              // Auto-generate receipt number and set current date
              const receiptNumber = document.getElementById('receiptNumber');
              const receiptDate = document.getElementById('receiptDate');
              const deliveryDate = document.getElementById('deliveryDate');
              
              // Generate receipt number (format: REC-YYYY-XXX)
              const currentYear = new Date().getFullYear();
              const randomNum = Math.floor(Math.random() * 1000).toString().padStart(3, '0');
              receiptNumber.value = `REC-${currentYear}-${randomNum}`;
              
              // Set current date
              const today = new Date().toISOString().split('T')[0];
              receiptDate.value = today;
              deliveryDate.value = today;

              // Add Item Button functionality
              if (addItemBtn && addItemForm) {
                addItemBtn.addEventListener('click', function() {
                  addItemForm.style.display = 'block';
                  addItemBtn.style.display = 'none';
                  // Populate dropdown when form opens
                  populatePOItemDropdown();
                });

                cancelItemBtn.addEventListener('click', function() {
                  addItemForm.style.display = 'none';
                  addItemBtn.style.display = 'inline-block';
                  clearItemFields();
                });
              }

              // Handle PO item selection in add item form
              const poItemSelect = document.getElementById('poItemSelect');
              if (poItemSelect) {
                poItemSelect.addEventListener('change', function() {
                  const selectedIndex = this.value;
                  if (selectedIndex !== '' && currentPOItems[selectedIndex]) {
                    const selectedItem = currentPOItems[selectedIndex];
                    
                    // Auto-fill form fields
                    document.getElementById('itemName').value = selectedItem.item_name;
                    document.getElementById('itemDesc').value = selectedItem.description || '';
                    document.getElementById('itemQty').value = selectedItem.quantity;
                    document.getElementById('itemUnit').value = selectedItem.unit;
                    document.getElementById('itemUnitPrice').value = selectedItem.unit_price || 0;
                    document.getElementById('itemCondition').value = 'New';
                    document.getElementById('itemLocation').value = 'receiving_area';
                    
                    console.log('Auto-filled form with PO item:', selectedItem);
                  }
                });
              }

              saveItemBtn.addEventListener('click', function() {
                console.log('Save button clicked'); // Debug log
                
                // Get values
                const name = document.getElementById('itemName').value.trim();
                const qty = document.getElementById('itemQty').value.trim();
                const unit = document.getElementById('itemUnit').value;
                const unitPrice = document.getElementById('itemUnitPrice').value.trim();
                const condition = document.getElementById('itemCondition').value;
                const location = document.getElementById('itemLocation').value;

                console.log('Form values:', { name, qty, unit, unitPrice, condition, location }); // Debug log

                // Simple validation
                if (!name || !qty || !unit || !condition || !location) {
                  alert('Please fill in all required fields');
                  return;
                }

                // Get uploaded image file
                const imageFile = document.getElementById('itemImage').files[0];
                
                // Use the reusable function to add item to table
                addItemToTable({
                  item_name: name,
                  description: '', // Default empty description
                  category: 'General', // Default category
                  quantity: parseInt(qty),
                  unit: unit,
                  unit_price: parseFloat(unitPrice || 0),
                  condition: condition,
                  storage_location: location,
                  image_file: imageFile
                });

                // Hide form and show add button
                addItemForm.style.display = 'none';
                addItemBtn.style.display = 'inline-block';
                
                // Clear fields
                clearItemFields();
                
                // Clear image preview
                document.getElementById('itemImage').value = '';
                document.getElementById('imagePreview').style.display = 'none';
                
                // Update summary
                updateReceiptSummary();
                
                console.log('Item saved successfully'); // Debug log
              });
              // Function to clear item input fields
              function clearItemFields() {
                document.getElementById('itemName').value = '';
                document.getElementById('itemQty').value = '';
                document.getElementById('itemUnit').value = '';
                document.getElementById('itemUnitPrice').value = '';
                document.getElementById('itemCondition').value = '';
                document.getElementById('itemLocation').value = '';
                document.getElementById('poItemSelect').value = ''; // Clear PO item selection
              }

              function updateReceiptSummary() {
                const rows = itemsTableBody.querySelectorAll('tr');
                let totalItems = rows.length;
                let totalReceivedQty = 0;
                let totalDamagedQty = 0;
                let totalGoodQty = 0;
                let totalValue = 0;
                let itemsWithDamage = 0;

                rows.forEach(row => {
                  const cells = row.querySelectorAll('td');
                  if (cells.length >= 8) {
                    // New column structure: Name(0), ReceivedQty(1), DamagedQty(2), GoodQty(3), Unit(4), UnitPrice(5), TotalPrice(6), Status(7), Location(8)
                    const receivedQty = parseInt(cells[1].textContent) || 0;
                    const damagedQtyInput = row.querySelector('.damaged-qty-input');
                    const damagedQty = parseInt(damagedQtyInput?.value) || 0;
                    const goodQty = parseInt(cells[3].textContent) || 0;
                    const totalPrice = parseFloat(cells[6].textContent.replace('₱', '')) || 0;
                    
                    totalReceivedQty += receivedQty;
                    totalDamagedQty += damagedQty;
                    totalGoodQty += goodQty;
                    totalValue += totalPrice;
                    
                    if (damagedQty > 0) {
                      itemsWithDamage++;
                    }
                  }
                });

                document.getElementById('totalItems').textContent = totalItems;
                document.getElementById('totalQuantity').textContent = totalGoodQty; // Show only good quantity for storage
                document.getElementById('totalValue').textContent = '₱' + totalValue.toFixed(2);
                document.getElementById('damagedItems').textContent = totalDamagedQty; // Show total damaged quantity
                
                // Update summary labels to be more descriptive
                const summaryContainer = document.querySelector('.row.text-center');
                if (summaryContainer && totalDamagedQty > 0) {
                  // Add damage summary if not exists
                  if (!document.getElementById('totalDamaged')) {
                    const damageColumn = document.createElement('div');
                    damageColumn.className = 'col-md-3';
                    damageColumn.innerHTML = `
                      <div class="border rounded p-3 bg-light">
                        <h6 class="text-muted mb-1">Total Damaged</h6>
                        <h3 class="text-danger mb-0" id="totalDamaged">${totalDamagedQty}</h3>
                      </div>
                    `;
                    summaryContainer.appendChild(damageColumn);
                  } else {
                    document.getElementById('totalDamaged').textContent = totalDamagedQty;
                  }
                }
              }

              function getConditionBadgeColor(condition) {
                switch(condition) {
                  case 'New': return 'success';
                  case 'Good': return 'primary';
                  case 'Fair': return 'warning';
                  case 'Damaged': return 'danger';
                  case 'Expired': return 'secondary';
                  default: return 'light';
                }
              }

              // Load dashboard statistics on page load (only if elements exist)
              if (document.getElementById('itemsReceivedTodaySummary')) {
                loadDashboardStats();
              }
              
              function loadDashboardStats() {
                fetch('/api/inventory/dashboard-stats')
                  .then(response => response.json())
                  .then(data => {
                    if (data.success) {
                      // Update elements that actually exist in the DOM
                      const itemsReceivedTodayElement = document.getElementById('itemsReceivedTodaySummary');
                      if (itemsReceivedTodayElement) {
                        itemsReceivedTodayElement.textContent = data.stats.items_received_today || 0;
                      }
                      
                      // Update progress bar if it exists
                      const progressElement = document.getElementById('itemsReceivedProgress');
                      if (progressElement && data.stats.items_received_today) {
                        const progressPercent = Math.min((data.stats.items_received_today / 100) * 100, 100);
                        progressElement.style.width = progressPercent + '%';
                      }
                    }
                  })
                  .catch(error => console.error('Error loading stats:', error));
              }

              // Handle supplier selection to load related purchase orders and invoices
              const supplierSelect = document.getElementById('supplier');
              const purchaseOrderSelect = document.getElementById('purchaseOrder');
              const invoiceNumberSelect = document.getElementById('invoiceNumber');

              if (supplierSelect) {
                supplierSelect.addEventListener('change', function() {
                  const selectedSupplier = this.value;
                  
                  // Reset dependent fields
                  purchaseOrderSelect.innerHTML = '<option value="">Select Purchase Order (Optional)</option>';
                  invoiceNumberSelect.innerHTML = '<option value="">Select Invoice (Optional)</option>';
                  
                  if (!selectedSupplier) {
                    return;
                  }
                  
                  // Show loading state
                  purchaseOrderSelect.innerHTML = '<option value="">Loading...</option>';
                  invoiceNumberSelect.innerHTML = '<option value="">Loading...</option>';
                  
                  // Fetch purchase orders and invoices for selected supplier
                  fetch(`/api/inventory/purchase-orders-by-supplier?supplier=${encodeURIComponent(selectedSupplier)}`)
                    .then(response => response.json())
                    .then(data => {
                      console.log('API Response:', data); // Debug log
                      
                      if (data.success) {
                        // Populate purchase orders
                        purchaseOrderSelect.innerHTML = '<option value="">Select Purchase Order (Optional)</option>';
                        data.purchase_orders.forEach(po => {
                          const option = document.createElement('option');
                          option.value = po.po_number;
                          option.textContent = `${po.po_number} - ${po.title} (₱${po.total_amount})`;
                          option.dataset.poData = JSON.stringify(po);
                          purchaseOrderSelect.appendChild(option);
                        });
                        
                        // Store all invoices for filtering and populate dropdown
                        allInvoices = data.invoices;
                        invoiceNumberSelect.innerHTML = '<option value="">Select Invoice (Optional)</option>';
                        data.invoices.forEach(invoice => {
                          const option = document.createElement('option');
                          option.value = invoice.invoice_no;
                          option.textContent = `${invoice.invoice_no} - ₱${invoice.amount} (${invoice.status})`;
                          option.dataset.invoiceData = JSON.stringify(invoice);
                          invoiceNumberSelect.appendChild(option);
                        });
                        
                        // Update help text with debug info
                        const poHelpText = purchaseOrderSelect.nextElementSibling;
                        const invoiceHelpText = invoiceNumberSelect.nextElementSibling;
                        
                        if (data.purchase_orders.length === 0) {
                          if (data.debug) {
                            poHelpText.textContent = `No completed POs found. Total POs: ${data.debug.all_pos_count}`;
                            if (data.debug.all_pos_count > 0) {
                              console.log('PO Statuses:', data.debug.all_pos_statuses);
                            }
                          } else {
                            poHelpText.textContent = 'No purchase orders found for this supplier';
                          }
                          poHelpText.className = 'text-warning';
                        } else {
                          poHelpText.textContent = `${data.purchase_orders.length} completed purchase order(s) available`;
                          poHelpText.className = 'text-success';
                        }
                        
                        if (data.invoices.length === 0) {
                          if (data.debug) {
                            invoiceHelpText.textContent = `No invoices found. Total invoices: ${data.debug.all_invoices_count}`;
                          } else {
                            invoiceHelpText.textContent = 'No invoices found for this supplier';
                          }
                          invoiceHelpText.className = 'text-warning';
                        } else {
                          invoiceHelpText.textContent = `${data.invoices.length} invoice(s) available`;
                          invoiceHelpText.className = 'text-success';
                        }
                      } else {
                        console.error('Error fetching supplier data:', data.message);
                        purchaseOrderSelect.innerHTML = '<option value="">Error loading purchase orders</option>';
                        invoiceNumberSelect.innerHTML = '<option value="">Error loading invoices</option>';
                      }
                    })
                    .catch(error => {
                      console.error('Error:', error);
                      purchaseOrderSelect.innerHTML = '<option value="">Error loading purchase orders</option>';
                      invoiceNumberSelect.innerHTML = '<option value="">Error loading invoices</option>';
                    });
                });
              }

              // Store all invoices for filtering
              let allInvoices = [];

              // Function to filter invoices based on selected PO
              function filterInvoicesByPO(selectedPONumber) {
                const invoiceNumberSelect = document.getElementById('invoiceNumber');
                if (!invoiceNumberSelect) return;
                
                // Clear current invoice options
                invoiceNumberSelect.innerHTML = '<option value="">Select Invoice (Optional)</option>';
                
                if (!selectedPONumber) {
                  // If no PO selected, show all invoices for the supplier
                  allInvoices.forEach(invoice => {
                    const option = document.createElement('option');
                    option.value = invoice.invoice_no;
                    option.textContent = `${invoice.invoice_no} - ₱${invoice.amount} (${invoice.status})`;
                    option.dataset.invoiceData = JSON.stringify(invoice);
                    invoiceNumberSelect.appendChild(option);
                  });
                  return;
                }
                
                // Filter invoices that are tied to the selected PO
                const filteredInvoices = allInvoices.filter(invoice => 
                  invoice.purchase_order_number === selectedPONumber
                );
                
                if (filteredInvoices.length > 0) {
                  filteredInvoices.forEach(invoice => {
                    const option = document.createElement('option');
                    option.value = invoice.invoice_no;
                    option.textContent = `${invoice.invoice_no} - ₱${invoice.amount} (${invoice.status})`;
                    option.dataset.invoiceData = JSON.stringify(invoice);
                    invoiceNumberSelect.appendChild(option);
                  });
                  
                  // Update help text
                  const invoiceHelpText = invoiceNumberSelect.nextElementSibling;
                  invoiceHelpText.textContent = `${filteredInvoices.length} invoice(s) found for this PO`;
                  invoiceHelpText.className = 'text-success';
                } else {
                  // No invoices found for this PO
                  const invoiceHelpText = invoiceNumberSelect.nextElementSibling;
                  invoiceHelpText.textContent = 'No invoices found for this PO';
                  invoiceHelpText.className = 'text-warning';
                }
              }

              // Handle purchase order selection to auto-fill delivery date and populate items
              if (purchaseOrderSelect) {
                purchaseOrderSelect.addEventListener('change', function() {
                  const selectedOption = this.options[this.selectedIndex];
                  const selectedPONumber = this.value;
                  
                  if (selectedOption.dataset.poData) {
                    const poData = JSON.parse(selectedOption.dataset.poData);
                    
                    // Auto-fill delivery date if expected delivery date is available
                    if (poData.expected_delivery_date) {
                      document.getElementById('deliveryDate').value = poData.expected_delivery_date;
                    }
                  }
                  
                  // Filter invoices based on selected PO
                  filterInvoicesByPO(selectedPONumber);
                  
                  // Fetch and populate PO items if a PO is selected
                  if (selectedPONumber) {
                    fetchAndPopulatePOItems(selectedPONumber);
                  } else {
                    // Clear items table if no PO selected
                    clearItemsTable();
                  }
                });
              }
              
              // Store PO items globally for form dropdown
              let currentPOItems = [];
              
              // Function to fetch and populate PO items
              function fetchAndPopulatePOItems(poNumber) {
                fetch(`/api/inventory/purchase-order-items?po_number=${encodeURIComponent(poNumber)}`)
                  .then(response => response.json())
                  .then(data => {
                    console.log('PO Items Response:', data); // Debug log
                    
                    if (data.success && data.items.length > 0) {
                      // Store items for form dropdown
                      currentPOItems = data.items;
                      
                      // Clear existing items
                      clearItemsTable();
                      
                      // Add each PO item to the receipt (exclude items already in storage)
                      data.items.forEach(item => {
                        if (!item.in_storage) {
                          addItemToTable({
                            item_name: item.item_name,
                            description: item.description || '',
                            quantity: item.quantity,
                            unit: item.unit,
                            unit_price: item.unit_price || 0,
                            condition: 'New', // Default condition for new receipts
                            storage_location: 'receiving_area' // Default location
                          });
                        }
                      });
                      
                      // Update summary
                      updateReceiptSummary();
                      
                      // Populate the add item form dropdown
                      populatePOItemDropdown();
                      
                      // Show success message with storage status
                      const helpText = purchaseOrderSelect.nextElementSibling;
                      const availableItems = data.items.filter(item => !item.in_storage).length;
                      const storageItems = data.items.filter(item => item.in_storage).length;
                      
                      if (storageItems > 0) {
                        helpText.innerHTML = `${availableItems} items loaded from PO<br><small class="text-warning">${storageItems} items already in storage (excluded)</small>`;
                      } else {
                        helpText.textContent = `${availableItems} items loaded from PO`;
                      }
                      helpText.className = 'text-success';
                      
                    } else {
                      console.warn('No items found in PO or error:', data.message);
                      currentPOItems = [];
                      populatePOItemDropdown();
                      const helpText = purchaseOrderSelect.nextElementSibling;
                      helpText.textContent = 'No items found in selected PO';
                      helpText.className = 'text-warning';
                    }
                  })
                  .catch(error => {
                    console.error('Error fetching PO items:', error);
                    currentPOItems = [];
                    populatePOItemDropdown();
                    const helpText = purchaseOrderSelect.nextElementSibling;
                    helpText.textContent = 'Error loading PO items';
                    helpText.className = 'text-danger';
                  });
              }
              
              // Function to populate PO item dropdown in add item form
              function populatePOItemDropdown() {
                const poItemSelect = document.getElementById('poItemSelect');
                if (!poItemSelect) return;
                
                // Clear existing options
                poItemSelect.innerHTML = '<option value="">Select an item from PO or enter manually</option>';
                
                // Add PO items as options (exclude items already in storage)
                currentPOItems.forEach((item, index) => {
                  const option = document.createElement('option');
                  option.value = index;
                  
                  if (item.in_storage) {
                    option.textContent = `${item.item_name} - ${item.quantity} ${item.unit} @ $${item.unit_price || 0} [IN STORAGE]`;
                    option.disabled = true;
                    option.style.color = '#6c757d';
                    option.style.fontStyle = 'italic';
                  } else {
                    option.textContent = `${item.item_name} - ${item.quantity} ${item.unit} @ $${item.unit_price || 0}`;
                  }
                  
                  poItemSelect.appendChild(option);
                });
              }
              
              // Function to clear items table
              function clearItemsTable() {
                itemsTableBody.innerHTML = '';
                updateReceiptSummary();
              }
              
              // Function to add item to table (reusable)
              function addItemToTable(itemData) {
                // Ensure unit_price is a number
                const unitPrice = parseFloat(itemData.unit_price) || 0;
                const quantity = parseInt(itemData.quantity) || 0;
                const totalPrice = (quantity * unitPrice).toFixed(2);
                
                // Handle image display
                let imageHtml = '';
                if (itemData.image_url) {
                  imageHtml = `<img src="${itemData.image_url}" alt="Item Image" class="img-thumbnail" style="max-width: 40px; max-height: 40px;">`;
                } else if (itemData.image_file) {
                  // For newly uploaded files, create a preview
                  const reader = new FileReader();
                  reader.onload = function(e) {
                    const imgElement = row.querySelector('.item-image');
                    if (imgElement) {
                      imgElement.innerHTML = `<img src="${e.target.result}" alt="Item Image" class="img-thumbnail" style="max-width: 40px; max-height: 40px;">`;
                    }
                  };
                  reader.readAsDataURL(itemData.image_file);
                  imageHtml = '<span class="item-image text-muted">Loading...</span>';
                } else {
                  imageHtml = '<span class="text-muted">No image</span>';
                }
                
                const row = document.createElement('tr');
                row.innerHTML = `
                  <td>${itemData.item_name}</td>
                  <td class="received-qty">${quantity}</td>
                  <td class="damaged-qty">
                    <div class="d-flex gap-1 align-items-center">
                      <input type="number" class="form-control form-control-sm damaged-qty-input" value="0" min="0" max="${quantity}" style="width: 60px;">
                      <select class="form-select form-select-sm damage-type-select" style="width: 100px; display: none;">
                        <option value="">Type</option>
                        <option value="Physical">Physical</option>
                        <option value="Water">Water</option>
                        <option value="Shipping">Shipping</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Expired">Expired</option>
                        <option value="Missing Parts">Missing Parts</option>
                        <option value="Other">Other</option>
                      </select>
                    </div>
                  </td>
                  <td class="good-qty text-success fw-bold">${quantity}</td>
                  <td>${itemData.unit}</td>
                  <td>₱${unitPrice.toFixed(2)}</td>
                  <td>₱${totalPrice}</td>
                  <td class="status-cell">
                    <span class="badge bg-success status-display">All Good</span>
                    <div class="damage-info" style="display: none;">
                      <span class="badge bg-warning">Partial Damage</span>
                      <small class="text-muted d-block">Return to vendor</small>
                    </div>
                  </td>
                  <td>${itemData.storage_location}</td>
                  <td class="item-image">
                    <div class="image-upload-container">
                      <div class="current-image">${imageHtml}</div>
                      <input type="file" class="form-control form-control-sm mt-1 item-image-input" accept="image/*" style="display: none;">
                      <button type="button" class="btn btn-sm btn-outline-primary upload-item-image-btn mt-1">
                        <i class="bi bi-camera"></i> Add Photo
                      </button>
                    </div>
                  </td>
                  <td class="damage-image">
                    <div class="damage-image-container" style="display: none;">
                      <div class="damage-image-preview text-muted">No damage image</div>
                      <input type="file" class="form-control form-control-sm mt-1 damage-image-input" accept="image/*" style="display: none;">
                      <button type="button" class="btn btn-sm btn-outline-warning upload-damage-image-btn mt-1">
                        <i class="bi bi-exclamation-triangle"></i> Add Evidence
                      </button>
                    </div>
                  </td>
                  <td>
                    <button type="button" class="btn btn-sm btn-primary editItemBtn me-1" title="Edit"><i class="bi bi-pencil"></i></button>
                    <button type="button" class="btn btn-sm btn-success saveItemBtn me-1" style="display: none;" title="Save"><i class="bi bi-check"></i></button>
                    <button type="button" class="btn btn-sm btn-secondary cancelEditBtn me-1" style="display: none;" title="Cancel"><i class="bi bi-x"></i></button>
                    <button type="button" class="btn btn-sm btn-danger removeItemBtn" title="Remove"><i class="bi bi-trash"></i></button>
                  </td>
                `;
                itemsTableBody.appendChild(row);

                // Store image file reference for form submission
                if (itemData.image_file) {
                  row.imageFile = itemData.image_file;
                }

                // Edit item functionality
                row.querySelector('.editItemBtn').addEventListener('click', function() {
                  enterEditMode(row);
                });

                // Save item functionality
                row.querySelector('.saveItemBtn').addEventListener('click', function() {
                  saveItemChanges(row);
                });

                // Cancel edit functionality
                row.querySelector('.cancelEditBtn').addEventListener('click', function() {
                  exitEditMode(row);
                });

                // Remove item functionality
                row.querySelector('.removeItemBtn').addEventListener('click', function() {
                  row.remove();
                  updateReceiptSummary();
                });

                // Damage quantity input functionality
                const damagedQtyInput = row.querySelector('.damaged-qty-input');
                const damageTypeSelect = row.querySelector('.damage-type-select');
                const goodQtyCell = row.querySelector('.good-qty');
                const statusCell = row.querySelector('.status-cell');
                const damageImageContainer = row.querySelector('.damage-image-container');
                const receivedQty = parseInt(row.querySelector('.received-qty').textContent);

                damagedQtyInput.addEventListener('input', function() {
                  const damagedQty = parseInt(this.value) || 0;
                  const goodQty = receivedQty - damagedQty;
                  
                  // Show/hide damage type selector and damage image container
                  if (damagedQty > 0) {
                    damageTypeSelect.style.display = 'block';
                    damageImageContainer.style.display = 'block';
                  } else {
                    damageTypeSelect.style.display = 'none';
                    damageImageContainer.style.display = 'none';
                    damageTypeSelect.value = '';
                  }
                  
                  // Update good quantity
                  goodQtyCell.textContent = goodQty;
                  goodQtyCell.className = goodQty > 0 ? 'good-qty text-success fw-bold' : 'good-qty text-muted fw-bold';
                  
                  // Update status
                  const statusDisplay = statusCell.querySelector('.status-display');
                  const damageInfo = statusCell.querySelector('.damage-info');
                  
                  if (damagedQty === 0) {
                    statusDisplay.textContent = 'All Good';
                    statusDisplay.className = 'badge bg-success status-display';
                    statusDisplay.style.display = 'inline';
                    damageInfo.style.display = 'none';
                  } else if (damagedQty === receivedQty) {
                    statusDisplay.textContent = 'All Damaged';
                    statusDisplay.className = 'badge bg-danger status-display';
                    statusDisplay.style.display = 'inline';
                    damageInfo.style.display = 'none';
                  } else {
                    statusDisplay.style.display = 'none';
                    damageInfo.style.display = 'block';
                    damageInfo.querySelector('.badge').textContent = `${damagedQty} Damaged`;
                  }
                  
                  // Update summary
                  updateReceiptSummary();
                });

                // Damage type selector functionality
                damageTypeSelect.addEventListener('change', function() {
                  const damageType = this.value;
                  const damagedQty = parseInt(damagedQtyInput.value) || 0;
                  
                  if (damagedQty > 0 && damageType) {
                    const damageInfo = statusCell.querySelector('.damage-info');
                    const badgeText = `${damagedQty} ${damageType} Damage`;
                    damageInfo.querySelector('.badge').textContent = badgeText;
                  }
                });

                // Item image upload functionality
                const itemImageInput = row.querySelector('.item-image-input');
                const uploadItemImageBtn = row.querySelector('.upload-item-image-btn');
                const currentImageDiv = row.querySelector('.current-image');

                uploadItemImageBtn.addEventListener('click', function() {
                  itemImageInput.click();
                });

                itemImageInput.addEventListener('change', function(e) {
                  const file = e.target.files[0];
                  if (file) {
                    console.log('Item image selected:', file.name, file.size);
                    const reader = new FileReader();
                    reader.onload = function(e) {
                      currentImageDiv.innerHTML = `<img src="${e.target.result}" alt="Item Image" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">`;
                      uploadItemImageBtn.innerHTML = '<i class="bi bi-camera"></i> Change Photo';
                      uploadItemImageBtn.className = 'btn btn-sm btn-success upload-item-image-btn mt-1';
                    };
                    reader.readAsDataURL(file);
                    row.itemImageFile = file;
                    console.log('Item image stored on row:', row.itemImageFile);
                  }
                });

                // Damage image upload functionality
                const damageImageInput = row.querySelector('.damage-image-input');
                const uploadDamageImageBtn = row.querySelector('.upload-damage-image-btn');
                const damageImagePreview = row.querySelector('.damage-image-preview');

                uploadDamageImageBtn.addEventListener('click', function() {
                  damageImageInput.click();
                });

                damageImageInput.addEventListener('change', function(e) {
                  const file = e.target.files[0];
                  if (file) {
                    console.log('Damage image selected:', file.name, file.size);
                    const reader = new FileReader();
                    reader.onload = function(e) {
                      damageImagePreview.innerHTML = `<img src="${e.target.result}" alt="Damage Evidence" class="img-thumbnail" style="max-width: 60px; max-height: 60px;">`;
                      uploadDamageImageBtn.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Change Evidence';
                      uploadDamageImageBtn.className = 'btn btn-sm btn-warning upload-damage-image-btn mt-1';
                    };
                    reader.readAsDataURL(file);
                    row.damageImageFile = file;
                    console.log('Damage image stored on row:', row.damageImageFile);
                  }
                });
              }

              // Edit mode functions (simplified for new table structure)
              function enterEditMode(row) {
                // Note: Edit functionality removed as table structure changed
                // Items can be removed and re-added if changes needed
                console.log('Edit mode not available in current table structure');
              }

              function saveItemChanges(row) {
                // Simplified - no edit mode in new structure
                console.log('Save changes not available in current table structure');
              }

              function exitEditMode(row) {
                // Simplified - no edit mode in new structure
                console.log('Exit edit mode not available in current table structure');
              }

              // Handle receipt form submission
              const receiptForm = document.getElementById('receiptForm');
              const submitReceiptBtn = document.getElementById('submitReceiptBtn');
              
              if (receiptForm && submitReceiptBtn) {
                receiptForm.addEventListener('submit', function(e) {
                  e.preventDefault();
                  
                  // Collect form data
                  const formData = new FormData(receiptForm);
                  const items = [];
                  
                  // Collect items from table
                  const itemRows = itemsTableBody.querySelectorAll('tr');
                  itemRows.forEach((row, index) => {
                    const cells = row.querySelectorAll('td');
                    if (cells.length >= 10) {
                      const damagedQtyInput = row.querySelector('.damaged-qty-input');
                      const damageTypeSelect = row.querySelector('.damage-type-select');
                      const damagedQty = parseInt(damagedQtyInput?.value) || 0;
                      const damageType = damageTypeSelect?.value || '';
                      
                      const itemData = {
                        item_name: cells[0].textContent.trim(),
                        description: '', // Default empty description
                        category: 'General', // Default category
                        quantity: parseInt(cells[1].textContent.trim()) || 0, // Received quantity
                        damaged_quantity: damagedQty,
                        damage_reason: damagedQty > 0 && damageType ? `${damageType} damage` : (damagedQty > 0 ? 'Damaged on receipt' : ''),
                        unit: cells[4].textContent.trim(),
                        unit_price: parseFloat(cells[5].textContent.replace('₱', '').trim()) || 0,
                        condition: 'Good', // Default condition
                        storage_location: cells[8].textContent.trim()
                      };
                      
                      // Store image files for later appending to submitFormData
                      if (row.itemImageFile) {
                        row.itemImageFileForSubmission = row.itemImageFile;
                      }
                      if (row.damageImageFile) {
                        row.damageImageFileForSubmission = row.damageImageFile;
                      }
                      
                      items.push(itemData);
                    }
                  });
                  
                  if (items.length === 0) {
                    alert('Please add at least one item to the receipt');
                    return;
                  }
                  
                  // Prepare FormData for submission (to handle file uploads)
                  const submitFormData = new FormData();
                  
                  // Add basic form fields
                  submitFormData.append('receipt_date', formData.get('receiptDate'));
                  submitFormData.append('supplier_name', formData.get('supplier'));
                  submitFormData.append('purchase_order_number', formData.get('purchaseOrder'));
                  submitFormData.append('delivery_date', formData.get('deliveryDate'));
                  submitFormData.append('invoice_number', formData.get('invoiceNumber'));
                  submitFormData.append('warehouse_location', 'Main Warehouse');
                  submitFormData.append('received_by', formData.get('receivedBy'));
                  submitFormData.append('notes', formData.get('notes'));
                  
                  // Add items data as JSON
                  submitFormData.append('items', JSON.stringify(items));
                  
                  // Add image files
                  itemRows.forEach((row, index) => {
                    if (row.itemImageFileForSubmission) {
                      console.log(`Adding item image for index ${index}:`, row.itemImageFileForSubmission.name);
                      submitFormData.append(`items[${index}][image]`, row.itemImageFileForSubmission);
                    }
                    if (row.damageImageFileForSubmission) {
                      console.log(`Adding damage image for index ${index}:`, row.damageImageFileForSubmission.name);
                      submitFormData.append(`items[${index}][damage_image]`, row.damageImageFileForSubmission);
                    }
                  });
                  
                  // Submit to server
                  submitReceiptBtn.disabled = true;
                  submitReceiptBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Processing...';
                  
                  fetch('/api/inventory/store-receipt', {
                    method: 'POST',
                    headers: {
                      'X-CSRF-TOKEN': csrfToken
                    },
                    body: submitFormData
                  })
                  .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);
                    return response.text().then(text => {
                      console.log('Raw response:', text);
                      if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}, response: ${text}`);
                      }
                      return text;
                    });
                  })
                  .then(text => {
                    try {
                      const result = JSON.parse(text);
                      return result;
                    } catch (e) {
                      console.error('JSON parse error:', e);
                      console.error('Response text:', text);
                      throw new Error('Invalid JSON response from server');
                    }
                  })
                  .then(result => {
                    if (result.success) {
                      const itemCount = items.length;
                      const receiptNumber = result.receipt_number;
                      const documentGenerated = result.document_generated;
                      
                      // Show success message with document generation status
                      let successMsg = `✅ Receipt created successfully! Receipt #: ${receiptNumber}
                      
📦 ${itemCount} item(s) have been received and are currently in the receiving area.`;

                      if (documentGenerated) {
                        successMsg += `\n📄 Document automatically generated and stored in DTRS (Document Tracking & Records System)`;
                      } else {
                        successMsg += `\n⚠️ Document generation in progress...`;
                      }

                      successMsg += `

Next steps:
1. Items need to be organized and assigned proper storage locations
2. Go to Storage Organization to assign zones and bins
3. Once organized, items will be available for picking and dispatch
4. View the generated document in DTRS system`;
                      
                      if (confirm(successMsg + '\n\nWould you like to go to Storage Organization now?')) {
                        window.location.href = '/storage-organization';
                      } else {
                        // Show additional option to view DTRS
                        if (documentGenerated && confirm('Would you like to view the document in DTRS system?')) {
                          window.open('/dtrs/document', '_blank');
                        }
                        receiptForm.reset();
                        itemsTableBody.innerHTML = '';
                        updateReceiptSummary();
                        location.reload(); // Refresh to show new receipt
                      }
                    } else {
                      alert('Error: ' + result.message);
                    }
                  })
                  .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while creating the receipt.');
                  })
                  .finally(() => {
                    submitReceiptBtn.disabled = false;
                    submitReceiptBtn.innerHTML = '<i class="bi bi-check-circle me-1"></i>Complete Receipt';
                  });
                });
              }

              // Receipt action functions
              function viewReceiptDetails(receiptId) {
                // TODO: Implement receipt details view
                alert('View receipt details for ID: ' + receiptId);
              }

              function printReceipt(receiptId) {
                // TODO: Implement receipt printing
                alert('Print receipt for ID: ' + receiptId);
              }

              function downloadReceipt(receiptId) {
                // TODO: Implement receipt download
                alert('Download receipt for ID: ' + receiptId);
              }
            });
            
            // Setup item edit handlers
            function setupItemEditHandlers() {
              // Edit button handlers
              document.querySelectorAll('.edit-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                  const row = this.closest('tr');
                  enterEditMode(row);
                });
              });
              
              // Save button handlers
              document.querySelectorAll('.save-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                  const row = this.closest('tr');
                  saveItemChanges(row);
                });
              });
              
              // Cancel button handlers
              document.querySelectorAll('.cancel-edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                  const row = this.closest('tr');
                  cancelEditMode(row);
                });
              });
              
              // Remove button handlers
              document.querySelectorAll('.remove-item-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                  const row = this.closest('tr');
                  if (confirm('Are you sure you want to remove this item?')) {
                    row.remove();
                    updateReceiptSummary();
                  }
                });
              });
              
              // Image upload handlers for edit mode
              document.querySelectorAll('.image-edit').forEach(input => {
                input.addEventListener('change', function() {
                  const file = this.files[0];
                  const row = this.closest('tr');
                  const preview = row.querySelector('.image-preview');
                  const img = preview.querySelector('img');
                  
                  if (file) {
                    // Validate file
                    if (file.size > 2 * 1024 * 1024) {
                      alert('File size must be less than 2MB');
                      this.value = '';
                      return;
                    }
                    
                    if (!file.type.match('image.*')) {
                      alert('Please select a valid image file');
                      this.value = '';
                      return;
                    }
                    
                    // Show preview
                    const reader = new FileReader();
                    reader.onload = function(e) {
                      img.src = e.target.result;
                      preview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                  }
                });
              });
            }
            
            function enterEditMode(row) {
              // Hide display elements and show edit elements
              const conditionDisplay = row.querySelector('.condition-display');
              const conditionEdit = row.querySelector('.condition-edit');
              const imageDisplay = row.querySelector('.image-display');
              const imageEdit = row.querySelector('.image-edit');
              
              // Store original values
              row.dataset.originalCondition = conditionDisplay.textContent;
              
              // Switch to edit mode
              conditionDisplay.style.display = 'none';
              conditionEdit.style.display = 'block';
              conditionEdit.value = conditionDisplay.textContent;
              
              imageDisplay.style.display = 'none';
              imageEdit.style.display = 'block';
              
              // Switch buttons
              row.querySelector('.edit-item-btn').style.display = 'none';
              row.querySelector('.save-item-btn').style.display = 'inline-block';
              row.querySelector('.cancel-edit-btn').style.display = 'inline-block';
              row.querySelector('.remove-item-btn').style.display = 'none';
            }
            
            function saveItemChanges(row) {
              const conditionDisplay = row.querySelector('.condition-display');
              const conditionEdit = row.querySelector('.condition-edit');
              const imageDisplay = row.querySelector('.image-display');
              const imageEdit = row.querySelector('.image-edit');
              const imagePreview = row.querySelector('.image-preview');
              
              // Update condition
              conditionDisplay.textContent = conditionEdit.value;
              
              // Update image display
              if (imageEdit.files[0]) {
                imageDisplay.innerHTML = '<span class="text-success"><i class="bi bi-image"></i> Image uploaded</span>';
              }
              
              // Exit edit mode
              cancelEditMode(row);
              
              // Show success message
              showAlert('Item updated successfully!', 'success');
            }
            
            function cancelEditMode(row) {
              const conditionDisplay = row.querySelector('.condition-display');
              const conditionEdit = row.querySelector('.condition-edit');
              const imageDisplay = row.querySelector('.image-display');
              const imageEdit = row.querySelector('.image-edit');
              const imagePreview = row.querySelector('.image-preview');
              
              // Restore original condition if cancelled
              if (row.dataset.originalCondition) {
                conditionEdit.value = row.dataset.originalCondition;
              }
              
              // Reset image input
              imageEdit.value = '';
              imagePreview.style.display = 'none';
              
              // Switch back to display mode
              conditionDisplay.style.display = 'inline';
              conditionEdit.style.display = 'none';
              imageDisplay.style.display = 'block';
              imageEdit.style.display = 'none';
              
              // Switch buttons back
              row.querySelector('.edit-item-btn').style.display = 'inline-block';
              row.querySelector('.save-item-btn').style.display = 'none';
              row.querySelector('.cancel-edit-btn').style.display = 'none';
              row.querySelector('.remove-item-btn').style.display = 'inline-block';
            }
            
            function updateReceiptSummary() {
              const rows = document.querySelectorAll('#itemsTableBody tr');
              let totalItems = rows.length;
              let totalQuantity = 0;
              let totalValue = 0;
              let damagedItems = 0;
              
              rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 6) {
                  const qty = parseInt(cells[1].textContent) || 0; // Quantity is now column 1
                  const price = parseFloat(cells[4].textContent.replace('₱', '')) || 0; // Total Price is now column 4
                  const condition = row.querySelector('.condition-display')?.textContent || '';
                  
                  totalQuantity += qty;
                  totalValue += price;
                  
                  if (condition === 'Damaged') {
                    damagedItems++;
                  }
                }
              });
              
              document.getElementById('totalItems').textContent = totalItems;
              document.getElementById('totalQuantity').textContent = totalQuantity;
              document.getElementById('totalValue').textContent = '₱' + totalValue.toFixed(2);
              document.getElementById('damagedItems').textContent = damagedItems;
            }
            
            function showAlert(message, type = 'info') {
              const alertDiv = document.createElement('div');
              alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
              alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              `;
              
              const container = document.querySelector('.card-body');
              container.insertBefore(alertDiv, container.firstChild);
              
              // Auto dismiss after 3 seconds
              setTimeout(() => {
                if (alertDiv.parentNode) {
                  alertDiv.remove();
                }
              }, 3000);
            }
            
            // Initialize summary on page load
            updateReceiptSummary();
          </script>
        </div>
      </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
      
      <!-- PO Preview (Hidden by default, shown when viewing PO) -->
      <div class="card shadow-sm border-0 mt-4 d-none" id="poPreviewCard">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">PO Preview</h5>
          <button class="btn btn-sm btn-close" id="closePOPreview"></button>
        </div>
        <div class="card-body" id="poPreviewContent">
          <!-- PO details will be loaded dynamically -->
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
    
    // Inventory Receipt functionality
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    // Add Item Button - Show modal for adding to permanent inventory
    // Note: This button is handled by the inline form above, not the modal
    // The modal is opened by the "Add Item" button in the Quick Actions section
    
    // Save Item Button - For modal form (adds to permanent inventory)
    const saveItemModalBtn = document.getElementById('saveItemModalBtn');
    if (saveItemModalBtn) {
      saveItemModalBtn.addEventListener('click', function() {
        const form = document.getElementById('addItemModalForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        fetch('/api/inventory/add-item', {
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
            alert('Item added to inventory successfully!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('addItemModal'));
            modal.hide();
            form.reset();
            
            // Show success message with next step
            const nextStepMsg = `Item "${data.item.name}" has been added to inventory and is currently in the receiving area. 
            
Next steps:
1. Go to Storage Organization to assign a proper location
2. The item will be available for picking and dispatch once organized`;
            
            if (confirm(nextStepMsg + '\n\nWould you like to go to Storage Organization now?')) {
              window.location.href = '/storage-organization';
            } else {
              location.reload();
            }
          } else {
            alert('Error: ' + data.message);
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('An error occurred while adding the item.');
        });
      });
    }
    
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