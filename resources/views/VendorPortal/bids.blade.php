<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Bids - JetLouge Travels</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        <a href="{{ route('vendor.bids') }}" class="nav-link text-dark active">
          <i class="bi bi-file-earmark-text me-2"></i> My Bids
        </a>
      </li>
      <li class="nav-item">
        <a href="{{ route('vendor.contracts') }}" class="nav-link text-dark">
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
            <i class="bi bi-file-earmark-text fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">My Bids</h2>
            <p class="text-muted mb-0">Track and manage your submitted bids and proposals.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Bids</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Bid Statistics -->
    <div class="row g-4 mb-4">
      <div class="col-md-3">
        <div class="card stat-card shadow-sm border-0">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="stat-icon bg-primary bg-opacity-10 text-primary me-3">
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $bids->count() }}</h3>
                <p class="text-muted mb-0 small">Total Bids</p>
                <small class="text-primary"><i class="bi bi-arrow-up"></i> All time</small>
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
                <i class="bi bi-clock"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $bids->whereIn('status', ['Under Review', 'Pending Evaluation'])->count() }}</h3>
                <p class="text-muted mb-0 small">In Review</p>
                <small class="text-warning"><i class="bi bi-clock"></i> Being Evaluated</small>
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
                <h3 class="fw-bold mb-0">{{ $bids->where('status', 'Won')->count() }}</h3>
                <p class="text-muted mb-0 small">Won Bids</p>
                <small class="text-success"><i class="bi bi-check"></i> Successful</small>
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
                <h3 class="fw-bold mb-0">₱{{ number_format($bids->sum('amount')) }}</h3>
                <p class="text-muted mb-0 small">Total Value</p>
                <small class="text-info"><i class="bi bi-graph-up"></i> Combined</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bids Content -->
    <div class="row g-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">My Submitted Bids</h5>
            <div class="d-flex gap-2">
              <select class="form-select form-select-sm" id="statusFilter" style="width: auto;">
                <option value="">All Status</option>
                <option value="Under Review">Under Review</option>
                <option value="Pending Evaluation">Pending Evaluation</option>
                <option value="Won">Won</option>
                <option value="Rejected">Rejected</option>
                <option value="Lost">Lost</option>
                <option value="Withdrawn">Withdrawn</option>
              </select>
              <a href="{{ route('vendor.bidding.landing') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Submit New Bid
              </a>
            </div>
          </div>
          <div class="card-body p-0">
            @if($bids->count() > 0)
              <!-- Desktop Table View -->
              <div class="d-none d-lg-block">
                <div class="table-responsive">
                  <table class="table table-hover mb-0 modern-table" id="bidsTable">
                    <thead class="table-header">
                      <tr>
                        <th class="border-0 fw-semibold text-uppercase small">Bid ID</th>
                        <th class="border-0 fw-semibold text-uppercase small">Project Details</th>
                        <th class="border-0 fw-semibold text-uppercase small">Category</th>
                        <th class="border-0 fw-semibold text-uppercase small">Amount</th>
                        <th class="border-0 fw-semibold text-uppercase small">Completion</th>
                        <th class="border-0 fw-semibold text-uppercase small">Status</th>
                        <th class="border-0 fw-semibold text-uppercase small">Submitted</th>
                        <th class="border-0 fw-semibold text-uppercase small">Actions</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($bids as $bid)
                      <tr class="table-row-hover">
                        <td class="align-middle py-3">
                          <div class="bid-id-badge">
                            <span class="fw-bold text-primary">{{ $bid->id ?? 'BID-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <div class="project-info">
                            <h6 class="mb-1 fw-semibold text-dark">{{ $bid->opportunity ? $bid->opportunity->title : ($bid->title ?? 'Sample Project ' . $loop->iteration) }}</h6>
                            <p class="mb-1 text-muted small">{{ Str::limit($bid->description ?? 'Project description here', 60) }}</p>
                            @if($bid->attachments && count($bid->attachments) > 0)
                              <small class="text-info d-flex align-items-center">
                                <i class="bi bi-paperclip me-1"></i> {{ count($bid->attachments) }} attachment(s)
                              </small>
                            @endif
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <span class="category-badge">{{ $bid->category ?? 'Logistics & Transportation' }}</span>
                        </td>
                        <td class="align-middle py-3">
                          <div class="amount-display">
                            <span class="fw-bold fs-6 text-success">₱{{ number_format($bid->amount ?? rand(50000, 500000)) }}</span>
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          <div class="completion-date">
                            @if($bid->completion_date)
                              <span class="text-muted small">{{ $bid->completion_date->format('M d, Y') }}</span>
                            @else
                              <span class="text-muted small">Not specified</span>
                            @endif
                          </div>
                        </td>
                        <td class="align-middle py-3">
                          @php
                            $status = $bid->status ?? 'Under Review';
                            $statusClass = [
                              'Under Review' => 'status-warning',
                              'Pending Evaluation' => 'status-info',
                              'Won' => 'status-success',
                              'Rejected' => 'status-danger',
                              'Lost' => 'status-danger',
                              'Withdrawn' => 'status-secondary'
                            ][$status] ?? 'status-secondary';
                          @endphp
                          <span class="status-badge {{ $statusClass }}">{{ $status }}</span>
                        </td>
                        <td class="align-middle py-3">
                          <span class="text-muted small">{{ $bid->submitted_at ?? now()->subDays(rand(1, 30))->format('M d, Y') }}</span>
                        </td>
                        <td class="align-middle py-3">
                          <div class="action-buttons">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-view-bid me-1" data-bid-id="{{ $bid->id }}" data-show-url="{{ route('vendor.api.bids.show', ['id' => $bid->id]) }}" title="View Details">
                              <i class="bi bi-eye"></i>
                            </button>
                            @if(($bid->status ?? 'Under Review') === 'Under Review')
                              <button class="btn btn-sm btn-outline-warning" onclick="withdrawBid({{ $bid->id }})" title="Withdraw">
                                <i class="bi bi-x-circle"></i>
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
              <div class="d-lg-none mobile-cards">
                @foreach($bids as $bid)
                  @php
                    $status = $bid->status ?? 'Under Review';
                    $statusClass = [
                      'Under Review' => 'status-warning',
                      'Pending Evaluation' => 'status-info',
                      'Won' => 'status-success',
                      'Rejected' => 'status-danger',
                      'Lost' => 'status-danger',
                      'Withdrawn' => 'status-secondary'
                    ][$status] ?? 'status-secondary';
                  @endphp
                  <div class="bid-card mb-3 mx-3">
                    <div class="card border-0 shadow-sm h-100">
                      <div class="card-body p-4">
                        <!-- Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <div class="bid-header">
                            <span class="bid-id fw-bold text-primary">{{ $bid->id ?? 'BID-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</span>
                            <span class="status-badge {{ $statusClass }} ms-2">{{ $status }}</span>
                          </div>
                          <div class="action-buttons">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-view-bid me-1" data-bid-id="{{ $bid->id }}" data-show-url="{{ route('vendor.api.bids.show', ['id' => $bid->id]) }}" title="View Details">
                              <i class="bi bi-eye"></i>
                            </button>
                            @if(($bid->status ?? 'Under Review') === 'Under Review')
                              <button class="btn btn-sm btn-outline-warning" onclick="withdrawBid({{ $bid->id }})" title="Withdraw">
                                <i class="bi bi-x-circle"></i>
                              </button>
                            @endif
                          </div>
                        </div>

                        <!-- Project Title -->
                        <h6 class="card-title fw-semibold mb-2 text-dark">{{ $bid->opportunity ? $bid->opportunity->title : ($bid->title ?? 'Sample Project ' . $loop->iteration) }}</h6>
                        
                        <!-- Description -->
                        <p class="card-text text-muted small mb-3">{{ Str::limit($bid->description ?? 'Project description here', 80) }}</p>
                        
                        <!-- Details Grid -->
                        <div class="row g-3 mb-3">
                          <div class="col-6">
                            <div class="detail-item">
                              <small class="text-muted d-block">Amount</small>
                              <span class="fw-bold text-success">₱{{ number_format($bid->amount ?? rand(50000, 500000)) }}</span>
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="detail-item">
                              <small class="text-muted d-block">Category</small>
                              <span class="small">{{ $bid->category ?? 'Logistics & Transportation' }}</span>
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="detail-item">
                              <small class="text-muted d-block">Completion</small>
                              <span class="small">
                                @if($bid->completion_date)
                                  {{ $bid->completion_date->format('M d, Y') }}
                                @else
                                  Not specified
                                @endif
                              </span>
                            </div>
                          </div>
                          <div class="col-6">
                            <div class="detail-item">
                              <small class="text-muted d-block">Submitted</small>
                              <span class="small">{{ $bid->submitted_at ?? now()->subDays(rand(1, 30))->format('M d, Y') }}</span>
                            </div>
                          </div>
                        </div>

                        <!-- Attachments -->
                        @if($bid->attachments && count($bid->attachments) > 0)
                          <div class="attachments-info">
                            <small class="text-info d-flex align-items-center">
                              <i class="bi bi-paperclip me-1"></i> {{ count($bid->attachments) }} attachment(s)
                            </small>
                          </div>
                        @endif
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @else
              <div class="text-center py-5">
                <i class="bi bi-file-earmark-text fa-3x text-muted mb-3"></i>
                <h5>No Bids Submitted Yet</h5>
                <p class="text-muted">You haven't submitted any bids yet. Browse available opportunities and submit your first bid!</p>
                <a href="{{ route('vendor.bidding.landing') }}" class="btn btn-primary">
                  <i class="bi bi-search me-2"></i>Browse Opportunities
                </a>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Custom CSS for Timeline -->
  <style>
    .timeline {
      position: relative;
      padding-left: 30px;
    }
    
    .timeline-item {
      position: relative;
      padding-bottom: 20px;
    }
    
    .timeline-item:not(:last-child)::before {
      content: '';
      position: absolute;
      left: -22px;
      top: 20px;
      width: 2px;
      height: calc(100% - 10px);
      background-color: #dee2e6;
    }
    
    .timeline-marker {
      position: absolute;
      left: -27px;
      top: 5px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      border: 2px solid white;
      box-shadow: 0 0 0 2px #dee2e6;
    }
    
    .timeline-content h6 {
      margin-bottom: 4px;
      font-size: 0.9rem;
    }
    
    .timeline-content p {
      font-size: 0.8rem;
      margin-bottom: 0;
    }
    
    /* Enhanced Modern Table Styling */
    .modern-table {
      border-radius: 0;
      overflow: hidden;
    }
    
    .table-header {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 3px solid #dee2e6;
    }
    
    .table-header th {
      font-weight: 600;
      font-size: 0.75rem;
      letter-spacing: 0.5px;
      color: #495057;
      padding: 16px 12px;
      border: none;
      position: relative;
    }
    
    .modern-table tbody tr {
      border-bottom: 1px solid #f1f3f4;
      transition: all 0.2s ease;
    }
    
    .table-row-hover:hover {
      background-color: #f8f9fa;
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .modern-table td {
      vertical-align: middle;
      padding: 16px 12px;
      border: none;
    }
    
    /* Bid ID Badge */
    .bid-id-badge {
      display: inline-flex;
      align-items: center;
      padding: 6px 12px;
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
      border-radius: 20px;
      border: 1px solid #e1f5fe;
    }
    
    /* Project Info Styling */
    .project-info h6 {
      font-size: 0.95rem;
      line-height: 1.4;
      margin-bottom: 4px;
    }
    
    .project-info p {
      font-size: 0.8rem;
      line-height: 1.3;
      margin-bottom: 2px;
    }
    
    /* Category Badge */
    .category-badge {
      display: inline-block;
      padding: 4px 10px;
      background-color: #f8f9fa;
      border: 1px solid #dee2e6;
      border-radius: 12px;
      font-size: 0.8rem;
      color: #6c757d;
      font-weight: 500;
    }
    
    /* Amount Display */
    .amount-display {
      font-family: 'Segoe UI', system-ui, sans-serif;
    }
    
    /* Status Badges */
    .status-badge {
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
    
    .status-warning {
      background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
      color: #856404;
      border-color: #ffeaa7;
    }
    
    .status-success {
      background: linear-gradient(135deg, #d4edda 0%, #a8e6cf 100%);
      color: #155724;
      border-color: #a8e6cf;
    }
    
    .status-danger {
      background: linear-gradient(135deg, #f8d7da 0%, #ff7675 100%);
      color: #721c24;
      border-color: #ff7675;
    }
    
    .status-secondary {
      background: linear-gradient(135deg, #e2e3e5 0%, #ced4da 100%);
      color: #383d41;
      border-color: #ced4da;
    }
    
    .status-info {
      background: linear-gradient(135deg, #cce7ff 0%, #74c0fc 100%);
      color: #0c5460;
      border-color: #74c0fc;
    }
    
    /* Action Buttons */
    .action-buttons .btn {
      border-radius: 8px;
      transition: all 0.2s ease;
    }
    
    .action-buttons .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
    }
    
    /* Mobile Card Styling */
    .mobile-cards {
      padding: 0;
    }
    
    .bid-card {
      transition: all 0.3s ease;
    }
    
    .bid-card:hover {
      transform: translateY(-2px);
    }
    
    .bid-card .card {
      border-radius: 16px;
      overflow: hidden;
      background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }
    
    .bid-card .card-body {
      position: relative;
    }
    
    .bid-card .card-body::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #007bff, #6f42c1, #e83e8c);
      border-radius: 16px 16px 0 0;
    }
    
    .bid-header {
      display: flex;
      align-items: center;
      flex-wrap: wrap;
      gap: 8px;
    }
    
    .bid-id {
      font-size: 0.9rem;
      padding: 4px 10px;
      background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
      border-radius: 12px;
      border: 1px solid #e1f5fe;
    }
    
    .detail-item {
      padding: 8px 0;
    }
    
    .detail-item small {
      font-size: 0.7rem;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      font-weight: 600;
    }
    
    .attachments-info {
      padding: 8px 12px;
      background-color: #f8f9fa;
      border-radius: 8px;
      border-left: 3px solid #17a2b8;
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
      
      .mobile-cards .bid-card {
        margin-left: 1rem !important;
        margin-right: 1rem !important;
      }
    }
    
    @media (max-width: 576px) {
      .bid-header {
        flex-direction: column;
        align-items: flex-start;
      }
      
      .action-buttons {
        margin-top: 8px;
      }
      
      .detail-item {
        text-align: center;
      }
    }
    
    /* Modal enhancements */
    .modal-lg {
      max-width: 900px;
    }
    
    #vbd-proposal {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      font-size: 0.9rem;
      border: 1px solid #e9ecef;
    }
    
    /* Attachment list styling */
    .list-group-item {
      border-left: none;
      border-right: none;
      padding: 12px 0;
    }
    
    .list-group-item:first-child {
      border-top: none;
    }
    
    .list-group-item:last-child {
      border-bottom: none;
    }
  </style>

  <!-- Sidebar toggle functionality -->
  <script>
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

      // Handle window resize for responsive behavior
      window.addEventListener('resize', () => {
        // Reset mobile sidebar state on desktop
        if (window.innerWidth >= 768) {
          sidebar.classList.remove('active');
          overlay.classList.remove('show');
          document.body.style.overflow = '';
        }
      });

      // Status filter functionality
      const statusFilter = document.getElementById('statusFilter');
      if (statusFilter) {
        statusFilter.addEventListener('change', function() {
          const filter = this.value;
          const rows = document.querySelectorAll('#bidsTable tbody tr');
          
          rows.forEach(row => {
            const statusCell = row.querySelector('td:nth-child(5)');
            const status = statusCell.textContent.trim();
            
            if (!filter || status.includes(filter)) {
              row.style.display = '';
            } else {
              row.style.display = 'none';
            }
          });
        });
      }
    });

    // View bid details modal
    document.querySelectorAll('.btn-view-bid').forEach(btn => {
      btn.addEventListener('click', async function() {
        const bidId = this.getAttribute('data-bid-id');
        if (!bidId) return;
        try {
          const url = this.getAttribute('data-show-url') || `/vendor/api/bids/${bidId}`;
          const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
          const data = await res.json();
          if (!res.ok || !data.success) { alert((data && (data.error || data.message)) || 'Failed to load bid'); return; }
          const bid = data.bid;
          const modalEl = document.getElementById('vendorBidDetailsModal');
          if (!modalEl) return;
          // Basic bid information
          modalEl.querySelector('#vbd-id').textContent = `BID-${String(bid.id).padStart(4, '0')}`;
          modalEl.querySelector('#vbd-project-title').textContent = bid.title || 'Project information not available';
          modalEl.querySelector('#vbd-amount').textContent = `₱${Number(bid.amount).toLocaleString(undefined, {minimumFractionDigits:2})}`;
          modalEl.querySelector('#vbd-submitted').textContent = bid.submitted_at ? new Date(bid.submitted_at).toLocaleString() : '—';
          modalEl.querySelector('#vbd-completion').textContent = bid.completion_date ? new Date(bid.completion_date).toLocaleDateString() : 'Not specified';
          
          // Status badge with detailed information
          const badge = modalEl.querySelector('#vbd-status-badge');
          const status = (bid.status || '').toString();
          badge.textContent = status || '—';
          badge.className = 'badge ' + (
            status === 'Won' ? 'bg-success' :
            status === 'Rejected' ? 'bg-danger' :
            status === 'Under Review' ? 'bg-warning' :
            status === 'Pending Evaluation' ? 'bg-info' : 'bg-secondary'
          );
          
          // Add status description
          const statusDescriptions = {
            'Under Review': 'Your bid has been received and is awaiting initial review by the procurement team.',
            'Pending Evaluation': 'Your bid is currently being evaluated against other submissions. The procurement team is comparing proposals and making their selection.',
            'Won': 'Congratulations! Your bid has been selected as the winning proposal.',
            'Rejected': 'Unfortunately, your bid was not selected for this opportunity.',
            'Withdrawn': 'You have withdrawn this bid from consideration.'
          };
          
          const statusDesc = modalEl.querySelector('#vbd-status-description');
          if (statusDesc) {
            statusDesc.textContent = statusDescriptions[status] || 'Status information not available.';
          }
          
          // Proposal content with formatting
          const proposalEl = modalEl.querySelector('#vbd-proposal');
          const proposal = bid.proposal || 'No proposal details available';
          proposalEl.textContent = proposal;
          
          // Calculate proposal statistics
          const wordCount = proposal.split(/\s+/).filter(word => word.length > 0).length;
          const charCount = proposal.length;
          modalEl.querySelector('#vbd-word-count').textContent = wordCount;
          modalEl.querySelector('#vbd-char-count').textContent = charCount;
          
          // Attachments
          const list = modalEl.querySelector('#vbd-attachments');
          const noAttachmentsEl = modalEl.querySelector('#vbd-no-attachments');
          const attachments = bid.attachments || [];
          
          list.innerHTML = '';
          modalEl.querySelector('#vbd-attachment-count').textContent = attachments.length;
          
          if (attachments.length > 0) {
            list.style.display = 'block';
            noAttachmentsEl.style.display = 'none';
            
            attachments.forEach((f, index) => {
              const li = document.createElement('li');
              li.className = 'list-group-item d-flex justify-content-between align-items-center';
              
              // Determine file type icon
              const extension = f.name.split('.').pop().toLowerCase();
              const fileIcon = {
                'pdf': 'bi-file-earmark-pdf text-danger',
                'doc': 'bi-file-earmark-word text-primary',
                'docx': 'bi-file-earmark-word text-primary',
                'jpg': 'bi-file-earmark-image text-success',
                'jpeg': 'bi-file-earmark-image text-success',
                'png': 'bi-file-earmark-image text-success'
              }[extension] || 'bi-file-earmark text-muted';
              
              // Format file size
              const formatFileSize = (bytes) => {
                if (!bytes) return '';
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                if (bytes === 0) return '0 Bytes';
                const i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
                return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
              };
              
              const fileSize = f.size ? formatFileSize(f.size) : '';
              const sizeText = fileSize ? ` • ${fileSize}` : '';
              
              li.innerHTML = `
                <div class="d-flex align-items-center">
                  <i class="bi ${fileIcon} me-2 fs-5"></i>
                  <div>
                    <div class="fw-bold">${f.name}</div>
                    <small class="text-muted">Document ${index + 1}${sizeText}</small>
                  </div>
                </div>
                <a href="${f.url}" target="_blank" class="btn btn-sm btn-outline-primary" title="Download ${f.name}">
                  <i class="bi bi-download"></i>
                </a>
              `;
              
              list.appendChild(li);
            });
          } else {
            list.style.display = 'none';
            noAttachmentsEl.style.display = 'block';
          }
          
          // Timeline information
          const submittedDate = bid.submitted_at ? new Date(bid.submitted_at) : new Date();
          modalEl.querySelector('#vbd-timeline-submitted').textContent = submittedDate.toLocaleString();
          modalEl.querySelector('#vbd-timeline-current').textContent = `${status} - Last updated ${submittedDate.toLocaleDateString()}`;
          
          // Update timeline marker color based on status
          const timelineMarker = modalEl.querySelector('#vbd-timeline-status .timeline-marker');
          timelineMarker.className = 'timeline-marker ' + (
            status === 'Won' ? 'bg-success' :
            status === 'Rejected' ? 'bg-danger' :
            status === 'Under Review' ? 'bg-warning' :
            status === 'Pending Evaluation' ? 'bg-info' : 'bg-secondary'
          );
          
          // Update timeline status text with more detail
          const timelineStatusText = {
            'Under Review': 'Initial review in progress',
            'Pending Evaluation': 'Being evaluated by procurement team',
            'Won': 'Selected as winning bid',
            'Rejected': 'Not selected',
            'Withdrawn': 'Withdrawn by vendor'
          };
          
          modalEl.querySelector('#vbd-timeline-current').textContent = 
            `${timelineStatusText[status] || status} - Last updated ${submittedDate.toLocaleDateString()}`;
          
          // Add evaluation timeline estimates
          const evaluationEstimate = modalEl.querySelector('#vbd-evaluation-estimate');
          if (evaluationEstimate) {
            let estimateText = '';
            const submissionDate = new Date(bid.submitted_at);
            const daysSinceSubmission = Math.floor((new Date() - submissionDate) / (1000 * 60 * 60 * 24));
            
            switch(status) {
              case 'Under Review':
                const reviewDaysLeft = Math.max(0, 5 - daysSinceSubmission);
                estimateText = reviewDaysLeft > 0 ? 
                  `Initial review expected to complete in ${reviewDaysLeft} day(s)` :
                  'Initial review should be completed soon';
                break;
              case 'Pending Evaluation':
                const evalDaysLeft = Math.max(0, 10 - daysSinceSubmission);
                estimateText = evalDaysLeft > 0 ? 
                  `Evaluation expected to complete in ${evalDaysLeft} day(s)` :
                  'Evaluation should be completed soon';
                break;
              case 'Won':
                estimateText = 'Contract preparation will begin shortly';
                break;
              case 'Rejected':
                estimateText = 'Evaluation process completed';
                break;
              default:
                estimateText = 'Timeline information not available';
            }
            evaluationEstimate.textContent = estimateText;
          }
          const modal = new bootstrap.Modal(modalEl);
          modal.show();
        } catch (e) {
          Swal.fire({
            title: 'Error!',
            text: 'Failed to load bid details. Please try again.',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
          });
        }
      });
    });

    // Withdraw bid
    async function withdrawBid(bidId) {
      console.log('withdrawBid function called with bidId:', bidId);
      let confirmed = false;
      
      // Check if SweetAlert2 is available, fallback to confirm if not
      if (typeof Swal === 'undefined') {
        console.warn('SweetAlert2 not loaded, falling back to basic confirm');
        confirmed = confirm('Are you sure you want to withdraw this bid? This action cannot be undone.');
      } else {
        // Show confirmation dialog with SweetAlert
        const result = await Swal.fire({
          title: 'Withdraw Bid?',
          text: 'Are you sure you want to withdraw this bid? This action cannot be undone.',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#dc3545',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, withdraw it!',
          cancelButtonText: 'Cancel',
          reverseButtons: true
        });
        
        confirmed = result.isConfirmed;
        
        // Show loading state if confirmed
        if (confirmed) {
          Swal.fire({
            title: 'Withdrawing Bid...',
            text: 'Please wait while we process your request.',
            icon: 'info',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: () => {
              Swal.showLoading();
            }
          });
        }
      }

      if (!confirmed) {
        return;
      }

      try {
        const response = await fetch(`/vendor/bids/${bidId}/withdraw`, {
          method: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
          }
        });

        const data = await response.json();

        if (data.success) {
          // Show success message
          if (typeof Swal !== 'undefined') {
            await Swal.fire({
              title: 'Success!',
              text: 'Your bid has been withdrawn successfully.',
              icon: 'success',
              confirmButtonColor: '#28a745',
              confirmButtonText: 'OK'
            });
          } else {
            alert('Bid withdrawn successfully!');
          }
          
          // Reload the page to reflect the changes
          window.location.reload();
        } else {
          // Show error message
          if (typeof Swal !== 'undefined') {
            Swal.fire({
              title: 'Error!',
              text: data.error || 'Failed to withdraw bid. Please try again.',
              icon: 'error',
              confirmButtonColor: '#dc3545',
              confirmButtonText: 'OK'
            });
          } else {
            alert('Error: ' + (data.error || 'Failed to withdraw bid. Please try again.'));
          }
        }
      } catch (error) {
        console.error('Error withdrawing bid:', error);
        
        // Show error message
        if (typeof Swal !== 'undefined') {
          Swal.fire({
            title: 'Network Error!',
            text: 'An error occurred while withdrawing the bid. Please check your connection and try again.',
            icon: 'error',
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'OK'
          });
        } else {
          alert('Network Error: An error occurred while withdrawing the bid. Please check your connection and try again.');
        }
      }
    }
  </script>

  <!-- Vendor Bid Details Modal (admin-style, without evaluation criteria) -->
  <div class="modal fade" id="vendorBidDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-sm">
        <div class="modal-header">
          <h5 class="modal-title">Bid Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <!-- Left Column: Basic Info -->
            <div class="col-md-6">
              <h6 class="fw-bold mb-3"><i class="bi bi-info-circle me-2"></i>Bid Information</h6>
              <div class="card bg-light border-0 mb-3">
                <div class="card-body">
                  <div class="row g-2">
                    <div class="col-6"><strong>Bid ID:</strong></div>
                    <div class="col-6"><span id="vbd-id" class="fw-bold"></span></div>
                    <div class="col-6"><strong>Project:</strong></div>
                    <div class="col-6"><span id="vbd-project-title"></span></div>
                    <div class="col-6"><strong>Amount:</strong></div>
                    <div class="col-6"><span id="vbd-amount" class="fw-bold text-primary fs-5"></span></div>
                    <div class="col-6"><strong>Completion Date:</strong></div>
                    <div class="col-6"><span id="vbd-completion" class="text-muted"></span></div>
                    <div class="col-6"><strong>Submitted:</strong></div>
                    <div class="col-6"><span id="vbd-submitted"></span></div>
                    <div class="col-6"><strong>Status:</strong></div>
                    <div class="col-6"><span id="vbd-status-badge" class="badge bg-secondary">—</span></div>
                  </div>
                  <div class="mt-3">
                    <div class="alert alert-info border-0 py-2 px-3">
                      <i class="bi bi-info-circle me-2"></i>
                      <small id="vbd-status-description">Status information will be displayed here.</small>
                    </div>
                  </div>
                </div>
              </div>

              <h6 class="fw-bold mb-3"><i class="bi bi-file-text me-2"></i>Your Submitted Proposal</h6>
              <div class="border rounded p-3 bg-white" id="vbd-proposal" style="max-height: 300px; overflow-y: auto; white-space: pre-wrap; line-height: 1.6;"></div>
            </div>
            
            <!-- Right Column: Attachments & Metadata -->
            <div class="col-md-6">
              <h6 class="fw-bold mb-3"><i class="bi bi-paperclip me-2"></i>Supporting Documents</h6>
              <div id="vbd-attachments-container">
                <ul id="vbd-attachments" class="list-group list-group-flush" style="max-height: 200px; overflow-y: auto;"></ul>
                <div id="vbd-no-attachments" class="text-center text-muted py-3" style="display: none;">
                  <i class="bi bi-file-earmark-x fs-2 mb-2"></i>
                  <p class="mb-0">No supporting documents attached</p>
                </div>
              </div>
              
              <h6 class="fw-bold mb-3 mt-4"><i class="bi bi-clock-history me-2"></i>Submission Timeline</h6>
              <div class="card bg-light border-0">
                <div class="card-body">
                  <div class="timeline">
                    <div class="timeline-item">
                      <div class="timeline-marker bg-success"></div>
                      <div class="timeline-content">
                        <h6 class="mb-1">Bid Submitted</h6>
                        <p class="text-muted mb-0 small" id="vbd-timeline-submitted"></p>
                      </div>
                    </div>
                    <div class="timeline-item" id="vbd-timeline-status">
                      <div class="timeline-marker bg-warning"></div>
                      <div class="timeline-content">
                        <h6 class="mb-1">Current Status</h6>
                        <p class="text-muted mb-0 small" id="vbd-timeline-current"></p>
                      </div>
                    </div>
                    <div class="timeline-item">
                      <div class="timeline-marker bg-info"></div>
                      <div class="timeline-content">
                        <h6 class="mb-1">Evaluation Timeline</h6>
                        <p class="text-muted mb-0 small" id="vbd-evaluation-estimate">Timeline information will be displayed here.</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="mt-3">
                <h6 class="fw-bold mb-2"><i class="bi bi-bar-chart me-2"></i>Submission Summary</h6>
                <div class="row g-2 text-center">
                  <div class="col-4">
                    <div class="bg-primary bg-opacity-10 rounded p-2">
                      <div class="fw-bold text-primary" id="vbd-word-count">0</div>
                      <small class="text-muted">Words</small>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="bg-info bg-opacity-10 rounded p-2">
                      <div class="fw-bold text-info" id="vbd-attachment-count">0</div>
                      <small class="text-muted">Files</small>
                    </div>
                  </div>
                  <div class="col-4">
                    <div class="bg-success bg-opacity-10 rounded p-2">
                      <div class="fw-bold text-success" id="vbd-char-count">0</div>
                      <small class="text-muted">Chars</small>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
