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
                <h3 class="fw-bold mb-0">{{ $bids->where('status', 'Under Review')->count() }}</h3>
                <p class="text-muted mb-0 small">Under Review</p>
                <small class="text-warning"><i class="bi bi-clock"></i> Pending</small>
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
                <option value="Won">Won</option>
                <option value="Lost">Lost</option>
                <option value="Withdrawn">Withdrawn</option>
              </select>
              <a href="{{ route('vendor.bidding.landing') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Submit New Bid
              </a>
            </div>
          </div>
          <div class="card-body">
            @if($bids->count() > 0)
              <div class="table-responsive">
                <table class="table table-hover" id="bidsTable">
                  <thead class="table-light">
                    <tr>
                      <th>Bid ID</th>
                      <th>Project Title</th>
                      <th>Category</th>
                      <th>Amount</th>
                      <th>Status</th>
                      <th>Submitted Date</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($bids as $bid)
                    <tr>
                      <td><strong>{{ $bid->id ?? 'BID-' . str_pad($loop->iteration, 3, '0', STR_PAD_LEFT) }}</strong></td>
                      <td>
                        <div>
                          <strong>{{ $bid->opportunity ? $bid->opportunity->title : ($bid->title ?? 'Sample Project ' . $loop->iteration) }}</strong><br>
                          <small class="text-muted">{{ $bid->opportunity ? $bid->opportunity->description : ($bid->description ?? 'Project description here') }}</small>
                        </div>
                      </td>
                      <td>{{ $bid->category ?? 'Logistics & Transportation' }}</td>
                      <td><strong>₱{{ number_format($bid->amount ?? rand(50000, 500000)) }}</strong></td>
                      <td>
                        @php
                          $status = $bid->status ?? 'Under Review';
                          $statusClass = [
                            'Under Review' => 'bg-warning',
                            'Won' => 'bg-success',
                            'Lost' => 'bg-danger',
                            'Withdrawn' => 'bg-secondary'
                          ][$status] ?? 'bg-secondary';
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ $status }}</span>
                      </td>
                      <td>{{ $bid->submitted_at ?? now()->subDays(rand(1, 30))->format('M d, Y') }}</td>
                      <td>
                        <div class="btn-group btn-group-sm">
                          <button type="button" class="btn btn-outline-primary btn-view-bid" data-bid-id="{{ $bid->id }}" data-show-url="{{ route('vendor.api.bids.show', ['id' => $bid->id]) }}">
                            <i class="bi bi-eye"></i>
                          </button>
                          @if(($bid->status ?? 'Under Review') === 'Under Review')
                            <button class="btn btn-outline-warning" onclick="withdrawBid({{ $loop->iteration }})">
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
          modalEl.querySelector('#vbd-id').textContent = bid.id;
          modalEl.querySelector('#vbd-title').textContent = bid.title;
          modalEl.querySelector('#vbd-amount').textContent = `₱${Number(bid.amount).toLocaleString(undefined, {minimumFractionDigits:2})}`;
          modalEl.querySelector('#vbd-submitted').textContent = bid.submitted_at || '—';
          const badge = modalEl.querySelector('#vbd-status-badge');
          const status = (bid.status || '').toString();
          badge.textContent = status || '—';
          badge.className = 'badge ' + (
            status === 'Won' ? 'bg-success' :
            status === 'Rejected' ? 'bg-danger' :
            status === 'Under Review' ? 'bg-warning' :
            status === 'Pending Evaluation' ? 'bg-info' : 'bg-secondary'
          );
          modalEl.querySelector('#vbd-proposal').textContent = bid.proposal || '';
          const list = modalEl.querySelector('#vbd-attachments');
          list.innerHTML = '';
          (bid.attachments || []).forEach(f => {
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between align-items-center';
            li.innerHTML = `<span><i class="bi bi-file-earmark me-2"></i>${f.name}</span><a class="btn btn-sm btn-outline-primary" href="${f.url}" target="_blank">Open</a>`;
            list.appendChild(li);
          });
          const modal = new bootstrap.Modal(modalEl);
          modal.show();
        } catch (e) {
          alert('Failed to load bid');
        }
      });
    });

    // Withdraw bid
    function withdrawBid(bidId) {
      if (confirm('Are you sure you want to withdraw this bid? This action cannot be undone.')) {
        alert('Bid withdrawn successfully!');
        // In a real application, this would make an API call to withdraw the bid
      }
    }
  </script>

  <!-- Vendor Bid Details Modal (admin-style, without evaluation criteria) -->
  <div class="modal fade" id="vendorBidDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content shadow-sm">
        <div class="modal-header">
          <h5 class="modal-title" id="vbd-title">Bid Details</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <div class="col-md-6">
              <h6 class="fw-bold">Bid Information</h6>
              <div class="mb-2"><strong>Bid ID:</strong> <span id="vbd-id"></span></div>
              <div class="mb-2"><strong>Title:</strong> <span id="vbd-title"></span></div>
              <div class="mb-2"><strong>Amount:</strong> <span id="vbd-amount" class="fw-bold text-primary"></span></div>
              <div class="mb-2"><strong>Submitted:</strong> <span id="vbd-submitted"></span></div>
              <div class="mb-3"><strong>Status:</strong> <span id="vbd-status-badge" class="badge bg-secondary">—</span></div>

              <h6 class="fw-bold">Proposal Details</h6>
              <div class="border rounded p-3 bg-light" id="vbd-proposal" style="max-height: 220px; overflow:auto;"></div>
            </div>
            <div class="col-md-6">
              <h6 class="fw-bold">Attachments</h6>
              <div id="vbd-attachments" class="d-flex flex-wrap gap-2" style="max-height: 260px; overflow:auto;"></div>
              <small class="text-muted d-block mt-2">Click a file to open in a new tab</small>
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
