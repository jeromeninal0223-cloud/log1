<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Vendor Dashboard - JetLouge Travels</title>

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
      <h6 class="fw-semibold mb-1">{{ $vendor->name }}</h6>
      <small class="text-muted">{{ $vendor->company_name }}</small>
    </div>

    <!-- Navigation Menu -->
    <ul class="nav flex-column">
      <li class="nav-item">
        <a href="{{ route('vendor.dashboard') }}" class="nav-link text-dark active">
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
            <i class="bi bi-speedometer2 fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">Vendor Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ $vendor->name }}! Here's your business overview.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
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
                <i class="bi bi-gavel"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['total_bids'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Total Bids</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +2 this month</small>
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
                <i class="bi bi-cart-check"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['total_orders'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Active Orders</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> +1 this week</small>
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
                <i class="bi bi-receipt"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">{{ $stats['pending_invoices'] ?? 0 }}</h3>
                <p class="text-muted mb-0 small">Pending Invoices</p>
                <small class="text-warning"><i class="bi bi-clock"></i> Awaiting payment</small>
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
                <h3 class="fw-bold mb-0">₱{{ number_format($stats['total_revenue'] ?? 0) }}</h3>
                <p class="text-muted mb-0 small">Total Revenue</p>
                <small class="text-info"><i class="bi bi-graph-up"></i> +15% this month</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dashboard Content -->
    <div class="row g-4">
      <div class="col-lg-8">
        <!-- Recent Activity -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Recent Activity</h5>
            <button class="btn btn-sm btn-outline-primary">View All</button>
          </div>
          <div class="card-body">
            <div class="timeline">
              @forelse(($recentActivity ?? []) as $entry)
                <div class="timeline-item">
                  <div class="timeline-marker bg-{{ $entry['color'] ?? 'secondary' }}"></div>
                  <div class="timeline-content">
                    <h6 class="mb-1">{{ $entry['title'] ?? 'Activity' }}</h6>
                    <p class="text-muted mb-0">{{ $entry['description'] ?? '' }}</p>
                    <small class="text-muted">{{ optional($entry['time'])->diffForHumans() }}</small>
                  </div>
                </div>
              @empty
                <div class="text-muted">No recent activity yet.</div>
              @endforelse
            </div>
          </div>
        </div>

        <!-- Performance Chart -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Monthly Performance</h5>
          </div>
          <div class="card-body">
            <canvas id="performanceChart" height="100"></canvas>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <!-- Quick Actions -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="{{ route('vendor.bidding.landing') }}" class="btn btn-primary">
                <i class="bi bi-search me-2"></i>Browse Opportunities
              </a>
              <a href="{{ route('vendor.bids') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-text me-2"></i>View My Bids
              </a>
              <a href="{{ route('vendor.orders') }}" class="btn btn-outline-primary">
                <i class="bi bi-cart-check me-2"></i>Check Orders
              </a>
              <a href="{{ route('vendor.invoices') }}" class="btn btn-outline-secondary">
                <i class="bi bi-receipt me-2"></i>View Invoices
              </a>
            </div>
          </div>
        </div>

        <!-- Company Information -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Company Information</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <label class="form-label small text-muted">Company Name</label>
              <p class="mb-0 fw-semibold">{{ $vendor->company_name }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label small text-muted">Business Type</label>
              <p class="mb-0 fw-semibold">{{ $vendor->business_type }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label small text-muted">Contact Email</label>
              <p class="mb-0 fw-semibold">{{ $vendor->email }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label small text-muted">Phone Number</label>
              <p class="mb-0 fw-semibold">{{ $vendor->phone }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label small text-muted">Account Status</label>
              <p class="mb-0">
                @if($vendor->status === 'Active')
                  <span class="badge bg-success">Active</span>
                @elseif($vendor->status === 'Pending')
                  <span class="badge bg-warning">Pending Approval</span>
                @else
                  <span class="badge bg-danger">Suspended</span>
                @endif
              </p>
            </div>
          </div>
        </div>

        <!-- Notifications -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Notifications</h5>
          </div>
          <div class="card-body">
            <div class="alert alert-info alert-sm mb-2">
              <i class="bi bi-info-circle me-2"></i>
              New bidding opportunity available
            </div>
            <div class="alert alert-success alert-sm mb-2">
              <i class="bi bi-check-circle me-2"></i>
              Your bid has been shortlisted
            </div>
            <div class="alert alert-warning alert-sm">
              <i class="bi bi-clock me-2"></i>
              Payment due in 3 days
            </div>
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
    });

    // Performance Chart
    document.addEventListener('DOMContentLoaded', function() {
      const ctx = document.getElementById('performanceChart');
      if (!ctx) return;

      const chartData = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
          label: 'Bids Submitted',
          data: [5, 8, 12, 10, 15, 18],
          borderColor: 'rgb(75, 192, 192)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.1
        }, {
          label: 'Orders Won',
          data: [2, 3, 5, 4, 6, 8],
          borderColor: 'rgb(255, 99, 132)',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          tension: 0.1
        }]
      };

      new Chart(ctx, {
        type: 'line',
        data: chartData,
        options: {
          responsive: true,
          plugins: {
            legend: {
              position: 'top',
            }
          },
          scales: {
            y: {
              beginAtZero: true
            }
          }
        }
      });
    });
  </script>

  <style>
    .timeline {
      position: relative;
      padding-left: 30px;
    }
    
    .timeline-item {
      position: relative;
      margin-bottom: 20px;
    }
    
    .timeline-marker {
      position: absolute;
      left: -35px;
      top: 5px;
      width: 12px;
      height: 12px;
      border-radius: 50%;
      border: 2px solid #fff;
      box-shadow: 0 0 0 2px #dee2e6;
    }
    
    .timeline-item:not(:last-child)::after {
      content: '';
      position: absolute;
      left: -29px;
      top: 17px;
      width: 2px;
      height: calc(100% + 3px);
      background-color: #dee2e6;
    }
  </style>
</body>
</html>
