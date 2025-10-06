<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Procurement & Sourcing Management - Purchase Orders</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- PSM Animations -->
  <link rel="stylesheet" href="{{ asset('assets/css/psm-animations.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* Enhanced table styles */
    .table-enhanced {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .table-enhanced thead th {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border: none;
      font-weight: 600;
      font-size: 0.875rem;
      letter-spacing: 0.5px;
      text-transform: uppercase;
      color: #495057;
      padding: 1rem 0.75rem;
      vertical-align: middle;
      position: relative;
    }
    
    .table-enhanced tbody td {
      border: none;
      border-bottom: 1px solid #f1f3f4;
      padding: 1rem 0.75rem;
      vertical-align: middle;
      font-size: 0.9rem;
      color: #495057;
    }
    
    .table-enhanced tbody tr {
      transition: all 0.2s ease;
      background-color: #ffffff;
    }
    
    .table-enhanced tbody tr:hover {
      background-color: #f8f9fa;
      transform: translateY(-1px);
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .table-enhanced tbody tr:last-child td {
      border-bottom: none;
    }
    
    .sortable {
      cursor: pointer;
      user-select: none;
      position: relative;
      transition: all 0.2s ease;
    }
    
    .sortable:hover {
      background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
      color: #212529;
    }
    
    .sortable i {
      opacity: 0.5;
      transition: opacity 0.2s ease;
    }
    
    .sortable:hover i {
      opacity: 1;
    }
    
    /* Enhanced badges */
    .badge-enhanced {
      padding: 0.4rem 0.8rem;
      border-radius: 20px;
      font-weight: 500;
      font-size: 0.75rem;
      letter-spacing: 0.3px;
      text-transform: uppercase;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      white-space: nowrap;
      min-width: fit-content;
      display: inline-block;
    }
    
    .badge-status-pending {
      background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
      color: white;
    }
    
    .badge-status-approved {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
    }
    
    .badge-status-in-progress {
      background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
      color: white;
    }
    
    .badge-status-completed {
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      color: white;
    }
    
    .badge-status-cancelled {
      background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
      color: white;
    }
    
    /* Enhanced action buttons */
    .btn-action {
      padding: 0.4rem 0.6rem;
      border-radius: 8px;
      border: 2px solid transparent;
      transition: all 0.2s ease;
      font-size: 0.875rem;
      font-weight: 500;
      margin: 0 2px;
    }
    
    .btn-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .btn-action-view {
      background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
      color: white;
      border-color: #0d6efd;
    }
    
    .btn-action-view:hover {
      background: linear-gradient(135deg, #0b5ed7 0%, #520dc2 100%);
      color: white;
    }
    
    .btn-action-edit {
      background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
      color: white;
      border-color: #ffc107;
    }
    
    .btn-action-edit:hover {
      background: linear-gradient(135deg, #e0a800 0%, #dc6502 100%);
      color: white;
    }
    
    .btn-action-delete {
      background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
      color: white;
      border-color: #dc3545;
    }
    
    .btn-action-delete:hover {
      background: linear-gradient(135deg, #c82333 0%, #d91a72 100%);
      color: white;
    }

    .btn-action-approve {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
      border-color: #28a745;
    }
    
    .btn-action-approve:hover {
      background: linear-gradient(135deg, #218838 0%, #1aa085 100%);
      color: white;
    }

    .btn-action-download {
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      color: white;
      border-color: #6c757d;
    }
    
    .btn-action-download:hover {
      background: linear-gradient(135deg, #5a6268 0%, #3d4142 100%);
      color: white;
    }
    
    /* Text alignment improvements */
    .text-center-custom {
      text-align: center !important;
      vertical-align: middle !important;
    }
    
    .text-left-custom {
      text-align: left !important;
      vertical-align: middle !important;
    }
    
    .text-right-custom {
      text-align: right !important;
      vertical-align: middle !important;
    }
    
    /* ID styling */
    .order-id {
      font-family: 'Courier New', monospace;
      font-weight: 700;
      color: #6f42c1;
      background: linear-gradient(135deg, #f8f4ff 0%, #ede4ff 100%);
      padding: 0.25rem 0.5rem;
      border-radius: 6px;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
    }
    
    /* Contract styling */
    .contract-text {
      font-weight: 600;
      color: #212529;
    }
    
    /* Date styling */
    .date-text {
      color: #6c757d;
      font-size: 0.85rem;
      font-weight: 500;
    }
    
    /* Amount styling */
    .amount-text {
      font-family: 'Courier New', monospace;
      font-weight: 600;
      color: #28a745;
    }
    
    .table-container {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
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
              <a href="{{ url('/psm/bidding') }}" class="nav-link text-dark small">
                <i class="bi bi-clipboard-check me-2"></i> Bidding & RFQ
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/contract') }}" class="nav-link text-dark small">
                <i class="bi bi-file-earmark-check me-2"></i> Contract Management
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/psm/order') }}" class="nav-link text-dark small active">
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
            <h2 class="fw-bold mb-1">Purchase Order Management</h2>
            <p class="text-muted mb-0">Welcome back, Sarah! Manage purchase orders and procurement processes.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/psm') }}" class="text-decoration-none">Procurement & Sourcing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Purchase Order</li>
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
                <i class="bi bi-cart-check"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Total Orders</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> All time</small>
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
                <h3 class="fw-bold mb-0">{{ $stats['pending_approval'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Pending Approval</p>
                <small class="text-warning"><i class="bi bi-exclamation-triangle"></i> Requires action</small>
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
                <i class="bi bi-arrow-repeat"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['in_progress'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">In Progress</p>
                <small class="text-info"><i class="bi bi-play-circle"></i> Active orders</small>
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
                <h3 class="fw-bold mb-0">{{ $stats['completed'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Completed</p>
                <small class="text-success"><i class="bi bi-check2-all"></i> Finished</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Purchase Order Management -->
    <div class="row g-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Purchase Orders</h5>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createOrderModal">
              <i class="bi bi-plus-circle me-2"></i>Create New Order
            </button>
          </div>
          <div class="card-body">
            <div class="table-responsive table-container">
              <table class="table table-enhanced">
                <thead>
                  <tr>
                    <th class="text-center-custom sortable">PO Number <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-left-custom sortable">Contract <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-left-custom sortable">Vendor <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-right-custom sortable">Amount <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-center-custom sortable">Status <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-center-custom sortable">Order Date <i class="bi bi-arrow-down-up ms-1"></i></th>
                    <th class="text-center-custom">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($purchaseOrders ?? [] as $order)
                  <tr>
                    <td class="text-center-custom">
                      <span class="order-id">#{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
                    </td>
                    <td class="text-left-custom">
                      <span class="contract-text">{{ $order->contract->contract_number ?? 'N/A' }}</span><br>
                      <small class="text-muted">{{ Str::limit($order->title, 40) }}</small>
                    </td>
                    <td class="text-left-custom">{{ $order->vendor->company_name ?? $order->vendor->name }}</td>
                    <td class="text-right-custom">
                      <span class="amount-text">₱{{ number_format($order->total_amount, 2) }}</span>
                    </td>
                    <td class="text-center-custom">
                      @php
                        $statusClass = match (strtolower(str_replace(' ', '-', $order->status))) {
                          'draft' => 'badge-status-pending',
                          'pending-approval' => 'badge-status-pending',
                          'approved' => 'badge-status-approved',
                          'issued' => 'badge-status-approved',
                          'in-progress' => 'badge-status-in-progress',
                          'completed' => 'badge-status-completed',
                          'cancelled' => 'badge-status-cancelled',
                          default => 'badge-status-pending',
                        };
                      @endphp
                      <span class="badge-enhanced {{ $statusClass }}">{{ $order->status }}</span>
                    </td>
                    <td class="text-center-custom">
                      <span class="date-text">{{ $order->order_date->format('M d, Y') }}</span>
                    </td>
                    <td class="text-center-custom">
                      <div class="d-flex justify-content-center align-items-center gap-1">
                        <button type="button" class="btn btn-action btn-action-view btn-view-details" data-po-id="{{ $order->id }}" title="View Details">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button type="button" class="btn btn-action btn-action-download btn-download-pdf" data-po-id="{{ $order->id }}" title="Download PDF">
                          <i class="bi bi-download"></i>
                        </button>
                        @if($order->status === 'Draft')
                          <button type="button" class="btn btn-action btn-action-edit" title="Edit">
                            <i class="bi bi-pencil"></i>
                          </button>
                          <button type="button" class="btn btn-action btn-action-approve btn-submit-approval" data-po-id="{{ $order->id }}" title="Submit for Approval">
                            <i class="bi bi-send"></i>
                          </button>
                        @elseif($order->status === 'Pending Approval')
                          <button type="button" class="btn btn-action btn-action-approve btn-approve" data-po-id="{{ $order->id }}" title="Approve">
                            <i class="bi bi-check-circle"></i>
                          </button>
                        @elseif($order->status === 'Approved')
                          <button class="btn btn-outline-primary btn-issue" data-po-id="{{ $order->id }}" title="Issue Order">
                            <i class="bi bi-send-check"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                      <i class="bi bi-cart-x fs-1 d-block mb-2"></i>
                      No purchase orders created yet
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
            
            <!-- Pagination -->
            @if(isset($purchaseOrders) && $purchaseOrders->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
              <div class="text-muted small">
                Showing {{ $purchaseOrders->firstItem() }} to {{ $purchaseOrders->lastItem() }} of {{ $purchaseOrders->total() }} results
              </div>
              <nav>
                {{ $purchaseOrders->links() }}
              </nav>
            </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    <!-- Create Order Modal -->
    <div class="modal fade" id="createOrderModal" tabindex="-1" aria-labelledby="createOrderModalLabel" aria-hidden="true" style="z-index: 1056;">
      <div class="modal-dialog modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="createOrderModalLabel">
              <i class="bi bi-cart-plus me-2"></i>Create Purchase Order
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="purchaseOrderForm">
              @csrf
              
              <!-- Contract Selection -->
              <div class="row mb-4">
                <div class="col-12">
                  <h6 class="fw-bold text-primary mb-3">1. Select Contract</h6>
                  <div class="form-group">
                    <label for="contract_id" class="form-label">Contract <span class="text-danger">*</span></label>
                    <select class="form-select" id="contract_id" name="contract_id" required>
                      <option value="">Select a contract...</option>
                      <!-- Contracts will be loaded via AJAX -->
                    </select>
                    <div class="form-text">Select an active contract to create the purchase order from.</div>
                  </div>
                  <!-- Contract Summary (auto-filled after selection) -->
                  <div id="contractSummary" class="card border-0 shadow-sm mt-3 d-none">
                    <div class="card-body">
                      <div class="row g-3 align-items-center">
                        <div class="col-md-4">
                          <div class="small text-muted">Contract</div>
                          <div class="fw-semibold" id="cs_contract_number">-</div>
                          <div id="cs_title" class="text-truncate">-</div>
                        </div>
                        <div class="col-md-3">
                          <div class="small text-muted">Vendor</div>
                          <div class="fw-semibold" id="cs_vendor">-</div>
                        </div>
                        <div class="col-md-3">
                          <div class="small text-muted">Value</div>
                          <div class="fw-semibold" id="cs_value">-</div>
                        </div>
                        <div class="col-md-2">
                          <div class="small text-muted">Period</div>
                          <div class="fw-semibold" id="cs_period">-</div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Basic Information -->
              <div class="row mb-4">
                <div class="col-12">
                  <h6 class="fw-bold text-primary mb-3">2. Order Information</h6>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="title" class="form-label">Order Title <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="title" name="title" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="currency" class="form-label">Currency <span class="text-danger">*</span></label>
                    <select class="form-select" id="currency" name="currency" required>
                      <option value="PHP">PHP (Philippine Peso)</option>
                      <option value="USD">USD (US Dollar)</option>
                      <option value="EUR">EUR (Euro)</option>
                    </select>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="order_date" name="order_date" required>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="expected_delivery_date" class="form-label">Expected Delivery Date</label>
                    <input type="date" class="form-control" id="expected_delivery_date" name="expected_delivery_date">
                  </div>
                </div>
                <div class="col-12">
                  <div class="form-group mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description of the purchase order..."></textarea>
                  </div>
                </div>
              </div>

              <!-- Delivery & Payment -->
              <div class="row mb-4">
                <div class="col-12">
                  <h6 class="fw-bold text-primary mb-3">3. Delivery & Payment Details</h6>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="delivery_address" class="form-label">Delivery Address</label>
                    <textarea class="form-control" id="delivery_address" name="delivery_address" rows="3" placeholder="Delivery address..."></textarea>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group mb-3">
                    <label for="payment_terms" class="form-label">Payment Terms</label>
                    <textarea class="form-control" id="payment_terms" name="payment_terms" rows="3" placeholder="Payment terms and conditions..."></textarea>
                  </div>
                </div>
              </div>

              <!-- Order Items -->
              <div class="row mb-4">
                <div class="col-12">
                  <h6 class="fw-bold text-primary mb-3">4. Order Items <span class="text-danger">*</span></h6>
                  <div id="orderItems">
                    <div class="order-item border rounded p-3 mb-3">
                      <div class="row">
                        <div class="col-md-4">
                          <div class="form-group mb-3">
                            <label class="form-label">Item Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="items[0][item_name]" required>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group mb-3">
                            <label class="form-label">Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="items[0][quantity]" min="1" required>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group mb-3">
                            <label class="form-label">Unit <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="items[0][unit]" placeholder="pcs, kg, etc." required>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group mb-3">
                            <label class="form-label">Unit Price <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="items[0][unit_price]" min="0" step="0.01" required>
                          </div>
                        </div>
                        <div class="col-md-2">
                          <div class="form-group mb-3">
                            <label class="form-label">Total</label>
                            <input type="text" class="form-control item-total" readonly>
                          </div>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="form-group mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="items[0][description]" rows="2" placeholder="Item description..."></textarea>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group mb-3">
                            <label class="form-label">Specifications</label>
                            <textarea class="form-control" name="items[0][specifications]" rows="2" placeholder="Technical specifications..."></textarea>
                          </div>
                        </div>
                      </div>
                      <div class="text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm remove-item">
                          <i class="bi bi-trash me-1"></i>Remove Item
                        </button>
                      </div>
                    </div>
                  </div>
                  
                  <div class="d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-primary" id="addItemBtn">
                      <i class="bi bi-plus-circle me-1"></i>Add Item
                    </button>
                    <div class="text-end">
                      <h5>Total Amount: <span class="text-primary" id="totalAmount">₱0.00</span></h5>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Notes -->
              <div class="row mb-4">
                <div class="col-12">
                  <h6 class="fw-bold text-primary mb-3">5. Additional Notes</h6>
                  <div class="form-group">
                    <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Any additional notes or special instructions..."></textarea>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
              <i class="bi bi-x-circle me-1"></i>Cancel
            </button>
            <button type="submit" form="purchaseOrderForm" class="btn btn-primary">
              <i class="bi bi-check-circle me-1"></i>Create Purchase Order
            </button>
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

      // Purchase Order Action Buttons
      document.querySelectorAll('.btn-submit-approval').forEach(btn => {
        btn.addEventListener('click', function() {
          const poId = this.getAttribute('data-po-id');
          if (poId) submitForApproval(poId);
        });
      });

      document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', function() {
          const poId = this.getAttribute('data-po-id');
          if (poId) approveOrder(poId);
        });
      });

      document.querySelectorAll('.btn-issue').forEach(btn => {
        btn.addEventListener('click', function() {
          const poId = this.getAttribute('data-po-id');
          if (poId) issueOrder(poId);
        });
      });

      // Quick Action Buttons
      document.getElementById('btn-bulk-approve')?.addEventListener('click', bulkApproveOrders);
      document.getElementById('btn-export-orders')?.addEventListener('click', exportOrders);
      document.getElementById('btn-generate-report')?.addEventListener('click', generateReport);

      // Modal functionality
      const createOrderModal = document.getElementById('createOrderModal');
      if (createOrderModal) {
        createOrderModal.addEventListener('show.bs.modal', function() {
          loadContracts();
          resetForm();
          setDefaultDates();
          // Ensure page overlay does not block modal clicks on desktop
          if (overlay) {
            overlay.classList.remove('show');
            overlay.style.display = 'none';
            document.body.style.overflow = '';
          }
        });

        createOrderModal.addEventListener('hidden.bs.modal', function() {
          resetForm();
        });
      }

      // View Details Modal functionality
      document.querySelectorAll('.btn-view-details').forEach(btn => {
        btn.addEventListener('click', function() {
          const poId = this.getAttribute('data-po-id');
          if (poId) viewOrderDetails(poId);
        });
      });

      // Download PDF functionality
      document.querySelectorAll('.btn-download-pdf').forEach(btn => {
        btn.addEventListener('click', function() {
          const poId = this.getAttribute('data-po-id');
          if (poId) downloadPurchaseOrderPDF(poId);
        });
      });

      // Bind Add Item button in modal
      document.getElementById('addItemBtn')?.addEventListener('click', function() {
        addOrderItem();
        calculateTotalAmount();
      });

      // Add loading animation to specific action buttons only
      document.querySelectorAll('.btn-submit-approval, .btn-approve, .btn-issue, #btn-bulk-approve, #btn-export-orders, #btn-generate-report').forEach(btn => {
        btn.addEventListener('click', function(e) {
          if (!this.classList.contains('loading') && !this.hasAttribute('data-bs-toggle')) {
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

    // Purchase Order Functions
    async function submitForApproval(poId) {
      try {
        const response = await fetch(`/psm/order/${poId}/submit-approval`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
          },
        });

        const data = await response.json();
        
        if (data.success) {
          showNotification('success', data.message);
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showNotification('error', data.message || 'Failed to submit for approval');
        }
      } catch (error) {
        console.error('Error submitting for approval:', error);
        showNotification('error', 'Failed to submit for approval');
      }
    }

    async function approveOrder(poId) {
      try {
        const response = await fetch(`/psm/order/${poId}/approve`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
          },
        });

        const data = await response.json();
        
        if (data.success) {
          showNotification('success', data.message);
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showNotification('error', data.message || 'Failed to approve order');
        }
      } catch (error) {
        console.error('Error approving order:', error);
        showNotification('error', 'Failed to approve order');
      }
    }

    async function issueOrder(poId) {
      try {
        const response = await fetch(`/psm/order/${poId}/issue`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
          },
        });

        const data = await response.json();
        
        if (data.success) {
          showNotification('success', data.message);
          setTimeout(() => window.location.reload(), 1500);
        } else {
          showNotification('error', data.message || 'Failed to issue order');
        }
      } catch (error) {
        console.error('Error issuing order:', error);
        showNotification('error', 'Failed to issue order');
      }
    }

    function bulkApproveOrders() {
      showNotification('info', 'Bulk approval feature coming soon');
    }

    function exportOrders() {
      showNotification('info', 'Export feature coming soon');
    }

    function generateReport() {
      showNotification('info', 'Report generation feature coming soon');
    }

    async function downloadPurchaseOrderPDF(poId) {
      try {
        // Show loading notification
        Swal.fire({
          title: 'Generating PDF...',
          text: 'Please wait while we prepare your purchase order document.',
          icon: 'info',
          allowOutsideClick: false,
          allowEscapeKey: false,
          showConfirmButton: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        const response = await fetch(`/psm/order/${poId}/download-pdf`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/pdf',
          },
        });

        if (!response.ok) {
          throw new Error(`HTTP error! status: ${response.status}`);
        }

        // Get the PDF blob
        const blob = await response.blob();
        
        // Create download link
        const url = window.URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `Purchase_Order_${poId}.pdf`;
        
        // Trigger download
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up
        window.URL.revokeObjectURL(url);
        
        // Close loading and show success
        Swal.close();
        showNotification('success', 'Purchase order PDF downloaded successfully!');
        
      } catch (error) {
        console.error('Error downloading PDF:', error);
        Swal.close();
        showNotification('error', 'Failed to download PDF. Please try again.');
      }
    }

    async function viewOrderDetails(poId) {
      try {
        const response = await fetch(`/psm/order/${poId}`, {
          method: 'GET',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
          },
        });

        if (!response.ok) {
          const errorText = await response.text();
          console.error('Server error response:', errorText);
          
          // Try to parse as JSON first, fallback to text
          try {
            const errorData = JSON.parse(errorText);
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
          } catch (parseError) {
            throw new Error(`Server error (${response.status}): Unable to load purchase order details`);
          }
        }

        const data = await response.json();
        
        if (data.success) {
          showOrderDetailsModal(data.order);
        } else {
          showNotification('error', data.message || 'Failed to load order details');
        }
      } catch (error) {
        console.error('Error loading order details:', error);
        showNotification('error', 'Failed to load order details');
      }
    }

    function showOrderDetailsModal(order) {
      // Create modal if it doesn't exist
      let modal = document.getElementById('viewOrderModal');
      if (!modal) {
        modal = createViewOrderModal();
        document.body.appendChild(modal);
      }
      
      // Populate modal with order data
      populateOrderModal(order);
      
      // Show modal
      const bsModal = new bootstrap.Modal(modal);
      bsModal.show();
    }

    function createViewOrderModal() {
      const modal = document.createElement('div');
      modal.className = 'modal fade';
      modal.id = 'viewOrderModal';
      modal.setAttribute('tabindex', '-1');
      modal.setAttribute('aria-labelledby', 'viewOrderModalLabel');
      modal.setAttribute('aria-hidden', 'true');
      modal.style.zIndex = '1056';
      
      modal.innerHTML = `
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="viewOrderModalLabel">
                <i class="bi bi-eye me-2"></i>Purchase Order Details
              </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row mb-4">
                <div class="col-md-6">
                  <h6 class="fw-bold text-primary mb-3">Order Information</h6>
                  <div class="row">
                    <div class="col-sm-4"><strong>PO Number:</strong></div>
                    <div class="col-sm-8" id="view-po-number">-</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4"><strong>Title:</strong></div>
                    <div class="col-sm-8" id="view-title">-</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4"><strong>Status:</strong></div>
                    <div class="col-sm-8" id="view-status">-</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4"><strong>Order Date:</strong></div>
                    <div class="col-sm-8" id="view-order-date">-</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4"><strong>Total Amount:</strong></div>
                    <div class="col-sm-8" id="view-total-amount">-</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <h6 class="fw-bold text-primary mb-3">Contract & Vendor</h6>
                  <div class="row">
                    <div class="col-sm-4"><strong>Contract:</strong></div>
                    <div class="col-sm-8" id="view-contract">-</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4"><strong>Vendor:</strong></div>
                    <div class="col-sm-8" id="view-vendor">-</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4"><strong>Currency:</strong></div>
                    <div class="col-sm-8" id="view-currency">-</div>
                  </div>
                  <div class="row">
                    <div class="col-sm-4"><strong>Expected Delivery:</strong></div>
                    <div class="col-sm-8" id="view-delivery-date">-</div>
                  </div>
                </div>
              </div>
              
              <div class="row mb-4">
                <div class="col-12">
                  <h6 class="fw-bold text-primary mb-3">Description</h6>
                  <p id="view-description" class="text-muted">-</p>
                </div>
              </div>
              
              <div class="row mb-4">
                <div class="col-md-6">
                  <h6 class="fw-bold text-primary mb-3">Delivery Address</h6>
                  <p id="view-delivery-address" class="text-muted">-</p>
                </div>
                <div class="col-md-6">
                  <h6 class="fw-bold text-primary mb-3">Payment Terms</h6>
                  <p id="view-payment-terms" class="text-muted">-</p>
                </div>
              </div>
              
              <div class="row">
                <div class="col-12">
                  <h6 class="fw-bold text-primary mb-3">Order Items</h6>
                  <div class="table-responsive">
                    <table class="table table-bordered">
                      <thead class="table-light">
                        <tr>
                          <th>Item Name</th>
                          <th>Quantity</th>
                          <th>Unit</th>
                          <th>Unit Price</th>
                          <th>Total</th>
                          <th>Description</th>
                        </tr>
                      </thead>
                      <tbody id="view-order-items">
                        <!-- Items will be populated here -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
              
              <div class="row" id="view-notes-section" style="display: none;">
                <div class="col-12">
                  <h6 class="fw-bold text-primary mb-3">Notes</h6>
                  <p id="view-notes" class="text-muted">-</p>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                <i class="bi bi-x-circle me-1"></i>Close
              </button>
            </div>
          </div>
        </div>
      `;
      
      return modal;
    }

    function populateOrderModal(order) {
      document.getElementById('view-po-number').textContent = order.po_number || '-';
      document.getElementById('view-title').textContent = order.title || '-';
      
      // Status badge
      const statusElement = document.getElementById('view-status');
      const badgeClass = {
        'Draft': 'bg-secondary',
        'Pending Approval': 'bg-warning',
        'Approved': 'bg-info',
        'Issued': 'bg-primary',
        'In Progress': 'bg-info',
        'Completed': 'bg-success',
        'Cancelled': 'bg-danger'
      }[order.status] || 'bg-secondary';
      statusElement.innerHTML = `<span class="badge ${badgeClass}">${order.status}</span>`;
      
      document.getElementById('view-order-date').textContent = order.order_date || '-';
      document.getElementById('view-total-amount').textContent = order.total_amount ? `₱${parseFloat(order.total_amount).toLocaleString('en-US', {minimumFractionDigits: 2})}` : '-';
      document.getElementById('view-contract').textContent = order.contract?.contract_number || '-';
      document.getElementById('view-vendor').textContent = order.vendor?.company_name || order.vendor?.name || '-';
      document.getElementById('view-currency').textContent = order.currency || '-';
      document.getElementById('view-delivery-date').textContent = order.expected_delivery_date || '-';
      document.getElementById('view-description').textContent = order.description || 'No description provided';
      document.getElementById('view-delivery-address').textContent = order.delivery_address || 'No delivery address specified';
      document.getElementById('view-payment-terms').textContent = order.payment_terms || 'No payment terms specified';
      
      // Populate items
      const itemsTable = document.getElementById('view-order-items');
      itemsTable.innerHTML = '';
      
      if (order.items && order.items.length > 0) {
        order.items.forEach(item => {
          const row = document.createElement('tr');
          const unitPrice = parseFloat(item.unit_price || 0);
          const quantity = parseFloat(item.quantity || 0);
          const total = unitPrice * quantity;
          
          row.innerHTML = `
            <td>${item.item_name || '-'}</td>
            <td>${item.quantity || '-'}</td>
            <td>${item.unit || '-'}</td>
            <td>₱${unitPrice.toFixed(2)}</td>
            <td>₱${total.toFixed(2)}</td>
            <td>${item.description || '-'}</td>
          `;
          itemsTable.appendChild(row);
        });
      } else {
        itemsTable.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No items found</td></tr>';
      }
      
      // Notes section
      const notesSection = document.getElementById('view-notes-section');
      const notesElement = document.getElementById('view-notes');
      if (order.notes && order.notes.trim()) {
        notesElement.textContent = order.notes;
        notesSection.style.display = 'block';
      } else {
        notesSection.style.display = 'none';
      }
    }

    function showNotification(type, message) {
      const iconMap = {
        success: 'success',
        error: 'error',
        warning: 'warning',
        info: 'info',
        danger: 'error'
      };
      
      Swal.fire({
        icon: iconMap[type] || 'info',
        title: type === 'success' ? 'Success!' : type === 'error' || type === 'danger' ? 'Error!' : 'Notice',
        text: message,
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true
      });
    }
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

  // Modal functionality for Purchase Order creation
  let itemCount = 1;
  let contractsById = {};
  const currencySymbols = { PHP: '₱', USD: '$', EUR: '€' };

  // Load contracts when modal opens
  async function loadContracts() {
    try {
      const response = await fetch('{{ route("psm.api.contracts") }}');
      const data = await response.json();
      
      if (data.success) {
        const contractSelect = document.getElementById('contract_id');
        contractSelect.innerHTML = '<option value="">Select a contract...</option>';
        
        contractsById = {};
        data.contracts.forEach(contract => {
          contractsById[contract.id] = contract;
          const option = document.createElement('option');
          option.value = contract.id;
          option.textContent = `${contract.contract_number} - ${contract.title} (${contract.vendor_name})`;
          contractSelect.appendChild(option);
        });
      }
    } catch (error) {
      console.error('Error loading contracts:', error);
    }
  }

  // Update Contract Summary card
  function updateContractSummary() {
    const select = document.getElementById('contract_id');
    const summary = document.getElementById('contractSummary');
    const selectedId = select.value;
    if (!selectedId || !contractsById[selectedId]) {
      summary.classList.add('d-none');
      document.getElementById('cs_contract_number').textContent = '-';
      document.getElementById('cs_title').textContent = '-';
      document.getElementById('cs_vendor').textContent = '-';
      document.getElementById('cs_value').textContent = '-';
      document.getElementById('cs_period').textContent = '-';
      return;
    }
    const c = contractsById[selectedId];
    document.getElementById('cs_contract_number').textContent = c.contract_number || '-';
    document.getElementById('cs_title').textContent = c.title || '-';
    document.getElementById('cs_vendor').textContent = c.vendor_name || '-';
    const symbol = currencySymbols[document.getElementById('currency').value] || '₱';
    document.getElementById('cs_value').textContent = `${symbol}${Number(c.value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
    const period = [c.start_date, c.end_date].filter(Boolean).join(' → ');
    document.getElementById('cs_period').textContent = period || '-';
    summary.classList.remove('d-none');
  }

  // Reset form when modal closes
  function resetForm() {
    const form = document.getElementById('purchaseOrderForm');
    form.reset();
    
    // Remove all items except the first one
    const orderItems = document.getElementById('orderItems');
    const firstItem = orderItems.querySelector('.order-item');
    orderItems.innerHTML = '';
    orderItems.appendChild(firstItem);
    
    // Reset item count
    itemCount = 1;
    
    // Reset total
    document.getElementById('totalAmount').textContent = '₱0.00';
    document.getElementById('contractSummary')?.classList.add('d-none');
    
    // Clear validation states
    form.querySelectorAll('.is-invalid').forEach(input => {
      input.classList.remove('is-invalid');
    });
    form.querySelectorAll('.invalid-feedback').forEach(feedback => {
      feedback.remove();
    });
  }

  // Set default dates
  function setDefaultDates() {
    document.getElementById('order_date').value = new Date().toISOString().split('T')[0];
  }

  // Add order item
  function addOrderItem() {
    const orderItems = document.getElementById('orderItems');
    const newItem = document.createElement('div');
    newItem.className = 'order-item border rounded p-3 mb-3';
    newItem.innerHTML = `
      <div class="row">
        <div class="col-md-4">
          <div class="form-group mb-3">
            <label class="form-label">Item Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="items[${itemCount}][item_name]" required>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group mb-3">
            <label class="form-label">Quantity <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="items[${itemCount}][quantity]" min="1" required>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group mb-3">
            <label class="form-label">Unit <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="items[${itemCount}][unit]" placeholder="pcs, kg, etc." required>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group mb-3">
            <label class="form-label">Unit Price <span class="text-danger">*</span></label>
            <input type="number" class="form-control" name="items[${itemCount}][unit_price]" min="0" step="0.01" required>
          </div>
        </div>
        <div class="col-md-2">
          <div class="form-group mb-3">
            <label class="form-label">Total</label>
            <input type="text" class="form-control item-total" readonly>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <div class="form-group mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="items[${itemCount}][description]" rows="2" placeholder="Item description..."></textarea>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group mb-3">
            <label class="form-label">Specifications</label>
            <textarea class="form-control" name="items[${itemCount}][specifications]" rows="2" placeholder="Technical specifications..."></textarea>
          </div>
        </div>
      </div>
      <div class="text-end">
        <button type="button" class="btn btn-outline-danger btn-sm remove-item">
          <i class="bi bi-trash me-1"></i>Remove Item
        </button>
      </div>
    `;
    
    orderItems.appendChild(newItem);
    itemCount++;
    calculateTotalAmount();
  }

  // Calculate item total
  function calculateItemTotal(input) {
    const itemDiv = input.closest('.order-item');
    const quantity = parseFloat(itemDiv.querySelector('input[name*="[quantity]"]').value) || 0;
    const unitPrice = parseFloat(itemDiv.querySelector('input[name*="[unit_price]"]').value) || 0;
    const total = quantity * unitPrice;
    
    itemDiv.querySelector('.item-total').value = total.toFixed(2);
  }

  // Calculate total amount
  function calculateTotalAmount() {
    const totals = Array.from(document.querySelectorAll('.item-total')).map(input => parseFloat(input.value) || 0);
    const total = totals.reduce((sum, val) => sum + val, 0);
    const currency = document.getElementById('currency')?.value || 'PHP';
    const symbol = currencySymbols[currency] || '₱';
    document.getElementById('totalAmount').textContent = `${symbol}${total.toFixed(2)}`;
  }

  // Form submission
  document.getElementById('purchaseOrderForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = this;
    const formData = new FormData(form);
    
    try {
      const response = await fetch('{{ route("psm.order.store") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
        },
      });
      
      const data = await response.json();
      
      if (data.success) {
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: data.message,
          showConfirmButton: false,
          timer: 2000,
          timerProgressBar: true
        }).then(() => {
          // Close modal
          const modal = bootstrap.Modal.getInstance(document.getElementById('createOrderModal'));
          modal.hide();
          
          // Reload page to show new order
          window.location.reload();
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error!',
          text: data.message || 'Failed to create purchase order'
        });
        
        if (data.errors) {
          Object.keys(data.errors).forEach((fieldName) => {
            const input = form.querySelector(`[name="${fieldName}"]`);
            if (input) {
              input.classList.add('is-invalid');
              const fb = input.parentNode.querySelector('.invalid-feedback') || document.createElement('div');
              fb.className = 'invalid-feedback';
              fb.textContent = data.errors[fieldName][0];
              if (!input.parentNode.querySelector('.invalid-feedback')) {
                input.parentNode.appendChild(fb);
              }
            }
          });
        }
      }
    } catch (error) {
      console.error('Error submitting form:', error);
      Swal.fire({
        icon: 'error',
        title: 'Error!',
        text: 'Failed to create purchase order. Please try again.'
      });
    }
  });

  // Calculate totals when inputs change
  document.addEventListener('input', function(e) {
    if (e.target.name && (e.target.name.includes('[quantity]') || e.target.name.includes('[unit_price]'))) {
      calculateItemTotal(e.target);
      calculateTotalAmount();
    }
    if (e.target.id === 'currency') {
      calculateTotalAmount();
      updateContractSummary();
    }
  });

  // Event delegation for removing items, keep at least 1
  document.addEventListener('click', function(e) {
    const btn = e.target.closest('.remove-item');
    if (!btn) return;
    const container = document.getElementById('orderItems');
    const items = container.querySelectorAll('.order-item');
    if (items.length <= 1) {
      Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'At least one item is required',
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000
      });
      return;
    }
    btn.closest('.order-item')?.remove();
    calculateTotalAmount();
  });

  // Update contract summary when selection changes
  document.getElementById('contract_id')?.addEventListener('change', updateContractSummary);

  // Ensure total uses correct currency on modal show
  document.getElementById('currency')?.addEventListener('change', calculateTotalAmount);
  </script>
