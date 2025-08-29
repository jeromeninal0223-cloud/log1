<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Profile - JetLouge Travels</title>

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
        <a href="{{ route('vendor.profile') }}" class="nav-link text-dark active">
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
            <i class="bi bi-person fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">My Profile</h2>
            <p class="text-muted mb-0">Manage your account information and preferences.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">My Profile</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Profile Content -->
    <div class="row g-4">
      <div class="col-lg-8">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Profile Information</h5>
          </div>
          <div class="card-body">
            @if(session('success'))
              <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            @if($errors->any())
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>Please fix the following errors:
                <ul class="mb-0 mt-2">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            @endif

            <form action="{{ route('vendor.profile.update') }}" method="POST">
              @csrf
              @method('PUT')
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="name" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="name" name="name" value="{{ $vendor->name }}" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" class="form-control" id="email" value="{{ $vendor->email }}" readonly>
                  <small class="text-muted">Email cannot be changed</small>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="company_name" class="form-label">Company Name</label>
                  <input type="text" class="form-control" id="company_name" name="company_name" value="{{ $vendor->company_name }}" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="business_type" class="form-label">Business Type</label>
                  <select class="form-select" id="business_type" name="business_type" required>
                    <option value="">Select Business Type</option>
                    <option value="Logistics & Transportation" {{ $vendor->business_type == 'Logistics & Transportation' ? 'selected' : '' }}>Logistics & Transportation</option>
                    <option value="Technology & Software" {{ $vendor->business_type == 'Technology & Software' ? 'selected' : '' }}>Technology & Software</option>
                    <option value="Manufacturing" {{ $vendor->business_type == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                    <option value="Consulting" {{ $vendor->business_type == 'Consulting' ? 'selected' : '' }}>Consulting</option>
                    <option value="Vehicle Services" {{ $vendor->business_type == 'Vehicle Services' ? 'selected' : '' }}>Vehicle Services</option>
                    <option value="International Logistics" {{ $vendor->business_type == 'International Logistics' ? 'selected' : '' }}>International Logistics</option>
                    <option value="Other" {{ $vendor->business_type == 'Other' ? 'selected' : '' }}>Other</option>
                  </select>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" class="form-control" id="phone" name="phone" value="{{ $vendor->phone }}" required>
                </div>
                <div class="col-md-6 mb-3">
                  <label for="status" class="form-label">Account Status</label>
                  <input type="text" class="form-control" id="status" value="{{ $vendor->status }}" readonly>
                  <small class="text-muted">Status is managed by administrators</small>
                </div>
              </div>

              <div class="mb-3">
                <label for="address" class="form-label">Business Address</label>
                <textarea class="form-control" id="address" name="address" rows="3" required>{{ $vendor->address }}</textarea>
              </div>

              <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">
                  <i class="bi bi-check-circle me-2"></i>Update Profile
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
      
      <div class="col-lg-4">
        <!-- Account Summary -->
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Account Summary</h5>
          </div>
          <div class="card-body">
            <div class="text-center mb-4">
              <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&h=150&fit=crop&crop=face"
                   alt="Profile Picture" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
              <h6 class="fw-semibold mb-1">{{ $vendor->name }}</h6>
              <p class="text-muted mb-0">{{ $vendor->company_name }}</p>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted">Member Since</label>
              <p class="mb-0 fw-semibold">{{ $vendor->created_at->format('M d, Y') }}</p>
            </div>

            <div class="mb-3">
              <label class="form-label small text-muted">Last Updated</label>
              <p class="mb-0 fw-semibold">{{ $vendor->updated_at->format('M d, Y') }}</p>
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

        <!-- Quick Actions -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Quick Actions</h5>
          </div>
          <div class="card-body">
            <div class="d-grid gap-2">
              <button class="btn btn-outline-primary" onclick="changePassword()">
                <i class="bi bi-key me-2"></i>Change Password
              </button>
              <button class="btn btn-outline-secondary" onclick="downloadProfile()">
                <i class="bi bi-download me-2"></i>Download Profile
              </button>
              <button class="btn btn-outline-info" onclick="contactSupport()">
                <i class="bi bi-headset me-2"></i>Contact Support
              </button>
            </div>
          </div>
        </div>

        <!-- Account Security -->
        <div class="card shadow-sm border-0 mt-4">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Account Security</h5>
          </div>
          <div class="card-body">
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1">Two-Factor Authentication</h6>
                  <small class="text-muted">Add an extra layer of security</small>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="twoFactorAuth">
                  <label class="form-check-label" for="twoFactorAuth"></label>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1">Login Notifications</h6>
                  <small class="text-muted">Get notified of new logins</small>
                </div>
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="loginNotifications" checked>
                  <label class="form-check-label" for="loginNotifications"></label>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <h6 class="mb-1">Session Management</h6>
                  <small class="text-muted">Manage active sessions</small>
                </div>
                <button class="btn btn-sm btn-outline-secondary" onclick="manageSessions()">
                  Manage
                </button>
              </div>
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

    // Change password function
    function changePassword() {
      alert('Change password functionality would be implemented here');
    }

    // Download profile function
    function downloadProfile() {
      alert('Downloading profile information...');
    }

    // Contact support function
    function contactSupport() {
      alert('Opening support contact form...');
    }

    // Manage sessions function
    function manageSessions() {
      alert('Opening session management panel...');
    }
  </script>
</body>
</html>
