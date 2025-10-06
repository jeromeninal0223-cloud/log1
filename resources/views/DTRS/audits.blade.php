<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Document Audit Trail - DTRS</title>

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
        <a href="{{ url('/dtrs/document') }}" class="nav-link text-dark small">
          <i class="bi bi-file-earmark-text me-2"></i> Documents
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ url('/dtrs/audits') }}" class="nav-link text-dark small active">
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
          <i class="bi bi-file-earmark-check fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Document Audit Trail</h2>
          <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Monitor and track all document activities and access logs.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/dtrs') }}" class="text-decoration-none">Document Tracking</a></li>
            <li class="breadcrumb-item active" aria-current="page">Document Audit Trail</li>
          </ol>
        </nav>
    </div>
  </div>

  <!-- Document Audit Statistics Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="card stat-card shadow-sm border-0">
        <div class="card-body">
          <div class="d-flex align-items-center">
            <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $todayStats['document_activities'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Document Activities Today</p>
              <small class="text-success"><i class="bi bi-file-earmark-text"></i> Views, downloads, uploads</small>
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
              <i class="bi bi-cloud-upload"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $todayStats['documents_uploaded'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Documents Uploaded</p>
              <small class="text-muted"><i class="bi bi-cloud-upload"></i> New documents added</small>
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
              <i class="bi bi-shield-exclamation"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $todayStats['access_violations'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Access Violations</p>
              <small class="text-danger"><i class="bi bi-shield-exclamation"></i> Unauthorized attempts</small>
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
              <i class="bi bi-clock-history"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $todayStats['version_changes'] ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Version Changes</p>
              <small class="text-success"><i class="bi bi-clock-history"></i> Document updates</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Main Content Area -->
  <div class="row g-4">
    <!-- Main Column -->
    <div class="col-12">
      <!-- Audit Filters -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header border-bottom bg-primary text-white">
          <h5 class="card-title mb-0">Document Audit Filters</h5>
        </div>
        <div class="card-body">
          <form id="auditFiltersForm" method="GET" action="{{ route('dtrs.audits') }}">
            <div class="row g-3">
              <!-- Date Range -->
              <div class="col-md-3">
                <label for="startDate" class="form-label">Start Date</label>
                <input type="date" class="form-control" id="startDate" name="start_date" value="{{ $startDate }}">
              </div>
              <div class="col-md-3">
                <label for="endDate" class="form-label">End Date</label>
                <input type="date" class="form-control" id="endDate" name="end_date" value="{{ $endDate }}">
              </div>
              
              <!-- User Filter -->
              <div class="col-md-3">
                <label for="userFilter" class="form-label">User</label>
                <select class="form-select" id="userFilter" name="user_id">
                  <option value="">All Users</option>
                  @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>
                      {{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})
                    </option>
                  @endforeach
                </select>
              </div>
              
              <!-- Document Action Filter -->
              <div class="col-md-3">
                <label for="actionFilter" class="form-label">Document Action</label>
                <select class="form-select" id="actionFilter" name="action">
                  <option value="">All Actions</option>
                  <option value="upload" {{ $action == 'upload' ? 'selected' : '' }}>Upload</option>
                  <option value="view" {{ $action == 'view' ? 'selected' : '' }}>View</option>
                  <option value="download" {{ $action == 'download' ? 'selected' : '' }}>Download</option>
                  <option value="edit" {{ $action == 'edit' ? 'selected' : '' }}>Edit</option>
                  <option value="delete" {{ $action == 'delete' ? 'selected' : '' }}>Delete</option>
                  <option value="approve" {{ $action == 'approve' ? 'selected' : '' }}>Approve</option>
                  <option value="reject" {{ $action == 'reject' ? 'selected' : '' }}>Reject</option>
                  <option value="version_create" {{ $action == 'version_create' ? 'selected' : '' }}>Version Create</option>
                  <option value="share" {{ $action == 'share' ? 'selected' : '' }}>Share</option>
                  <option value="access_denied" {{ $action == 'access_denied' ? 'selected' : '' }}>Access Denied</option>
                </select>
              </div>
              
              <!-- Document Type Filter -->
              <div class="col-md-3">
                <label for="documentTypeFilter" class="form-label">Document Type</label>
                <select class="form-select" id="documentTypeFilter" name="document_type">
                  <option value="">All Document Types</option>
                  <option value="contract" {{ $document_type == 'contract' ? 'selected' : '' }}>Contracts</option>
                  <option value="invoice" {{ $document_type == 'invoice' ? 'selected' : '' }}>Invoices</option>
                  <option value="purchase_order" {{ $document_type == 'purchase_order' ? 'selected' : '' }}>Purchase Orders</option>
                  <option value="receipt" {{ $document_type == 'receipt' ? 'selected' : '' }}>Receipts</option>
                  <option value="policy" {{ $document_type == 'policy' ? 'selected' : '' }}>Policies</option>
                  <option value="procedure" {{ $document_type == 'procedure' ? 'selected' : '' }}>Procedures</option>
                  <option value="report" {{ $document_type == 'report' ? 'selected' : '' }}>Reports</option>
                  <option value="certificate" {{ $document_type == 'certificate' ? 'selected' : '' }}>Certificates</option>
                  <option value="specification" {{ $document_type == 'specification' ? 'selected' : '' }}>Specifications</option>
                  <option value="other" {{ $document_type == 'other' ? 'selected' : '' }}>Other</option>
                </select>
              </div>
              
              <!-- Search Field -->
              <div class="col-md-9">
                <label for="searchFilter" class="form-label">Search</label>
                <input type="text" class="form-control" id="searchFilter" name="search" 
                       value="{{ $search ?? '' }}" placeholder="Search by document name, user name, or action description...">
              </div>
              
              <!-- Actions -->
              <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary me-2">
                  <i class="bi bi-search me-1"></i>Filter
                </button>
                <button type="button" class="btn btn-outline-secondary me-2" id="resetFiltersBtn">
                  <i class="bi bi-arrow-clockwise me-1"></i>Reset
                </button>
                <button type="button" class="btn btn-outline-success" id="exportAuditBtn">
                  <i class="bi bi-download me-1"></i>Export
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
      
      <!-- Audit Trail Table -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">Document Activity Log</h5>
          <div>
            <button class="btn btn-sm btn-outline-primary me-2" id="refreshAuditBtn">
              <i class="bi bi-arrow-clockwise"></i> Refresh
            </button>
            <span class="badge bg-primary">{{ $auditLogs->total() ?? 0 }} Records</span>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Timestamp</th>
                  <th>User</th>
                  <th>Document</th>
                  <th>Action</th>
                  <th>Details</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody id="auditTableBody">
                @forelse($auditLogs as $log)
                <tr>
                  <td><small>{{ $log->created_at->format('Y-m-d H:i:s') }}</small></td>
                  <td>
                    <div class="d-flex align-items-center">
                      <div class="avatar-sm {{ $log->user_avatar_color ?? 'bg-primary' }} text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-fill"></i>
                      </div>
                      <div>
                        <div class="fw-semibold">{{ $log->user_name ?? 'Unknown User' }}</div>
                        <small class="text-muted">{{ ucfirst(str_replace('_', ' ', $log->user_role ?? 'Unknown')) }}</small>
                      </div>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex align-items-center">
                      <i class="bi bi-file-earmark-text text-primary me-2"></i>
                      <div>
                        <div class="fw-semibold">{{ $log->document_name ?? 'Unknown Document' }}</div>
                        <small class="text-muted">{{ ucfirst($log->document_type ?? 'Unknown Type') }}</small>
                      </div>
                    </div>
                  </td>
                  <td><span class="badge {{ $log->action_badge_class ?? 'bg-secondary' }}">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span></td>
                  <td><small>{{ $log->description }}</small></td>
                  <td><i class="bi {{ $log->status_icon ?? 'bi-check-circle' }} text-{{ $log->status === 'success' ? 'success' : ($log->status === 'failed' ? 'danger' : 'warning') }}"></i></td>
                </tr>
                @empty
                <tr>
                  <td colspan="6" class="text-center py-4">
                    <div class="text-muted">
                      <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                      <p class="mb-0">No document audit logs found for the selected criteria.</p>
                      <small>Try adjusting your filters, date range, or document type.</small>
                    </div>
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <!-- Enhanced Pagination -->
          <div class="card-footer border-top bg-light">
            <div class="row align-items-center">
              <div class="col-md-6">
                <div class="d-flex align-items-center text-muted small">
                  <i class="bi bi-info-circle me-2"></i>
                  <span>
                    @if($auditLogs->total() > 0)
                      Showing <strong>{{ $auditLogs->firstItem() }}</strong> to <strong>{{ $auditLogs->lastItem() }}</strong> 
                      of <strong>{{ number_format($auditLogs->total()) }}</strong> audit records
                    @else
                      No audit records found
                    @endif
                  </span>
                </div>
                @if($auditLogs->hasPages())
                <div class="mt-2">
                  <small class="text-muted">
                    <i class="bi bi-layers me-1"></i>
                    Page {{ $auditLogs->currentPage() }} of {{ $auditLogs->lastPage() }}
                    ({{ $auditLogs->perPage() }} records per page)
                  </small>
                </div>
                @endif
              </div>
              <div class="col-md-6">
                @if($auditLogs->total() > 0)
                <div class="d-flex justify-content-end">
                  <nav aria-label="Audit logs pagination">
                    <ul class="pagination pagination-sm mb-0">
                      {{-- Previous Page Link --}}
                      @if ($auditLogs->onFirstPage())
                        <li class="page-item disabled">
                          <span class="page-link">&laquo;</span>
                        </li>
                      @else
                        <li class="page-item">
                          <a class="page-link" href="{{ $auditLogs->appends(request()->query())->previousPageUrl() }}" rel="prev">&laquo;</a>
                        </li>
                      @endif

                      {{-- Always show at least page 1 --}}
                      @if($auditLogs->lastPage() > 1)
                        {{-- Pagination Elements --}}
                        @php
                          $currentPage = $auditLogs->currentPage();
                          $lastPage = $auditLogs->lastPage();
                          $showPages = 5; // Number of pages to show around current page
                          $halfShow = floor($showPages / 2);
                        @endphp

                        {{-- First page --}}
                        @if ($currentPage > $halfShow + 2)
                          <li class="page-item">
                            <a class="page-link" href="{{ $auditLogs->appends(request()->query())->url(1) }}">1</a>
                          </li>
                          @if ($currentPage > $halfShow + 3)
                            <li class="page-item disabled">
                              <span class="page-link">...</span>
                            </li>
                          @endif
                        @endif

                        {{-- Pages around current page --}}
                        @for ($i = max(1, $currentPage - $halfShow); $i <= min($lastPage, $currentPage + $halfShow); $i++)
                          @if ($i == $currentPage)
                            <li class="page-item active">
                              <span class="page-link">{{ $i }}</span>
                            </li>
                          @else
                            <li class="page-item">
                              <a class="page-link" href="{{ $auditLogs->appends(request()->query())->url($i) }}">{{ $i }}</a>
                            </li>
                          @endif
                        @endfor

                        {{-- Last page --}}
                        @if ($currentPage < $lastPage - $halfShow - 1)
                          @if ($currentPage < $lastPage - $halfShow - 2)
                            <li class="page-item disabled">
                              <span class="page-link">...</span>
                            </li>
                          @endif
                          <li class="page-item">
                            <a class="page-link" href="{{ $auditLogs->appends(request()->query())->url($lastPage) }}">{{ $lastPage }}</a>
                          </li>
                        @endif
                      @else
                        {{-- Single page --}}
                        <li class="page-item active">
                          <span class="page-link">1</span>
                        </li>
                      @endif

                      {{-- Next Page Link --}}
                      @if ($auditLogs->hasMorePages())
                        <li class="page-item">
                          <a class="page-link" href="{{ $auditLogs->appends(request()->query())->nextPageUrl() }}" rel="next">&raquo;</a>
                        </li>
                      @else
                        <li class="page-item disabled">
                          <span class="page-link">&raquo;</span>
                        </li>
                      @endif
                    </ul>
                  </nav>
                </div>
                @endif
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Audit Trail Functionality -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Filter form submission
      const auditFiltersForm = document.getElementById('auditFiltersForm');
      auditFiltersForm.addEventListener('submit', function(e) {
        e.preventDefault();
        filterAuditLogs();
      });

      // Reset filters
      document.getElementById('resetFiltersBtn').addEventListener('click', function() {
        auditFiltersForm.reset();
        document.getElementById('startDate').value = '{{ date('Y-m-d', strtotime('-7 days')) }}';
        document.getElementById('endDate').value = '{{ date('Y-m-d') }}';
        filterAuditLogs();
      });

      // Export audit logs
      document.getElementById('exportAuditBtn').addEventListener('click', function() {
        Swal.fire({
          title: 'Export Audit Logs',
          text: 'Generate audit trail report for the selected filters?',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Export',
          confirmButtonColor: '#198754'
        }).then((result) => {
          if (result.isConfirmed) {
            exportAuditLogs();
          }
        });
      });

      // Refresh audit logs
      document.getElementById('refreshAuditBtn').addEventListener('click', function() {
        filterAuditLogs();
      });

      function filterAuditLogs() {
        const formData = new FormData(auditFiltersForm);
        
        Swal.fire({
          title: 'Loading...',
          text: 'Filtering audit logs',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        // Simulate API call
        setTimeout(() => {
          Swal.close();
          updateAuditTable();
        }, 1500);
      }

      function updateAuditTable() {
        // This would typically make an AJAX call to get filtered results
        Swal.fire({
          icon: 'success',
          title: 'Filters Applied',
          text: 'Audit logs have been updated.',
          timer: 2000,
          showConfirmButton: false
        });
      }

      function exportAuditLogs() {
        Swal.fire({
          title: 'Generating Export...',
          text: 'Please wait while we prepare your audit report.',
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          }
        });

        // Simulate export generation
        setTimeout(() => {
          Swal.fire({
            icon: 'success',
            title: 'Export Complete!',
            text: 'Your audit trail report has been downloaded.',
            confirmButtonColor: '#0d6efd'
          });
          
          // Simulate file download
          const link = document.createElement('a');
          link.href = '#';
          link.download = `audit_trail_${new Date().toISOString().split('T')[0]}.xlsx`;
          link.click();
        }, 3000);
      }

      function generateAuditReport() {
        Swal.fire({
          title: 'Generate Audit Report',
          html: `
            <div class="mb-3">
              <label class="form-label">Report Type</label>
              <select class="form-select" id="reportType">
                <option value="summary">Activity Summary</option>
                <option value="detailed">Detailed Log</option>
                <option value="security">Security Report</option>
                <option value="compliance">Compliance Report</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Format</label>
              <select class="form-select" id="reportFormat">
                <option value="pdf">PDF</option>
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
              </select>
            </div>
          `,
          confirmButtonText: 'Generate Report',
          showCancelButton: true,
          confirmButtonColor: '#0d6efd'
        }).then((result) => {
          if (result.isConfirmed) {
            const reportType = document.getElementById('reportType').value;
            const format = document.getElementById('reportFormat').value;
            
            Swal.fire({
              icon: 'success',
              title: 'Report Generated!',
              text: `Your ${reportType} report in ${format.toUpperCase()} format is ready.`,
              confirmButtonColor: '#0d6efd'
            });
          }
        });
      }

      // Logout functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
          e.preventDefault();
          
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
    });
  </script>

</body>
</html>
