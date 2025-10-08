<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vendor Login - JetLouge Travels</title>
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
            <a href="#" class="btn-forgot" data-bs-toggle="modal" data-bs-target="#forgotPasswordModal">Forgot your password?</a>
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

  <!-- Forgot Password Modal -->
  <div class="modal fade" id="forgotPasswordModal" tabindex="-1" aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title fw-bold" id="forgotPasswordModalLabel" style="color: var(--jetlouge-primary);">
            <i class="bi bi-key me-2"></i>Reset Password
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div id="forgotPasswordStep1">
            <p class="text-muted mb-4">
              Enter your email address and we'll send you a link to reset your password.
            </p>
            
            <form id="forgotPasswordForm">
              @csrf
              <div class="mb-3">
                <label for="forgotEmail" class="form-label fw-semibold">Email Address</label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-envelope"></i>
                  </span>
                  <input type="email" class="form-control" id="forgotEmail" name="email" 
                         placeholder="Enter your registered email" required>
                </div>
                <div class="invalid-feedback" id="forgotEmailError"></div>
              </div>
              
              <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary" id="sendResetBtn">
                  <i class="bi bi-send me-2"></i>Send Reset Link
                </button>
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                  Cancel
                </button>
              </div>
            </form>
          </div>
          
          <!-- Success Step -->
          <div id="forgotPasswordStep2" class="text-center" style="display: none;">
            <div class="mb-4">
              <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
            </div>
            <h5 class="fw-bold mb-3">Reset Link Sent!</h5>
            <p class="text-muted mb-4">
              We've sent a password reset link to <strong id="sentToEmail"></strong>. 
              Please check your email and follow the instructions to reset your password.
            </p>
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              <small>
                Didn't receive the email? Check your spam folder or 
                <a href="#" id="resendLink" class="text-decoration-none">resend the link</a>.
              </small>
            </div>
            <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
              Got it, thanks!
            </button>
          </div>
        </div>
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

      // Forgot Password functionality
      const forgotPasswordForm = document.getElementById('forgotPasswordForm');
      const forgotPasswordModal = document.getElementById('forgotPasswordModal');
      const step1 = document.getElementById('forgotPasswordStep1');
      const step2 = document.getElementById('forgotPasswordStep2');
      const sendResetBtn = document.getElementById('sendResetBtn');
      const resendLink = document.getElementById('resendLink');

      // Reset modal when it's closed
      forgotPasswordModal.addEventListener('hidden.bs.modal', function() {
        resetForgotPasswordModal();
      });

      // Handle forgot password form submission
      forgotPasswordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const email = document.getElementById('forgotEmail').value;
        const emailError = document.getElementById('forgotEmailError');
        const emailInput = document.getElementById('forgotEmail');
        
        // Clear previous errors
        emailInput.classList.remove('is-invalid');
        emailError.textContent = '';
        
        // Validate email
        if (!email) {
          showForgotPasswordError('Please enter your email address');
          return;
        }
        
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
          showForgotPasswordError('Please enter a valid email address');
          return;
        }
        
        // Show loading state
        const originalText = sendResetBtn.innerHTML;
        sendResetBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Sending...';
        sendResetBtn.disabled = true;
        
        // Simulate API call (replace with actual endpoint)
        sendPasswordResetRequest(email)
          .then(response => {
            if (response.success) {
              showSuccessStep(email);
            } else {
              throw new Error(response.message || 'Failed to send reset link');
            }
          })
          .catch(error => {
            showForgotPasswordError(error.message || 'Failed to send reset link. Please try again.');
            sendResetBtn.innerHTML = originalText;
            sendResetBtn.disabled = false;
          });
      });

      // Handle resend link
      resendLink.addEventListener('click', function(e) {
        e.preventDefault();
        const email = document.getElementById('sentToEmail').textContent;
        
        resendLink.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Sending...';
        
        sendPasswordResetRequest(email)
          .then(response => {
            if (response.success) {
              resendLink.innerHTML = 'Resent!';
              setTimeout(() => {
                resendLink.innerHTML = 'resend the link';
              }, 3000);
            } else {
              throw new Error(response.message || 'Failed to resend');
            }
          })
          .catch(error => {
            resendLink.innerHTML = 'Failed to resend';
            setTimeout(() => {
              resendLink.innerHTML = 'resend the link';
            }, 3000);
          });
      });

      // Helper functions
      function resetForgotPasswordModal() {
        step1.style.display = 'block';
        step2.style.display = 'none';
        document.getElementById('forgotEmail').value = '';
        document.getElementById('forgotEmail').classList.remove('is-invalid');
        document.getElementById('forgotEmailError').textContent = '';
        sendResetBtn.innerHTML = '<i class="bi bi-send me-2"></i>Send Reset Link';
        sendResetBtn.disabled = false;
      }

      function showForgotPasswordError(message) {
        const emailInput = document.getElementById('forgotEmail');
        const emailError = document.getElementById('forgotEmailError');
        
        emailInput.classList.add('is-invalid');
        emailError.textContent = message;
        emailError.style.display = 'block';
      }

      function showSuccessStep(email) {
        document.getElementById('sentToEmail').textContent = email;
        step1.style.display = 'none';
        step2.style.display = 'block';
      }

      async function sendPasswordResetRequest(email) {
        try {
          const response = await fetch('/vendor/forgot-password', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ email: email })
          });

          const data = await response.json();
          
          if (!response.ok) {
            throw new Error(data.message || 'Network error occurred');
          }
          
          return data;
        } catch (error) {
          console.error('Password reset error:', error);
          throw error;
        }
      }
    });
  </script>
</body>
</html>
