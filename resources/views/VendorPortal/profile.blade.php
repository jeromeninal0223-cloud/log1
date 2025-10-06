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
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">

  <style>
    .spin {
      animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    
    .password-requirements ul {
      padding-left: 0;
    }
    
    .password-requirements li {
      transition: color 0.3s ease;
    }
  </style>

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

            <form id="profileUpdateForm" action="{{ route('vendor.profile.update') }}" method="POST">
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
                <button type="submit" class="btn btn-primary" id="updateProfileBtn">
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
                  <input class="form-check-input" type="checkbox" id="twoFactorAuth" {{ $vendor->two_factor_enabled ? 'checked' : '' }} onchange="toggle2FA(this)">
                  <label class="form-check-label" for="twoFactorAuth"></label>
                </div>
              </div>
              @if($vendor->two_factor_enabled)
                <div class="mt-2">
                  <span class="badge bg-success"><i class="bi bi-shield-check me-1"></i>Enabled</span>
                  <button class="btn btn-sm btn-outline-danger ms-2" onclick="disable2FA()">
                    <i class="bi bi-shield-x me-1"></i>Disable
                  </button>
                </div>
              @endif
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

  <!-- Change Password Modal -->
  <div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordModalLabel">
            <i class="bi bi-key me-2"></i>Change Password
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="changePasswordForm" action="{{ route('vendor.password.update') }}" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <!-- Alert Container -->
            <div id="passwordAlert" class="alert d-none" role="alert"></div>
            
            <!-- Current Password -->
            <div class="mb-3">
              <label for="current_password" class="form-label">Current Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="current_password" name="current_password" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('current_password')">
                  <i class="bi bi-eye" id="current_password_icon"></i>
                </button>
              </div>
              <div class="invalid-feedback" id="current_password_error"></div>
            </div>

            <!-- New Password -->
            <div class="mb-3">
              <label for="new_password" class="form-label">New Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="new_password" name="password" required minlength="8">
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('new_password')">
                  <i class="bi bi-eye" id="new_password_icon"></i>
                </button>
              </div>
              <div class="password-requirements mt-2">
                <small class="text-muted">Password must contain:</small>
                <ul class="list-unstyled small mt-1">
                  <li id="length-req" class="text-muted">
                    <i class="bi bi-x-circle me-1"></i>At least 8 characters
                  </li>
                  <li id="uppercase-req" class="text-muted">
                    <i class="bi bi-x-circle me-1"></i>One uppercase letter
                  </li>
                  <li id="lowercase-req" class="text-muted">
                    <i class="bi bi-x-circle me-1"></i>One lowercase letter
                  </li>
                  <li id="number-req" class="text-muted">
                    <i class="bi bi-x-circle me-1"></i>One number
                  </li>
                  <li id="special-req" class="text-muted">
                    <i class="bi bi-x-circle me-1"></i>One special character
                  </li>
                </ul>
              </div>
              <!-- Password Strength Indicator -->
              <div class="mt-2">
                <div class="progress" style="height: 5px;">
                  <div class="progress-bar" id="password-strength-bar" role="progressbar" style="width: 0%"></div>
                </div>
                <small id="password-strength-text" class="text-muted">Password strength: Weak</small>
              </div>
              <div class="invalid-feedback" id="new_password_error"></div>
            </div>

            <!-- Confirm Password -->
            <div class="mb-3">
              <label for="password_confirmation" class="form-label">Confirm New Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                <button class="btn btn-outline-secondary" type="button" onclick="togglePasswordVisibility('password_confirmation')">
                  <i class="bi bi-eye" id="password_confirmation_icon"></i>
                </button>
              </div>
              <div class="invalid-feedback" id="password_confirmation_error"></div>
            </div>

            <!-- Security Notice -->
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              <strong>Security Notice:</strong> You will be logged out after changing your password and will need to log in again with your new credentials.
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary" id="changePasswordBtn" disabled>
              <i class="bi bi-check-circle me-2"></i>Change Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Two-Factor Authentication Setup Modal -->
  <div class="modal fade" id="setup2FAModal" tabindex="-1" aria-labelledby="setup2FAModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="setup2FAModalLabel">
            <i class="bi bi-shield-check me-2"></i>Setup Two-Factor Authentication
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Step 1: Download App -->
          <div id="step1" class="setup-step">
            <div class="text-center mb-4">
              <i class="bi bi-phone text-primary" style="font-size: 3rem;"></i>
              <h6 class="mt-3">Step 1: Download Authenticator App</h6>
              <p class="text-muted">Install one of these apps on your mobile device:</p>
            </div>
            
            <div class="row text-center mb-4">
              <div class="col-4">
                <div class="p-3 border rounded">
                  <i class="bi bi-google text-primary fs-4"></i>
                  <p class="small mt-2 mb-0">Google Authenticator</p>
                </div>
              </div>
              <div class="col-4">
                <div class="p-3 border rounded">
                  <i class="bi bi-microsoft text-primary fs-4"></i>
                  <p class="small mt-2 mb-0">Microsoft Authenticator</p>
                </div>
              </div>
              <div class="col-4">
                <div class="p-3 border rounded">
                  <i class="bi bi-shield-check text-primary fs-4"></i>
                  <p class="small mt-2 mb-0">Authy</p>
                </div>
              </div>
            </div>
            
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-primary" onclick="showStep2()">
                Next: Scan QR Code <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>

          <!-- Step 2: Scan QR Code -->
          <div id="step2" class="setup-step d-none">
            <div class="text-center mb-4">
              <i class="bi bi-qr-code text-primary" style="font-size: 3rem;"></i>
              <h6 class="mt-3">Step 2: Scan QR Code</h6>
              <p class="text-muted">Open your authenticator app and scan this QR code:</p>
            </div>
            
            <div class="text-center mb-4">
              <div id="qrcode" class="d-inline-block p-3 border rounded bg-light">
                <!-- QR Code will be generated here -->
                <div class="spinner-border text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </div>
            </div>
            
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              <strong>Can't scan?</strong> Manually enter this key in your app:
              <div class="mt-2">
                <code id="manual-key" class="user-select-all"></code>
                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyManualKey()">
                  <i class="bi bi-clipboard"></i>
                </button>
              </div>
            </div>
            
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" onclick="showStep1()">
                <i class="bi bi-arrow-left me-1"></i> Back
              </button>
              <button type="button" class="btn btn-primary" onclick="showStep3()">
                Next: Verify Code <i class="bi bi-arrow-right ms-1"></i>
              </button>
            </div>
          </div>

          <!-- Step 3: Verify Code -->
          <div id="step3" class="setup-step d-none">
            <div class="text-center mb-4">
              <i class="bi bi-key text-primary" style="font-size: 3rem;"></i>
              <h6 class="mt-3">Step 3: Verify Setup</h6>
              <p class="text-muted">Enter the 6-digit code from your authenticator app:</p>
            </div>
            
            <form id="verify2FAForm">
              @csrf
              <div class="mb-3">
                <label for="verification_code" class="form-label">Verification Code</label>
                <input type="text" class="form-control text-center" id="verification_code" name="verification_code" 
                       maxlength="6" pattern="[0-9]{6}" placeholder="000000" style="font-size: 1.5rem; letter-spacing: 0.5rem;">
                <div class="invalid-feedback" id="verification_error"></div>
              </div>
              
              <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Important:</strong> Save your backup codes in a secure location. You'll need them if you lose access to your authenticator app.
              </div>
              
              <div id="backup-codes" class="d-none">
                <h6>Backup Codes:</h6>
                <div class="row" id="backup-codes-list">
                  <!-- Backup codes will be generated here -->
                </div>
                <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="downloadBackupCodes()">
                  <i class="bi bi-download me-1"></i>Download Backup Codes
                </button>
              </div>
            </form>
            
            <div class="d-flex justify-content-between mt-4">
              <button type="button" class="btn btn-secondary" onclick="showStep2()">
                <i class="bi bi-arrow-left me-1"></i> Back
              </button>
              <button type="button" class="btn btn-success" onclick="enable2FAWithCode()" id="enable2FABtn">
                <i class="bi bi-shield-check me-1"></i> Enable 2FA
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Disable 2FA Confirmation Modal -->
  <div class="modal fade" id="disable2FAModal" tabindex="-1" aria-labelledby="disable2FAModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="disable2FAModalLabel">
            <i class="bi bi-shield-x me-2 text-danger"></i>Disable Two-Factor Authentication
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Warning:</strong> Disabling two-factor authentication will make your account less secure.
          </div>
          
          <p>To confirm you want to disable 2FA, please enter your current password:</p>
          
          <form id="disable2FAForm">
            @csrf
            <div class="mb-3">
              <label for="disable_password" class="form-label">Current Password</label>
              <input type="password" class="form-control" id="disable_password" name="password" required>
              <div class="invalid-feedback" id="disable_password_error"></div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger" onclick="confirmDisable2FA()">
            <i class="bi bi-shield-x me-1"></i> Disable 2FA
          </button>
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
    });

    // Change password function
    function changePassword() {
      const modal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
      modal.show();
      
      // Reset form when modal is shown
      document.getElementById('changePasswordForm').reset();
      resetPasswordValidation();
    }

    // Password visibility toggle
    function togglePasswordVisibility(fieldId) {
      const field = document.getElementById(fieldId);
      const icon = document.getElementById(fieldId + '_icon');
      
      if (field.type === 'password') {
        field.type = 'text';
        icon.className = 'bi bi-eye-slash';
      } else {
        field.type = 'password';
        icon.className = 'bi bi-eye';
      }
    }

    // Password strength checker
    function checkPasswordStrength(password) {
      let score = 0;
      const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /\d/.test(password),
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
      };

      // Update requirement indicators
      Object.keys(requirements).forEach(req => {
        const element = document.getElementById(req + '-req');
        const icon = element.querySelector('i');
        
        if (requirements[req]) {
          element.className = 'text-success';
          icon.className = 'bi bi-check-circle me-1';
          score++;
        } else {
          element.className = 'text-muted';
          icon.className = 'bi bi-x-circle me-1';
        }
      });

      // Update strength bar
      const strengthBar = document.getElementById('password-strength-bar');
      const strengthText = document.getElementById('password-strength-text');
      const percentage = (score / 5) * 100;
      
      strengthBar.style.width = percentage + '%';
      
      if (score <= 2) {
        strengthBar.className = 'progress-bar bg-danger';
        strengthText.textContent = 'Password strength: Weak';
        strengthText.className = 'text-danger';
      } else if (score <= 3) {
        strengthBar.className = 'progress-bar bg-warning';
        strengthText.textContent = 'Password strength: Fair';
        strengthText.className = 'text-warning';
      } else if (score <= 4) {
        strengthBar.className = 'progress-bar bg-info';
        strengthText.textContent = 'Password strength: Good';
        strengthText.className = 'text-info';
      } else {
        strengthBar.className = 'progress-bar bg-success';
        strengthText.textContent = 'Password strength: Strong';
        strengthText.className = 'text-success';
      }

      return score >= 4; // Require at least 4 out of 5 criteria
    }

    // Validate password confirmation
    function validatePasswordConfirmation() {
      const newPassword = document.getElementById('new_password').value;
      const confirmPassword = document.getElementById('password_confirmation').value;
      const confirmField = document.getElementById('password_confirmation');
      const errorDiv = document.getElementById('password_confirmation_error');

      if (confirmPassword && newPassword !== confirmPassword) {
        confirmField.classList.add('is-invalid');
        errorDiv.textContent = 'Passwords do not match';
        return false;
      } else {
        confirmField.classList.remove('is-invalid');
        errorDiv.textContent = '';
        return true;
      }
    }

    // Reset password validation
    function resetPasswordValidation() {
      // Reset all form fields
      document.querySelectorAll('.is-invalid').forEach(field => {
        field.classList.remove('is-invalid');
      });
      
      // Reset error messages
      document.querySelectorAll('.invalid-feedback').forEach(error => {
        error.textContent = '';
      });
      
      // Reset requirements
      document.querySelectorAll('[id$="-req"]').forEach(req => {
        req.className = 'text-muted';
        req.querySelector('i').className = 'bi bi-x-circle me-1';
      });
      
      // Reset strength indicator
      const strengthBar = document.getElementById('password-strength-bar');
      const strengthText = document.getElementById('password-strength-text');
      strengthBar.style.width = '0%';
      strengthBar.className = 'progress-bar';
      strengthText.textContent = 'Password strength: Weak';
      strengthText.className = 'text-muted';
      
      // Reset alert
      const alert = document.getElementById('passwordAlert');
      alert.className = 'alert d-none';
      alert.textContent = '';
      
      // Disable submit button
      document.getElementById('changePasswordBtn').disabled = true;
    }

    // Validate form
    function validatePasswordForm() {
      const currentPassword = document.getElementById('current_password').value;
      const newPassword = document.getElementById('new_password').value;
      const confirmPassword = document.getElementById('password_confirmation').value;
      
      const isCurrentPasswordValid = currentPassword.length > 0;
      const isNewPasswordStrong = checkPasswordStrength(newPassword);
      const isConfirmationValid = validatePasswordConfirmation();
      
      const isFormValid = isCurrentPasswordValid && isNewPasswordStrong && isConfirmationValid && newPassword.length >= 8;
      
      document.getElementById('changePasswordBtn').disabled = !isFormValid;
      return isFormValid;
    }

    // Handle profile update form submission
    function handleProfileUpdate() {
      const profileForm = document.getElementById('profileUpdateForm');
      if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const submitBtn = document.getElementById('updateProfileBtn');
          const originalText = submitBtn.innerHTML;
          
          // Show loading state
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-2"></i>Updating Profile...';
          
          // Get form data
          const formData = new FormData(profileForm);
          
          // Submit via fetch
          fetch(profileForm.action, {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              Swal.fire({
                icon: 'success',
                title: 'Profile Updated!',
                text: data.message || 'Your profile has been updated successfully.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#28a745'
              });
            } else {
              throw new Error(data.message || 'Failed to update profile');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            Swal.fire({
              icon: 'error',
              title: 'Update Failed',
              text: error.message || 'An error occurred while updating your profile. Please try again.',
              confirmButtonText: 'OK',
              confirmButtonColor: '#dc3545'
            });
          })
          .finally(() => {
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          });
        });
      }
    }

    // Initialize password form event listeners
    document.addEventListener('DOMContentLoaded', function() {
      // Initialize profile update handler
      handleProfileUpdate();
      const newPasswordField = document.getElementById('new_password');
      const confirmPasswordField = document.getElementById('password_confirmation');
      const currentPasswordField = document.getElementById('current_password');
      
      if (newPasswordField) {
        newPasswordField.addEventListener('input', function() {
          validatePasswordForm();
        });
      }
      
      if (confirmPasswordField) {
        confirmPasswordField.addEventListener('input', function() {
          validatePasswordForm();
        });
      }
      
      if (currentPasswordField) {
        currentPasswordField.addEventListener('input', function() {
          validatePasswordForm();
        });
      }
      
      // Handle form submission
      const changePasswordForm = document.getElementById('changePasswordForm');
      if (changePasswordForm) {
        changePasswordForm.addEventListener('submit', function(e) {
          e.preventDefault();
          
          if (!validatePasswordForm()) {
            showPasswordAlert('Please fix all validation errors before submitting.', 'danger');
            return;
          }
          
          const submitBtn = document.getElementById('changePasswordBtn');
          const originalText = submitBtn.innerHTML;
          
          // Show loading state
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise spin me-2"></i>Changing Password...';
          
          // Get form data
          const formData = new FormData(changePasswordForm);
          
          // Submit via fetch
          fetch(changePasswordForm.action, {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
              'Accept': 'application/json'
            }
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              showPasswordAlert('Password changed successfully! You will be logged out in 3 seconds...', 'success');
              
              // Auto logout after 3 seconds
              setTimeout(() => {
                window.location.href = '/vendor/bidding';
              }, 3000);
            } else {
              throw new Error(data.message || 'Failed to change password');
            }
          })
          .catch(error => {
            console.error('Error:', error);
            showPasswordAlert(error.message || 'An error occurred while changing your password. Please try again.', 'danger');
            
            // Reset button
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
          });
        });
      }
    });

    // Show password alert
    function showPasswordAlert(message, type) {
      const alert = document.getElementById('passwordAlert');
      alert.className = `alert alert-${type}`;
      alert.innerHTML = `<i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}`;
      alert.classList.remove('d-none');
    }

    // Other quick action functions (placeholder)
    function downloadProfile() {
      Swal.fire({
        icon: 'info',
        title: 'Feature Coming Soon',
        text: 'Download profile functionality will be implemented.',
        confirmButtonText: 'OK'
      });
    }

    function contactSupport() {
      Swal.fire({
        icon: 'info',
        title: 'Contact Support',
        text: 'Contact support functionality will be implemented.',
        confirmButtonText: 'OK'
      });
    }

    function manageSessions() {
      Swal.fire({
        icon: 'info',
        title: 'Session Management',
        text: 'Session management functionality will be implemented.',
        confirmButtonText: 'OK'
      });
    }

    // Two-Factor Authentication Functions
    function toggle2FA(checkbox) {
      if (checkbox.checked) {
        // Show setup modal
        const modal = new bootstrap.Modal(document.getElementById('setup2FAModal'));
        modal.show();
        showStep1();
      } else {
        // Uncheck the box since we need to go through disable process
        checkbox.checked = true;
      }
    }

    function disable2FA() {
      const modal = new bootstrap.Modal(document.getElementById('disable2FAModal'));
      modal.show();
    }

    // Setup Modal Step Navigation
    function showStep1() {
      document.getElementById('step1').classList.remove('d-none');
      document.getElementById('step2').classList.add('d-none');
      document.getElementById('step3').classList.add('d-none');
    }

    function showStep2() {
      document.getElementById('step1').classList.add('d-none');
      document.getElementById('step2').classList.remove('d-none');
      document.getElementById('step3').classList.add('d-none');
      
      // Generate QR code and secret key
      generate2FASecret();
    }

    function showStep3() {
      document.getElementById('step1').classList.add('d-none');
      document.getElementById('step2').classList.add('d-none');
      document.getElementById('step3').classList.remove('d-none');
    }

    // Generate 2FA Secret and QR Code
    function generate2FASecret() {
      fetch('/vendor/2fa/generate', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Display QR code
          document.getElementById('qrcode').innerHTML = data.qr_code;
          
          // Display manual key
          document.getElementById('manual-key').textContent = data.secret;
          
          // Store secret for verification
          window.tempSecret = data.secret;
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Error generating 2FA secret. Please try again.',
            confirmButtonText: 'OK'
          });
        }
      })
      .catch(error => {
        console.error('Error:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Error generating 2FA secret. Please try again.',
          confirmButtonText: 'OK'
        });
      });
    }

    // Copy manual key to clipboard
    function copyManualKey() {
      const manualKey = document.getElementById('manual-key').textContent;
      navigator.clipboard.writeText(manualKey).then(() => {
        // Show temporary success feedback
        const btn = event.target.closest('button');
        const originalIcon = btn.innerHTML;
        btn.innerHTML = '<i class="bi bi-check"></i>';
        setTimeout(() => {
          btn.innerHTML = originalIcon;
        }, 2000);
      });
    }

    // Enable 2FA with verification code
    function enable2FAWithCode() {
      const code = document.getElementById('verification_code').value;
      
      if (!code || code.length !== 6) {
        showError('verification_error', 'Please enter a valid 6-digit code.');
        return;
      }

      const btn = document.getElementById('enable2FABtn');
      const originalText = btn.innerHTML;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
      btn.disabled = true;

      fetch('/vendor/2fa/enable', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          verification_code: code,
          secret: window.tempSecret
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show backup codes
          displayBackupCodes(data.backup_codes);
          
          // Update UI
          document.getElementById('twoFactorAuth').checked = true;
          
          // Show success message
          Swal.fire({
            icon: 'success',
            title: '2FA Enabled!',
            text: 'Two-factor authentication has been successfully enabled for your account.',
            confirmButtonText: 'OK'
          }).then(() => {
            bootstrap.Modal.getInstance(document.getElementById('setup2FAModal')).hide();
            location.reload(); // Refresh to show updated status
          });
        } else {
          showError('verification_error', data.message || 'Invalid verification code.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('verification_error', 'Error enabling 2FA. Please try again.');
      })
      .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
      });
    }

    // Display backup codes
    function displayBackupCodes(codes) {
      const backupCodesDiv = document.getElementById('backup-codes');
      const codesList = document.getElementById('backup-codes-list');
      
      codesList.innerHTML = '';
      codes.forEach((code, index) => {
        const col = document.createElement('div');
        col.className = 'col-6 mb-2';
        col.innerHTML = `<code class="d-block p-2 bg-light rounded">${code}</code>`;
        codesList.appendChild(col);
      });
      
      backupCodesDiv.classList.remove('d-none');
      window.backupCodes = codes;
    }

    // Download backup codes
    function downloadBackupCodes() {
      if (!window.backupCodes) return;
      
      const content = 'Two-Factor Authentication Backup Codes\n' +
                     'Generated: ' + new Date().toLocaleString() + '\n\n' +
                     window.backupCodes.join('\n') + '\n\n' +
                     'Keep these codes in a safe place. Each code can only be used once.';
      
      const blob = new Blob([content], { type: 'text/plain' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = '2fa-backup-codes.txt';
      a.click();
      window.URL.revokeObjectURL(url);
    }

    // Confirm disable 2FA
    function confirmDisable2FA() {
      const password = document.getElementById('disable_password').value;
      
      if (!password) {
        showError('disable_password_error', 'Please enter your password.');
        return;
      }

      fetch('/vendor/2fa/disable', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          password: password
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update UI
          document.getElementById('twoFactorAuth').checked = false;
          bootstrap.Modal.getInstance(document.getElementById('disable2FAModal')).hide();
          
          Swal.fire({
            icon: 'success',
            title: '2FA Disabled',
            text: data.message || 'Two-factor authentication has been disabled for your account.',
            confirmButtonText: 'OK'
          }).then(() => {
            location.reload(); // Refresh to show updated status
          });
        } else {
          showError('disable_password_error', data.message || 'Invalid password.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showError('disable_password_error', 'Error disabling 2FA. Please try again.');
      });
    }

    // Helper function to show errors
    function showError(elementId, message) {
      const errorElement = document.getElementById(elementId);
      const inputElement = errorElement.previousElementSibling;
      
      errorElement.textContent = message;
      errorElement.style.display = 'block';
      inputElement.classList.add('is-invalid');
      
      // Clear error after 5 seconds
      setTimeout(() => {
        errorElement.style.display = 'none';
        inputElement.classList.remove('is-invalid');
      }, 5000);
    }
  </script>

  <style>
