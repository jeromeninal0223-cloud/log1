<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Contract Approval - PSM</title>
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- PSM Animations -->
  <link rel="stylesheet" href="{{ asset('assets/css/psm-animations.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    :root { --jetlouge-primary: #1a237e; }
    body { padding-top: 70px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
    .navbar { z-index: 1050; }
    .sidebar-toggle { background: none; border: none; color: white; }
    #sidebar { position: fixed; top: 70px; left: 0; height: calc(100vh - 70px); width: 280px; z-index: 1040; transition: all 0.3s ease; overflow-y: auto; }
    #sidebar.collapsed { width: 70px; }
    #main-content { margin-left: 280px; transition: all 0.3s ease; }
    #main-content.expanded { margin-left: 70px; }
    @media (max-width: 767.98px) {
      #sidebar { transform: translateX(-100%); }
      #sidebar.active { transform: translateX(0); }
      #main-content { margin-left: 0; }
    }
    .nav-link { color: #333; border-radius: 8px; margin-bottom: 4px; }
    .nav-link:hover, .nav-link.active { background-color: var(--jetlouge-primary); color: white; }
    .profile-section { border-bottom: 1px solid #eee; padding-bottom: 20px; margin-bottom: 20px; }
    .profile-img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; }
    .card { border: none; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    .approval-card { border-left: 4px solid var(--jetlouge-primary); transition: all 0.3s ease; }
    .approval-card:hover { transform: translateY(-2px); box-shadow: 0 4px 20px rgba(0,0,0,0.15); }
    .signature-badge { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2em; }
    .overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1035; display: none; }
    .overlay.show { display: block; }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: var(--jetlouge-primary);">
    <div class="container-fluid">
      <button class="sidebar-toggle desktop-toggle me-3" id="desktop-toggle">
        <i class="bi bi-list fs-5"></i>
      </button>
      <a class="navbar-brand fw-bold" href="#"><i class="bi bi-airplane me-2"></i>Jetlouge Travels</a>
      <button class="sidebar-toggle mobile-toggle" id="menu-btn">
        <i class="bi bi-list fs-5"></i>
      </button>
    </div>
  </nav>

  <!-- Sidebar -->
  <aside id="sidebar" class="bg-white border-end p-3 shadow-sm">
    <div class="profile-section text-center">
      <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face" alt="Profile" class="profile-img mb-2">
      <h6 class="mb-1">{{ Auth::user()->name ?? 'Procurement Officer' }}</h6>
      <small class="text-muted">{{ Auth::user()->email ?? 'procurement@company.com' }}</small>
    </div>
    <nav class="nav flex-column">
      <a href="/psm/contract" class="nav-link">
        <i class="bi bi-file-earmark-text me-2"></i>
        <span>Contracts</span>
      </a>
      <a href="/psm/draft-contracts" class="nav-link">
        <i class="bi bi-file-earmark-plus me-2"></i>
        <span>Draft Contracts</span>
      </a>
      <a href="/psm/contract-approval" class="nav-link active">
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

  <div class="overlay" id="overlay"></div>

  <!-- Main Content -->
  <main id="main-content">
    <div class="container-fluid p-4">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 class="fw-bold text-primary mb-1">Contract Approval</h2>
          <p class="text-muted mb-0">Review and approve fully signed contracts</p>
        </div>
        <div class="text-end">
          <small class="text-muted d-block">Approval Officer:</small>
          <strong class="text-primary">{{ Auth::user()->name ?? 'Procurement Officer' }}</strong>
        </div>
      </div>

      <!-- Stats -->
      <div class="row mb-4">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                  <h6 class="text-muted mb-1">Pending Approval</h6>
                  <h4 class="mb-0 text-warning">{{ $contracts->where('workflow_status', 'pending_approval')->count() }}</h4>
                </div>
                <div class="text-warning"><i class="bi bi-clock-history fs-2"></i></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                  <h6 class="text-muted mb-1">Approved Today</h6>
                  <h4 class="mb-0 text-success">{{ $contracts->where('workflow_status', 'approved')->where('approved_at', '>=', today())->count() }}</h4>
                </div>
                <div class="text-success"><i class="bi bi-check-circle fs-2"></i></div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <div class="d-flex align-items-center">
                <div class="flex-grow-1">
                  <h6 class="text-muted mb-1">Total Value Pending</h6>
                  <h4 class="mb-0 text-info">₱{{ number_format($contracts->where('workflow_status', 'pending_approval')->sum('negotiated_value'), 2) }}</h4>
                </div>
                <div class="text-info"><i class="bi bi-currency-dollar fs-2"></i></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Contracts -->
      <div class="row">
        @forelse($contracts->where('workflow_status', 'pending_approval') as $contract)
        <div class="col-md-6 col-lg-4 mb-4">
          <div class="card approval-card h-100">
            <div class="card-header bg-light d-flex justify-content-between">
              <h6 class="mb-0 fw-bold">{{ $contract->contract_number ?? 'CON-' . str_pad($contract->id, 4, '0', STR_PAD_LEFT) }}</h6>
              <span class="badge bg-warning">Pending Approval</span>
            </div>
            <div class="card-body">
              <h6 class="card-title">{{ $contract->title ?? 'Contract for Bid #' . $contract->bid_id }}</h6>
              <div class="mb-3">
                <div class="row text-sm">
                  <div class="col-6">
                    <small class="text-muted">Value:</small>
                    <div class="fw-bold text-success">₱{{ number_format($contract->negotiated_value ?? $contract->value ?? 0, 2) }}</div>
                  </div>
                  <div class="col-6">
                    <small class="text-muted">Vendor:</small>
                    <div class="fw-bold">{{ $contract->vendor->company_name ?? $contract->vendor->name ?? 'N/A' }}</div>
                  </div>
                </div>
              </div>
              <div class="mb-3">
                <small class="text-muted d-block mb-2">Signatures:</small>
                <div class="d-flex gap-2 align-items-center">
                  <div class="signature-badge bg-success text-white"><i class="bi bi-person-check"></i></div>
                  <div class="signature-badge bg-success text-white"><i class="bi bi-building-check"></i></div>
                  <small class="text-muted">Both parties signed</small>
                </div>
              </div>
            </div>
            <div class="card-footer bg-transparent">
              <div class="d-flex gap-2">
                <button class="btn btn-outline-primary btn-sm flex-fill" onclick="viewContract({{ $contract->id }})">
                  <i class="bi bi-diagram-3 me-2"></i> Project Planning view
                </button>
                <button class="btn btn-success btn-sm flex-fill" onclick="approveContract({{ $contract->id }})">
                  <i class="bi bi-check-lg me-1"></i> Approve
                </button>
                <button class="btn btn-outline-danger btn-sm" onclick="rejectContract({{ $contract->id }})">
                  <i class="bi bi-x-lg"></i>
                </button>
              </div>
            </div>
          </div>
        </div>
        @empty
        <div class="col-12">
          <div class="card">
            <div class="card-body text-center py-5">
              <i class="bi bi-check-circle text-muted" style="font-size: 3rem;"></i>
              <h5 class="mt-3 text-muted">No Contracts Pending Approval</h5>
              <p class="text-muted">All contracts have been processed.</p>
            </div>
          </div>
        </div>
        @endforelse
      </div>
    </div>
  </main>

  <!-- Modals -->
  <div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Approve Contract</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info">You are about to approve this contract.</div>
          <div class="mb-3">
            <label class="form-label">Approval Notes</label>
            <textarea class="form-control" id="approvalNotes" rows="3"></textarea>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="confirmApproval" required>
            <label class="form-check-label" for="confirmApproval">I confirm approval</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success" onclick="submitApproval()" disabled id="submitApprovalBtn">Approve</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = '{{ csrf_token() }}';
    let currentContractId;

    window.viewContract = function(id) {
      window.open(`/api/contracts/${id}/view`, '_blank');
    };

    window.approveContract = function(id) {
      currentContractId = id;
      new bootstrap.Modal(document.getElementById('approvalModal')).show();
    };

    window.rejectContract = function(id) {
      const reason = prompt('Rejection reason:');
      if (reason) {
        fetch(`/api/draft-contracts/${id}/reject`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
          body: JSON.stringify({ rejection_reason: reason })
        }).then(r => r.json()).then(d => {
          if (d.success) { alert('Contract rejected!'); location.reload(); }
          else alert('Error: ' + d.error);
        });
      }
    };

    window.submitApproval = function() {
      const notes = document.getElementById('approvalNotes').value;
      fetch(`/api/draft-contracts/${currentContractId}/approve`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify({ approval_notes: notes })
      }).then(r => r.json()).then(d => {
        if (d.success) { alert('Contract approved!'); location.reload(); }
        else alert('Error: ' + d.error);
      });
    };

    document.getElementById('confirmApproval').addEventListener('change', function() {
      document.getElementById('submitApprovalBtn').disabled = !this.checked;
    });

    // Sidebar functionality
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('main-content');
    const overlay = document.getElementById('overlay');
    
    document.getElementById('desktop-toggle')?.addEventListener('click', () => {
      sidebar.classList.toggle('collapsed');
      mainContent.classList.toggle('expanded');
    });
    
    document.getElementById('menu-btn')?.addEventListener('click', () => {
      sidebar.classList.toggle('active');
      overlay.classList.toggle('show');
    });
    
    overlay?.addEventListener('click', () => {
      sidebar.classList.remove('active');
      overlay.classList.remove('show');
    });
  });
  </script>
</body>
</html>
