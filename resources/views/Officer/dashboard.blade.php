<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Procurement Officer Dashboard - Jetlouge Travels</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- PSM Animations -->
  <link rel="stylesheet" href="{{ asset('assets/css/psm-animations.css') }}">
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
        <a href="{{ url('/officer/dashboard') }}" class="nav-link text-dark active">
          <i class="bi bi-speedometer2 me-2"></i> Dashboard
        </a>
      </li>
      <li class="nav-item">
        <a href="#" class="nav-link text-dark" data-bs-toggle="collapse" data-bs-target="#procurementSubmenu" aria-expanded="true" aria-controls="procurementSubmenu">
          <i class="bi bi-cart-plus me-2"></i> Procurement & Sourcing
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <div class="collapse show" id="procurementSubmenu">
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
                <i class="bi bi-clipboard-check me-2"></i> Bidding & RFQ
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
      <li class="nav-item mt-3">
        <a href="#" class="nav-link text-danger" id="logoutBtn">
          <i class="bi bi-box-arrow-right me-2"></i> Logout
        </a>
      </li>
    </ul>
  </aside>

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
            <h2 class="fw-bold mb-1">Procurement Officer Dashboard</h2>
            <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Manage your procurement operations efficiently.</p>
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
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">12</h3>
                <p class="text-muted mb-0 small">Pending Requests</p>
                <small class="text-info"><i class="bi bi-arrow-up"></i> +3 this week</small>
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
                <h3 class="fw-bold mb-0">8</h3>
                <p class="text-muted mb-0 small">Active Contracts</p>
                <small class="text-success"><i class="bi bi-arrow-up"></i> 2 new this month</small>
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
                <i class="bi bi-building"></i>
              </div>
              <div>
                <h3 class="fw-bold mb-0">25</h3>
                <p class="text-muted mb-0 small">Active Vendors</p>
                <small class="text-warning"><i class="bi bi-arrow-up"></i> 5 pending approval</small>
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
                <h3 class="fw-bold mb-0">₱2.4M</h3>
                <p class="text-muted mb-0 small">Total Orders</p>
                <small class="text-info"><i class="bi bi-arrow-up"></i> This quarter</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Dashboard Content -->
    <div class="row g-4">
      <!-- Quick Actions -->
      <div class="col-lg-4">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <a href="{{ url('/psm/request') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>New Purchase Request
              </a>
              <a href="{{ url('/psm/vendor') }}" class="btn btn-outline-primary">
                <i class="bi bi-building me-2"></i>Manage Vendors
              </a>
              <a href="{{ url('/psm/bidding') }}" class="btn btn-outline-primary">
                <i class="bi bi-clipboard-check me-2"></i>Review Bids
              </a>
              <a href="{{ url('/psm/contract') }}" class="btn btn-outline-secondary">
                <i class="bi bi-file-earmark-check me-2"></i>Contract Management
              </a>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Recent Activity</h5>
          </div>
          <div class="card-body">
            <div class="timeline">
              <div class="timeline-item mb-3">
                <small class="text-muted">2 hours ago</small>
                <p class="mb-1 small">New purchase request submitted</p>
                <small class="text-primary">Office supplies - ₱15,000</small>
              </div>
              <div class="timeline-item mb-3">
                <small class="text-muted">4 hours ago</small>
                <p class="mb-1 small">Contract approved and signed</p>
                <small class="text-success">Vehicle maintenance - ₱45,000</small>
              </div>
              <div class="timeline-item mb-3">
                <small class="text-muted">6 hours ago</small>
                <p class="mb-1 small">New vendor registration</p>
                <small class="text-info">ABC Supplies Ltd.</small>
              </div>
              <div class="timeline-item">
                <small class="text-muted">1 day ago</small>
                <p class="mb-1 small">Purchase order completed</p>
                <small class="text-success">Travel equipment - ₱28,500</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts and Analytics -->
      <div class="col-lg-8">
        <!-- Monthly Procurement Overview -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Monthly Procurement Overview</h5>
          </div>
          <div class="card-body">
            <canvas id="procurementChart" height="100"></canvas>
          </div>
        </div>

        <!-- Pending Tasks -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Pending Tasks</h5>
            <a href="{{ url('/psm/request') }}" class="btn btn-sm btn-outline-primary">View All</a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead class="table-light">
                  <tr>
                    <th>Task</th>
                    <th>Type</th>
                    <th>Priority</th>
                    <th>Due Date</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>Review vendor application</td>
                    <td><span class="badge bg-info">Vendor</span></td>
                    <td><span class="badge bg-warning">Medium</span></td>
                    <td>Today</td>
                    <td><a href="{{ url('/psm/vendor') }}" class="btn btn-sm btn-outline-primary">Review</a></td>
                  </tr>
                  <tr>
                    <td>Approve purchase request</td>
                    <td><span class="badge bg-primary">Request</span></td>
                    <td><span class="badge bg-danger">High</span></td>
                    <td>Tomorrow</td>
                    <td><a href="{{ url('/psm/request') }}" class="btn btn-sm btn-outline-primary">Approve</a></td>
                  </tr>
                  <tr>
                    <td>Evaluate bid proposals</td>
                    <td><span class="badge bg-success">Bidding</span></td>
                    <td><span class="badge bg-warning">Medium</span></td>
                    <td>Dec 15</td>
                    <td><a href="{{ url('/psm/bidding') }}" class="btn btn-sm btn-outline-primary">Evaluate</a></td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <!-- PSM Animations JavaScript -->
  <script src="{{ asset('assets/js/psm-animations.js') }}"></script>

  <!-- Dashboard JavaScript -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Logout functionality
      const logoutBtn = document.getElementById('logoutBtn');
      if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
          e.preventDefault();

          // Use Laravel's session-based logout
          const form = document.createElement('form');
          form.method = 'POST';
          form.action = '{{ route('logout') }}';
          
          const csrfToken = document.createElement('input');
          csrfToken.type = 'hidden';
          csrfToken.name = '_token';
          csrfToken.value = '{{ csrf_token() }}';
          form.appendChild(csrfToken);
          
          document.body.appendChild(form);
          form.submit();
        });
      }

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

      // Initialize procurement chart
      const ctx = document.getElementById('procurementChart');
      if (ctx) {
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
              label: 'Purchase Orders',
              data: [12, 19, 8, 15, 22, 18],
              borderColor: 'rgb(75, 192, 192)',
              backgroundColor: 'rgba(75, 192, 192, 0.1)',
              tension: 0.1
            }, {
              label: 'Contracts',
              data: [5, 8, 4, 7, 12, 9],
              borderColor: 'rgb(255, 99, 132)',
              backgroundColor: 'rgba(255, 99, 132, 0.1)',
              tension: 0.1
            }]
          },
          options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      }
    });
  </script>

</body>
</html>
