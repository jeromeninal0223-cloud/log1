<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password - JetLouge Travels</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Login Page Styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/login-style.css') }}">
</head>
<body>
  <div class="login-container">
    <div class="row g-0">
      <!-- Left Side - Welcome -->
      <div class="col-lg-6 login-left">
        <div class="floating-shapes">
          <div class="shape"></div>
          <div class="shape"></div>
          <div class="shape"></div>
        </div>
        
        <div class="logo-container">
          <div class="logo-box">
            <img src="{{ asset('assets/images/jetlouge_logo.png') }}" alt="JetLouge Travels">
          </div>
          <h1 class="brand-text">JetLouge Travels</h1>
          <p class="brand-subtitle">Vendor Portal</p>
        </div>
        
        <h2 class="welcome-text">Reset Your Password</h2>
        <p class="welcome-subtitle">
          Enter your email address and we'll send you a secure link to reset your password and regain access to your vendor account.
        </p>
        
        <ul class="feature-list">
          <li>
            <i class="bi bi-shield-check"></i>
            <span>Secure password reset</span>
          </li>
          <li>
            <i class="bi bi-envelope-check"></i>
            <span>Email verification required</span>
          </li>
          <li>
            <i class="bi bi-clock"></i>
            <span>One-time use reset link</span>
          </li>
          <li>
            <i class="bi bi-lock"></i>
            <span>Encrypted data protection</span>
          </li>
        </ul>
      </div>
      
      <!-- Right Side - Forgot Password Form -->
      <div class="col-lg-6 login-right">
        <h3 class="text-center mb-4" style="color: var(--jetlouge-primary); font-weight: 700;">
          <i class="bi bi-key me-2"></i>Forgot Password
        </h3>
        
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        
        @if (session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif
        
        @if (session('error'))
          <div class="alert alert-danger">
            <i class="bi bi-exclamation-triangle me-2"></i>
            {{ session('error') }}
          </div>
        @endif

        @if (session('status'))
          <!-- Success State -->
          <div class="text-center">
            <div class="mb-4">
              <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            </div>
            <h5 class="fw-bold mb-3">Reset Link Sent!</h5>
            <p class="text-muted mb-4">
              We've sent a password reset link to your email address. 
              Please check your email and follow the instructions to reset your password.
            </p>
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              <small>
                Didn't receive the email? Check your spam folder or try again in a few minutes.
              </small>
            </div>
            <div class="d-grid gap-2">
              <a href="{{ route('vendor.login') }}" class="btn btn-primary">
                <i class="bi bi-arrow-left me-2"></i>Back to Login
              </a>
              <a href="{{ route('vendor.forgot-password') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise me-2"></i>Send Another Link
              </a>
            </div>
          </div>
        @else
          <!-- Form State -->
          <div class="mb-4">
            <p class="text-muted">
              Enter your registered email address below and we'll send you a link to reset your password.
            </p>
          </div>

          <form method="POST" action="{{ route('vendor.forgot-password.submit') }}" id="forgotPasswordForm">
            @csrf
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email Address</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-envelope"></i>
                </span>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                       id="email" placeholder="Enter your registered email" value="{{ old('email') }}" required>
                @error('email')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <button type="submit" class="btn btn-login mb-3" id="sendResetBtn">
              <i class="bi bi-send me-2"></i>
              Send Reset Link
            </button>
            
            <div class="text-center">
              <a href="{{ route('vendor.login') }}" class="btn-forgot">
                <i class="bi bi-arrow-left me-1"></i>Back to Login
              </a>
            </div>

            <hr class="my-4">

            <div class="text-center">
              <p class="mb-2">Don't have a vendor account?</p>
              <a href="{{ route('vendor.register') }}" class="btn btn-outline-primary">
                <i class="bi bi-person-plus me-2"></i>
                Register as Vendor
              </a>
            </div>
          </form>
        @endif
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Form submission
      const forgotPasswordForm = document.getElementById('forgotPasswordForm');
      if (forgotPasswordForm) {
        forgotPasswordForm.addEventListener('submit', function(e) {
          const email = document.getElementById('email').value;
          const submitBtn = document.getElementById('sendResetBtn');
          const originalText = submitBtn.innerHTML;

          // Clear previous error messages
          clearErrorMessages();

          // Validate email
          if (!email) {
            e.preventDefault();
            showErrorMessage('Please enter your email address');
            return false;
          } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            e.preventDefault();
            showErrorMessage('Please enter a valid email address');
            return false;
          }
          
          // Show loading state
          submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Sending Reset Link...';
          submitBtn.disabled = true;
        });
      }

      // Helper functions for error handling
      function showErrorMessage(message) {
        clearErrorMessages();
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-3';
        errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i>${message}`;
        const form = document.getElementById('forgotPasswordForm');
        if (form) {
          form.appendChild(errorDiv);
        }
      }

      function clearErrorMessages() {
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => {
          if (alert.classList.contains('alert-danger') && !alert.querySelector('.btn-close')) {
            alert.remove();
          }
        });
      }

      // Add floating animation to shapes
      const shapes = document.querySelectorAll('.shape');
      shapes.forEach((shape, index) => {
        shape.style.animationDelay = `${index * 2}s`;
      });
    });
  </script>
</body>
</html>
