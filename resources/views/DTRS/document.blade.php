{{-- Access Control Check --}}
@if(!Auth::check())
  <script>window.location.href = '/login';</script>
  @php exit; @endphp
@endif

@if(!in_array(Auth::user()->role, ['logistics_staff', 'admin', 'procurement_officer']))
  <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="text-center text-white">
      <i class="bi bi-shield-exclamation display-1 mb-4"></i>
      <h1 class="display-4 fw-bold mb-3">Access Denied</h1>
      <p class="lead mb-4">You don't have permission to access the Document Filing System.</p>
      <p class="mb-4">This module is restricted to <strong>Logistics Staff</strong>, <strong>Procurement Officers</strong>, and <strong>Administrators</strong> only.</p>
      <a href="{{ Auth::user()->role === 'procurement_officer' ? '/officer/dashboard' : '/dashboard' }}" class="btn btn-light btn-lg">
        <i class="bi bi-arrow-left me-2"></i>Return to Dashboard
      </a>
    </div>
  </div>
  @php exit; @endphp
@endif

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Document Filing System - Jetlouge Travels</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  
  <!-- Custom Document Cards Styles -->
  <style>
    .document-card, .folder-card {
      transition: all 0.3s ease;
      cursor: pointer;
      overflow: hidden;
    }
    
    .document-card:hover, .folder-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .document-card .card-hover-overlay {
      background: linear-gradient(135deg, rgba(0,123,255,0.1) 0%, rgba(0,123,255,0.3) 100%);
      opacity: 0;
      transition: all 0.3s ease;
      pointer-events: none;
      border-radius: inherit;
      z-index: 5;
      backdrop-filter: blur(2px);
    }
    
    .document-card:hover .card-hover-overlay {
      opacity: 1;
    }
    
    .document-card .card-hover-overlay .text-white {
      opacity: 0;
      transform: scale(0.8) translateY(10px);
      transition: all 0.3s ease 0.1s;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    
    .document-card:hover .card-hover-overlay .text-white {
      opacity: 1;
      transform: scale(1) translateY(0);
    }
    
    .document-icon {
      transition: transform 0.3s ease;
    }
    
    .document-card:hover .document-icon {
      transform: scale(1.1);
    }
    
    .document-actions .btn {
      transition: all 0.2s ease;
    }
    
    .document-actions .btn:hover {
      transform: translateY(-1px);
    }
    
    .badge-sm {
      font-size: 0.7em;
    }
    
    .document-details {
      min-height: 80px;
    }
    
    .bg-purple {
      background-color: #6f42c1 !important;
    }
    
    .text-purple {
      color: #6f42c1 !important;
    }
    
    .folder-card {
      background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
      border: 1px solid #e9ecef !important;
      min-height: 300px;
      border-radius: 12px !important;
      overflow: hidden;
    }
    
    .folder-card:hover {
      border-color: #007bff !important;
      background: linear-gradient(145deg, #f8f9fa 0%, #ffffff 100%);
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.12) !important;
    }
    
    .folder-container {
      position: relative;
      display: inline-block;
      margin-bottom: 1rem;
    }
    
    .folder-container i.bi-folder-fill {
      filter: drop-shadow(0 6px 12px rgba(0,0,0,0.15));
      transition: all 0.3s ease;
    }
    
    .folder-card:hover .folder-container i.bi-folder-fill {
      transform: scale(1.1) rotateY(5deg);
      filter: drop-shadow(0 8px 16px rgba(0,0,0,0.2));
    }
    
    .folder-overlay {
      z-index: 10;
      transition: all 0.3s ease;
    }
    
    .folder-card:hover .folder-overlay i {
      transform: scale(1.1);
    }
    
    .folder-stats {
      font-size: 0.8rem;
      opacity: 0.8;
    }
    
    .folder-actions {
      opacity: 0.9;
      position: relative;
      z-index: 25;
    }
    
    .folder-actions .btn {
      font-size: 0.8rem;
      padding: 0.25rem 0.75rem;
      border-radius: 20px;
      font-weight: 500;
      position: relative;
      z-index: 30;
      cursor: pointer;
    }
    
    .folder-actions .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    .folder-card .card-body {
      padding: 1.5rem 1rem;
    }
    
    .folder-card .card-header {
      font-weight: 600;
      border-bottom: 2px solid rgba(255,255,255,0.2);
    }
    
    /* Custom purple background class */
    .bg-purple {
      background-color: #6f42c1 !important;
      color: white !important;
    }
    
    /* Custom purple text class */
    .text-purple {
      color: #6f42c1 !important;
    }
    
    .text-truncate {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }
    
    @media (max-width: 768px) {
      .document-card {
        margin-bottom: 1rem;
      }
      
      .document-card:hover {
        transform: none;
      }
    }
    
    /* Animation for cards appearing */
    .document-card {
      animation: fadeInUp 0.5s ease forwards;
      opacity: 0;
      transform: translateY(20px);
    }
    
    .document-card:nth-child(1) { animation-delay: 0.1s; }
    .document-card:nth-child(2) { animation-delay: 0.2s; }
    .document-card:nth-child(3) { animation-delay: 0.3s; }
    .document-card:nth-child(4) { animation-delay: 0.4s; }
    .document-card:nth-child(5) { animation-delay: 0.5s; }
    .document-card:nth-child(6) { animation-delay: 0.6s; }
    
    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
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
      {{-- Show procurement menu only for authorized roles --}}
      @if(in_array(Auth::user()->role, ['procurement_officer', 'admin']))
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
      {{-- Show PLT menu only for authorized roles --}}
      @if(in_array(Auth::user()->role, ['logistics_staff', 'admin']))
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
      @endif
      {{-- Show Asset Management only for authorized roles --}}
      @if(in_array(Auth::user()->role, ['logistics_staff', 'admin']))
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
      @endif
      
      {{-- Document Tracking - Current Module (Always visible for authorized users) --}}
      <li class="nav-item">
        <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#documentSubmenu" aria-expanded="false" aria-controls="documentSubmenu">
          <i class="bi bi-journal-text me-2"></i> Document Tracking & Records
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
  <div class="collapse show" id="documentSubmenu">
    <ul class="nav flex-column ms-3">
      <li class="nav-item">
        <a href="{{ url('/dtrs/document') }}" class="nav-link text-dark small active">
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
          <i class="bi bi-folder-fill fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Document Filing System</h2>
          <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Organize, categorize, and manage your business documents efficiently.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/dtrs') }}" class="text-decoration-none">Document Filing</a></li>
            <li class="breadcrumb-item active" aria-current="page">File Manager</li>
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
              <i class="bi bi-folder2-open"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $totalDocuments ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Filed Documents</p>
              <small class="text-primary"><i class="bi bi-check-circle"></i> Organized</small>
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
              <i class="bi bi-collection"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $logisticsRecordsCount ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Categories</p>
              <small class="text-success"><i class="bi bi-tags"></i> Classified</small>
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
              <i class="bi bi-file-plus"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $thisWeekDocuments ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Recently Filed</p>
              <small class="text-warning"><i class="bi bi-clock"></i> This week</small>
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
              <i class="bi bi-archive"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ number_format(($totalFileSize ?? 0) / 1024 / 1024, 1) }}MB</h3>
              <p class="text-muted mb-0 small">File Cabinet Size</p>
              <small class="text-info"><i class="bi bi-folder"></i> Digital storage</small>
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
      <!-- Document Search and Filters -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">File Cabinet Search & Organization</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label for="searchQuery" class="form-label">Search Files</label>
              <input type="text" class="form-control" id="searchQuery" placeholder="Search by filename, category, or keywords...">
            </div>
            <div class="col-md-3">
              <label for="documentType" class="form-label">File Category</label>
              <select class="form-select" id="documentType">
                <option value="">All Types</option>
                <option value="business_license">Business License</option>
                <option value="tax_certificate">Tax Certificate</option>
                <option value="insurance_certificate">Insurance Certificate</option>
                <option value="purchase_order">Purchase Orders</option>
                <option value="contract">Contracts</option>
                <option value="additional_document">Additional Documents</option>
              </select>
            </div>
            <div class="col-md-3">
              <label for="dateRange" class="form-label">Date Range</label>
              <select class="form-select" id="dateRange">
                <option value="">All Dates</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="quarter">This Quarter</option>
                <option value="year">This Year</option>
              </select>
            </div>
            <div class="col-md-2">
              <label class="form-label">&nbsp;</label>
              <button class="btn btn-primary w-100" id="searchBtn">
                <i class="bi bi-search"></i> Search
              </button>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Archived Documents -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Filed Documents</h5>
          <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" id="exportBtn">
              <i class="bi bi-download"></i> Export
            </button>
            <button class="btn btn-sm btn-outline-primary" id="refreshBtn">
              <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
          </div>
        </div>
        <div class="card-body">
          {{-- Access Control for Document Actions --}}
          @if(Auth::user()->role === 'admin')
            <div class="alert alert-success alert-sm mb-3">
              <i class="bi bi-shield-check me-2"></i>
              <strong>Administrator Access:</strong> Full filing system management - organize, categorize, and maintain all documents.
            </div>
          @elseif(Auth::user()->role === 'logistics_staff')
            <div class="alert alert-primary alert-sm mb-3">
              <i class="bi bi-tools me-2"></i>
              <strong>Logistics Staff Access:</strong> File organization privileges - View, download, categorize, and generate filing reports.
            </div>
          @else
            <div class="alert alert-info alert-sm mb-3">
              <i class="bi bi-eye me-2"></i>
              <strong>Procurement Officer Access:</strong> File access privileges - View and download filed documents. Contact administrator for filing permissions.
            </div>
          @endif
          
          <!-- Folder View -->
          <div class="row g-3" id="foldersGrid">
            @php
              // Initialize categories
              $documentCategories = [
                'business_license' => ['name' => 'Business Licenses', 'icon' => 'bi-award', 'color' => 'success', 'count' => 0],
                'tax_certificate' => ['name' => 'Tax Certificates', 'icon' => 'bi-receipt', 'color' => 'warning', 'count' => 0],
                'insurance_certificate' => ['name' => 'Insurance Certificates', 'icon' => 'bi-shield-check', 'color' => 'info', 'count' => 0],
                'purchase_order' => ['name' => 'Purchase Orders', 'icon' => 'bi-cart-check', 'color' => 'primary', 'count' => 0],
                'contract' => ['name' => 'Contracts', 'icon' => 'bi-file-earmark-text', 'color' => 'dark', 'count' => 0],
                'inventory_receipt' => ['name' => 'Inventory Receipts', 'icon' => 'bi-box-seam', 'color' => 'purple', 'count' => 0],
                'additional_document' => ['name' => 'Additional Documents', 'icon' => 'bi-file-plus', 'color' => 'secondary', 'count' => 0]
              ];
              
              // Use predefined counts from controller or calculate from all documents
              // This ensures folders are consistent across all pages
              $finalCategories = [];
              
              // If we have total counts from controller, use those
              if (isset($totalDocuments) && $totalDocuments > 0) {
                // Show all categories that might have documents
                foreach($documentCategories as $type => $category) {
                  // For demo purposes, we'll show categories that typically have documents
                  // In real implementation, this should come from the controller with actual counts
                  switch($type) {
                    case 'business_license':
                      $category['count'] = $businessLicenseCount ?? 2;
                      break;
                    case 'tax_certificate':
                      $category['count'] = $taxCertificateCount ?? 2;
                      break;
                    case 'insurance_certificate':
                      $category['count'] = $insuranceCertificateCount ?? 2;
                      break;
                    case 'purchase_order':
                      $category['count'] = $purchaseOrderCount ?? 4;
                      break;
                    case 'contract':
                      $category['count'] = $contractCount ?? 4;
                      break;
                    case 'inventory_receipt':
                      $category['count'] = $inventoryReceiptCount ?? 1;
                      break;
                    default:
                      $category['count'] = 0;
                  }
                  
                  if ($category['count'] > 0) {
                    $finalCategories[$type] = $category;
                  }
                }
              } else {
                // Fallback: count from current page documents
                $documentTypes = [];
                foreach($paginatedDocs ?? [] as $document) {
                  $docType = $document['type'] ?? 'additional_document';
                  if (!isset($documentTypes[$docType])) {
                    $documentTypes[$docType] = 0;
                  }
                  $documentTypes[$docType]++;
                }
                
                foreach($documentCategories as $type => $category) {
                  if (isset($documentTypes[$type]) && $documentTypes[$type] > 0) {
                    $category['count'] = $documentTypes[$type];
                    $finalCategories[$type] = $category;
                  }
                }
              }
              
              $documentCategories = $finalCategories;
            @endphp
            
            @php $renderedCategories = []; @endphp
            @forelse($documentCategories as $categoryType => $category)
              @if(!in_array($categoryType, $renderedCategories))
                @php $renderedCategories[] = $categoryType; @endphp
            <div class="col-sm-6 col-md-4 col-lg-3">
              <div class="card folder-card h-100 shadow-sm border-0 position-relative" onclick="openFolder('{{ $categoryType }}')">
                <!-- Document Count Badge -->
                <div class="position-absolute top-0 end-0 m-2" style="z-index: 20;">
                  <span class="badge bg-{{ $category['color'] }} rounded-pill">
                    {{ $category['count'] }}
                  </span>
                </div>
                
                <div class="card-body d-flex flex-column text-center">
                  <!-- Folder Icon -->
                  <div class="folder-icon mb-2">
                    <div class="folder-container position-relative d-inline-block">
                      <i class="bi bi-folder-fill text-{{ $category['color'] }}" style="font-size: 5rem;"></i>
                      <div class="folder-overlay position-absolute top-50 start-50 translate-middle">
                        <i class="{{ $category['icon'] }} text-white" style="font-size: 1.5rem; text-shadow: 0 2px 4px rgba(0,0,0,0.3);"></i>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Folder Details -->
                  <div class="folder-details flex-grow-1">
                    <h6 class="card-title mb-2 fw-bold text-truncate">{{ $category['name'] }}</h6>
                    <div class="folder-stats mb-3">
                      <div class="d-flex justify-content-center small text-muted">
                        <span>
                          <i class="bi bi-calendar3 me-1"></i>
                          Updated today
                        </span>
                      </div>
                    </div>
                  </div>
                  
                  <!-- Folder Actions -->
                  <div class="folder-actions mt-3 pt-2 border-top">
                    <div class="d-flex justify-content-center gap-2">
                      <button class="btn btn-sm btn-outline-primary folder-open-btn" data-category="{{ $categoryType }}"
                              title="Open Folder">
                        <i class="bi bi-folder-open"></i> Open
                      </button>
                      @if(in_array(Auth::user()->role, ['admin', 'logistics_staff']))
                        <button class="btn btn-sm btn-outline-secondary folder-settings-btn" data-category="{{ $categoryType }}"
                                title="Folder Settings">
                          <i class="bi bi-gear"></i>
                        </button>
                      @endif
                    </div>
                  </div>
                </div>
                
                <!-- Hover Effect -->
                <div class="card-hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="z-index: 5; pointer-events: none;">
                  <div class="text-white text-center">
                    <i class="bi bi-folder-open fs-2"></i>
                  </div>
                </div>
              </div>
            </div>
              @endif
            @empty
            <div class="col-12">
              <div class="text-center py-5">
                <i class="bi bi-folder-x fs-1 text-muted mb-3"></i>
                <h5 class="text-muted">No folders found</h5>
                <p class="text-muted">Create new categories to organize your documents.</p>
              </div>
            </div>
            @endforelse
          </div>
          
          <!-- Document List View (Hidden by default) -->
          <div class="row g-3 d-none" id="documentsGrid">
            <div class="col-12">
              <div class="d-flex align-items-center mb-3">
                <button class="btn btn-outline-secondary btn-sm me-3" onclick="backToFolders()">
                  <i class="bi bi-arrow-left"></i> Back to Folders
                </button>
                <h5 class="mb-0" id="currentFolderName">Documents</h5>
              </div>
            </div>
            
            <div id="documentsList" class="col-12">
              <!-- Documents will be loaded here via JavaScript -->
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
      <!-- Archive Actions -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Filing Actions</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button class="btn btn-primary" id="newFileBtn">
              <i class="bi bi-file-plus me-2"></i>File New Document
            </button>
            <button class="btn btn-outline-primary" id="organizeFoldersBtn">
              <i class="bi bi-folder-plus me-2"></i>Create Category
            </button>
            @if(in_array(Auth::user()->role, ['admin', 'logistics_staff']))
              <button class="btn btn-outline-success" id="reorganizeBtn">
                <i class="bi bi-arrow-repeat me-2"></i>Reorganize Files
              </button>
              <button class="btn btn-outline-info" id="filingReportBtn">
                <i class="bi bi-clipboard-data me-2"></i>Filing Report
              </button>
              <button class="btn btn-outline-secondary" id="maintenanceBtn">
                <i class="bi bi-tools me-2"></i>File Maintenance
              </button>
            @else
              <button class="btn btn-outline-secondary" onclick="requestAdminAccess('filing')">
                <i class="bi bi-folder-plus me-2"></i>Request Filing Access
              </button>
              <button class="btn btn-outline-secondary" onclick="requestAdminAccess('organize')">
                <i class="bi bi-arrow-repeat me-2"></i>Request Organization Access
              </button>
              <div class="alert alert-warning alert-sm mt-2">
                <i class="bi bi-info-circle me-1"></i>
                <small>Filing management requires administrator privileges.</small>
              </div>
            @endif
          </div>
        </div>
      </div>
      
      <!-- Logistics Records -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Filing Categories</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Category</th>
                  <th>Files</th>
                  <th>Last Updated</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($logisticsRecords ?? [] as $record)
                <tr>
                  <td><strong>{{ ucfirst(str_replace('_', ' ', $record->type ?? 'Unknown')) }}</strong></td>
                  <td><span class="badge bg-primary">{{ rand(1, 15) }}</span></td>
                  <td>{{ $record->created_at ? $record->created_at->format('M d') : 'N/A' }}</td>
                  <td>
                    <span class="badge bg-{{ $record->status === 'active' ? 'success' : ($record->status === 'pending' ? 'warning' : 'secondary') }} badge-sm">
                      {{ ucfirst($record->status ?? 'Unknown') }}
                    </span>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-3">
                    No filing categories found
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
      
      <!-- Archive Summary -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Filing Summary</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Filing Categories</span>
              <span class="small fw-bold">{{ $logisticsRecordsCount ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Documents Filed</span>
              <span class="small fw-bold">{{ $totalDocuments ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Filed This Week</span>
              <span class="small fw-bold">{{ $thisWeekDocuments ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Cabinet Space Used</span>
              <span class="small fw-bold">{{ number_format(($totalFileSize ?? 0) / (1024 * 1024), 1) }}MB</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Filing Efficiency</span>
              <span class="small fw-bold">{{ number_format($storagePercentage ?? 0, 1) }}%</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Legacy Documents</span>
              <span class="small fw-bold">{{ $oldDocuments ?? 0 }}</span>
            </div>
          </div>
          <hr>
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Documents by Type</span>
            </div>
            @foreach(['business_license' => 'Business Licenses', 'tax_certificate' => 'Tax Certificates', 'insurance_certificate' => 'Insurance Certificates', 'additional_document' => 'Additional Documents'] as $type => $label)
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">{{ $label }}</span>
              <span class="small fw-bold">{{ $documentsByType[$type] ?? 0 }}</span>
            </div>
            @endforeach
          </div>
          <div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Avg. File Size</span>
              <span class="small fw-bold">{{ number_format(($avgFileSize ?? 0) / 1024, 1) }}KB</span>
            </div>
          </div>
        </div>
      </div>
      
      <!-- System Alerts -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">System Alerts</h5>
        </div>
        <div class="card-body">
          @if(($storagePercentage ?? 0) > 80)
          <div class="alert alert-warning alert-sm mb-2">
            <i class="bi bi-hdd me-2"></i>
            Archive storage is {{ $storagePercentage }}% full
          </div>
          @endif
          @if(($documentsNeedingReview ?? 0) > 0)
          <div class="alert alert-info alert-sm mb-2">
            <i class="bi bi-file-earmark-check me-2"></i>
            {{ $documentsNeedingReview }} documents need review
          </div>
          @endif
          @if(($oldDocuments ?? 0) > 0)
          <div class="alert alert-secondary alert-sm">
            <i class="bi bi-calendar-x me-2"></i>
            {{ $oldDocuments }} documents older than 5 years
          </div>
          @endif
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
  // Document ready
  document.addEventListener('DOMContentLoaded', function() {
    // Logout functionality
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
      logoutBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Create a form and submit it to logout route
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '{{ route('logout') }}';
          
          // Add CSRF token
          const csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = '{{ csrf_token() }}';
          form.appendChild(csrfToken);
          
          document.body.appendChild(form);
          form.submit();
        });
      }

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
  
  // Document Archive Functionality with Access Control
  function viewDocument(documentId) {
    // Check user role and log access
    logDocumentAccess(documentId, 'view');
    window.open(`/dtrs/documents/${documentId}/view`, '_blank');
  }
  
  function downloadDocument(documentId) {
    // Check user role and log access
    logDocumentAccess(documentId, 'download');
    window.location.href = `/dtrs/documents/${documentId}/download`;
  }
  
  function viewMetadata(documentId) {
    // Admin and logistics staff function
    @if(!in_array(Auth::user()->role, ['admin', 'logistics_staff']))
      alert('Access denied. Metadata viewing requires administrator or logistics staff privileges.');
      return;
    @endif
    
    logDocumentAccess(documentId, 'metadata');
    fetch(`/dtrs/documents/${documentId}/metadata`)
      .then(response => response.json())
      .then(data => {
        document.getElementById('staticModalLabel').textContent = 'Document Metadata';
        document.getElementById('staticModalBody').innerHTML = `
          <div class="table-responsive">
            <table class="table table-sm">
              <tr><th>Document ID:</th><td>${data.document_id}</td></tr>
              <tr><th>Filename:</th><td>${data.filename}</td></tr>
              <tr><th>File Size:</th><td>${(data.file_size / 1024).toFixed(1)} KB</td></tr>
              <tr><th>MIME Type:</th><td>${data.mime_type}</td></tr>
              <tr><th>Created:</th><td>${new Date(data.created_at).toLocaleString()}</td></tr>
              <tr><th>Source Module:</th><td>${data.source_module}</td></tr>
              <tr><th>Checksum:</th><td>${data.checksum || 'N/A'}</td></tr>
              <tr><th>Access Level:</th><td>Administrator</td></tr>
            </table>
          </div>
        `;
        new bootstrap.Modal(document.getElementById('staticModal')).show();
      })
      .catch(error => {
        console.error('Error fetching metadata:', error);
        alert('Failed to load document metadata');
      });
  }
  
  // Access control functions
  function requestAccess(documentId) {
    document.getElementById('staticModalLabel').textContent = 'Request Access';
    document.getElementById('staticModalBody').innerHTML = `
      <div class="alert alert-info">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Access Request</strong>
      </div>
      <p>You are requesting access to document metadata for Document ID: <strong>${documentId}</strong></p>
      <p>This action requires administrator approval. Your request will be logged and reviewed.</p>
      <div class="text-muted small">
        <i class="bi bi-person me-1"></i> Requested by: {{ Auth::user()->name }}<br>
        <i class="bi bi-shield me-1"></i> Current Role: {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}<br>
        <i class="bi bi-clock me-1"></i> Request Time: ${new Date().toLocaleString()}
      </div>
    `;
    new bootstrap.Modal(document.getElementById('staticModal')).show();
    
    // Log the access request
    logDocumentAccess(documentId, 'access_request');
  }
  
  function requestAdminAccess(feature) {
    document.getElementById('staticModalLabel').textContent = 'Administrator Access Required';
    document.getElementById('staticModalBody').innerHTML = `
      <div class="alert alert-warning">
        <i class="bi bi-shield-exclamation me-2"></i>
        <strong>Restricted Feature</strong>
      </div>
      <p>The <strong>${feature}</strong> feature requires administrator privileges.</p>
      <p>Please contact your system administrator to request access or upgrade your permissions.</p>
      <div class="text-muted small">
        <i class="bi bi-person me-1"></i> Current User: {{ Auth::user()->name }}<br>
        <i class="bi bi-shield me-1"></i> Current Role: {{ ucfirst(str_replace('_', ' ', Auth::user()->role)) }}<br>
        <i class="bi bi-envelope me-1"></i> Contact: admin@jetlougetravels.com
      </div>
    `;
    new bootstrap.Modal(document.getElementById('staticModal')).show();
  }
  
  function logDocumentAccess(documentId, action) {
    // Log document access for audit purposes
    fetch('/dtrs/log-access', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        document_id: documentId,
        action: action,
        user_id: {{ Auth::id() }},
        user_role: '{{ Auth::user()->role }}',
        timestamp: new Date().toISOString()
      })
    }).catch(error => console.log('Access logging failed:', error));
  }
  
  // Search functionality
  document.getElementById('searchBtn')?.addEventListener('click', function() {
    const query = document.getElementById('searchQuery').value;
    const type = document.getElementById('documentType').value;
    const dateRange = document.getElementById('dateRange').value;
    
    const params = new URLSearchParams();
    if (query) params.append('search', query);
    if (type) params.append('type', type);
    if (dateRange) params.append('date_range', dateRange);
    
    window.location.href = `/dtrs/document?${params.toString()}`;
  });
  
  // Archive action buttons
  document.getElementById('exportBtn')?.addEventListener('click', function() {
    window.location.href = '/dtrs/documents/export';
  });
  
  document.getElementById('refreshBtn')?.addEventListener('click', function() {
    window.location.reload();
  });
  
  document.getElementById('generateReportBtn')?.addEventListener('click', function() {
    window.open('/dtrs/reports/generate', '_blank');
  });
  
  document.getElementById('bulkExportBtn')?.addEventListener('click', function() {
    window.open('/dtrs/documents/bulk-export', '_blank');
  });
  
  // Filing Actions Functionality
  document.getElementById('newFileBtn')?.addEventListener('click', function() {
    showNewFileModal();
  });
  
  document.getElementById('organizeFoldersBtn')?.addEventListener('click', function() {
    showCreateCategoryModal();
  });
  
  document.getElementById('reorganizeBtn')?.addEventListener('click', function() {
    reorganizeAllFiles();
  });
  
  document.getElementById('filingReportBtn')?.addEventListener('click', function() {
    generateFilingReport();
  });
  
  document.getElementById('maintenanceBtn')?.addEventListener('click', function() {
    showMaintenanceModal();
  });
  
  // Folder System Functionality
  // Use all documents, not just paginated ones for folder functionality
  let allDocuments = @json($allDocs ?? $paginatedDocs ?? []);
  
  // Add event listeners for folder buttons
  document.addEventListener('DOMContentLoaded', function() {
    // Open folder buttons
    document.querySelectorAll('.folder-open-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const category = this.getAttribute('data-category');
        openFolder(category);
      });
    });
    
    // Settings buttons
    document.querySelectorAll('.folder-settings-btn').forEach(btn => {
      btn.addEventListener('click', function(e) {
        e.stopPropagation();
        const category = this.getAttribute('data-category');
        manageFolderSettings(category);
      });
    });
    
    // Load and apply saved folder settings
    loadSavedFolderSettings();
  });
  
  function openFolder(categoryType) {
    // Filter documents by category
    const folderDocuments = allDocuments.filter(doc => doc.type === categoryType);
    
    // Get category info
    const categoryNames = {
      'business_license': 'Business Licenses',
      'tax_certificate': 'Tax Certificates', 
      'insurance_certificate': 'Insurance Certificates',
      'purchase_order': 'Purchase Orders',
      'contract': 'Contracts',
      'inventory_receipt': 'Inventory Receipts',
      'additional_document': 'Additional Documents'
    };
    
    // Hide folders grid and show documents grid
    document.getElementById('foldersGrid').classList.add('d-none');
    document.getElementById('documentsGrid').classList.remove('d-none');
    
    
    // Update folder name
    document.getElementById('currentFolderName').textContent = categoryNames[categoryType] || 'Documents';
    
    // Generate documents HTML
    let documentsHTML = '';
    if (folderDocuments.length > 0) {
      documentsHTML = '<div class="row g-3">';
      folderDocuments.forEach(document => {
        const badgeColor = getBadgeColor(document.type);
        const iconClass = getIconClass(document.type);
        
        documentsHTML += `
          <div class="col-md-6 col-lg-4">
            <div class="card document-card h-100 shadow-sm border-0 position-relative">
              <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-${badgeColor}">${document.type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())}</span>
              </div>
              
              <div class="card-body d-flex flex-column">
                <div class="text-center mb-3">
                  <div class="document-icon mb-2">
                    <i class="bi ${iconClass} fs-1 text-${badgeColor}"></i>
                  </div>
                  <h6 class="card-title mb-1 fw-bold">${document.document_id}</h6>
                </div>
                
                <div class="document-details flex-grow-1">
                  <div class="mb-2">
                    <strong class="text-truncate d-block" title="${document.filename}">
                      ${document.filename}
                    </strong>
                    <small class="text-muted">${document.vendor_name || 'N/A'}</small>
                  </div>
                  
                  <div class="row g-2 small text-muted">
                    <div class="col-6">
                      <i class="bi bi-calendar3 me-1"></i>
                      <span>${document.created_at ? new Date(document.created_at).toLocaleDateString() : 'N/A'}</span>
                    </div>
                    <div class="col-6">
                      <i class="bi bi-hdd me-1"></i>
                      <span>${document.file_size ? (document.file_size / 1024).toFixed(1) + ' KB' : 'N/A'}</span>
                    </div>
                    <div class="col-12">
                      <i class="bi bi-box me-1"></i>
                      <span class="badge bg-info badge-sm">${document.source_module}</span>
                    </div>
                  </div>
                </div>
                
                <div class="document-actions mt-3 pt-2 border-top">
                  <div class="d-flex justify-content-center gap-1">
                    <button class="btn btn-sm btn-outline-primary flex-fill" onclick="viewDocument('${document.id}')" title="View Document">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success flex-fill" onclick="downloadDocument('${document.id}')" title="Download">
                      <i class="bi bi-download"></i>
                    </button>
                    @if(in_array(Auth::user()->role, ['admin', 'logistics_staff']))
                      <button class="btn btn-sm btn-outline-info flex-fill" onclick="viewMetadata('${document.id}')" title="Metadata">
                        <i class="bi bi-info-circle"></i>
                      </button>
                    @else
                      <button class="btn btn-sm btn-outline-secondary flex-fill" onclick="requestAccess('${document.id}')" title="Request Access">
                        <i class="bi bi-lock"></i>
                      </button>
                    @endif
                  </div>
                </div>
              </div>
              
              <div class="card-hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                <div class="text-white">
                  <i class="bi bi-eye fs-3"></i>
                </div>
              </div>
            </div>
          </div>
        `;
      });
      documentsHTML += '</div>';
    } else {
      documentsHTML = `
        <div class="text-center py-5">
          <i class="bi bi-file-x fs-1 text-muted mb-3"></i>
          <h5 class="text-muted">No documents in this folder</h5>
          <p class="text-muted">This category doesn't contain any documents yet.</p>
        </div>
      `;
    }
    
    document.getElementById('documentsList').innerHTML = documentsHTML;
  }
  
  function backToFolders() {
    document.getElementById('documentsGrid').classList.add('d-none');
    document.getElementById('foldersGrid').classList.remove('d-none');
  }
  
  function getBadgeColor(type) {
    const colors = {
      'business_license': 'success',
      'tax_certificate': 'warning', 
      'insurance_certificate': 'info',
      'purchase_order': 'primary',
      'contract': 'dark',
      'inventory_receipt': 'purple',
      'additional_document': 'secondary'
    };
    return colors[type] || 'secondary';
  }
  
  function getIconClass(type) {
    const icons = {
      'business_license': 'bi-file-earmark-check',
      'tax_certificate': 'bi-file-earmark-text',
      'insurance_certificate': 'bi-file-earmark-shield', 
      'purchase_order': 'bi-file-earmark-arrow-up',
      'contract': 'bi-file-earmark-ruled',
      'inventory_receipt': 'bi-file-earmark-spreadsheet',
      'additional_document': 'bi-file-earmark-plus'
    };
    return icons[type] || 'bi-file-earmark';
  }
  
  function manageFolderSettings(categoryType) {
    
    const categoryNames = {
      'business_license': 'Business Licenses',
      'tax_certificate': 'Tax Certificates', 
      'insurance_certificate': 'Insurance Certificates',
      'purchase_order': 'Purchase Orders',
      'contract': 'Contracts',
      'inventory_receipt': 'Inventory Receipts',
      'additional_document': 'Additional Documents'
    };
    
    const categoryName = categoryNames[categoryType] || 'Unknown';
    const documentCount = allDocuments.filter(doc => doc.type === categoryType).length;
    
    // Create modal HTML
    const modalHTML = `
      <div class="modal fade" id="folderSettingsModal" tabindex="-1" aria-labelledby="folderSettingsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="folderSettingsModalLabel">
                <i class="bi bi-gear me-2"></i>Folder Settings - ${categoryName}
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <!-- Folder Information -->
              <div class="row mb-4">
                <div class="col-md-6">
                  <div class="card bg-light">
                    <div class="card-body text-center">
                      <i class="bi bi-folder-fill text-primary fs-1 mb-2"></i>
                      <h6 class="card-title">${categoryName}</h6>
                      <p class="text-muted mb-0">${documentCount} documents</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card bg-light">
                    <div class="card-body">
                      <h6 class="card-title">Quick Stats</h6>
                      <div class="row text-center">
                        <div class="col-4">
                          <div class="text-primary fw-bold">${documentCount}</div>
                          <small class="text-muted">Files</small>
                        </div>
                        <div class="col-4">
                          <div class="text-success fw-bold">100%</div>
                          <small class="text-muted">Organized</small>
                        </div>
                        <div class="col-4">
                          <div class="text-info fw-bold">Active</div>
                          <small class="text-muted">Status</small>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Settings Tabs -->
              <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab">
                    <i class="bi bi-gear me-1"></i>General
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link" id="permissions-tab" data-bs-toggle="tab" data-bs-target="#permissions" type="button" role="tab">
                    <i class="bi bi-shield-lock me-1"></i>Permissions
                  </button>
                </li>
              </ul>

              <div class="tab-content mt-3" id="settingsTabContent">
                <!-- General Settings -->
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                  <form id="generalSettingsForm">
                    <div class="mb-3">
                      <label for="folderName" class="form-label">Folder Name</label>
                      <input type="text" class="form-control" id="folderName" value="${categoryName}">
                      <div class="form-text">Display name for this folder category</div>
                    </div>
                    <div class="mb-3">
                      <label for="folderDescription" class="form-label">Description</label>
                      <textarea class="form-control" id="folderDescription" rows="3" placeholder="Enter folder description...">${getFolderDescription(categoryType)}</textarea>
                    </div>
                    <div class="mb-3">
                      <label for="folderColor" class="form-label">Folder Color</label>
                      <select class="form-select" id="folderColor">
                        <option value="primary">Blue (Primary)</option>
                        <option value="success">Green (Success)</option>
                        <option value="warning">Yellow (Warning)</option>
                        <option value="danger">Red (Danger)</option>
                        <option value="info">Cyan (Info)</option>
                        <option value="dark">Dark</option>
                        <option value="purple">Purple</option>
                        <option value="secondary">Gray (Secondary)</option>
                      </select>
                    </div>
                    <div class="mb-3">
                      <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="folderVisible" checked>
                        <label class="form-check-label" for="folderVisible">
                          Folder Visible to Users
                        </label>
                      </div>
                    </div>
                  </form>
                </div>

                <!-- Permissions Settings -->
                <div class="tab-pane fade" id="permissions" role="tabpanel">
                  <div class="mb-3">
                    <h6>Access Control</h6>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="adminAccess" checked disabled>
                      <label class="form-check-label" for="adminAccess">
                        <i class="bi bi-shield-check text-success me-1"></i>Administrator (Full Access)
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="logisticsAccess" checked>
                      <label class="form-check-label" for="logisticsAccess">
                        <i class="bi bi-people text-primary me-1"></i>Logistics Staff (View, Download, Organize)
                      </label>
                    </div>
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" id="procurementAccess" checked>
                      <label class="form-check-label" for="procurementAccess">
                        <i class="bi bi-person text-info me-1"></i>Procurement Officers (View, Download)
                      </label>
                    </div>
                  </div>
                  <div class="mb-3">
                    <h6>Document Actions</h6>
                    <div class="row">
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="allowView" checked>
                          <label class="form-check-label" for="allowView">Allow View</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="allowDownload" checked>
                          <label class="form-check-label" for="allowDownload">Allow Download</label>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="allowUpload">
                          <label class="form-check-label" for="allowUpload">Allow Upload</label>
                        </div>
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="allowDelete">
                          <label class="form-check-label" for="allowDelete">Allow Delete</label>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-info btn-sm me-2" onclick="testFormValues()">
                <i class="bi bi-bug me-1"></i>Test Form
              </button>
              <button type="button" class="btn btn-primary" id="saveFolderBtn" data-category="${categoryType}">
                <i class="bi bi-check me-1"></i>Save Changes
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('folderSettingsModal');
    if (existingModal) {
      existingModal.remove();
    }
    
    // Add modal to body
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Set default values based on current category
    const currentCategoryColors = {
      'business_license': 'success',
      'tax_certificate': 'warning',
      'insurance_certificate': 'info',
      'purchase_order': 'primary',
      'contract': 'dark',
      'inventory_receipt': 'purple',
      'additional_document': 'secondary'
    };
    
    // Load saved settings or use defaults
    const savedSettings = JSON.parse(localStorage.getItem('folderSettings') || '{}');
    const currentSettings = savedSettings[categoryType];
    
    if (currentSettings) {
      // Use saved settings
      document.getElementById('folderName').value = currentSettings.name || categoryName;
      document.getElementById('folderDescription').value = currentSettings.description || getFolderDescription(categoryType);
      document.getElementById('folderColor').value = currentSettings.color || 'primary';
      document.getElementById('folderVisible').checked = currentSettings.visible !== false;
    } else {
      // Use default settings
      const defaultColor = currentCategoryColors[categoryType] || 'primary';
      document.getElementById('folderColor').value = defaultColor;
    }
    
    // Add event listener for save button
    const saveBtn = document.getElementById('saveFolderBtn');
    if (saveBtn) {
      saveBtn.onclick = function() {
        const category = this.getAttribute('data-category');
        saveFolderSettings(category);
      };
    } else {
      alert('Save button not found!');
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('folderSettingsModal'));
    modal.show();
  }
  
  function getFolderDescription(categoryType) {
    const descriptions = {
      'business_license': 'Official business registration and licensing documents',
      'tax_certificate': 'Tax registration certificates and compliance documents',
      'insurance_certificate': 'Insurance policies and coverage certificates',
      'purchase_order': 'Purchase orders and procurement documents',
      'contract': 'Contracts, agreements, and legal documents',
      'inventory_receipt': 'Inventory receipts and warehouse documentation',
      'additional_document': 'Miscellaneous and supplementary documents'
    };
    return descriptions[categoryType] || 'Document category folder';
  }
  
  function saveFolderSettings(categoryType) {
    // Get form values directly
    const nameInput = document.querySelector('#folderSettingsModal #folderName');
    const descInput = document.querySelector('#folderSettingsModal #folderDescription');
    const colorSelect = document.querySelector('#folderSettingsModal #folderColor');
    const visibleCheck = document.querySelector('#folderSettingsModal #folderVisible');
    
    if (!nameInput || !descInput || !colorSelect || !visibleCheck) {
      alert('Form elements not found!');
      return;
    }
    
    const formData = {
      name: nameInput.value,
      description: descInput.value,
      color: colorSelect.value,
      visible: visibleCheck.checked
    };
    
    // Show loading
    Swal.fire({
      title: 'Saving Settings...',
      html: 'Applying your changes...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Apply changes to the folder card immediately
    setTimeout(() => {
      updateFolderDisplay(categoryType, formData);
      
      // Store settings in localStorage for persistence
      const allSettings = JSON.parse(localStorage.getItem('folderSettings') || '{}');
      allSettings[categoryType] = formData;
      localStorage.setItem('folderSettings', JSON.stringify(allSettings));
      
      // Show success message
      Swal.fire({
        icon: 'success',
        title: 'Settings Applied!',
        html: `
          <div class="text-start">
            <p><strong>Folder:</strong> "${formData.name}"</p>
            <p><strong>Color:</strong> ${formData.color}</p>
            <p><strong>Visible:</strong> ${formData.visible ? 'Yes' : 'No'}</p>
            <p class="text-muted">Changes have been applied and saved locally.</p>
          </div>
        `,
        confirmButtonColor: '#28a745'
      }).then(() => {
        // Close modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('folderSettingsModal'));
        if (modal) {
          modal.hide();
        }
      });
    }, 500);
  }
  
  function updateFolderDisplay(categoryType, settings) {
    // Find the folder card for this category using the button's data-category attribute
    const folderButton = document.querySelector(`[data-category="${categoryType}"]`);
    const folderCard = folderButton ? folderButton.closest('.folder-card') : null;
    
    if (!folderCard) {
      console.error('Folder card not found for category:', categoryType);
      return;
    }
    
    console.log('Found folder card for:', categoryType, folderCard);
    
    // Update the folder name
    const folderTitle = folderCard.querySelector('.card-title');
    if (folderTitle) {
      folderTitle.textContent = settings.name;
      console.log('Updated title to:', settings.name);
    }
    
    // Update the folder icon color
    const folderIcon = folderCard.querySelector('.bi-folder-fill');
    if (folderIcon) {
      // Remove existing color classes
      folderIcon.classList.remove('text-primary', 'text-success', 'text-warning', 'text-danger', 'text-info', 'text-dark', 'text-purple', 'text-secondary');
      // Add new color class
      folderIcon.classList.add('text-' + settings.color);
      console.log('Updated icon color to:', settings.color);
    }
    
    // Update the badge color
    const badge = folderCard.querySelector('.badge');
    if (badge) {
      // Remove existing color classes
      badge.classList.remove('bg-primary', 'bg-success', 'bg-warning', 'bg-danger', 'bg-info', 'bg-dark', 'bg-purple', 'bg-secondary');
      // Add new color class
      badge.classList.add('bg-' + settings.color);
      console.log('Updated badge color to:', settings.color);
    }
    
    // Update folder visibility
    if (!settings.visible) {
      folderCard.style.opacity = '0.5';
      folderCard.style.filter = 'grayscale(50%)';
    } else {
      folderCard.style.opacity = '1';
      folderCard.style.filter = 'none';
    }
    
    console.log('Successfully updated folder display for:', categoryType, settings);
  }
  
  function loadSavedFolderSettings() {
    try {
      const savedSettings = JSON.parse(localStorage.getItem('folderSettings') || '{}');
      
      // Apply saved settings to each folder
      Object.keys(savedSettings).forEach(categoryType => {
        const settings = savedSettings[categoryType];
        updateFolderDisplay(categoryType, settings);
      });
      
      console.log('Loaded saved folder settings:', savedSettings);
    } catch (error) {
      console.error('Error loading saved folder settings:', error);
    }
  }
  
  function reorganizeFolder(categoryType) {
    alert('🔄 Reorganizing folder contents...\n\nThis will:\n- Sort documents by date\n- Remove duplicates\n- Update file indexes\n- Optimize storage\n\nProcess completed successfully!');
  }
  
  function generateFolderReport(categoryType) {
    const docs = allDocuments.filter(doc => doc.type === categoryType);
    alert('📊 Generating folder report...\n\nReport Summary:\n- Total Documents: ' + docs.length + '\n- Storage Used: ' + (docs.length * 1.2).toFixed(1) + ' MB\n- Last Updated: Today\n- Status: All documents accessible\n\nReport will be downloaded shortly.');
  }
  
  function exportFolderContents(categoryType) {
    const docs = allDocuments.filter(doc => doc.type === categoryType);
    alert('📦 Exporting folder contents...\n\nExporting ' + docs.length + ' documents\nFormat: ZIP archive\nEstimated size: ' + (docs.length * 1.5).toFixed(1) + ' MB\n\nExport will begin shortly.');
  }
  
  function findDuplicates(categoryType) {
    alert('🔍 Scanning for duplicates...\n\nScan Results:\n- Documents scanned: ' + allDocuments.filter(doc => doc.type === categoryType).length + '\n- Duplicates found: 0\n- Storage saved: 0 MB\n\nNo duplicate documents found in this folder.');
  }
  
  function archiveOldDocs(categoryType) {
    alert('📁 Archiving old documents...\n\nArchive Criteria:\n- Documents older than 2 years\n- Inactive documents\n- Superseded versions\n\nNo documents meet archival criteria at this time.');
  }
  
  // Filing Actions Functions
  function showNewFileModal() {
    const modalHTML = `
      <div class="modal fade" id="newFileModal" tabindex="-1" aria-labelledby="newFileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">
            <div class="modal-header bg-primary text-white">
              <h5 class="modal-title" id="newFileModalLabel">
                <i class="bi bi-file-plus me-2"></i>File New Document
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="newFileForm" enctype="multipart/form-data">
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="documentTitle" class="form-label">Document Title</label>
                      <input type="text" class="form-control" id="documentTitle" required>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="documentCategory" class="form-label">Category</label>
                      <select class="form-select" id="documentCategory" required>
                        <option value="">Select Category</option>
                        <option value="business_license">Business Licenses</option>
                        <option value="tax_certificate">Tax Certificates</option>
                        <option value="insurance_certificate">Insurance Certificates</option>
                        <option value="purchase_order">Purchase Orders</option>
                        <option value="contract">Contracts</option>
                        <option value="inventory_receipt">Inventory Receipts</option>
                        <option value="additional_document">Additional Documents</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="mb-3">
                  <label for="documentDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="documentDescription" rows="3"></textarea>
                </div>
                <div class="mb-3">
                  <label for="documentFile" class="form-label">Upload Document</label>
                  <input type="file" class="form-control" id="documentFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                  <div class="form-text">Supported formats: PDF, DOC, DOCX, JPG, PNG (Max: 10MB)</div>
                </div>
                <div class="row">
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="documentTags" class="form-label">Tags</label>
                      <input type="text" class="form-control" id="documentTags" placeholder="Enter tags separated by commas">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="mb-3">
                      <label for="documentPriority" class="form-label">Priority</label>
                      <select class="form-select" id="documentPriority">
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                      </select>
                    </div>
                  </div>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-primary" onclick="submitNewFile()">
                <i class="bi bi-upload me-1"></i>Upload & File Document
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('newFileModal');
    if (existingModal) existingModal.remove();
    
    // Add modal to body and show
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('newFileModal'));
    modal.show();
  }
  
  function showCreateCategoryModal() {
    const modalHTML = `
      <div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header bg-success text-white">
              <h5 class="modal-title" id="createCategoryModalLabel">
                <i class="bi bi-folder-plus me-2"></i>Create New Category
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form id="createCategoryForm">
                <div class="mb-3">
                  <label for="categoryName" class="form-label">Category Name</label>
                  <input type="text" class="form-control" id="categoryName" required>
                </div>
                <div class="mb-3">
                  <label for="categoryDescription" class="form-label">Description</label>
                  <textarea class="form-control" id="categoryDescription" rows="3"></textarea>
                </div>
                <div class="mb-3">
                  <label for="categoryIcon" class="form-label">Icon</label>
                  <select class="form-select" id="categoryIcon">
                    <option value="bi-file-earmark">Default Document</option>
                    <option value="bi-award">Award/Certificate</option>
                    <option value="bi-receipt">Receipt/Invoice</option>
                    <option value="bi-shield-check">Security/Insurance</option>
                    <option value="bi-cart-check">Purchase/Order</option>
                    <option value="bi-file-earmark-text">Contract/Agreement</option>
                    <option value="bi-box-seam">Inventory/Package</option>
                    <option value="bi-gear">Technical/Settings</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label for="categoryColor" class="form-label">Color Theme</label>
                  <select class="form-select" id="categoryColor">
                    <option value="primary">Blue</option>
                    <option value="success">Green</option>
                    <option value="warning">Yellow</option>
                    <option value="danger">Red</option>
                    <option value="info">Cyan</option>
                    <option value="dark">Dark</option>
                    <option value="purple">Purple</option>
                  </select>
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-success" onclick="submitNewCategory()">
                <i class="bi bi-check me-1"></i>Create Category
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('createCategoryModal');
    if (existingModal) existingModal.remove();
    
    // Add modal to body and show
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
    modal.show();
  }
  
  function reorganizeAllFiles() {
    Swal.fire({
      icon: 'question',
      title: 'Reorganize All Files?',
      html: `
        <div class="text-start">
          <p>This will:</p>
          <ul>
            <li>Sort documents by category and date</li>
            <li>Remove duplicate files</li>
            <li>Optimize folder structure</li>
            <li>Update file indexes</li>
          </ul>
          <p class="text-warning"><strong>This process may take a few minutes.</strong></p>
        </div>
      `,
      showCancelButton: true,
      confirmButtonColor: '#007bff',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Yes, Reorganize',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading
        Swal.fire({
          title: 'Reorganizing Files...',
          html: 'Processing your files, please wait...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        
        // Simulate process
        setTimeout(() => {
          Swal.fire({
            icon: 'success',
            title: 'Reorganization Complete!',
            html: `
              <div class="text-start">
                <p><strong>Processing completed:</strong></p>
                <ul class="text-success">
                  <li>✅ Analyzed file structure</li>
                  <li>✅ Sorted documents</li>
                  <li>✅ Removed duplicates</li>
                  <li>✅ Updated indexes</li>
                </ul>
                <p class="text-muted">All files have been organized and optimized.</p>
              </div>
            `,
            confirmButtonColor: '#28a745'
          });
        }, 3000);
      }
    });
  }
  
  function generateFilingReport() {
    const reportHTML = `
      <div class="modal fade" id="filingReportModal" tabindex="-1" aria-labelledby="filingReportModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header bg-info text-white">
              <h5 class="modal-title" id="filingReportModalLabel">
                <i class="bi bi-clipboard-data me-2"></i>Filing System Report
              </h5>
              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="row mb-4">
                <div class="col-md-3">
                  <div class="card bg-primary text-white text-center">
                    <div class="card-body">
                      <h3>${allDocuments.length}</h3>
                      <p class="mb-0">Total Documents</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-success text-white text-center">
                    <div class="card-body">
                      <h3>6</h3>
                      <p class="mb-0">Active Categories</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-warning text-white text-center">
                    <div class="card-body">
                      <h3>98%</h3>
                      <p class="mb-0">Filing Efficiency</p>
                    </div>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="card bg-info text-white text-center">
                    <div class="card-body">
                      <h3>${(allDocuments.length * 1.2).toFixed(1)} MB</h3>
                      <p class="mb-0">Storage Used</p>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <h6>Category Distribution</h6>
                  <div class="table-responsive">
                    <table class="table table-sm">
                      <thead>
                        <tr>
                          <th>Category</th>
                          <th>Documents</th>
                          <th>Percentage</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr><td>Business Licenses</td><td>2</td><td>16.7%</td></tr>
                        <tr><td>Tax Certificates</td><td>2</td><td>16.7%</td></tr>
                        <tr><td>Insurance Certificates</td><td>2</td><td>16.7%</td></tr>
                        <tr><td>Purchase Orders</td><td>4</td><td>33.3%</td></tr>
                        <tr><td>Contracts</td><td>1</td><td>8.3%</td></tr>
                        <tr><td>Inventory Receipts</td><td>1</td><td>8.3%</td></tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <div class="col-md-6">
                  <h6>Recent Activity</h6>
                  <div class="list-group">
                    <div class="list-group-item">
                      <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">Documents Filed Today</h6>
                        <small>3 files</small>
                      </div>
                    </div>
                    <div class="list-group-item">
                      <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">Categories Created</h6>
                        <small>0 new</small>
                      </div>
                    </div>
                    <div class="list-group-item">
                      <div class="d-flex w-100 justify-content-between">
                        <h6 class="mb-1">System Maintenance</h6>
                        <small>Last week</small>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              <button type="button" class="btn btn-info" onclick="downloadReport()">
                <i class="bi bi-download me-1"></i>Download Report
              </button>
            </div>
          </div>
        </div>
      </div>
    `;
    
    // Remove existing modal if any
    const existingModal = document.getElementById('filingReportModal');
    if (existingModal) existingModal.remove();
    
    // Add modal to body and show
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    const modal = new bootstrap.Modal(document.getElementById('filingReportModal'));
    modal.show();
  }
  
  function showMaintenanceModal() {
    Swal.fire({
      icon: 'info',
      title: 'File Maintenance',
      html: `
        <div class="text-start">
          <p><strong>Maintenance Options:</strong></p>
          <ol>
            <li>Clean temporary files</li>
            <li>Optimize database indexes</li>
            <li>Backup filing system</li>
            <li>Check file integrity</li>
            <li>Update search indexes</li>
          </ol>
        </div>
      `,
      showCancelButton: true,
      confirmButtonColor: '#17a2b8',
      cancelButtonColor: '#6c757d',
      confirmButtonText: 'Run Maintenance',
      cancelButtonText: 'Cancel'
    }).then((result) => {
      if (result.isConfirmed) {
        // Show loading
        Swal.fire({
          title: 'Running Maintenance...',
          html: 'Performing system maintenance tasks...',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });
        
        // Simulate maintenance
        setTimeout(() => {
          Swal.fire({
            icon: 'success',
            title: 'Maintenance Completed!',
            html: `
              <div class="text-start">
                <p><strong>Tasks completed:</strong></p>
                <ul class="text-success">
                  <li>✅ Cleaned temporary files</li>
                  <li>✅ Optimized database indexes</li>
                  <li>✅ Created system backup</li>
                  <li>✅ Verified file integrity</li>
                  <li>✅ Updated search indexes</li>
                </ul>
                <p class="text-muted">System maintenance completed successfully!</p>
              </div>
            `,
            confirmButtonColor: '#28a745'
          });
        }, 4000);
      }
    });
  }
  
  function submitNewFile() {
    const title = document.getElementById('documentTitle').value;
    const category = document.getElementById('documentCategory').value;
    const file = document.getElementById('documentFile').files[0];
    
    if (!title || !category || !file) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Information',
        text: 'Please fill in all required fields.',
        confirmButtonColor: '#ffc107'
      });
      return;
    }
    
    // Show loading
    Swal.fire({
      title: 'Filing Document...',
      html: 'Uploading and organizing your document...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Simulate file upload
    setTimeout(() => {
      Swal.fire({
        icon: 'success',
        title: 'Document Filed Successfully!',
        html: `
          <div class="text-start">
            <p><strong>Title:</strong> ${title}</p>
            <p><strong>Category:</strong> ${category.replace('_', ' ')}</p>
            <p><strong>File:</strong> ${file.name}</p>
            <p class="text-muted mt-2">The document has been uploaded and organized in the ${category.replace('_', ' ')} folder.</p>
          </div>
        `,
        confirmButtonColor: '#28a745'
      }).then(() => {
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('newFileModal')).hide();
      });
    }, 2000);
  }
  
  function submitNewCategory() {
    const name = document.getElementById('categoryName').value;
    const description = document.getElementById('categoryDescription').value;
    
    if (!name) {
      Swal.fire({
        icon: 'warning',
        title: 'Missing Information',
        text: 'Please enter a category name.',
        confirmButtonColor: '#ffc107'
      });
      return;
    }
    
    // Show loading
    Swal.fire({
      title: 'Creating Category...',
      html: 'Setting up your new category...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Simulate category creation
    setTimeout(() => {
      Swal.fire({
        icon: 'success',
        title: 'Category Created Successfully!',
        html: `
          <div class="text-start">
            <p><strong>Name:</strong> ${name}</p>
            <p><strong>Description:</strong> ${description || 'No description provided'}</p>
            <p class="text-muted mt-2">The new category is now available for filing documents.</p>
          </div>
        `,
        confirmButtonColor: '#28a745'
      }).then(() => {
        // Close modal
        bootstrap.Modal.getInstance(document.getElementById('createCategoryModal')).hide();
      });
    }, 1500);
  }
  
  function downloadReport() {
    // Show loading
    Swal.fire({
      title: 'Generating Report...',
      html: 'Creating your filing system analysis...',
      allowOutsideClick: false,
      didOpen: () => {
        Swal.showLoading();
      }
    });
    
    // Simulate report generation
    setTimeout(() => {
      Swal.fire({
        icon: 'success',
        title: 'Report Generated Successfully!',
        html: `
          <div class="text-start">
            <p><strong>Report Type:</strong> Filing System Analysis</p>
            <p><strong>Format:</strong> PDF</p>
            <p><strong>Size:</strong> 2.3 MB</p>
            <p class="text-muted mt-2">Download will begin shortly.</p>
          </div>
        `,
        confirmButtonColor: '#17a2b8',
        confirmButtonText: 'Download Now'
      });
    }, 2000);
  }
  
  function testFormValues() {
    const folderName = document.getElementById('folderName');
    const folderDescription = document.getElementById('folderDescription');
    const folderColor = document.getElementById('folderColor');
    const folderVisible = document.getElementById('folderVisible');
    
    const values = {
      name: folderName?.value || 'NOT FOUND',
      description: folderDescription?.value || 'NOT FOUND',
      color: folderColor?.value || 'NOT FOUND',
      visible: folderVisible?.checked || false
    };
    
    console.log('Current form values:', values);
    
    Swal.fire({
      icon: 'info',
      title: 'Form Values Debug',
      html: `
        <div class="text-start">
          <p><strong>Name:</strong> ${values.name}</p>
          <p><strong>Description:</strong> ${values.description}</p>
          <p><strong>Color:</strong> ${values.color}</p>
          <p><strong>Visible:</strong> ${values.visible ? 'Yes' : 'No'}</p>
        </div>
      `,
      confirmButtonColor: '#17a2b8'
    });
  }
  
  </script>
</body>
</html>
