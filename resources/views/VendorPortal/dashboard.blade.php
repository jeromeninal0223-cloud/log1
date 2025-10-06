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
        <!-- Enhanced Notification Icon -->
        <div class="dropdown me-3">
          <button class="btn btn-link text-white position-relative notification-btn" type="button" id="notificationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-bell fs-5 notification-icon"></i>
            @php
              $unreadCount = isset($notifications) ? collect($notifications)->where('read', false)->count() : 0;
            @endphp
            @if($unreadCount > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge pulse">
              {{ $unreadCount }}
              <span class="visually-hidden">unread notifications</span>
            </span>
            @endif
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-lg notification-dropdown" aria-labelledby="notificationDropdown">
            <li class="notification-header">
              <div class="d-flex justify-content-between align-items-center px-3 py-2">
                <h6 class="mb-0 fw-bold">
                  <i class="bi bi-bell-fill me-2 text-primary"></i>Notifications
                </h6>
                <div class="d-flex align-items-center">
                  @if($unreadCount > 0)
                    <span class="badge bg-primary rounded-pill me-2">{{ $unreadCount }} new</span>
                  @endif
                  <button class="btn btn-sm btn-outline-secondary mark-all-read" title="Mark all as read">
                    <i class="bi bi-check2-all"></i>
                  </button>
                </div>
              </div>
            </li>
            <li><hr class="dropdown-divider m-0"></li>
            
            @forelse($notifications as $notification)
              <li class="notification-item {{ $notification['read'] ?? true ? 'read' : 'unread' }}">
                <a class="dropdown-item py-3 px-3" href="{{ $notification['action_url'] ?? '#' }}">
                  <div class="d-flex">
                    <div class="notification-icon-wrapper bg-{{ $notification['type'] ?? 'primary' }} bg-opacity-10 rounded-circle p-2 me-3">
                      <i class="bi bi-{{ $notification['icon'] ?? 'bell' }} text-{{ $notification['type'] ?? 'primary' }}"></i>
                    </div>
                    <div class="flex-grow-1" style="min-width: 0;">
                      <div class="d-flex justify-content-between align-items-start mb-1">
                        <div class="fw-semibold text-dark" style="flex: 1; min-width: 0; white-space: normal; overflow: hidden; margin-right: 8px;">{{ $notification['title'] ?? 'Notification' }}</div>
                        <small class="text-muted" style="white-space: nowrap;">{{ $notification['time_ago'] ?? 'Recently' }}</small>
                      </div>
                      <div class="text-muted small mb-2" style="line-height: 1.4; word-wrap: break-word; overflow-wrap: break-word;">{{ $notification['description'] ?? '' }}</div>
                      @if(isset($notification['action_text']) && $notification['action_text'])
                        <div class="d-flex gap-2">
                          <button class="btn btn-{{ $notification['type'] ?? 'primary' }} btn-sm notification-action-btn" 
                                  data-action-url="{{ $notification['action_url'] ?? '#' }}"
                                  data-opportunity-id="{{ $notification['opportunity_id'] ?? '' }}"
                                  {{ ($notification['action_disabled'] ?? false) ? 'disabled' : '' }}>
                            @if(isset($notification['action_icon']))
                              <i class="bi bi-{{ $notification['action_icon'] }} me-1"></i>
                            @endif
                            {{ $notification['action_text'] }}
                          </button>
                          @if(isset($notification['secondary_action']))
                            <button class="btn btn-outline-{{ $notification['type'] ?? 'primary' }} btn-sm notification-secondary-btn"
                                    data-action-url="{{ $notification['secondary_action']['url'] ?? '#' }}">
                              @if(isset($notification['secondary_action']['icon']))
                                <i class="bi bi-{{ $notification['secondary_action']['icon'] }} me-1"></i>
                              @endif
                              {{ $notification['secondary_action']['text'] }}
                            </button>
                          @endif
                        </div>
                      @endif
                    </div>
                  </div>
                </a>
              </li>
            @empty
              <li class="notification-item">
                <div class="dropdown-item py-4 text-center text-muted">
                  <i class="bi bi-bell fs-1 mb-3 d-block"></i>
                  <p class="mb-0">No notifications yet</p>
                </div>
              </li>
            @endforelse
            
            <li><hr class="dropdown-divider m-0"></li>
            <li class="notification-footer">
              <a class="dropdown-item text-center py-3 fw-semibold text-primary" href="#">
                <i class="bi bi-arrow-right-circle me-2"></i>View All Notifications
              </a>
            </li>
          </ul>
        </div>
        
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

    <!-- Bid Status Notifications -->
    @php
      $recentStatusChanges = collect($recentActivity ?? [])->filter(function($activity) {
        return isset($activity['type']) && $activity['type'] === 'bid_status_change';
      })->take(3);
    @endphp
    
    @if($recentStatusChanges->count() > 0)
    <div class="row mb-4">
      <div class="col-12">
        <div class="alert alert-info border-0 shadow-sm" role="alert">
          <div class="d-flex align-items-start">
            <div class="me-3">
              <i class="bi bi-bell-fill fs-4 text-info"></i>
            </div>
            <div class="flex-grow-1">
              <h6 class="alert-heading mb-2">
                <i class="bi bi-info-circle me-2"></i>Bid Status Updates
              </h6>
              @foreach($recentStatusChanges as $change)
                <div class="mb-2">
                  <strong>{{ $change['title'] ?? 'Bid Update' }}</strong>
                  <span class="badge bg-{{ $change['status'] === 'Won' ? 'success' : ($change['status'] === 'Pending Evaluation' ? 'info' : 'warning') }} ms-2">
                    {{ $change['status'] }}
                  </span>
                  <br>
                  <small class="text-muted">{{ $change['description'] ?? 'Status updated' }} - {{ $change['time'] ?? 'Recently' }}</small>
                </div>
              @endforeach
              <div class="mt-3">
                <a href="{{ route('vendor.bids') }}" class="btn btn-sm btn-outline-info">
                  <i class="bi bi-eye me-1"></i>View All Bids
                </a>
              </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
    </div>
    @endif

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
              @forelse($recentActivity as $entry)
                <div class="timeline-item">
                  <div class="timeline-marker bg-{{ $entry['color'] ?? 'secondary' }}"></div>
                  <div class="timeline-content">
                    <h6 class="mb-1">{{ $entry['title'] ?? 'Activity' }}</h6>
                    <p class="text-muted mb-0">{{ $entry['description'] ?? '' }}</p>
                    <small class="text-muted">{{ optional($entry['time'])->diffForHumans() ?? 'Recently' }}</small>
                  </div>
                </div>
              @empty
                <div class="text-center text-muted py-4">
                  <i class="bi bi-clock-history fs-1 mb-3 d-block"></i>
                  <p>No recent activity yet.</p>
                  <a href="{{ route('vendor.bidding.landing') }}" class="btn btn-primary btn-sm">Browse Opportunities</a>
                </div>
              @endforelse
            </div>
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
              <a href="{{ route('vendor.bidding.landing') }}" class="btn btn-primary position-relative">
                <i class="bi bi-search me-2"></i>Browse Opportunities
                @php
                  $newOpps = \App\Models\Opportunity::where('current_status', 'Open')->where('created_at', '>=', now()->subDays(7))->count();
                @endphp
                @if($newOpps > 0)
                  <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">{{ $newOpps }}</span>
                @endif
              </a>
              <a href="{{ route('vendor.bids') }}" class="btn btn-outline-primary">
                <i class="bi bi-file-earmark-text me-2"></i>View My Bids
                <small class="text-muted d-block">{{ $stats['total_bids'] ?? 0 }} total bids</small>
              </a>
              <a href="{{ route('vendor.orders') }}" class="btn btn-outline-success">
                <i class="bi bi-cart-check me-2"></i>Check Orders
                <small class="text-muted d-block">{{ $stats['total_orders'] ?? 0 }} active orders</small>
              </a>
              <a href="{{ route('vendor.invoices') }}" class="btn btn-outline-warning">
                <i class="bi bi-receipt me-2"></i>View Invoices
                @php
                  $pendingAmount = \App\Models\Invoice::where('vendor_id', $vendor->id)->whereIn('payment_status', ['Unpaid', 'Partial'])->sum('amount');
                @endphp
                <small class="text-muted d-block">₱{{ number_format($pendingAmount) }} pending</small>
              </a>
              <hr class="my-3">
              <a href="{{ route('vendor.profile') }}" class="btn btn-outline-secondary">
                <i class="bi bi-person-gear me-2"></i>Update Profile
              </a>
            </div>
          </div>
        </div>


      </div>
    </div>
  </div>

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

      // Handle notification action buttons
      document.addEventListener('click', function(e) {
        // Handle primary action buttons (like "View")
        if (e.target.closest('.notification-action-btn')) {
          e.preventDefault();
          e.stopPropagation();
          
          const button = e.target.closest('.notification-action-btn');
          const actionUrl = button.getAttribute('data-action-url');
          const opportunityId = button.getAttribute('data-opportunity-id');
          
          // Debug: Log the button data
          console.log('Button clicked:', button);
          console.log('Action URL:', actionUrl);
          console.log('Opportunity ID:', opportunityId);
          console.log('Button text:', button.textContent.trim());
          
          // If it's a "View" button for an opportunity, redirect to bidding landing with the specific opportunity
          if (button.textContent.trim().toLowerCase().includes('view')) {
            if (opportunityId) {
              console.log('Redirecting with opportunity ID:', opportunityId);
              // Redirect to bidding landing page with opportunity highlighted
              window.location.href = `{{ route('vendor.bidding.landing') }}?opportunity=${opportunityId}&from=notification`;
            } else {
              console.log('No opportunity ID found, using action URL or default redirect');
              // If no opportunity ID, just go to bidding landing page but indicate it came from notification
              window.location.href = `{{ route('vendor.bidding.landing') }}?from=notification`;
            }
          } else if (actionUrl && actionUrl !== '#') {
            // For other actions, use the provided URL
            window.location.href = actionUrl;
          }
        }
        
        // Handle secondary action buttons
        if (e.target.closest('.notification-secondary-btn')) {
          e.preventDefault();
          e.stopPropagation();
          
          const button = e.target.closest('.notification-secondary-btn');
          const actionUrl = button.getAttribute('data-action-url');
          
          if (actionUrl && actionUrl !== '#') {
            window.location.href = actionUrl;
          }
        }
      });

      // Mark all notifications as read functionality
      document.querySelector('.mark-all-read')?.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Add your mark all as read logic here
        // For now, just remove the unread styling
        document.querySelectorAll('.notification-item.unread').forEach(item => {
          item.classList.remove('unread');
          item.classList.add('read');
        });
        
        // Hide the notification badge
        const badge = document.querySelector('.notification-badge');
        if (badge) {
          badge.style.display = 'none';
        }
        
        // Update the "new" count
        const newCount = document.querySelector('.badge.bg-primary.rounded-pill');
        if (newCount) {
          newCount.style.display = 'none';
        }
      });
    });

    // Chart data configuration
    const dynamicChartData = {
      labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
      datasets: {
        bids: [5, 8, 12, 7, 15, 10, 18, 14, 22, 16, 25, 20],
        orders: [2, 3, 5, 3, 6, 4, 7, 5, 8, 6, 9, 7],
        revenue: [50, 75, 120, 85, 150, 100, 180, 140, 220, 160, 250, 200]
      }
    };

    const chartData6M = {
      labels: dynamicChartData.labels.slice(-6),
        datasets: [{
          label: 'Bids Submitted',
          data: dynamicChartData.datasets.bids.slice(-6),
          borderColor: 'rgb(75, 192, 192)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.1
        }, {
          label: 'Orders Won',
          data: dynamicChartData.datasets.orders.slice(-6),
          borderColor: 'rgb(255, 99, 132)',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          tension: 0.1
        }, {
          label: 'Revenue (₱100k)',
          data: dynamicChartData.datasets.revenue.slice(-6),
          borderColor: 'rgb(255, 206, 86)',
          backgroundColor: 'rgba(255, 206, 86, 0.2)',
          tension: 0.1
        }]
      };

      const chartData1Y = {
        labels: dynamicChartData.labels,
        datasets: [{
          label: 'Bids Submitted',
          data: dynamicChartData.datasets.bids,
          borderColor: 'rgb(75, 192, 192)',
          backgroundColor: 'rgba(75, 192, 192, 0.2)',
          tension: 0.1
        }, {
          label: 'Orders Won',
          data: dynamicChartData.datasets.orders,
          borderColor: 'rgb(255, 99, 132)',
          backgroundColor: 'rgba(255, 99, 132, 0.2)',
          tension: 0.1
        }, {
          label: 'Revenue (₱100k)',
          data: dynamicChartData.datasets.revenue,
          borderColor: 'rgb(255, 206, 86)',
          backgroundColor: 'rgba(255, 206, 86, 0.2)',
          tension: 0.1
        }]
      };

    let currentChart;

    function createChart(data) {
      const canvas = document.getElementById('performanceChart');
      if (!canvas) return;
      
      const ctx = canvas.getContext('2d');
      if (currentChart) {
        currentChart.destroy();
      }
      currentChart = new Chart(ctx, {
        type: 'line',
        data: data,
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
    }

    // Only initialize chart if canvas exists
    const canvas = document.getElementById('performanceChart');
    if (canvas) {
      // Initialize with 6 months data
      createChart(chartData6M);

      // Chart period toggle functionality
      document.querySelectorAll('input[name="chartPeriod"]').forEach(radio => {
        radio.addEventListener('change', function() {
          if (this.id === 'chart6months') {
            createChart(chartData6M);
          } else if (this.id === 'chart1year') {
            createChart(chartData1Y);
          }
        });
      });
    }

    // Support functions
    function startLiveChat() {
      alert('Live chat feature will be implemented soon. Please use email or phone support for immediate assistance.');
    }
  </script>

  <style>
    /* Timeline Styles */
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

    /* Enhanced Notification Styles */
    .notification-btn {
      border: none !important;
      padding: 8px 12px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .notification-btn:hover {
      background-color: rgba(255, 255, 255, 0.1) !important;
      transform: translateY(-1px);
    }

    .notification-icon {
      transition: all 0.3s ease;
    }

    .notification-btn:hover .notification-icon {
      transform: rotate(15deg) scale(1.1);
    }

    .notification-badge {
      font-size: 0.7rem;
      min-width: 18px;
      height: 18px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .pulse {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
      }
      70% {
        box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
      }
    }

    .notification-dropdown {
      width: 350px !important;
      max-height: none;
      overflow: visible;
      border: none;
      border-radius: 8px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      margin-top: 10px !important;
      z-index: 9999 !important;
      animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
      from {
        opacity: 0;
        transform: translateY(-10px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .notification-header {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-bottom: 1px solid #dee2e6;
    }

    .notification-footer {
      background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
      border-top: 1px solid #dee2e6;
    }

    .notification-item {
      transition: all 0.2s ease;
      border-left: 3px solid transparent;
    }

    .notification-item.unread {
      background-color: rgba(13, 110, 253, 0.02);
      border-left-color: #0d6efd;
    }

    .notification-item:hover {
      background-color: rgba(13, 110, 253, 0.05);
      transform: translateX(2px);
    }

    .notification-item.read {
      opacity: 0.8;
    }

    .notification-icon-wrapper {
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .mark-all-read {
      border-radius: 6px;
      transition: all 0.2s ease;
    }

    .mark-all-read:hover {
      background-color: #6c757d;
      border-color: #6c757d;
      color: white;
      transform: scale(1.05);
    }

    /* Custom scrollbar for notification dropdown */
    .notification-dropdown::-webkit-scrollbar {
      width: 6px;
    }

    .notification-dropdown::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 3px;
    }

    .notification-dropdown::-webkit-scrollbar-thumb {
      background: #c1c1c1;
      border-radius: 3px;
    }

    .notification-dropdown::-webkit-scrollbar-thumb:hover {
      background: #a8a8a8;
    }

    /* Button hover effects */
    .notification-item .btn {
      transition: all 0.2s ease;
      font-size: 0.8rem;
    }

    .notification-item .btn:hover {
      transform: translateY(-1px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }
  </style>
</body>
</html>
