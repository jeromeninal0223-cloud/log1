<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Purchase Request Approval</title>
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
  <style>
    body { background-color: #f8f9fa; }
    .approval-card { border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    .status-pending { background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%); color: white; }
    .status-approved { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; }
    .status-rejected { background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); color: white; }
    .btn-approve { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none; }
    .btn-reject { background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%); border: none; }
    .btn-approve:hover, .btn-reject:hover { transform: translateY(-2px); }
    .request-item { transition: all 0.3s ease; }
    .request-item:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0,0,0,0.15); }
  </style>
</head>
<body>

<div class="container-fluid py-4">
  <!-- Header -->
  <div class="row mb-4">
    <div class="col-12">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="fw-bold mb-1">
            <i class="bi bi-clipboard-check text-primary me-2"></i>
            Purchase Request Approval
          </h2>
          <p class="text-muted mb-0">Review and approve submitted purchase requests</p>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ url('/psm/request') }}">PSM</a></li>
            <li class="breadcrumb-item active">Purchase Request Approval</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card approval-card border-0">
        <div class="card-body text-center">
          <div class="text-warning mb-2"><i class="bi bi-clock-history fs-1"></i></div>
          <h4 class="fw-bold">{{ $stats['pending'] ?? 0 }}</h4>
          <small class="text-muted">Pending Approval</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card approval-card border-0">
        <div class="card-body text-center">
          <div class="text-success mb-2"><i class="bi bi-check-circle fs-1"></i></div>
          <h4 class="fw-bold">{{ $stats['approved'] ?? 0 }}</h4>
          <small class="text-muted">Approved</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card approval-card border-0">
        <div class="card-body text-center">
          <div class="text-danger mb-2"><i class="bi bi-x-circle fs-1"></i></div>
          <h4 class="fw-bold">{{ $stats['rejected'] ?? 0 }}</h4>
          <small class="text-muted">Rejected</small>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card approval-card border-0">
        <div class="card-body text-center">
          <div class="text-info mb-2"><i class="bi bi-currency-dollar fs-1"></i></div>
          <h4 class="fw-bold">₱{{ number_format($stats['total_value'] ?? 0) }}</h4>
          <small class="text-muted">Total Value</small>
        </div>
      </div>
    </div>
  </div>

  <!-- Purchase Requests Table -->
  <div class="row">
    <div class="col-12">
      <div class="card approval-card border-0">
        <div class="card-header bg-white border-bottom">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Submitted Purchase Requests</h5>
            <div class="d-flex gap-2">
              <a href="{{ url('/psm/request') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>New Request
              </a>
              <select class="form-select form-select-sm" id="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="rejected">Rejected</option>
              </select>
            </div>
          </div>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover mb-0">
              <thead class="table-light">
                <tr>
                  <th>Request ID</th>
                  <th>Requestor</th>
                  <th>Department</th>
                  <th>Items</th>
                  <th>Amount</th>
                  <th>Date Submitted</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($requests ?? [] as $request)
                  @php
                    $statusClass = match($request->status) {
                      'Pending' => 'pending',
                      'Approved' => 'approved', 
                      'Rejected' => 'rejected',
                      default => 'pending'
                    };
                  @endphp
                  <tr class="request-item" data-status="{{ $statusClass }}">
                    <td><strong>{{ $request->request_number }}</strong></td>
                    <td>{{ $request->requestedBy->name ?? 'Unknown' }}</td>
                    <td>{{ $request->category }}</td>
                    <td>{{ $request->item_description }}</td>
                    <td><strong>₱{{ number_format($request->estimated_cost, 2) }}</strong></td>
                    <td>{{ $request->created_at->format('M d, Y') }}</td>
                    <td><span class="badge status-{{ $statusClass }} px-3 py-2">{{ $request->status }}</span></td>
                    <td>
                      @if($request->status === 'Pending')
                        <button class="btn btn-approve btn-sm text-white me-1" onclick="approveRequest({{ $request->id }})">
                          <i class="bi bi-check-lg"></i> Approve
                        </button>
                        <button class="btn btn-reject btn-sm text-white" onclick="rejectRequest({{ $request->id }})">
                          <i class="bi bi-x-lg"></i> Reject
                        </button>
                      @elseif($request->status === 'Approved')
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                          <i class="bi bi-check-circle"></i> Approved
                        </button>
                      @else
                        <button class="btn btn-outline-secondary btn-sm" disabled>
                          <i class="bi bi-x-circle"></i> Rejected
                        </button>
                      @endif
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="8" class="text-center py-4">
                      <i class="bi bi-inbox text-muted fs-1 mb-2"></i>
                      <h6 class="text-muted">No purchase requests found</h6>
                      <p class="text-muted small mb-0">Submit a new request to see it here.</p>
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
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Approve Request Function
function approveRequest(requestId) {
  Swal.fire({
    title: 'Approve Purchase Request?',
    text: `Are you sure you want to approve this request?`,
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#28a745',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, Approve',
    cancelButtonText: 'Cancel'
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: 'Processing...',
        text: 'Approving purchase request',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });
      
      // Make actual API call
      fetch('{{ route("psm.request.approve-item") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          request_id: requestId
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: 'Approved!',
            text: data.message,
            icon: 'success',
            confirmButtonColor: '#28a745'
          }).then(() => {
            location.reload(); // Reload page to show updated data
          });
        } else {
          throw new Error(data.message || 'Failed to approve request');
        }
      })
      .catch(error => {
        Swal.fire({
          title: 'Error!',
          text: 'Failed to approve request: ' + error.message,
          icon: 'error',
          confirmButtonColor: '#dc3545'
        });
      });
    }
  });
}

// Reject Request Function
function rejectRequest(requestId) {
  Swal.fire({
    title: 'Reject Purchase Request?',
    text: 'Please provide a reason for rejection:',
    input: 'textarea',
    inputPlaceholder: 'Enter rejection reason...',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'Reject Request',
    cancelButtonText: 'Cancel',
    inputValidator: (value) => {
      if (!value) {
        return 'Please provide a reason for rejection!';
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // Show loading
      Swal.fire({
        title: 'Processing...',
        text: 'Rejecting purchase request',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
      });
      
      // Make actual API call
      fetch('{{ route("psm.request.reject-item") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          request_id: requestId,
          reason: result.value
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: 'Rejected!',
            text: data.message,
            icon: 'success',
            confirmButtonColor: '#dc3545'
          }).then(() => {
            location.reload(); // Reload page to show updated data
          });
        } else {
          throw new Error(data.message || 'Failed to reject request');
        }
      })
      .catch(error => {
        Swal.fire({
          title: 'Error!',
          text: 'Failed to reject request: ' + error.message,
          icon: 'error',
          confirmButtonColor: '#dc3545'
        });
      });
    }
  });
}

// Update Request Status in UI
function updateRequestStatus(requestId, status) {
  const row = document.querySelector(`tr:has(td:contains('${requestId}'))`);
  if (row) {
    const statusBadge = row.querySelector('.badge');
    const actionCell = row.querySelector('td:last-child');
    
    // Update status badge
    statusBadge.className = `badge status-${status} px-3 py-2`;
    statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
    
    // Update action buttons
    if (status === 'approved') {
      actionCell.innerHTML = '<button class="btn btn-outline-secondary btn-sm" disabled><i class="bi bi-check-circle"></i> Approved</button>';
    } else if (status === 'rejected') {
      actionCell.innerHTML = '<button class="btn btn-outline-secondary btn-sm" disabled><i class="bi bi-x-circle"></i> Rejected</button>';
    }
    
    // Update row data attribute
    row.setAttribute('data-status', status);
  }
}

// Status Filter
document.getElementById('statusFilter').addEventListener('change', function() {
  const filterValue = this.value;
  const rows = document.querySelectorAll('.request-item');
  
  rows.forEach(row => {
    if (filterValue === '' || row.getAttribute('data-status') === filterValue) {
      row.style.display = '';
    } else {
      row.style.display = 'none';
    }
  });
});
</script>

</body>
</html>
