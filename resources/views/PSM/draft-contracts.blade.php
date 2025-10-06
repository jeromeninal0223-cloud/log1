<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Draft Contracts - PSM</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- PSM Animations -->
  <link rel="stylesheet" href="{{ asset('assets/css/psm-animations.css') }}">
  <style>
    :root {
      --jetlouge-primary: #1a237e;
      --jetlouge-secondary: #3949ab;
      --jetlouge-accent: #7986cb;
    }
    
    body { padding-top: 70px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .navbar { z-index: 1050; }
    .sidebar-toggle { background: none; border: none; color: white; }
    .sidebar-toggle:hover { color: #ddd; }
    
    #sidebar {
      position: fixed;
      top: 70px;
      left: 0;
      height: calc(100vh - 70px);
      width: 280px;
      z-index: 1040;
      transition: all 0.3s ease;
      overflow-y: auto;
    }
    
    #sidebar.collapsed { width: 70px; }
    #main-content { margin-left: 280px; transition: all 0.3s ease; }
    #main-content.expanded { margin-left: 70px; }
    
    @media (max-width: 767.98px) {
      #sidebar { transform: translateX(-100%); }
      #sidebar.active { transform: translateX(0); }
      #main-content { margin-left: 0; }
      #main-content.expanded { margin-left: 0; }
    }
    
    .nav-link { color: #333; border-radius: 8px; margin-bottom: 4px; }
    .nav-link:hover, .nav-link.active { background-color: var(--jetlouge-primary); color: white; }
    .nav-link i { width: 20px; }
    
    .profile-section { border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
    .profile-img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
    
    .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .table th { background-color: #f8f9fa; font-weight: 600; }
    .badge { font-size: 0.75em; }
    
    .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1035; display: none; }
    .overlay.show { display: block; }
  </style>
</head>
<body>
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
           alt="Profile" class="profile-img mb-2">
      <h6 class="mb-1">{{ Auth::user()->name ?? 'Procurement Officer' }}</h6>
      <small class="text-muted">{{ Auth::user()->email ?? 'procurement@company.com' }}</small>
    </div>

    <!-- Navigation Menu -->
    <nav class="nav flex-column">
      <a href="/psm/contract" class="nav-link">
        <i class="bi bi-file-earmark-text me-2"></i>
        <span>Contracts</span>
      </a>
      <a href="/psm/draft-contracts" class="nav-link active">
        <i class="bi bi-file-earmark-plus me-2"></i>
        <span>Draft Contracts</span>
      </a>
      <a href="/psm/contract-approval" class="nav-link">
        <i class="bi bi-check-circle me-2"></i>
        <span>Contract Approval</span>
      </a>
      <a href="/psm/bidding" class="nav-link">
        <i class="bi bi-trophy me-2"></i>
        <span>Bidding</span>
      </a>
      <a href="/psm/vendor" class="nav-link">
        <i class="bi bi-building me-2"></i>
        <span>Vendors</span>
      </a>
      <a href="/psm/order" class="nav-link">
        <i class="bi bi-cart me-2"></i>
        <span>Purchase Orders</span>
      </a>
    </nav>
  </aside>

  <!-- Overlay for mobile -->
  <div class="overlay" id="overlay"></div>

  <!-- Main Content -->
  <main id="main-content">
    <div class="container-fluid p-4">
      <div class="main-content p-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <div>
            <h2 class="fw-bold text-primary mb-1">Draft Contracts</h2>
            <p class="text-muted mb-0">Manage contracts pending signatures</p>
          </div>
          <div class="d-flex align-items-center gap-3">
            <div class="text-end">
              <small class="text-muted d-block">Logged in as:</small>
              <strong class="text-primary">{{ Auth::user()->name ?? 'Procurement Officer' }}</strong>
              <small class="text-muted d-block">{{ Auth::user()->email ?? 'procurement@company.com' }}</small>
            </div>
            <div class="vr"></div>
            <button class="btn btn-outline-primary btn-sm" onclick="location.reload()">
              <i class="bi bi-arrow-clockwise me-1"></i>Refresh
            </button>
          </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="text-muted mb-1">Draft Contracts</h6>
                    <h4 class="mb-0 text-primary" id="draftCount">{{ $contracts->where('workflow_status', 'draft')->count() }}</h4>
                  </div>
                  <div class="text-primary">
                    <i class="bi bi-file-earmark-text fs-2"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="text-muted mb-1">Pending Vendor Signature</h6>
                    <h4 class="mb-0 text-warning" id="vendorPendingCount">{{ $contracts->where('workflow_status', 'pending_vendor_signature')->count() }}</h4>
                  </div>
                  <div class="text-warning">
                    <i class="bi bi-person-check fs-2"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="text-muted mb-1">Pending Procurement Signature</h6>
                    <h4 class="mb-0 text-info" id="procurementPendingCount">{{ $contracts->where('workflow_status', 'pending_procurement_signature')->count() }}</h4>
                  </div>
                  <div class="text-info">
                    <i class="bi bi-building-check fs-2"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex align-items-center">
                  <div class="flex-grow-1">
                    <h6 class="text-muted mb-1">Pending Approval</h6>
                    <h4 class="mb-0 text-success" id="pendingApprovalCount">{{ $contracts->where('workflow_status', 'pending_approval')->count() }}</h4>
                  </div>
                  <div class="text-success">
                    <i class="bi bi-check-circle fs-2"></i>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters -->
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label">Status Filter</label>
                <select class="form-select" id="statusFilter">
                  <option value="">All Statuses</option>
                  <option value="draft">Draft</option>
                  <option value="pending_vendor_signature">Pending Vendor Signature</option>
                  <option value="pending_procurement_signature">Pending Procurement Signature</option>
                  <option value="pending_approval">Pending Approval</option>
                </select>
              </div>
              <div class="col-md-3">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" id="searchInput" placeholder="Search contracts...">
              </div>
              <div class="col-md-3">
                <label class="form-label">Date Range</label>
                <input type="date" class="form-control" id="dateFilter">
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-outline-secondary me-2" onclick="clearFilters()">
                  <i class="bi bi-x-circle me-1"></i>Clear
                </button>
                <button class="btn btn-primary" onclick="applyFilters()">
                  <i class="bi bi-funnel me-1"></i>Filter
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Contracts Table -->
        <div class="card border-0 shadow-sm">
          <div class="card-header bg-light">
            <h5 class="mb-0">Draft Contracts List</h5>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0" id="contractsTable">
                <thead class="bg-light">
                  <tr>
                    <th>Contract #</th>
                    <th>Title</th>
                    <th>Vendor</th>
                    <th>Value</th>
                    <th>Status</th>
                    <th>Signatures</th>
                    <th>Created</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($contracts as $contract)
                  <tr data-contract-id="{{ $contract->id }}" data-status="{{ $contract->workflow_status }}">
                    <td>
                      <strong class="text-primary">{{ $contract->contract_number ?? 'CON-' . str_pad($contract->id, 4, '0', STR_PAD_LEFT) }}</strong>
                    </td>
                    <td>
                      <div>
                        <strong>{{ $contract->title ?? 'Contract for Bid #' . $contract->bid_id }}</strong>
                        @if($contract->description)
                        <br><small class="text-muted">{{ Str::limit($contract->description, 50) }}</small>
                        @endif
                      </div>
                    </td>
                    <td>
                      @if($contract->vendor)
                      <div>
                        <strong>{{ $contract->vendor->company_name ?? $contract->vendor->name }}</strong>
                        <br><small class="text-muted">{{ $contract->vendor->email }}</small>
                      </div>
                      @else
                      <span class="text-muted">No vendor assigned</span>
                      @endif
                    </td>
                    <td>
                      <strong class="text-success">₱{{ number_format($contract->negotiated_value ?? $contract->value ?? 0, 2) }}</strong>
                      @if($contract->negotiated_value && $contract->negotiated_value != $contract->value)
                      <br><small class="text-muted">Original: ₱{{ number_format($contract->value, 2) }}</small>
                      @endif
                    </td>
                    <td>
                      <span class="badge bg-{{ getWorkflowStatusColor($contract->workflow_status) }}">
                        {{ getWorkflowStatusLabel($contract->workflow_status) }}
                      </span>
                    </td>
                    <td>
                      <div class="d-flex gap-1">
                        <span class="badge {{ $contract->vendor_signed_at ? 'bg-success' : 'bg-secondary' }}" title="Vendor Signature">
                          <i class="bi bi-person{{ $contract->vendor_signed_at ? '-check' : '' }}"></i>
                        </span>
                        <span class="badge {{ $contract->procurement_signed_at ? 'bg-success' : 'bg-secondary' }}" title="Procurement Signature">
                          <i class="bi bi-building{{ $contract->procurement_signed_at ? '-check' : '' }}"></i>
                        </span>
                      </div>
                    </td>
                    <td>
                      <small class="text-muted">{{ $contract->created_at->format('M d, Y') }}</small>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-primary" onclick="viewContract({{ $contract->id }})" title="View Contract">
                          <i class="bi bi-eye"></i>
                        </button>
                        @if(!$contract->vendor_signed_at)
                        <button class="btn btn-sm btn-outline-warning" onclick="sendForVendorSignature({{ $contract->id }})" title="Send to Vendor">
                          <i class="bi bi-send"></i>
                        </button>
                        @endif
                        @if($contract->vendor_signed_at && !$contract->procurement_signed_at)
                        <button class="btn btn-sm btn-outline-success" onclick="signAsProcurement({{ $contract->id }})" title="Sign as Procurement">
                          <i class="bi bi-pen"></i>
                        </button>
                        @endif
                        @if($contract->workflow_status === 'pending_approval')
                        <button class="btn btn-sm btn-success" onclick="approveContract({{ $contract->id }})" title="Approve Contract">
                          <i class="bi bi-check-circle"></i>
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="rejectContract({{ $contract->id }})" title="Reject Contract">
                          <i class="bi bi-x-circle"></i>
                        </button>
                        @endif
                        <button class="btn btn-sm btn-outline-secondary" onclick="downloadContract({{ $contract->id }})" title="Download">
                          <i class="bi bi-download"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                  @empty
                  <tr>
                    <td colspan="8" class="text-center py-4">
                      <div class="text-muted">
                        <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
                        <p class="mb-0">No draft contracts found</p>
                      </div>
                    </td>
                  </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        @if($contracts->hasPages())
        <div class="d-flex justify-content-center mt-4">
          {{ $contracts->links() }}
        </div>
        @endif
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Contract View Modal -->
<div class="modal fade" id="contractViewModal" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Contract Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body" id="contractViewContent">
        <!-- Contract details will be loaded here -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary" id="modalActionBtn" style="display: none;">Action</button>
      </div>
    </div>
  </div>
</div>

<!-- Procurement Signing Modal -->
<div class="modal fade" id="procurementSigningModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Sign Contract as Procurement Officer</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6">
            <h6 class="fw-semibold mb-3">Contract Preview</h6>
            <div id="contractPreview" class="border rounded p-3 mb-3" style="max-height: 300px; overflow-y: auto;">
              <!-- Contract preview will be loaded here -->
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="agreeTerms" required>
              <label class="form-check-label" for="agreeTerms">
                I have reviewed and agree to all contract terms and conditions
              </label>
            </div>
            <div class="form-check mb-3">
              <input class="form-check-input" type="checkbox" id="confirmApproval" required>
              <label class="form-check-label" for="confirmApproval">
                I approve this contract on behalf of the procurement department
              </label>
            </div>
          </div>
          <div class="col-md-6">
            <h6 class="fw-semibold mb-3">Digital Signature</h6>
            <div class="border rounded mb-3" style="height: 200px;">
              <canvas id="procurementSignaturePad" style="width: 100%; height: 100%;"></canvas>
            </div>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="clearSignature()">
                <i class="bi bi-arrow-clockwise me-1"></i>Clear
              </button>
              <button type="button" class="btn btn-sm btn-success" onclick="submitSignature()" disabled id="submitSignBtn">
                <i class="bi bi-pen me-1"></i>Sign Contract
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
  let signaturePad;
  let currentContractId;

  // View contract function
  window.viewContract = function(contractId) {
    fetch(`/api/contracts/${contractId}/view`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          displayContractDetails(data.contract);
          new bootstrap.Modal(document.getElementById('contractViewModal')).show();
        } else {
          alert('Error loading contract: ' + (data.error || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error loading contract');
      });
  };

  // Send for vendor signature
  window.sendForVendorSignature = function(contractId) {
    if (confirm('Send this contract to the vendor for signature?')) {
      fetch(`/api/contracts/${contractId}/send-for-vendor-signature`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Contract sent to vendor successfully!');
          location.reload();
        } else {
          alert('Error: ' + (data.error || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error sending contract');
      });
    }
  };

  // Sign as procurement officer
  window.signAsProcurement = function(contractId) {
    currentContractId = contractId;
    loadContractForSigning(contractId);
    new bootstrap.Modal(document.getElementById('procurementSigningModal')).show();
  };

  // Load contract for signing
  function loadContractForSigning(contractId) {
    fetch(`/api/contracts/${contractId}/signing-status`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          const contract = data.contract;
          document.getElementById('contractPreview').innerHTML = `
            <div class="mb-3">
              <h6 class="fw-semibold">Contract ${contract.contract_number || 'N/A'}</h6>
              <p class="mb-2"><strong>Title:</strong> ${contract.title || 'N/A'}</p>
              <p class="mb-2"><strong>Value:</strong> ₱${numberFormat(contract.negotiated_value || contract.value || 0)}</p>
              <p class="mb-2"><strong>Vendor:</strong> ${contract.vendor ? contract.vendor.name : 'N/A'}</p>
              <p class="mb-2"><strong>Status:</strong> <span class="badge bg-info">${(contract.workflow_status || 'draft').replace('_', ' ').toUpperCase()}</span></p>
              <p class="mb-0"><strong>Terms:</strong> ${contract.terms || 'Standard contract terms apply'}</p>
            </div>
          `;
        }
      })
      .catch(error => {
        console.error('Error loading contract:', error);
      });
  }

  // Initialize signature pad when modal is shown
  document.getElementById('procurementSigningModal').addEventListener('shown.bs.modal', function () {
    const canvas = document.getElementById('procurementSignaturePad');
    signaturePad = new SignaturePad(canvas, {
      backgroundColor: 'rgba(255, 255, 255, 0)',
      penColor: 'rgb(0, 0, 0)'
    });
    
    signaturePad.addEventListener('endStroke', function () {
      checkFormValidity();
    });
    
    resizeCanvas();
  });

  function resizeCanvas() {
    const canvas = document.getElementById('procurementSignaturePad');
    const ratio = Math.max(window.devicePixelRatio || 1, 1);
    canvas.width = canvas.offsetWidth * ratio;
    canvas.height = canvas.offsetHeight * ratio;
    canvas.getContext('2d').scale(ratio, ratio);
    signaturePad.clear();
  }

  // Clear signature
  window.clearSignature = function() {
    signaturePad.clear();
    checkFormValidity();
  };

  // Check form validity
  function checkFormValidity() {
    const agreeTerms = document.getElementById('agreeTerms').checked;
    const confirmApproval = document.getElementById('confirmApproval').checked;
    const hasSignature = signaturePad && !signaturePad.isEmpty();
    
    document.getElementById('submitSignBtn').disabled = !(agreeTerms && confirmApproval && hasSignature);
  }

  // Add event listeners for checkboxes
  document.getElementById('agreeTerms').addEventListener('change', checkFormValidity);
  document.getElementById('confirmApproval').addEventListener('change', checkFormValidity);

  // Submit signature
  window.submitSignature = function() {
    if (signaturePad.isEmpty()) {
      alert('Please provide your signature');
      return;
    }

    const signatureData = signaturePad.toDataURL().split(',')[1];
    
    fetch(`/api/contracts/${currentContractId}/procurement-sign`, {
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
        alert('Contract signed successfully! Status updated to pending approval.');
        bootstrap.Modal.getInstance(document.getElementById('procurementSigningModal')).hide();
        location.reload();
      } else {
        alert('Error signing contract: ' + (data.error || data.message || 'Unknown error'));
      }
    })
    .catch(error => {
      console.error('Error signing contract:', error);
      if (error.error) {
        alert('Error signing contract: ' + error.error);
      } else {
        alert('Error signing contract: ' + (error.message || 'Network error'));
      }
    });
  };

  // Download contract
  window.downloadContract = function(contractId) {
    window.open(`/api/contracts/${contractId}/download/draft`, '_blank');
  };

  // Display contract details
  function displayContractDetails(contract) {
    document.getElementById('contractViewContent').innerHTML = `
      <div class="row">
        <div class="col-md-6">
          <h6 class="fw-semibold mb-3">Contract Information</h6>
          <table class="table table-sm">
            <tr><td><strong>Contract Number:</strong></td><td>${contract.contract_number || 'N/A'}</td></tr>
            <tr><td><strong>Title:</strong></td><td>${contract.title || 'N/A'}</td></tr>
            <tr><td><strong>Value:</strong></td><td>₱${numberFormat(contract.negotiated_value || contract.value || 0)}</td></tr>
            <tr><td><strong>Status:</strong></td><td><span class="badge bg-info">${(contract.workflow_status || 'draft').replace('_', ' ').toUpperCase()}</span></td></tr>
            <tr><td><strong>Start Date:</strong></td><td>${formatDate(contract.start_date)}</td></tr>
            <tr><td><strong>End Date:</strong></td><td>${formatDate(contract.end_date)}</td></tr>
          </table>
        </div>
        <div class="col-md-6">
          <h6 class="fw-semibold mb-3">Vendor Information</h6>
          ${contract.vendor ? `
            <table class="table table-sm">
              <tr><td><strong>Company:</strong></td><td>${contract.vendor.name}</td></tr>
              <tr><td><strong>Email:</strong></td><td>${contract.vendor.email}</td></tr>
              <tr><td><strong>Phone:</strong></td><td>${contract.vendor.phone || 'N/A'}</td></tr>
              <tr><td><strong>Address:</strong></td><td>${contract.vendor.address || 'N/A'}</td></tr>
            </table>
          ` : '<p class="text-muted">No vendor information available</p>'}
        </div>
      </div>
      <div class="row mt-4">
        <div class="col-12">
          <h6 class="fw-semibold mb-3">Signature Status</h6>
          <div class="d-flex gap-3">
            <div class="text-center">
              <div class="badge ${contract.vendor_signed ? 'bg-success' : 'bg-secondary'} mb-2">
                <i class="bi bi-person${contract.vendor_signed ? '-check' : ''}"></i>
              </div>
              <div><small>Vendor Signature</small></div>
              <div><small class="text-muted">${contract.vendor_signed_at ? formatDate(contract.vendor_signed_at) : 'Pending'}</small></div>
            </div>
            <div class="text-center">
              <div class="badge ${contract.procurement_signed ? 'bg-success' : 'bg-secondary'} mb-2">
                <i class="bi bi-building${contract.procurement_signed ? '-check' : ''}"></i>
              </div>
              <div><small>Procurement Signature</small></div>
              <div><small class="text-muted">${contract.procurement_signed_at ? formatDate(contract.procurement_signed_at) : 'Pending'}</small></div>
            </div>
          </div>
        </div>
      </div>
      ${contract.terms ? `
        <div class="row mt-4">
          <div class="col-12">
            <h6 class="fw-semibold mb-3">Contract Terms</h6>
            <div class="bg-light p-3 rounded">
              <p class="mb-0">${contract.terms}</p>
            </div>
          </div>
        </div>
      ` : ''}
    `;
  }

  // Filter functions
  window.applyFilters = function() {
    const status = document.getElementById('statusFilter').value;
    const search = document.getElementById('searchInput').value.toLowerCase();
    const date = document.getElementById('dateFilter').value;
    
    const rows = document.querySelectorAll('#contractsTable tbody tr[data-contract-id]');
    
    rows.forEach(row => {
      let show = true;
      
      if (status && row.dataset.status !== status) {
        show = false;
      }
      
      if (search && !row.textContent.toLowerCase().includes(search)) {
        show = false;
      }
      
      row.style.display = show ? '' : 'none';
    });
  };

  window.clearFilters = function() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('searchInput').value = '';
    document.getElementById('dateFilter').value = '';
    applyFilters();
  };

  // Helper functions
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

  // Approval and rejection functions
  window.approveContract = function(contractId) {
    if (confirm('Are you sure you want to approve this contract?')) {
      fetch(`/api/draft-contracts/${contractId}/approve`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Contract approved successfully!');
          location.reload();
        } else {
          alert('Error approving contract: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error approving contract');
      });
    }
  };

  window.rejectContract = function(contractId) {
    const reason = prompt('Please provide a reason for rejection:');
    if (reason && reason.trim()) {
      fetch(`/api/draft-contracts/${contractId}/reject`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          rejection_reason: reason.trim()
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Contract rejected successfully!');
          location.reload();
        } else {
          alert('Error rejecting contract: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error rejecting contract');
      });
    }
  };
});
</script>

<!-- Signature Pad -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>

<script>
// Sidebar functionality
document.addEventListener('DOMContentLoaded', function() {
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
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');

      localStorage.setItem('sidebarCollapsed', !isCollapsed);
      setTimeout(() => {
        window.dispatchEvent(new Event('resize'));
      }, 300);
    });
  }

  // Restore sidebar state
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

  // Handle window resize
  window.addEventListener('resize', () => {
    if (window.innerWidth >= 768) {
      sidebar.classList.remove('active');
      overlay.classList.remove('show');
      document.body.style.overflow = '';
    }
  });
});
</script>

</body>
</html>

@php
function getWorkflowStatusColor($status) {
  return match($status) {
    'draft' => 'secondary',
    'under_negotiation' => 'info',
    'pending_vendor_signature' => 'warning',
    'pending_procurement_signature' => 'primary',
    'pending_approval' => 'success',
    'fully_signed' => 'success',
    default => 'secondary'
  };
}

function getWorkflowStatusLabel($status) {
  return match($status) {
    'draft' => 'Draft',
    'under_negotiation' => 'Under Negotiation',
    'pending_vendor_signature' => 'Pending Vendor Signature',
    'pending_procurement_signature' => 'Pending Procurement Signature',
    'pending_approval' => 'Pending Approval',
    'fully_signed' => 'Fully Signed',
    default => 'Unknown'
  };
}
@endphp
