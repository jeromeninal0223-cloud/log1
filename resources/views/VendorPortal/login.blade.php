<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vendor Login - JetLouge Travels</title>

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
        
        <h2 class="welcome-text">Welcome Back!</h2>
        <p class="welcome-subtitle">
          Access your vendor dashboard to manage bids, track orders, 
          and grow your business with JetLouge Travels.
        </p>
        
        <ul class="feature-list">
          <li>
            <i class="bi bi-check"></i>
            <span>Submit competitive bids</span>
          </li>
          <li>
            <i class="bi bi-check"></i>
            <span>Track order status</span>
          </li>
          <li>
            <i class="bi bi-check"></i>
            <span>Manage invoices & payments</span>
          </li>
          <li>
            <i class="bi bi-check"></i>
            <span>Secure vendor access</span>
          </li>
        </ul>
      </div>
      
      <!-- Right Side - Login Form -->
      <div class="col-lg-6 login-right">
        <h3 class="text-center mb-4" style="color: var(--jetlouge-primary); font-weight: 700;">
          Vendor Sign In
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
            {{ session('error') }}
          </div>
        @endif
        
        <form method="POST" action="{{ route('vendor.login') }}" id="loginForm">
          @csrf
          <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-envelope"></i>
              </span>
              <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                     id="email" placeholder="Enter your email" value="{{ old('email') }}" required>
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          
          <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Password</label>
            <div class="input-group">
              <span class="input-group-text">
                <i class="bi bi-lock"></i>
              </span>
              <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                     id="password" placeholder="Enter your password" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye"></i>
              </button>
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          
          <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label class="form-check-label" for="remember">
              Remember me
            </label>
          </div>
          
          <button type="submit" class="btn btn-login mb-3" id="loginBtn">
            <i class="bi bi-box-arrow-in-right me-2"></i>
            Sign In
          </button>
          
          <div class="text-center">
            <a href="#" class="btn-forgot">Forgot your password?</a>
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
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Password toggle functionality
      const togglePassword = document.getElementById('togglePassword');
      const passwordInput = document.getElementById('password');
      
      togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
      });
      
      // Form submission
      const loginForm = document.getElementById('loginForm');
      loginForm.addEventListener('submit', function(e) {
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const submitBtn = document.getElementById('loginBtn');
        const originalText = submitBtn.innerHTML;

        // Clear previous error messages
        clearErrorMessages();

        // Validate email
        if (!email) {
          e.preventDefault();
          showErrorMessage('Please enter your email');
          return false;
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          e.preventDefault();
          showErrorMessage('Please enter a valid email');
          return false;
        }
        
        // Validate password
        if (!password) {
          e.preventDefault();
          showErrorMessage('Please enter your password');
          return false;
        } else if (password.length < 6) {
          e.preventDefault();
          showErrorMessage('Password must be at least 6 characters');
          return false;
        }
        
        // Show loading state
        submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Signing In...';
        submitBtn.disabled = true;
      });

      // Helper functions for error handling
      function showErrorMessage(message) {
        clearErrorMessages();
        const errorDiv = document.createElement('div');
        errorDiv.className = 'alert alert-danger mt-3';
        errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle me-2"></i>${message}`;
        const form = document.getElementById('loginForm');
        form.appendChild(errorDiv);
      }

      function clearErrorMessages() {
        const existingAlerts = document.querySelectorAll('.alert');
        existingAlerts.forEach(alert => alert.remove());
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
