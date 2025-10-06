{{-- Access Control Check --}}
@if(!Auth::check())
  <script>window.location.href = '/login';</script>
  @php exit; @endphp
@endif

@if(!in_array(Auth::user()->role, ['logistics_staff', 'admin']))
  <div class="container-fluid d-flex justify-content-center align-items-center" style="height: 100vh; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="text-center text-white">
      <i class="bi bi-shield-exclamation display-1 mb-4"></i>
      <h1 class="display-4 fw-bold mb-3">Access Denied</h1>
      <p class="lead mb-4">You don't have permission to access the Document Version History.</p>
      <p class="mb-4">This module is restricted to <strong>Logistics Staff</strong> and <strong>Administrators</strong> only.</p>
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
  <title>Document Version History - Jetlouge Travels</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">

  <style>
    .avatar-sm {
      width: 32px;
      height: 32px;
      font-size: 12px;
    }
    
    .changes-summary {
      max-width: 200px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    }
    
    .version-actions .btn-group {
      gap: 2px;
    }
    
    .version-actions .btn {
      border-radius: 4px !important;
      margin-right: 2px;
    }
    
    .version-actions .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .btn-action-view { border-color: #0d6efd; color: #0d6efd; }
    .btn-action-view:hover { background-color: #0d6efd; color: white; }
    
    .btn-action-download { border-color: #198754; color: #198754; }
    .btn-action-download:hover { background-color: #198754; color: white; }
    
    .btn-action-restore { border-color: #fd7e14; color: #fd7e14; }
    .btn-action-restore:hover { background-color: #fd7e14; color: white; }
    
    .btn-action-compare { border-color: #20c997; color: #20c997; }
    .btn-action-compare:hover { background-color: #20c997; color: white; }
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
        <a href="#" class="nav-link text-dark active" data-bs-toggle="collapse" data-bs-target="#documentSubmenu" aria-expanded="true" aria-controls="documentSubmenu">
          <i class="bi bi-journal-text me-2"></i> Document Tracking & Logistics Records
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
              <a href="{{ url('/dtrs/audits') }}" class="nav-link text-dark small">
                <i class="bi bi-clipboard-check me-2"></i> Audits
              </a>
            </li>
            <li class="nav-item">
              <a href="{{ url('/dtrs/version') }}" class="nav-link text-dark small active">
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
        <i class="bi bi-clock-history fs-1 text-primary"></i>
      </div>
      <div>
        <h2 class="fw-bold mb-1">Document Version History</h2>
        <p class="text-muted mb-0">Track and manage document versions and changes over time.</p>
      </div>
    </div>
    <nav aria-label="breadcrumb">
      <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}" class="text-decoration-none">Home</a></li>
        <li class="breadcrumb-item"><a href="{{ url('/dtrs/document') }}" class="text-decoration-none">DTRS</a></li>
        <li class="breadcrumb-item active" aria-current="page">Version History</li>
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
            <small class="text-success"><i class="bi bi-arrow-up"></i> Active tracking</small>
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
            <i class="bi bi-clock-history"></i>
          </div>
          <div>
            <h3 class="fw-bold mb-0">{{ $totalVersions ?? 0 }}</h3>
            <p class="text-muted mb-0 small">Total Versions</p>
            <small class="text-success"><i class="bi bi-arrow-up"></i> All revisions</small>
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
            <i class="bi bi-pencil-square"></i>
          </div>
          <div>
            <h3 class="fw-bold mb-0">{{ $recentChanges ?? 0 }}</h3>
            <p class="text-muted mb-0 small">Recent Changes</p>
            <small class="text-warning"><i class="bi bi-clock"></i> Last 7 days</small>
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
            <i class="bi bi-people"></i>
          </div>
          <div>
            <h3 class="fw-bold mb-0">{{ $activeUsers ?? 0 }}</h3>
            <p class="text-muted mb-0 small">Active Contributors</p>
            <small class="text-success"><i class="bi bi-person-check"></i> This month</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Main Content Area -->
<div class="row g-4">
  <!-- Document Version History -->
  <div class="col-12">
    <!-- Document Version History -->
    <div class="card shadow-sm border-0 mb-4">
      <div class="card-header border-bottom bg-primary text-white">
        <h5 class="card-title mb-0">Document Version History</h5>
      </div>
      <div class="card-body">
        <!-- Document Selection -->
        <div class="row g-3 mb-4">
          <div class="col-md-8">
            <label for="documentSelect" class="form-label">Select Document</label>
            <select class="form-select" id="documentSelect" {{ $documents->isEmpty() ? 'disabled' : '' }}>
              @if($documents->isEmpty())
                <option value="">No documents available</option>
              @else
                <option value="">Choose a document to view version history...</option>
                @foreach($documents as $document)
                  <option value="{{ $document->id }}">{{ $document->title }} - {{ $document->document_type }}</option>
                @endforeach
              @endif
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">&nbsp;</label>
            <div class="d-grid">
              <button type="button" class="btn btn-primary" id="loadVersionsBtn" {{ $documents->isEmpty() ? 'disabled' : '' }}>
                <i class="bi bi-search me-2"></i>Load Version History
              </button>
            </div>
          </div>
        </div>

        @if($documents->isEmpty())
        <!-- No Documents Message -->
        <div class="alert alert-info d-flex align-items-center" role="alert">
          <i class="bi bi-info-circle fs-4 me-3"></i>
          <div>
            <h6 class="alert-heading mb-1">No Documents Found</h6>
            <p class="mb-0">{{ $noDocumentsMessage ?? 'No documents are available for version tracking. Please create documents first.' }}</p>
          </div>
        </div>
        @endif
        
        <!-- Version History Table -->
        <div id="versionHistoryContainer" style="display: none;">
          <div class="table-responsive">
            <table class="table table-hover" id="versionHistoryTable">
              <thead class="table-light">
                <tr>
                  <th>Version</th>
                  <th>Modified By</th>
                  <th>Date Modified</th>
                  <th>Changes</th>
                  <th>File Size</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="versionHistoryBody">
                <!-- Version history will be loaded here -->
              </tbody>
            </table>
          </div>
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-5">
          <i class="bi bi-clock-history fs-1 text-muted mb-3"></i>
          <h5 class="text-muted">No Document Selected</h5>
          <p class="text-muted">Select a document above to view its version history and track changes over time.</p>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Version History JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const documentSelect = document.getElementById('documentSelect');
    const loadVersionsBtn = document.getElementById('loadVersionsBtn');
    const versionHistoryContainer = document.getElementById('versionHistoryContainer');
    const emptyState = document.getElementById('emptyState');
    const versionHistoryBody = document.getElementById('versionHistoryBody');

    // Load version history
    loadVersionsBtn.addEventListener('click', function() {
        const documentId = documentSelect.value;
        if (!documentId) {
            alert('Please select a document first.');
            return;
        }

        // Show loading state
        loadVersionsBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Loading...';
        loadVersionsBtn.disabled = true;

        // Fetch version history from API
        fetch(`/dtrs/document/${documentId}/versions`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayVersionHistory(data.versions);
            } else {
                throw new Error(data.message || 'Failed to load version history');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading version history: ' + error.message);
            // Show empty state on error
            versionHistoryBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-danger">
                            <i class="bi bi-exclamation-triangle fs-4 d-block mb-2"></i>
                            <p class="mb-0">Error loading version history. Please try again.</p>
                        </div>
                    </td>
                </tr>
            `;
            emptyState.style.display = 'none';
            versionHistoryContainer.style.display = 'block';
        })
        .finally(() => {
            loadVersionsBtn.innerHTML = '<i class="bi bi-search me-2"></i>Load Version History';
            loadVersionsBtn.disabled = false;
        });
    });

    function displayVersionHistory(versions) {
        versionHistoryBody.innerHTML = '';
        
        if (versions.length === 0) {
            versionHistoryBody.innerHTML = `
                <tr>
                    <td colspan="7" class="text-center py-4">
                        <div class="text-muted">
                            <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                            <p class="mb-0">No version history found for this document.</p>
                        </div>
                    </td>
                </tr>
            `;
        } else {
            versions.forEach((version, index) => {
                const row = createVersionRow(version, index === 0);
                versionHistoryBody.appendChild(row);
            });
        }

        emptyState.style.display = 'none';
        versionHistoryContainer.style.display = 'block';
    }

    function createVersionRow(version, isCurrent) {
        const row = document.createElement('tr');
        const statusBadge = getStatusBadge(version.status, isCurrent);
        const changesSummary = version.changes_summary || 'No changes recorded';
        
        row.innerHTML = `
            <td>
                <strong>v${version.version_number}</strong>
                ${isCurrent ? '<span class="badge bg-success ms-2">Current</span>' : ''}
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-sm bg-primary text-white rounded-circle me-2 d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <div>
                        <div class="fw-semibold">${version.modified_by}</div>
                        <small class="text-muted">${version.user_role || 'User'}</small>
                    </div>
                </div>
            </td>
            <td>
                <div>${formatDate(version.created_at)}</div>
                <small class="text-muted">${formatTime(version.created_at)}</small>
            </td>
            <td>
                <div class="changes-summary">
                    ${changesSummary}
                </div>
            </td>
            <td>
                <span class="badge bg-light text-dark">${formatFileSize(version.file_size)}</span>
            </td>
            <td>${statusBadge}</td>
            <td class="version-actions">
                <div class="d-flex gap-1">
                    <button type="button" class="btn btn-sm btn-action-view" onclick="viewVersion('${version.id}', this)" title="View Version">
                        <i class="bi bi-eye"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-action-download" onclick="downloadVersion('${version.id}', this)" title="Download Version">
                        <i class="bi bi-download"></i>
                    </button>
                    ${!isCurrent ? `<button type="button" class="btn btn-sm btn-action-restore" onclick="restoreVersion('${version.id}', this)" title="Restore Version (Admin Only)">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>` : `<button type="button" class="btn btn-sm btn-secondary" disabled title="Current Version">
                        <i class="bi bi-check-circle"></i>
                    </button>`}
                    <button type="button" class="btn btn-sm btn-action-compare" onclick="compareVersions('${version.id}', this)" title="Compare with Current">
                        <i class="bi bi-file-diff"></i>
                    </button>
                </div>
            </td>
        `;
        
        return row;
    }

    function getStatusBadge(status, isCurrent) {
        if (isCurrent) return '<span class="badge bg-success">Current</span>';
        
        switch(status) {
            case 'active': return '<span class="badge bg-success">Active</span>';
            case 'archived': return '<span class="badge bg-secondary">Archived</span>';
            case 'deleted': return '<span class="badge bg-danger">Deleted</span>';
            default: return '<span class="badge bg-secondary">Unknown</span>';
        }
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString();
    }

    function formatTime(dateString) {
        return new Date(dateString).toLocaleTimeString();
    }

    function formatFileSize(bytes) {
        if (!bytes) return 'N/A';
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(1024));
        return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
    }

});

// Global functions for version actions (defined outside DOMContentLoaded for global access)
window.viewVersion = function(versionId, buttonElement) {
    console.log('viewVersion called with:', versionId, buttonElement);
    
    // Get button element - either passed directly or find it
    const button = buttonElement || document.querySelector(`[onclick*="viewVersion(${versionId})"]`);
    if (button) {
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        }, 500);
    }
    
    // Open version view for all document types
    window.open(`/dtrs/document/version/${versionId}/view`, '_blank');
};

window.downloadVersion = function(versionId, buttonElement) {
    console.log('downloadVersion called with:', versionId, buttonElement);
    
    // Get button element
    const button = buttonElement || document.querySelector(`[onclick*="downloadVersion(${versionId})"]`);
    if (button) {
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        }, 1000);
    }
    
    // Download version for all document types
    window.location.href = `/dtrs/document/version/${versionId}/download`;
};

window.restoreVersion = function(versionId, buttonElement) {
    // Check if this is a vendor document
    if (typeof versionId === 'string' && versionId.startsWith('vendor_')) {
        alert('⚠️ Restore functionality is not available for vendor documents.\n\nVendor documents are managed through the vendor registration system.');
        return;
    }
    
    if (confirm('⚠️ Are you sure you want to restore this version?\n\nThis will create a new version based on the selected one and make it the current version.')) {
        const button = buttonElement || document.querySelector(`[onclick*="restoreVersion(${versionId})"]`);
        let originalContent = '';
        
        if (button) {
            originalContent = button.innerHTML;
            button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
            button.disabled = true;
        }
        
        fetch(`/dtrs/document/version/${versionId}/restore`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.className = 'alert alert-success alert-dismissible fade show position-fixed';
                successMsg.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
                successMsg.innerHTML = `
                    <i class="bi bi-check-circle me-2"></i>
                    <strong>Version Restored!</strong><br>
                    New version: ${data.new_version || 'N/A'}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;
                document.body.appendChild(successMsg);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (successMsg.parentNode) {
                        successMsg.remove();
                    }
                }, 5000);
                
                // Reload version history
                setTimeout(() => {
                    const loadVersionsBtn = document.getElementById('loadVersionsBtn');
                    if (loadVersionsBtn) {
                        loadVersionsBtn.click();
                    }
                }, 1000);
            } else {
                alert('❌ Error restoring version: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('❌ Error restoring version. Please try again.');
        })
        .finally(() => {
            if (button) {
                button.innerHTML = originalContent;
                button.disabled = false;
            }
        });
    }
};

window.compareVersions = function(versionId, buttonElement) {
    // Get button element
    const button = buttonElement || document.querySelector(`[onclick*="compareVersions(${versionId})"]`);
    if (button) {
        const originalContent = button.innerHTML;
        button.innerHTML = '<i class="bi bi-hourglass-split"></i>';
        button.disabled = true;
        
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.disabled = false;
        }, 500);
    }
    
    // Open comparison view for all document types
    window.open(`/dtrs/document/version/${versionId}/compare`, '_blank');
};
</script>

  </main>

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
          
          if (confirm('Are you sure you want to logout?')) {
            // Create a form and submit it to logout
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("logout") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
          }
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
