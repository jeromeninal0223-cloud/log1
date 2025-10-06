<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Procurement & Sourcing Management - Contract Management</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- PSM Animations -->
  <link rel="stylesheet" href="{{ asset('assets/css/psm-animations.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <!-- SweetAlert2 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.min.css" rel="stylesheet">
  
  <style>
    /* Enhanced table styles */
    .table-enhanced {
      border: none;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
      background: white;
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
      text-align: center;
    }
    
    .table-enhanced tbody td {
      border: none;
      border-bottom: 1px solid #f1f3f4;
      padding: 1rem 0.75rem;
      vertical-align: middle;
      font-size: 0.9rem;
      color: #495057;
      transition: all 0.2s ease;
    }
    
    .table-enhanced tbody tr {
      transition: all 0.3s ease;
      background-color: #ffffff;
      cursor: pointer;
    }
    
    .table-enhanced tbody tr:hover {
      background: linear-gradient(135deg, #f8f9fa 0%, #e3f2fd 100%);
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
      border-radius: 8px;
    }
    
    .table-enhanced tbody tr:last-child td {
      border-bottom: none;
    }
    
    /* Contract ID styling */
    .contract-id {
      font-family: 'Courier New', monospace;
      font-weight: 700;
      color: #6f42c1;
      background: linear-gradient(135deg, #f8f4ff 0%, #ede4ff 100%);
      padding: 0.4rem 0.8rem;
      border-radius: 8px;
      font-size: 0.85rem;
      letter-spacing: 0.5px;
      display: inline-block;
      min-width: 120px;
      text-align: center;
    }
    
    /* Vendor name styling */
    .vendor-name {
      font-weight: 600;
      color: #212529;
      font-size: 0.95rem;
    }
    
    /* Value styling */
    .contract-value {
      font-family: 'Courier New', monospace;
      font-weight: 700;
      color: #28a745;
      font-size: 1rem;
      text-align: right;
    }
    
    /* Date styling */
    .contract-date {
      color: #6c757d;
      font-size: 0.85rem;
      font-weight: 500;
      text-align: center;
    }
    
    /* Enhanced badges */
    .badge-enhanced {
      padding: 0.5rem 1rem;
      border-radius: 25px;
      font-weight: 600;
      font-size: 0.75rem;
      letter-spacing: 0.3px;
      text-transform: uppercase;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      min-width: 80px;
      text-align: center;
      display: inline-block;
    }
    
    .badge-status-active {
      background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
      color: white;
    }
    
    .badge-status-pending {
      background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
      color: white;
    }
    
    .badge-status-expired {
      background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);
      color: white;
    }
    
    /* Enhanced action buttons */
    .btn-action-group {
      display: flex;
      gap: 0.5rem;
      justify-content: center;
      align-items: center;
    }
    
    .btn-action {
      padding: 0.5rem 0.75rem;
      border-radius: 8px;
      border: 2px solid transparent;
      transition: all 0.3s ease;
      font-size: 0.875rem;
      font-weight: 500;
      position: relative;
      overflow: hidden;
    }
    
    .btn-action::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
      transition: left 0.5s;
    }
    
    .btn-action:hover::before {
      left: 100%;
    }
    
    .btn-action:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }
    
    .btn-action-view {
      background: linear-gradient(135deg, #0d6efd 0%, #6610f2 100%);
      color: white;
      border-color: #0d6efd;
    }
    
    .btn-action-view:hover {
      background: linear-gradient(135deg, #0b5ed7 0%, #520dc2 100%);
      color: white;
      border-color: #0b5ed7;
    }
    
    .btn-action-download {
      background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
      color: white;
      border-color: #17a2b8;
    }
    
    .btn-action-download:hover {
      background: linear-gradient(135deg, #138496 0%, #1aa085 100%);
      color: white;
      border-color: #138496;
    }
    
    /* Text alignment classes */
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
    
    /* Card enhancements */
    .card-enhanced {
      border: none;
      border-radius: 15px;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      overflow: hidden;
    }
    
    .card-enhanced:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }
    
    .card-header-enhanced {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 1px solid #dee2e6;
      padding: 1.25rem 1.5rem;
      font-weight: 600;
      color: #495057;
    }
    
    /* Quick action buttons */
    .btn-quick-action {
      border-radius: 10px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }
    
    .btn-quick-action:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Progress bars enhancement */
    .progress-enhanced {
      height: 8px;
      border-radius: 10px;
      background-color: #e9ecef;
      overflow: hidden;
    }
    
    .progress-enhanced .progress-bar {
      border-radius: 10px;
      transition: width 0.6s ease;
    }
    
    /* Alert enhancements */
    .alert-enhanced {
      border: none;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      border-left: 4px solid;
    }
    
    .alert-enhanced.alert-danger {
      border-left-color: #dc3545;
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    }
    
    .alert-enhanced.alert-warning {
      border-left-color: #ffc107;
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    }
    
    .alert-enhanced.alert-info {
      border-left-color: #17a2b8;
      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    }
    
    /* Modal enhancements */
    .modal-content-enhanced {
      border: none;
      border-radius: 15px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    }
    
    .modal-header-enhanced {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 1px solid #dee2e6;
      border-radius: 15px 15px 0 0;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
      .table-enhanced {
        font-size: 0.8rem;
      }
      
      .contract-id {
        min-width: 100px;
        font-size: 0.75rem;
        padding: 0.3rem 0.6rem;
      }
      
      .btn-action {
        padding: 0.4rem 0.6rem;
        font-size: 0.8rem;
      }
      
      .btn-action-group {
        flex-direction: column;
        gap: 0.3rem;
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
      <h6 class="fw-semibold mb-1">{{ Auth::user()->name }}</h6>
      <small class="text-muted">{{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}</small>
    </div>

      <!-- Navigation Menu -->
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="{{ url('/officer/dashboard') }}" class="nav-link text-dark">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#procurementSubmenu" aria-expanded="true" aria-controls="procurementSubmenu">
          <i class="bi bi-cart-plus me-2"></i> Procurement & Sourcing
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse show" id="procurementSubmenu">
          <ul class="nav flex-column ms-3">
            <li class="nav-item">
              <a href="{{ url('/officer/purchaserequest') }}" class="nav-link text-dark small">
                <i class="bi bi-file-earmark-text me-2"></i> Purchase Request
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/officer/vendorlist') }}" class="nav-link text-dark small">
                <i class="bi bi-building me-2"></i> Vendor Management
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/officer/biddinglist') }}" class="nav-link text-dark small">
                <i class="bi bi-clipboard-check me-2"></i> Bidding & RFQ
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/officer/contractlist') }}" class="nav-link text-dark small active">
                <i class="bi bi-file-earmark-check me-2"></i> Contract Management
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/officer/orderlist') }}" class="nav-link text-dark small">
                <i class="bi bi-cart-check me-2"></i> Purchase Orders
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/officer/trackinglist') }}" class="nav-link text-dark small">
                <i class="bi bi-truck me-2"></i> Delivery Tracking
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/officer/invoicelist') }}" class="nav-link text-dark small">
                <i class="bi bi-receipt me-2"></i> Invoice Management
              </a>
            </li>
          </ul>
        </div>
      </li>
      @if(Auth::user()->role !== 'procurement_officer')
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
            <h2 class="fw-bold mb-1">Contract Management</h2>
            <p class="text-muted mb-0">Welcome back, Sarah! Manage contracts and vendor agreements.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/psm') }}" class="text-decoration-none">Procurement & Sourcing</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contract Management</li>
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
                <i class="bi bi-file-earmark-check"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">12</h3>
                <p class="text-muted mb-0 small">Active Contracts</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +2 this week</small>
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
                <h3 class="fw-bold mb-0">5</h3>
                <p class="text-muted mb-0 small">Pending Approval</p>
                <small class="text-warning"><i class="bi bi-arrow-up"></i> +1</small>
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
                <h3 class="fw-bold mb-0">28</h3>
                <p class="text-muted mb-0 small">Completed</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +3 this month</small>
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
                <h3 class="fw-bold mb-0">$2.4M</h3>
                <p class="text-muted mb-0 small">Total Value</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +18%</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Contract Management -->
    <div class="row">
      <div class="col-12">
        <div class="card card-enhanced shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Contract Management</h5>
            <div class="d-flex gap-2">
              <select class="form-select form-select-sm" style="width: auto;">
                <option value="">All Contracts</option>
                <option value="active">Active</option>
                <option value="pending">Pending Approval</option>
                <option value="expired">Expired</option>
              </select>
              <button class="btn btn-sm btn-outline-primary">View All</button>
            </div>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-enhanced">
                <thead>
                  <tr>
                    <th class="text-center-custom">Contract ID</th>
                    <th class="text-left-custom">Vendor</th>
                    <th class="text-right-custom">Value</th>
                    <th class="text-center-custom">Start Date</th>
                    <th class="text-center-custom">End Date</th>
                    <th class="text-center-custom">Status</th>
                    <th class="text-center-custom">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($contracts ?? [] as $contract)
                  <tr>
                    <td class="text-center-custom">
                      <span class="contract-id">{{ $contract->contract_number }}</span>
                    </td>
                    <td class="text-left-custom">
                      <span class="vendor-name">{{ optional($contract->vendor)->company_name ?? optional($contract->vendor)->name ?? '—' }}</span>
                    </td>
                    <td class="text-right-custom">
                      <span class="contract-value">₱{{ number_format($contract->value, 2) }}</span>
                    </td>
                    <td class="text-center-custom">
                      <span class="contract-date">{{ optional($contract->start_date)->format('M d, Y') ?? '—' }}</span>
                    </td>
                    <td class="text-center-custom">
                      <span class="contract-date">{{ optional($contract->end_date)->format('M d, Y') ?? '—' }}</span>
                    </td>
                    <td class="text-center-custom">
                      @php
                        $statusClass = match ($contract->status) {
                          'Active' => 'badge-status-active',
                          'Pending' => 'badge-status-pending',
                          'Expired' => 'badge-status-expired',
                          default => 'badge-status-pending',
                        };
                      @endphp
                      <span class="badge-enhanced {{ $statusClass }}">{{ $contract->status }}</span>
                    </td>
                    <td class="text-center-custom">
                      <div class="btn-action-group">
                        <button class="btn btn-action btn-action-view view-contract-btn" 
                                data-contract-id="{{ $contract->id }}" 
                                data-contract-number="{{ $contract->contract_number }}"
                                title="View Contract">
                          <i class="bi bi-eye"></i>
                        </button>
                        <button class="btn btn-action btn-action-download download-contract-btn" 
                                data-contract-id="{{ $contract->id }}" 
                                data-contract-number="{{ $contract->contract_number }}"
                                title="Download">
                          <i class="bi bi-download"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                      <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                      No contracts yet
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- View Contract Modal -->
  <div class="modal fade" id="viewContractModal" tabindex="-1" aria-labelledby="viewContractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="viewContractModalLabel">Contract Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="contractContent">
            <!-- Contract content will be loaded here -->
            <div class="text-center py-4">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading contract details...</p>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" id="signFromModal" style="display: none;">
            <i class="bi bi-pen me-2"></i>Sign Contract
          </button>
          <button type="button" class="btn btn-primary" id="downloadFromModal">
            <i class="bi bi-download me-2"></i>Download PDF
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Contract Signing Modal -->
  <div class="modal fade" id="psmSigningModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Digital Contract Signing - Procurement</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="psmContractPreview" class="mb-4">
            <!-- Contract preview will be loaded here -->
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <h6 class="fw-semibold mb-3">Contract Terms Agreement</h6>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="psmAgreeTerms" required>
                <label class="form-check-label" for="psmAgreeTerms">
                  I have read and agree to all contract terms and conditions
                </label>
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="psmAgreeNegotiatedTerms" required>
                <label class="form-check-label" for="psmAgreeNegotiatedTerms">
                  I approve the negotiated price and terms on behalf of procurement
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <h6 class="fw-semibold mb-3">Digital Signature</h6>
              <div class="border rounded p-3 mb-3" style="height: 200px;">
                <canvas id="psmSignaturePad" class="w-100 h-100"></canvas>
              </div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearPsmSignature()">
                  <i class="bi bi-arrow-clockwise me-1"></i>Clear
                </button>
                <button type="button" class="btn btn-sm btn-success" onclick="submitPsmSignature()" disabled id="psmSubmitSignBtn">
                  <i class="bi bi-pen me-1"></i>Sign Contract
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.1/dist/sweetalert2.all.min.js"></script>
  <!-- Signature Pad -->
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

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
          form.action = '/logout';
          
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
        // Always keep Smart Warehousing active on SWS pages
        warehouseDropdown.classList.add('active');
        
        // Check if user manually closed the dropdown
        const userManuallyClosed = localStorage.getItem('warehouseDropdownClosed') === 'true';
        
        // Only auto-expand if user hasn't manually closed it
        if (!userManuallyClosed) {
          // If we're on any warehouse sub-page, expand the dropdown
          if (currentPath.includes('/inventory-receipt') || 
              currentPath.includes('/storage-organization') || 
              currentPath.includes('/picking-dispatch') || 
              currentPath.includes('/stock-replenishment')) {
            
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

      // Contract view and download functionality
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
      let currentContractId = null;

      // View Contract functionality
      document.querySelectorAll('.view-contract-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          const contractId = this.dataset.contractId;
          const contractNumber = this.dataset.contractNumber;
          currentContractId = contractId;
          
          // Update modal title
          document.getElementById('viewContractModalLabel').textContent = `Contract ${contractNumber}`;
          
          // Show modal
          const modal = new bootstrap.Modal(document.getElementById('viewContractModal'));
          modal.show();
          
          // Load contract details
          loadContractDetails(contractId);
        });
      });

      // Download Contract functionality
      document.querySelectorAll('.download-contract-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
          e.preventDefault();
          const contractId = this.dataset.contractId;
          const contractNumber = this.dataset.contractNumber;
          downloadContract(contractId, contractNumber);
        });
      });

      // Download from modal
      document.getElementById('downloadFromModal').addEventListener('click', function() {
        if (currentContractId) {
          const contractNumber = document.getElementById('viewContractModalLabel').textContent.replace('Contract ', '');
          downloadContract(currentContractId, contractNumber);
        }
      });

      // Sign from modal
      document.getElementById('signFromModal').addEventListener('click', function() {
        const contractId = this.getAttribute('data-contract-id');
        if (contractId) {
          signContractPSM(contractId);
        }
      });

      // PSM Signing functionality
      let psmSignaturePad;
      let currentPsmContractId;

      function signContractPSM(contractId) {
        currentPsmContractId = contractId;
        
        // Load contract preview
        fetch(`/api/contracts/${contractId}/signing-status`)
          .then(response => {
            if (!response.ok) {
              throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              const contract = data.contract;
              document.getElementById('psmContractPreview').innerHTML = `
                <div class="bg-light p-3 rounded">
                  <h6 class="fw-semibold">Contract ${contract.contract_number || 'N/A'}</h6>
                  <p class="mb-2"><strong>Contract Number:</strong> ${contract.contract_number || 'N/A'}</p>
                  <p class="mb-2"><strong>Contract Value:</strong> ${numberFormat(contract.negotiated_value || contract.value || 0)}</p>
                  <p class="mb-2"><strong>Status:</strong> <span class="badge bg-${getStatusBadgeClass(contract.workflow_status || 'draft')}">${(contract.workflow_status || 'draft').replace('_', ' ').toUpperCase()}</span></p>
                  <p class="mb-0"><strong>Terms:</strong> Please review all terms and conditions before signing.</p>
                </div>
              `;
              
              new bootstrap.Modal(document.getElementById('psmSigningModal')).show();
            } else {
              alert('Error loading contract: ' + (data.error || 'Contract not found'));
            }
          })
          .catch(error => {
            console.error('Error loading contract for signing:', error);
            alert('Failed to load contract for signing. Error: ' + error.message);
          });
      }

      // Initialize PSM signature pad when signing modal is shown
      document.getElementById('psmSigningModal').addEventListener('shown.bs.modal', function () {
        const canvas = document.getElementById('psmSignaturePad');
        psmSignaturePad = new SignaturePad(canvas, {
          backgroundColor: 'rgba(255, 255, 255, 0)',
          penColor: 'rgb(0, 0, 0)'
        });
        
        // Enable submit button when signature is drawn
        psmSignaturePad.addEventListener('endStroke', function () {
          checkPsmFormValidity();
        });
        
        // Resize canvas
        resizePsmCanvas();
      });

      function resizePsmCanvas() {
        const canvas = document.getElementById('psmSignaturePad');
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        canvas.width = canvas.offsetWidth * ratio;
        canvas.height = canvas.offsetHeight * ratio;
        canvas.getContext('2d').scale(ratio, ratio);
        psmSignaturePad.clear();
      }

      // Make function globally accessible
      window.clearPsmSignature = function() {
        psmSignaturePad.clear();
        checkPsmFormValidity();
      }

      function checkPsmFormValidity() {
        const agreeTerms = document.getElementById('psmAgreeTerms').checked;
        const agreeNegotiatedTerms = document.getElementById('psmAgreeNegotiatedTerms').checked;
        const hasSignature = psmSignaturePad && !psmSignaturePad.isEmpty();
        
        document.getElementById('psmSubmitSignBtn').disabled = !(agreeTerms && agreeNegotiatedTerms && hasSignature);
      }

      // Add event listeners for PSM checkboxes
      document.getElementById('psmAgreeTerms').addEventListener('change', checkPsmFormValidity);
      document.getElementById('psmAgreeNegotiatedTerms').addEventListener('change', checkPsmFormValidity);

      // Make function globally accessible
      window.submitPsmSignature = function() {
        if (psmSignaturePad.isEmpty()) {
          Swal.fire({
            icon: 'warning',
            title: 'Signature Required',
            text: 'Please provide your signature before proceeding.',
            confirmButtonColor: '#0d6efd'
          });
          return;
        }

        const signatureData = psmSignaturePad.toDataURL().split(',')[1]; // Remove data URL prefix
        
        fetch(`/api/contracts/${currentPsmContractId}/procurement-sign`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          },
          body: JSON.stringify({
            signature_data: signatureData,
            final_approval: true
          })
        })
        .then(response => {
          if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
          }
          return response.json();
        })
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Contract Signed!',
              text: 'The contract has been signed successfully.',
              confirmButtonColor: '#28a745',
              timer: 3000,
              timerProgressBar: true
            }).then(() => {
              bootstrap.Modal.getInstance(document.getElementById('psmSigningModal')).hide();
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Signing Failed',
              text: 'Error signing contract: ' + (data.error || data.message || 'Unknown error'),
              confirmButtonColor: '#dc3545'
            });
          }
        })
        .catch(error => {
          console.error('Error signing contract:', error);
          Swal.fire({
            icon: 'error',
            title: 'Network Error',
            text: 'Error signing contract: ' + (error.error || error.message || 'Network error occurred'),
            confirmButtonColor: '#dc3545'
          });
        });
      }

      // Load contract details function
      function loadContractDetails(contractId) {
        const contentDiv = document.getElementById('contractContent');
        
        // Show loading state
        contentDiv.innerHTML = `
          <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading contract details...</p>
          </div>
        `;

        // Fetch contract details
        fetch(`/api/contracts/${contractId}/view`, {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayContractDetails(data.contract);
          } else {
            contentDiv.innerHTML = `
              <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                Error loading contract: ${data.message}
              </div>
            `;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          contentDiv.innerHTML = `
            <div class="alert alert-danger">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Failed to load contract details. Please try again.
            </div>
          `;
        });
      }

      // Display contract details function
      function displayContractDetails(contract) {
        const contentDiv = document.getElementById('contractContent');
        
        contentDiv.innerHTML = `
          <div class="contract-details">
            <!-- Contract Header -->
            <div class="row mb-4">
              <div class="col-md-6">
                <h6 class="text-muted mb-2">Contract Information</h6>
                <table class="table table-sm table-borderless">
                  <tr>
                    <td class="fw-semibold">Contract Number:</td>
                    <td>${contract.contract_number}</td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Status:</td>
                    <td><span class="badge bg-${getStatusBadgeClass(contract.status)}">${contract.status}</span></td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Contract Value:</td>
                    <td class="fw-bold text-success">${numberFormat(contract.value)}</td>
                  </tr>
                </table>
              </div>
              <div class="col-md-6">
                <h6 class="text-muted mb-2">Dates & Duration</h6>
                <table class="table table-sm table-borderless">
                  <tr>
                    <td class="fw-semibold">Start Date:</td>
                    <td>${formatDate(contract.start_date)}</td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">End Date:</td>
                    <td>${formatDate(contract.end_date)}</td>
                  </tr>
                  <tr>
                    <td class="fw-semibold">Duration:</td>
                    <td>${calculateDuration(contract.start_date, contract.end_date)}</td>
                  </tr>
                </table>
              </div>
            </div>

            <!-- Vendor Information -->
            <div class="row mb-4">
              <div class="col-12">
                <h6 class="text-muted mb-2">Vendor Information</h6>
                <div class="card bg-light">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <p class="mb-1"><strong>Company:</strong> ${contract.vendor?.company_name || contract.vendor?.name || 'N/A'}</p>
                        <p class="mb-1"><strong>Contact Person:</strong> ${contract.vendor?.name || 'N/A'}</p>
                        <p class="mb-0"><strong>Email:</strong> ${contract.vendor?.email || 'N/A'}</p>
                      </div>
                      <div class="col-md-6">
                        <p class="mb-1"><strong>Phone:</strong> ${contract.vendor?.phone || 'N/A'}</p>
                        <p class="mb-1"><strong>Business Type:</strong> ${contract.vendor?.business_type || 'N/A'}</p>
                        <p class="mb-0"><strong>Address:</strong> ${contract.vendor?.address || 'N/A'}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Contract Terms -->
            <div class="row mb-4">
              <div class="col-12">
                <h6 class="text-muted mb-2">Contract Terms & Conditions</h6>
                <div class="card">
                  <div class="card-body">
                    <div class="contract-terms" style="max-height: 300px; overflow-y: auto;">
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Digital Signatures Section -->
            <div class="row mb-4" id="signaturesSection" style="display: none;">
              <div class="col-12">
                <h6 class="text-muted mb-2">Digital Signatures</h6>
                <div class="card">
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-6">
                        <div class="signature-block">
                          <h6 class="fw-semibold mb-2">Vendor Signature</h6>
                          <div id="vendorSignatureStatus" class="signature-status">
                            <span class="badge bg-secondary">Not Signed</span>
                          </div>
                          <div id="vendorSignatureDate" class="text-muted small mt-1" style="display: none;"></div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="signature-block">
                          <h6 class="fw-semibold mb-2">Procurement Signature</h6>
                          <div id="procurementSignatureStatus" class="signature-status">
                            <span class="badge bg-secondary">Not Signed</span>
                          </div>
                          <div id="procurementSignatureDate" class="text-muted small mt-1" style="display: none;"></div>
                        </div>
                      </div>
                    </div>
                    
                    <!-- Contract Status Summary -->
                    <div class="mt-3 pt-3 border-top">
                      <div class="d-flex align-items-center justify-content-between">
                        <div>
                          <h6 class="fw-semibold mb-1">Contract Status</h6>
                          <div id="contractStatusBadge">
                            <span class="badge bg-warning">Pending Signatures</span>
                          </div>
                        </div>
                        <div class="text-end">
                          <div id="signingProgress" class="progress" style="width: 200px; height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 0%"></div>
                          </div>
                          <small class="text-muted mt-1 d-block" id="progressText">0% Complete</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Related Documents -->
            <div class="row">
              <div class="col-12">
                <h6 class="text-muted mb-2">Related Documents</h6>
                <div class="list-group">
                  ${contract.documents && contract.documents.length > 0 ? 
                    contract.documents.map(doc => `
                      <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                          <i class="bi bi-file-earmark-text me-2"></i>
                          ${doc.name}
                        </div>
                        <button class="btn btn-sm btn-outline-primary" onclick="downloadDocument('${doc.id}')">
                          <i class="bi bi-download"></i>
                        </button>
                      </div>
                    `).join('') : 
                    '<div class="list-group-item text-muted">No documents attached</div>'
                  }
                </div>
              </div>
            </div>
          </div>
        `;
        
        // Set the HTML content for contract terms
        const termsDiv = contentDiv.querySelector('.contract-terms');
        if (termsDiv && contract.terms) {
          // Filter out IP address and signature hash from terms
          let filteredTerms = contract.terms;
          
          // Remove IP Address lines
          filteredTerms = filteredTerms.replace(/IP Address:\s*\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/gi, '');
          
          // Remove Signature Hash lines
          filteredTerms = filteredTerms.replace(/Signature Hash:\s*\$2y\$[^\s\n\r<]*/gi, '');
          
          // Clean up any extra whitespace or empty lines left behind
          filteredTerms = filteredTerms.replace(/\n\s*\n/g, '\n');
          filteredTerms = filteredTerms.replace(/<p>\s*<\/p>/g, '');
          filteredTerms = filteredTerms.replace(/<br\s*\/?>\s*<br\s*\/?>/g, '<br>');
          
          termsDiv.innerHTML = filteredTerms;
        } else if (termsDiv) {
          termsDiv.innerHTML = '<p class="text-muted">No terms specified</p>';
        }

        // Update signature display
        updateSignatureDisplay(contract);

        // Show/hide sign button based on contract status
        const signBtn = document.getElementById('signFromModal');
        if (contract.status === 'Pending' || contract.workflow_status === 'pending_procurement_signature') {
          signBtn.style.display = 'inline-block';
          signBtn.setAttribute('data-contract-id', contract.id);
        } else {
          signBtn.style.display = 'none';
        }
      }

      // Enhanced notification function using SweetAlert
      function showNotification(message, type = 'info') {
        const config = {
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 4000,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
          }
        };
        
        switch(type) {
          case 'success':
            Swal.fire({
              ...config,
              icon: 'success',
              title: message,
              background: '#d4edda',
              color: '#155724'
            });
            break;
          case 'error':
            Swal.fire({
              ...config,
              icon: 'error',
              title: message,
              background: '#f8d7da',
              color: '#721c24',
              timer: 6000
            });
            break;
          case 'warning':
            Swal.fire({
              ...config,
              icon: 'warning',
              title: message,
              background: '#fff3cd',
              color: '#856404'
            });
            break;
          default:
            Swal.fire({
              ...config,
              icon: 'info',
              title: message,
              background: '#d1ecf1',
              color: '#0c5460'
            });
        }
      }

      // Enhanced download contract function with SweetAlert
      function downloadContract(contractId, contractNumber) {
        Swal.fire({
          title: 'Download Contract',
          text: `Download contract ${contractNumber}?`,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#17a2b8',
          cancelButtonColor: '#6c757d',
          confirmButtonText: '<i class="bi bi-download"></i> Download',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            // Show loading state
            const btn = event.target.closest('button');
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="bi bi-arrow-clockwise me-1"></i>Downloading...';
            btn.disabled = true;

            // Create download link
            const downloadUrl = `/psm/contract/${contractId}/download`;
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.download = `Contract_${contractNumber}.pdf`;
            
            // Add CSRF token as parameter
            const url = new URL(link.href, window.location.origin);
            url.searchParams.append('_token', csrfToken);
            link.href = url.toString();
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            // Show success notification
            showNotification(`Contract ${contractNumber} download started`, 'success');

            // Reset button after delay
            setTimeout(() => {
              btn.innerHTML = originalContent;
              btn.disabled = false;
            }, 2000);
          }
        });
      }

      // Update signature display function
      function updateSignatureDisplay(contract) {
        const signaturesSection = document.getElementById('signaturesSection');
        const vendorSignatureStatus = document.getElementById('vendorSignatureStatus');
        const vendorSignatureDate = document.getElementById('vendorSignatureDate');
        const procurementSignatureStatus = document.getElementById('procurementSignatureStatus');
        const procurementSignatureDate = document.getElementById('procurementSignatureDate');
        const contractStatusBadge = document.getElementById('contractStatusBadge');
        const signingProgress = document.getElementById('signingProgress');
        const progressText = document.getElementById('progressText');

        // Show signatures section
        if (signaturesSection) {
          signaturesSection.style.display = 'block';
        }

        // Update vendor signature status
        if (contract.vendor_signed) {
          vendorSignatureStatus.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Signed</span>';
          if (contract.vendor_signed_at) {
            vendorSignatureDate.textContent = `Signed on: ${formatDate(contract.vendor_signed_at)}`;
            vendorSignatureDate.style.display = 'block';
          }
        } else {
          vendorSignatureStatus.innerHTML = '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>Not Signed</span>';
          vendorSignatureDate.style.display = 'none';
        }

        // Update procurement signature status
        if (contract.procurement_signed) {
          procurementSignatureStatus.innerHTML = '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Signed</span>';
          if (contract.procurement_signed_at) {
            procurementSignatureDate.textContent = `Signed on: ${formatDate(contract.procurement_signed_at)}`;
            procurementSignatureDate.style.display = 'block';
          }
        } else {
          procurementSignatureStatus.innerHTML = '<span class="badge bg-secondary"><i class="bi bi-clock me-1"></i>Not Signed</span>';
          procurementSignatureDate.style.display = 'none';
        }

        // Update contract status and progress
        let progress = 0;
        let statusText = 'Pending Signatures';
        let statusClass = 'bg-warning';

        if (contract.vendor_signed && contract.procurement_signed) {
          progress = 100;
          statusText = 'Fully Signed';
          statusClass = 'bg-success';
        } else if (contract.vendor_signed || contract.procurement_signed) {
          progress = 50;
          statusText = 'Partially Signed';
          statusClass = 'bg-info';
        }

        // Update status badge
        contractStatusBadge.innerHTML = `<span class="badge ${statusClass}"><i class="bi bi-file-earmark-check me-1"></i>${statusText}</span>`;

        // Update progress bar
        const progressBar = signingProgress.querySelector('.progress-bar');
        if (progressBar) {
          progressBar.style.width = `${progress}%`;
          progressBar.setAttribute('aria-valuenow', progress);
        }

        // Update progress text
        progressText.textContent = `${progress}% Complete`;
      }

      // Helper functions
      function getStatusBadgeClass(status) {
        const classes = {
          'Active': 'success',
          'Pending': 'warning', 
          'Expired': 'danger'
        };
        return classes[status] || 'secondary';
      }

      function numberFormat(number) {
        return new Intl.NumberFormat('en-PH', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        }).format(number);
      }

      function formatDate(dateString) {
        if (!dateString) return 'N/A';
        return new Date(dateString).toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      }

      function calculateDuration(startDate, endDate) {
        if (!startDate || !endDate) return 'N/A';
        
        // Ensure we have valid dates
        const start = new Date(startDate);
        const end = new Date(endDate);
        
        // Check if dates are valid
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
          return 'Invalid dates';
        }
        
        // Calculate difference in milliseconds
        const diffTime = Math.abs(end - start);
        
        // Convert to days and ensure it's a whole number
        const diffDays = Math.round(diffTime / (1000 * 60 * 60 * 24));
        
        // Handle edge case where difference is less than 1 day
        if (diffDays === 0) {
          return 'Same day';
        }
        
        if (diffDays < 30) {
          return `${diffDays} calendar day${diffDays > 1 ? 's' : ''}`;
        } else if (diffDays < 365) {
          const months = Math.floor(diffDays / 30);
          const remainingDays = diffDays % 30;
          if (remainingDays === 0) {
            return `${months} month${months > 1 ? 's' : ''}`;
          } else {
            return `${months} month${months > 1 ? 's' : ''} ${remainingDays} day${remainingDays > 1 ? 's' : ''}`;
          }
        } else {
          const years = Math.floor(diffDays / 365);
          const remainingDays = diffDays % 365;
          const remainingMonths = Math.floor(remainingDays / 30);
          const finalDays = remainingDays % 30;
          
          let result = `${years} year${years > 1 ? 's' : ''}`;
          if (remainingMonths > 0) {
            result += ` ${remainingMonths} month${remainingMonths > 1 ? 's' : ''}`;
          }
          if (finalDays > 0) {
            result += ` ${finalDays} day${finalDays > 1 ? 's' : ''}`;
          }
          return result;
        }
      }

      // Add loading animation to other quick action buttons (excluding contract buttons)
      document.querySelectorAll('.btn:not(.view-contract-btn):not(.download-contract-btn)').forEach(btn => {
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

  // Contracts are now persisted in DB and rendered server-side.
  // Client-only mock insertion has been removed.
  </script>

  <style>
    #psmSignaturePad {
      border: 1px dashed #dee2e6;
      cursor: crosshair;
    }
    
    .signature-block {
      padding: 15px;
      border: 1px solid #e9ecef;
      border-radius: 8px;
      background-color: #f8f9fa;
      margin-bottom: 15px;
    }
    
    .signature-status .badge {
      font-size: 0.875rem;
      padding: 0.5rem 0.75rem;
    }
    
    #signaturesSection .progress {
      background-color: #e9ecef;
    }
    
    #signaturesSection .progress-bar {
      transition: width 0.6s ease;
    }
    
    .signature-block h6 {
      color: #495057;
      margin-bottom: 10px;
    }
  </style>
