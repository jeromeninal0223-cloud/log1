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
      @endif
      <li class="nav-item">
  <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#documentSubmenu" aria-expanded="false" aria-controls="documentSubmenu">
    <i class="bi bi-journal-text me-2"></i> Document Tracking & Logistics Records
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
          <i class="bi bi-box-seam fs-1 text-primary"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">Document Archive</h2>
          <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! View and manage archived documents and logistics records.</p>
        </div>
      </div>
              <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/dtrs') }}" class="text-decoration-none">Document Tracking</a></li>
            <li class="breadcrumb-item active" aria-current="page">Document Archive</li>
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
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $totalDocuments ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Total Documents</p>
              <small class="text-info"><i class="bi bi-archive"></i> Archived</small>
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
              <h3 class="fw-bold mb-0">{{ $logisticsRecordsCount ?? 0 }}</h3>
              <p class="text-muted mb-0 small">Logistics Records</p>
              <small class="text-success"><i class="bi bi-check-circle"></i> Tracked</small>
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
              <i class="bi bi-calendar-week"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ $thisWeekDocuments ?? 0 }}</h3>
              <p class="text-muted mb-0 small">This Week</p>
              <small class="text-warning"><i class="bi bi-plus"></i> New additions</small>
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
              <i class="bi bi-hdd"></i>
            </div>
            <div>
              <h3 class="fw-bold mb-0">{{ number_format(($totalFileSize ?? 0) / 1024 / 1024, 1) }}MB</h3>
              <p class="text-muted mb-0 small">Storage Used</p>
              <small class="text-info"><i class="bi bi-database"></i> Archive size</small>
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
          <h5 class="card-title mb-0">Document Search & Filters</h5>
        </div>
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-4">
              <label for="searchQuery" class="form-label">Search Documents</label>
              <input type="text" class="form-control" id="searchQuery" placeholder="Search by filename, type, or content...">
            </div>
            <div class="col-md-3">
              <label for="documentType" class="form-label">Document Type</label>
              <select class="form-select" id="documentType">
                <option value="">All Types</option>
                <option value="business_license">Business License</option>
                <option value="tax_certificate">Tax Certificate</option>
                <option value="insurance_certificate">Insurance Certificate</option>
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
          <h5 class="card-title mb-0">Archived Documents</h5>
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
          <div class="table-responsive">
            <table class="table table-hover" id="documentsTable">
              <thead class="table-light">
                <tr>
                  <th>Document ID</th>
                  <th>Filename</th>
                  <th>Type</th>
                  <th>Date Created</th>
                  <th>Size</th>
                  <th>Source Module</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($paginatedDocs ?? [] as $document)
                <tr>
                  <td><strong>{{ $document['document_id'] }}</strong></td>
                  <td>
                    <i class="bi bi-file-earmark-{{ $document['type'] === 'business_license' ? 'check' : ($document['type'] === 'tax_certificate' ? 'text' : ($document['type'] === 'insurance_certificate' ? 'shield' : 'plus')) }} me-2"></i>
                    {{ $document['filename'] }}
                    <br><small class="text-muted">{{ $document['vendor_name'] }}</small>
                  </td>
                  <td>
                    <span class="badge bg-{{ $document['type'] === 'business_license' ? 'success' : ($document['type'] === 'tax_certificate' ? 'warning' : ($document['type'] === 'insurance_certificate' ? 'info' : 'secondary')) }}">
                      {{ ucfirst(str_replace('_', ' ', $document['type'])) }}
                    </span>
                  </td>
                  <td>{{ $document['created_at'] ? $document['created_at']->format('M d, Y H:i') : 'N/A' }}</td>
                  <td>{{ $document['file_size'] ? number_format($document['file_size'] / 1024, 1) . ' KB' : 'N/A' }}</td>
                  <td>
                    <span class="badge bg-info">
                      {{ $document['source_module'] }}
                    </span>
                  </td>
                  <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewDocument('{{ $document['id'] }}')"
                            title="View Document">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" onclick="downloadDocument('{{ $document['id'] }}')"
                            title="Download">
                      <i class="bi bi-download"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" onclick="viewMetadata('{{ $document['id'] }}')"
                            title="View Metadata">
                      <i class="bi bi-info-circle"></i>
                    </button>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    <i class="bi bi-archive fs-1 d-block mb-2 opacity-50"></i>
                    No documents found in the archive
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if(isset($total) && $total > $perPage)
          <div class="d-flex justify-content-center mt-3">
            <nav aria-label="Document pagination">
              <ul class="pagination">
                @if($currentPage > 1)
                  <li class="page-item">
                    <a class="page-link" href="?page={{ $currentPage - 1 }}">Previous</a>
                  </li>
                @endif
                
                @for($i = max(1, $currentPage - 2); $i <= min(ceil($total / $perPage), $currentPage + 2); $i++)
                  <li class="page-item {{ $i == $currentPage ? 'active' : '' }}">
                    <a class="page-link" href="?page={{ $i }}">{{ $i }}</a>
                  </li>
                @endfor
                
                @if($currentPage < ceil($total / $perPage))
                  <li class="page-item">
                    <a class="page-link" href="?page={{ $currentPage + 1 }}">Next</a>
                  </li>
                @endif
              </ul>
            </nav>
          </div>
          @endif
        </div>
      </div>
    </div>
    
    <!-- Right Column -->
    <div class="col-lg-4">
      <!-- Archive Actions -->
      <div class="card shadow-sm border-0">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Archive Actions</h5>
        </div>
        <div class="card-body">
          <div class="d-grid gap-2">
            <button class="btn btn-primary" id="searchAdvancedBtn">
              <i class="bi bi-search me-2"></i>Advanced Search
            </button>
            <button class="btn btn-outline-primary" id="generateReportBtn">
              <i class="bi bi-file-earmark-bar-graph me-2"></i>Generate Report
            </button>
            <button class="btn btn-outline-primary" id="bulkExportBtn">
              <i class="bi bi-download me-2"></i>Bulk Export
            </button>
            <button class="btn btn-outline-secondary" id="archiveStatsBtn">
              <i class="bi bi-graph-up me-2"></i>Archive Statistics
            </button>
          </div>
        </div>
      </div>
      
      <!-- Logistics Records -->
      <div class="card shadow-sm border-0 mt-4">
        <div class="card-header border-bottom">
          <h5 class="card-title mb-0">Recent Logistics Records</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-sm">
              <thead>
                <tr>
                  <th>Record ID</th>
                  <th>Type</th>
                  <th>Date</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @forelse($logisticsRecords ?? [] as $record)
                <tr>
                  <td><strong>{{ $record->record_id ?? 'LR-' . str_pad($record->id ?? 0, 6, '0', STR_PAD_LEFT) }}</strong></td>
                  <td>{{ ucfirst(str_replace('_', ' ', $record->type ?? 'Unknown')) }}</td>
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
                    No logistics records found
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
          <h5 class="card-title mb-0">Archive Summary</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Logistics Records</span>
              <span class="small fw-bold">{{ $logisticsRecordsCount ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Documents Tracked</span>
              <span class="small fw-bold">{{ $totalDocuments ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Added This Week</span>
              <span class="small fw-bold">{{ $thisWeekDocuments ?? 0 }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Storage Used</span>
              <span class="small fw-bold">{{ number_format(($totalFileSize ?? 0) / (1024 * 1024), 1) }}MB</span>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-1">
              <span class="small">Archive Utilization</span>
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
  
  // Document Archive Functionality
  function viewDocument(documentId) {
    // Open document viewer modal or new window
    window.open(`/dtrs/documents/${documentId}/view`, '_blank');
  }
  
  function downloadDocument(documentId) {
    // Trigger document download
    window.location.href = `/dtrs/documents/${documentId}/download`;
  }
  
  function viewMetadata(documentId) {
    // Show document metadata in modal
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
  
  </script>
</body>
</html>
