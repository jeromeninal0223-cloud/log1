<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Contracts - JetLouge Travels</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- Signature Pad -->
  <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

</head>
<body style="background-color: #f8f9fa !important;">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: var(--jetlouge-primary);">
    <div class="container-fluid">
      <button class="sidebar-toggle desktop-toggle me-3" id="desktop-toggle" title="Toggle Sidebar">
        <i class="bi bi-list fs-5"></i>
      </button>
      <a class="navbar-brand fw-bold" href="#">
        <i class="bi bi-building me-2"></i>JetLouge Travels
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
           alt="Vendor Profile" class="profile-img mb-2">
      <h6 class="fw-semibold mb-1">{{ auth('vendor')->user()->name }}</h6>
      <small class="text-muted">{{ auth('vendor')->user()->company_name }}</small>
    </div>

    <!-- Navigation Menu -->
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="{{ route('vendor.dashboard') }}" class="nav-link text-dark">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.bidding.landing') }}" class="nav-link text-dark">
          <i class="bi bi-gavel me-2"></i> Browse Opportunities
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.bids') }}" class="nav-link text-dark">
          <i class="bi bi-file-earmark-text me-2"></i> My Bids
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.contracts') }}" class="nav-link text-dark active">
          <i class="bi bi-file-earmark-check me-2"></i> My Contracts
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.orders') }}" class="nav-link text-dark">
          <i class="bi bi-cart-check me-2"></i> My Orders
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.invoices') }}" class="nav-link text-dark">
          <i class="bi bi-receipt me-2"></i> My Invoices
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.damage.reports') }}" class="nav-link text-dark">
          <i class="bi bi-exclamation-triangle me-2"></i> Damage Reports
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.profile') }}" class="nav-link text-dark">
          <i class="bi bi-person me-2"></i> My Profile
        </a>
      </li>
      <li class="nav-item mt-3">
        <a href="{{ route('vendor.logout') }}" class="nav-link text-danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
      </li>
    </ul>
  </aside>

  <!-- Logout Form -->
  <form id="logout-form" action="{{ route('vendor.logout') }}" method="POST" style="display: none;">
    @csrf
  </form>

  <!-- Overlay for mobile -->
  <div id="overlay" class="position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50" style="z-index:1040; display: none;"></div>

  <!-- Main Content -->
  <main id="main-content">
    <!-- Page Header -->
    <div class="page-header-container mb-4">
      <div class="d-flex justify-content-between align-items-center page-header">
        <div class="d-flex align-items-center">
          <div class="dashboard-logo me-3">
            <i class="bi bi-file-earmark-check fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">My Contracts</h2>
            <p class="text-muted mb-0">Manage your contract negotiations and digital signatures</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Contracts</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Contract Statistics -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-warning bg-opacity-10 text-warning me-3">
                <i class="bi bi-clock-history"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['pending_signature'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Pending Signature</p>
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
                <i class="bi bi-pencil-square"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['under_negotiation'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Under Negotiation</p>
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
                <h3 class="fw-bold mb-0">{{ $stats['fully_signed'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Fully Signed</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="bi bi-currency-dollar"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">₱{{ number_format($stats['total_contract_value'] ?? 0, 0) }}</h3>
                <p class="text-muted mb-0 small">Total Contract Value</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Contracts Table -->
    <div class="card shadow-sm border-0">
      <div class="card-header bg-white border-bottom-0 py-3">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="fw-semibold mb-0">Contract List</h5>
          <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
              <option value="">All Status</option>
              <option value="draft">Draft</option>
              <option value="under_negotiation">Under Negotiation</option>
              <option value="pending_vendor_signature">Pending My Signature</option>
              <option value="pending_procurement_signature">Pending Procurement Signature</option>
              <option value="fully_signed">Fully Signed</option>
            </select>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        @if($contracts->count() > 0)
          <!-- Desktop Table View -->
          <div class="d-none d-lg-block">
            <div class="table-responsive">
              <table class="table table-hover mb-0 modern-contracts-table" id="contractsTable">
                <thead class="contracts-table-header">
                  <tr>
                    <th class="border-0 fw-semibold text-uppercase small">Contract #</th>
                    <th class="border-0 fw-semibold text-uppercase small">Project Details</th>
                    <th class="border-0 fw-semibold text-uppercase small">Original Value</th>
                    <th class="border-0 fw-semibold text-uppercase small">Negotiated Value</th>
                    <th class="border-0 fw-semibold text-uppercase small">Status</th>
                    <th class="border-0 fw-semibold text-uppercase small">Created</th>
                    <th class="border-0 fw-semibold text-uppercase small">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($contracts as $contract)
                  <tr class="contract-row-hover">
                    <td class="align-middle py-3">
                      <div class="contract-number-badge">
                        <span class="fw-bold text-primary">{{ $contract->contract_number }}</span>
                      </div>
                    </td>
                    <td class="align-middle py-3">
                      <div class="contract-info">
                        <h6 class="mb-1 fw-semibold text-dark">{{ $contract->title }}</h6>
                        <small class="text-muted">Bid #{{ $contract->bid->id ?? 'N/A' }}</small>
                      </div>
                    </td>
                    <td class="align-middle py-3">
                      <div class="value-display">
                        <span class="fw-semibold text-info">₱{{ number_format($contract->value, 2) }}</span>
                      </div>
                    </td>
                    <td class="align-middle py-3">
                      <div class="negotiated-value">
                        @if($contract->negotiated_value)
                          <span class="fw-semibold text-success">₱{{ number_format($contract->negotiated_value, 2) }}</span>
                          @if($contract->negotiated_value != $contract->value)
                            <small class="d-block mt-1">
                              @if($contract->negotiated_value < $contract->value)
                                <span class="text-success"><i class="bi bi-arrow-down"></i> {{ number_format((($contract->value - $contract->negotiated_value) / $contract->value) * 100, 1) }}% reduction</span>
                              @else
                                <span class="text-warning"><i class="bi bi-arrow-up"></i> {{ number_format((($contract->negotiated_value - $contract->value) / $contract->value) * 100, 1) }}% increase</span>
                              @endif
                            </small>
                          @endif
                        @else
                          <span class="text-muted small">Not negotiated</span>
                        @endif
                      </div>
                    </td>
                    <td class="align-middle py-3">
                      @php
                        $statusConfig = [
                          'draft' => ['class' => 'contract-status-draft', 'icon' => 'file-earmark', 'text' => 'Draft'],
                          'under_negotiation' => ['class' => 'contract-status-negotiation', 'icon' => 'pencil-square', 'text' => 'Under Negotiation'],
                          'pending_vendor_signature' => ['class' => 'contract-status-pending', 'icon' => 'clock-history', 'text' => 'Pending My Signature'],
                          'pending_procurement_signature' => ['class' => 'contract-status-waiting', 'icon' => 'hourglass-split', 'text' => 'Pending Procurement'],
                          'fully_signed' => ['class' => 'contract-status-signed', 'icon' => 'check-circle', 'text' => 'Fully Signed'],
                          'approved' => ['class' => 'contract-status-signed', 'icon' => 'check-circle-fill', 'text' => 'Approved'],
                          'active' => ['class' => 'contract-status-signed', 'icon' => 'check-circle', 'text' => 'Active'],
                          'completed' => ['class' => 'contract-status-signed', 'icon' => 'check-all', 'text' => 'Completed']
                        ];
                        $status = $statusConfig[strtolower($contract->workflow_status)] ?? $statusConfig[strtolower($contract->status ?? '')] ?? ['class' => 'contract-status-draft', 'icon' => 'file-earmark', 'text' => $contract->workflow_status ?? $contract->status ?? 'Unknown'];
                      @endphp
                      <span class="contract-status-badge {{ $status['class'] }}">
                        <i class="bi bi-{{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                      </span>
                    </td>
                    <td class="align-middle py-3">
                      <div class="date-info">
                        <span class="fw-medium">{{ $contract->created_at->format('M d, Y') }}</span>
                        <small class="text-muted d-block">{{ $contract->created_at->format('h:i A') }}</small>
                      </div>
                    </td>
                    <td class="align-middle py-3">
                      <div class="contract-actions">
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="viewContract({{ $contract->id }})" title="View Details">
                          <i class="bi bi-eye"></i>
                        </button>
                        @if($contract->workflow_status === 'pending_vendor_signature')
                          <button class="btn btn-sm btn-success me-1" onclick="signContract({{ $contract->id }})" title="Sign Contract">
                            <i class="bi bi-pen"></i>
                          </button>
                        @endif
                        @if($contract->draft_document_path || $contract->final_document_path)
                          <button class="btn btn-sm btn-outline-secondary" onclick="downloadContract({{ $contract->id }})" title="Download PDF">
                            <i class="bi bi-download"></i>
                          </button>
                        @endif
                      </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>

          <!-- Mobile Card View -->
          <div class="d-lg-none mobile-contracts">
            @foreach($contracts as $contract)
              @php
                $statusConfig = [
                  'draft' => ['class' => 'contract-status-draft', 'icon' => 'file-earmark', 'text' => 'Draft'],
                  'under_negotiation' => ['class' => 'contract-status-negotiation', 'icon' => 'pencil-square', 'text' => 'Under Negotiation'],
                  'pending_vendor_signature' => ['class' => 'contract-status-pending', 'icon' => 'clock-history', 'text' => 'Pending My Signature'],
                  'pending_procurement_signature' => ['class' => 'contract-status-waiting', 'icon' => 'hourglass-split', 'text' => 'Pending Procurement'],
                  'fully_signed' => ['class' => 'contract-status-signed', 'icon' => 'check-circle', 'text' => 'Fully Signed'],
                  'approved' => ['class' => 'contract-status-signed', 'icon' => 'check-circle-fill', 'text' => 'Approved'],
                  'active' => ['class' => 'contract-status-signed', 'icon' => 'check-circle', 'text' => 'Active'],
                  'completed' => ['class' => 'contract-status-signed', 'icon' => 'check-all', 'text' => 'Completed']
                ];
                $status = $statusConfig[strtolower($contract->workflow_status)] ?? $statusConfig[strtolower($contract->status ?? '')] ?? ['class' => 'contract-status-draft', 'icon' => 'file-earmark', 'text' => $contract->workflow_status ?? $contract->status ?? 'Unknown'];
              @endphp
              <div class="contract-card mb-3 mx-3">
                <div class="card border-0 shadow-sm h-100">
                  <div class="card-body p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-start mb-3">
                      <div class="contract-header">
                        <span class="contract-number fw-bold text-primary">{{ $contract->contract_number }}</span>
                        <span class="contract-status-badge {{ $status['class'] }} ms-2">
                          <i class="bi bi-{{ $status['icon'] }} me-1"></i>{{ $status['text'] }}
                        </span>
                      </div>
                    </div>

                    <!-- Contract Title -->
                    <h6 class="card-title fw-semibold mb-2 text-dark">{{ $contract->title }}</h6>
                    <p class="text-muted small mb-3">Bid #{{ $contract->bid->id ?? 'N/A' }}</p>
                    
                    <!-- Values Grid -->
                    <div class="row g-3 mb-3">
                      <div class="col-6">
                        <div class="contract-detail-item">
                          <small class="text-muted d-block">Original Value</small>
                          <span class="fw-bold text-info">₱{{ number_format($contract->value, 2) }}</span>
                        </div>
                      </div>
                      <div class="col-6">
                        <div class="contract-detail-item">
                          <small class="text-muted d-block">Negotiated Value</small>
                          @if($contract->negotiated_value)
                            <span class="fw-bold text-success">₱{{ number_format($contract->negotiated_value, 2) }}</span>
                            @if($contract->negotiated_value != $contract->value)
                              <small class="d-block mt-1">
                                @if($contract->negotiated_value < $contract->value)
                                  <span class="text-success"><i class="bi bi-arrow-down"></i> {{ number_format((($contract->value - $contract->negotiated_value) / $contract->value) * 100, 1) }}%</span>
                                @else
                                  <span class="text-warning"><i class="bi bi-arrow-up"></i> {{ number_format((($contract->negotiated_value - $contract->value) / $contract->value) * 100, 1) }}%</span>
                                @endif
                              </small>
                            @endif
                          @else
                            <span class="text-muted small">Not negotiated</span>
                          @endif
                        </div>
                      </div>
                      <div class="col-12">
                        <div class="contract-detail-item">
                          <small class="text-muted d-block">Created Date</small>
                          <span class="small">{{ $contract->created_at->format('M d, Y') }} at {{ $contract->created_at->format('h:i A') }}</span>
                        </div>
                      </div>
                    </div>

                    <!-- Actions -->
                    <div class="contract-actions d-flex gap-2 justify-content-center">
                      <button class="btn btn-sm btn-outline-primary" onclick="viewContract({{ $contract->id }})" title="View Details">
                        <i class="bi bi-eye me-1"></i>View
                      </button>
                      @if($contract->workflow_status === 'pending_vendor_signature')
                        <button class="btn btn-sm btn-success" onclick="signContract({{ $contract->id }})" title="Sign Contract">
                          <i class="bi bi-pen me-1"></i>Sign
                        </button>
                      @endif
                      @if($contract->draft_document_path || $contract->final_document_path)
                        <button class="btn btn-sm btn-outline-secondary" onclick="downloadContract({{ $contract->id }})" title="Download PDF">
                          <i class="bi bi-download me-1"></i>Download
                        </button>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="text-center py-5">
            <div class="text-muted">
              <i class="bi bi-file-earmark-check fs-1 d-block mb-3"></i>
              <h6>No contracts found</h6>
              <p class="mb-0">Your contracts will appear here once bids are accepted.</p>
            </div>
          </div>
        @endif
      </div>
    </div>

    <!-- Pagination -->
    @if($contracts->hasPages())
    <div class="d-flex justify-content-center mt-4">
      {{ $contracts->links() }}
    </div>
    @endif
  </main>

  <!-- Contract Details Modal -->
  <div class="modal fade" id="contractModal" tabindex="-1" aria-labelledby="contractModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="contractModalLabel">Contract Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="contractDetails">
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
  <div class="modal fade" id="signingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Digital Contract Signing</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="contractPreview" class="mb-4">
            <!-- Contract preview will be loaded here -->
          </div>
          
          <div class="row">
            <div class="col-md-6">
              <h6 class="fw-semibold mb-3">Contract Terms Agreement</h6>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="agreeTerms" required>
                <label class="form-check-label" for="agreeTerms">
                  I have read and agree to all contract terms and conditions
                </label>
              </div>
              <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="agreeNegotiatedTerms" required>
                <label class="form-check-label" for="agreeNegotiatedTerms">
                  I accept the negotiated price and terms
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <h6 class="fw-semibold mb-3">Digital Signature</h6>
              <div class="border rounded p-3 mb-3" style="height: 200px;">
                <canvas id="signaturePad" class="w-100 h-100"></canvas>
              </div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                  <i class="bi bi-arrow-clockwise me-1"></i>Clear
                </button>
                <button type="button" class="btn btn-sm btn-primary" onclick="submitSignature()" disabled id="submitSignBtn">
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
  
  <script>
    let signaturePad;
    let currentContractId;

    // Helper functions to match PSM contract view
    function getStatusBadgeClass(status) {
      const statusMap = {
        'draft': 'secondary',
        'under_negotiation': 'info',
        'pending_vendor_signature': 'warning',
        'pending_procurement_signature': 'primary',
        'fully_signed': 'success',
        'Active': 'success',
        'Pending': 'warning',
        'Expired': 'danger'
      };
      return statusMap[status] || 'secondary';
    }

    function numberFormat(value) {
      return parseFloat(value).toLocaleString('en-PH', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    }

    function formatDate(dateString) {
      if (!dateString || dateString === null || dateString === 'null') {
        console.log('formatDate received empty/null date:', dateString);
        return 'N/A';
      }
      try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
          console.log('formatDate received invalid date:', dateString);
          return 'N/A';
        }
        return date.toLocaleDateString('en-US', {
          year: 'numeric',
          month: 'short',
          day: 'numeric'
        });
      } catch (error) {
        console.log('formatDate error:', error, 'for date:', dateString);
        return 'N/A';
      }
    }

    function calculateDuration(startDate, endDate) {
      if (!startDate || !endDate || startDate === 'null' || endDate === 'null') {
        console.log('calculateDuration received empty dates:', {startDate, endDate});
        return 'N/A';
      }
      try {
        const start = new Date(startDate);
        const end = new Date(endDate);
        if (isNaN(start.getTime()) || isNaN(end.getTime())) {
          console.log('calculateDuration received invalid dates:', {startDate, endDate});
          return 'N/A';
        }
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 30) {
          return `${diffDays} days`;
        } else if (diffDays < 365) {
          const months = Math.floor(diffDays / 30);
          return `${months} month${months > 1 ? 's' : ''}`;
        } else {
          const years = Math.floor(diffDays / 365);
          const remainingMonths = Math.floor((diffDays % 365) / 30);
          return `${years} year${years > 1 ? 's' : ''}${remainingMonths > 0 ? ` ${remainingMonths} month${remainingMonths > 1 ? 's' : ''}` : ''}`;
        }
      } catch (error) {
        console.log('calculateDuration error:', error);
        return 'N/A';
      }
    }

    // Initialize signature pad when signing modal is shown
    document.getElementById('signingModal').addEventListener('shown.bs.modal', function () {
      const canvas = document.getElementById('signaturePad');
      signaturePad = new SignaturePad(canvas, {
        backgroundColor: 'rgba(255, 255, 255, 0)',
        penColor: 'rgb(0, 0, 0)'
      });
      
      // Enable submit button when signature is drawn
      signaturePad.addEventListener('endStroke', function () {
        checkFormValidity();
      });
      
      // Resize canvas
      resizeCanvas();
    });

    function resizeCanvas() {
      const canvas = document.getElementById('signaturePad');
      const ratio = Math.max(window.devicePixelRatio || 1, 1);
      canvas.width = canvas.offsetWidth * ratio;
      canvas.height = canvas.offsetHeight * ratio;
      canvas.getContext('2d').scale(ratio, ratio);
      signaturePad.clear();
    }

    function clearSignature() {
      signaturePad.clear();
      checkFormValidity();
    }

    function checkFormValidity() {
      const agreeTerms = document.getElementById('agreeTerms').checked;
      const agreeNegotiatedTerms = document.getElementById('agreeNegotiatedTerms').checked;
      const hasSignature = signaturePad && !signaturePad.isEmpty();
      
      document.getElementById('submitSignBtn').disabled = !(agreeTerms && agreeNegotiatedTerms && hasSignature);
    }

    // Add event listeners for checkboxes
    document.getElementById('agreeTerms').addEventListener('change', checkFormValidity);
    document.getElementById('agreeNegotiatedTerms').addEventListener('change', checkFormValidity);

    // Load contract details function
    function loadContractDetails(contractId) {
      const contentDiv = document.getElementById('contractDetails');
      
      // Show loading state
      contentDiv.innerHTML = `
        <div class="text-center py-4">
          <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
          <p class="mt-2">Loading contract details...</p>
        </div>
      `;

      // Fetch contract details using the same endpoint as PSM
      fetch(`/api/contracts/${contractId}/view`, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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

    // Display contract details function (same as PSM)
    function displayContractDetails(contract) {
      const contentDiv = document.getElementById('contractDetails');
      const signButton = document.getElementById('signFromModal');
      
      // Show/hide sign button based on contract status
      if (contract.workflow_status === 'pending_vendor_signature') {
        signButton.style.display = 'inline-block';
        signButton.onclick = function() {
          bootstrap.Modal.getInstance(document.getElementById('contractModal')).hide();
          signContract(contract.id);
        };
      } else {
        signButton.style.display = 'none';
      }
      
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
                  <td class="fw-bold text-success">₱${numberFormat(contract.value)}</td>
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
        termsDiv.innerHTML = contract.terms;
      } else if (termsDiv) {
        termsDiv.innerHTML = '<p class="text-muted">No terms specified</p>';
      }
    }

    let currentContractForView = null;

    function viewContract(contractId) {
      currentContractForView = contractId;
      
      // Update modal title
      document.getElementById('contractModalLabel').textContent = `Contract Details`;
      
      // Show modal
      const modal = new bootstrap.Modal(document.getElementById('contractModal'));
      modal.show();
      
      // Load contract details
      loadContractDetails(contractId);
    }

    // Download from modal functionality
    document.getElementById('downloadFromModal').addEventListener('click', function() {
      if (currentContractForView) {
        downloadContract(currentContractForView);
      }
    });

    function signContract(contractId) {
      currentContractId = contractId;
      
      // Load contract preview
      fetch(`/api/contracts/${contractId}/signing-status`)
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            const contract = data.contract;
            document.getElementById('contractPreview').innerHTML = `
              <div class="bg-light p-3 rounded">
                <h6 class="fw-semibold">Contract ${contract.contract_number}</h6>
                <p class="mb-2"><strong>Contract Number:</strong> ${contract.contract_number}</p>
                <p class="mb-2"><strong>Contract Value:</strong> ₱${numberFormat(contract.negotiated_value || contract.value)}</p>
                <p class="mb-2"><strong>Status:</strong> <span class="badge bg-${getStatusBadgeClass(contract.workflow_status)}">${contract.workflow_status.replace('_', ' ').toUpperCase()}</span></p>
                <p class="mb-0"><strong>Terms:</strong> Please review all terms and conditions before signing.</p>
              </div>
            `;
            
            new bootstrap.Modal(document.getElementById('signingModal')).show();
          }
        });
    }

    function submitSignature() {
      if (signaturePad.isEmpty()) {
        alert('Please provide your signature');
        return;
      }

      const signatureData = signaturePad.toDataURL();
      
      fetch(`/api/contracts/${currentContractId}/vendor-sign`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          signature_data: signatureData,
          agreed_terms: true
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Contract signed successfully!');
          bootstrap.Modal.getInstance(document.getElementById('signingModal')).hide();
          location.reload();
        } else {
          alert('Error signing contract: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error signing contract:', error);
        alert('Error signing contract');
      });
    }

    function downloadContract(contractId) {
      window.open(`/api/contracts/${contractId}/download`, '_blank');
    }

    // Status filter functionality
    document.getElementById('statusFilter').addEventListener('change', function() {
      const status = this.value;
      const rows = document.querySelectorAll('tbody tr');
      
      rows.forEach(row => {
        if (status === '') {
          row.style.display = '';
        } else {
          const statusBadge = row.querySelector('.badge');
          if (statusBadge && statusBadge.textContent.toLowerCase().includes(status.replace('_', ' '))) {
            row.style.display = '';
          } else {
            row.style.display = 'none';
          }
        }
      });
    });

    // Sidebar functionality
    document.getElementById('desktop-toggle').addEventListener('click', function() {
      document.getElementById('sidebar').classList.toggle('collapsed');
      document.getElementById('main-content').classList.toggle('expanded');
    });

    document.getElementById('menu-btn').addEventListener('click', function() {
      document.getElementById('sidebar').classList.add('show');
      document.getElementById('overlay').style.display = 'block';
    });

    document.getElementById('overlay').addEventListener('click', function() {
      document.getElementById('sidebar').classList.remove('show');
      this.style.display = 'none';
    });
  </script>

  <style>
    /* Enhanced Modern Contracts Table Styling */
    .modern-contracts-table {
      border-radius: 0;
      overflow: hidden;
    }
    
    .contracts-table-header {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 3px solid #dee2e6;
    }
    
    .contracts-table-header th {
      font-weight: 600;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      color: #495057;
      padding: 16px 12px;
      border: none;
      position: relative;
    }
    
    .modern-contracts-table tbody tr {
      border-bottom: 1px solid #f1f3f4;
      transition: all 0.2s ease;
    }
    
    .contract-row-hover:hover {
      background-color: #f8f9fa;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .modern-contracts-table td {
      vertical-align: middle;
      padding: 16px 12px;
      border: none;
    }
    
    /* Contract Number Badge */
    .contract-number-badge {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
      border-radius: 20px;
      border: 1px solid #e1f5fe;
    }
    
    /* Contract Info Styling */
    .contract-info h6 {
      font-size: 0.95rem;
      line-height: 1.4;
      margin-bottom: 4px;
    }
    
    /* Value Display */
    .value-display, .negotiated-value {
      font-family: 'Segoe UI', system-ui, sans-serif;
    }
    
    /* Contract Status Badges */
    .contract-status-badge {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      border-radius: 20px;
      font-size: 0.75rem;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      border: 2px solid transparent;
    }
    
    .contract-status-draft {
      background: linear-gradient(135deg, #e2e3e5 0%, #ced4da 100%);
      color: #383d41;
      border-color: #ced4da;
    }
    
    .contract-status-negotiation {
      background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
      color: #0c5460;
      border-color: #bee5eb;
    }
    
    .contract-status-pending {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      color: #856404;
      border-color: #ffeaa7;
    }
    
    .contract-status-waiting {
      background: linear-gradient(135deg, #cce7ff 0%, #b3d9ff 100%);
      color: #004085;
      border-color: #b3d9ff;
    }
    
    .contract-status-signed {
      background: linear-gradient(135deg, #d4edda 0%, #a8e6cf 100%);
      color: #155724;
      border-color: #a8e6cf;
    }
    
    /* Contract Actions */
    .contract-actions .btn {
      border-radius: 8px;
      transition: all 0.2s ease;
    }
    
    .contract-actions .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    /* Mobile Contract Cards */
    .mobile-contracts {
      padding: 0;
    }
    
    .contract-card {
      transition: all 0.3s ease;
    }
    
    .contract-card:hover {
      transform: translateY(-2px);
    }
    
    .contract-card .card {
      border-radius: 16px;
      overflow: hidden;
      background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .contract-card .card-body {
      position: relative;
    }
    
    .contract-card .card-body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #007bff, #28a745, #17a2b8);
      border-radius: 16px 16px 0 0;
    }
    
    .contract-header {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }
    
    .contract-number {
      font-size: 0.9rem;
      padding: 4px 10px;
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
      border-radius: 12px;
      border: 1px solid #e1f5fe;
    }
    
    .contract-detail-item {
      padding: 8px 0;
    }
    
    .contract-detail-item small {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }
    
    /* Date Info */
    .date-info {
      text-align: left;
    }
    
    /* Responsive Improvements */
    @media (max-width: 768px) {
      .page-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
      }
      
      .page-header .breadcrumb {
        margin-top: 0.5rem;
      }
      
      .card-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
      }
      
      .card-header .d-flex {
        width: 100%;
        justify-content: space-between;
      }
      
      .mobile-contracts .contract-card {
        margin-left: 1rem !important;
        margin-right: 1rem !important;
      }
    }
    
    @media (max-width: 576px) {
      .contract-header {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .contract-actions {
        margin-top: 8px;
        flex-wrap: wrap;
      }
      
      .contract-detail-item {
        text-align: center;
      }
    }
    
    /* Progress Steps Styling */
    .progress-steps .step {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }
    .progress-steps .step i {
      margin-right: 8px;
      font-size: 1.2em;
    }
    .progress-steps .step.completed span {
      color: #198754;
      font-weight: 500;
    }
    
    /* Signature Pad */
    #signaturePad {
      border: 1px dashed #dee2e6;
      cursor: crosshair;
    }
  </style>

</body>
</html>
