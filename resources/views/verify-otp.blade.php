<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Verify OTP - Jetlouge Travels Staff Portal</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Login Page Styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/login-style.css') }}">
  
  <style>
    .otp-input {
      font-size: 1.5rem;
      text-align: center;
      letter-spacing: 0.5rem;
      font-family: 'Courier New', monospace;
      font-weight: bold;
    }
    
    .countdown-timer {
      font-size: 1.1rem;
      font-weight: 600;
      color: #dc3545;
    }
    
    .countdown-timer.warning {
      color: #fd7e14;
      animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
      0% { opacity: 1; }
      50% { opacity: 0.5; }
      100% { opacity: 1; }
    }
  </style>
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
               <img src="{{ asset('assets/images/jetlouge_logo.png') }}" alt="Jetlouge Travels">
                </div>
                <h1 class="brand-text">Jetlouge Travels</h1>
                <p class="brand-subtitle">Admin Portal</p>
              </div>
              
              <h2 class="welcome-text">Almost There!</h2>
              <p class="welcome-subtitle">
                We've sent a secure OTP to your email. Enter the code 
                and create your new password to regain access.
              </p>
              
              <ul class="feature-list">
                <li>
                  <i class="bi bi-envelope-check"></i>
                  <span>Check your email inbox</span>
                </li>
                <li>
                  <i class="bi bi-stopwatch"></i>
                  <span>OTP expires in 10 minutes</span>
                </li>
                <li>
                  <i class="bi bi-shield-lock"></i>
                  <span>Secure password reset</span>
                </li>
                <li>
                  <i class="bi bi-arrow-clockwise"></i>
                  <span>Resend if needed</span>
                </li>
              </ul>
            </div>
            
            <!-- Right Side - OTP Verification Form -->
            <div class="col-lg-6 login-right">
              <div class="text-center mb-4">
                <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-3" style="color: var(--jetlouge-primary); font-weight: 700;">
                  Verify Your Identity
                </h3>
                <p class="text-muted">Enter the OTP sent to <strong id="emailDisplay">{{ request('email') }}</strong></p>
              </div>
              
              <!-- Display success message -->
              @if (session('success'))
                <div class="alert alert-success">
                  <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                </div>
              @endif

              <!-- Display validation errors -->
              @if ($errors->any())
                <div class="alert alert-danger">
                  <i class="bi bi-exclamation-triangle me-2"></i>
                  <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif

              <div id="otpVerificationAlert"></div>
              
              <form id="otpVerificationForm">
                @csrf
                <input type="hidden" id="email" name="email" value="{{ request('email') }}">
                
                <div class="mb-4">
                  <label for="otp" class="form-label fw-semibold">Enter 6-Digit OTP</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-key"></i>
                    </span>
                    <input type="text" class="form-control otp-input" id="otp" name="otp" 
                           placeholder="000000" maxlength="6" required>
                  </div>
                  <div class="form-text d-flex justify-content-between align-items-center">
                    <span id="otpTimer" class="countdown-timer"></span>
                    <a href="#" id="resendOtpLink" class="text-decoration-none" style="display: none;">
                      <i class="bi bi-arrow-clockwise me-1"></i>Resend OTP
                    </a>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="password" class="form-label fw-semibold">New Password</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Enter new password" minlength="8" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
                
                <div class="mb-4">
                  <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-lock-fill"></i>
                    </span>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" 
                           placeholder="Confirm new password" minlength="8" required>
                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
                
                <button type="submit" class="btn btn-login mb-3" id="resetPasswordBtn">
                  <i class="bi bi-check-circle me-2"></i>
                  Reset Password
                </button>
                
                <div class="text-center">
                  <a href="{{ route('forgot.password') }}" class="btn-forgot me-3">
                    <i class="bi bi-arrow-left me-1"></i>Back
                  </a>
                  <a href="{{ route('login') }}" class="btn-forgot">
                    <i class="bi bi-house me-1"></i>Login Page
                  </a>
                </div>

                <hr class="my-4">
                
                <div class="alert alert-warning">
                  <h6 class="alert-heading">
                    <i class="bi bi-exclamation-triangle me-2"></i>Security Notice
                  </h6>
                  <ul class="mb-0 small">
                    <li>Don't share your OTP with anyone</li>
                    <li>Use a strong password with at least 8 characters</li>
                    <li>Include uppercase, lowercase, numbers, and symbols</li>
                    <li>Don't reuse old passwords</li>
                  </ul>
                </div>
              </form>
            </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // CSRF Token setup
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      
      // Check if email is provided
      const email = document.getElementById('email').value;
      if (!email) {
        window.location.href = '/forgot-password';
        return;
      }
      
      // Add floating animation to shapes
      const shapes = document.querySelectorAll('.shape');
      shapes.forEach((shape, index) => {
        shape.style.animationDelay = `${index * 2}s`;
      });

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

      const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
      const confirmPasswordInput = document.getElementById('password_confirmation');
      
      toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
      });

      // OTP Timer variables
      let otpTimer;
      let timeLeft = 600; // 10 minutes in seconds

      // Utility function to show alerts
      function showAlert(message, type = 'danger') {
        const container = document.getElementById('otpVerificationAlert');
        container.innerHTML = `
          <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        `;
      }

      // Start OTP countdown timer
      function startOtpTimer() {
        timeLeft = 600; // Reset to 10 minutes
        const timerElement = document.getElementById('otpTimer');
        const resendLink = document.getElementById('resendOtpLink');
        
        resendLink.style.display = 'none';
        
        otpTimer = setInterval(function() {
          const minutes = Math.floor(timeLeft / 60);
          const seconds = timeLeft % 60;
          timerElement.textContent = `Expires in ${minutes}:${seconds.toString().padStart(2, '0')}`;
          
          // Add warning class when less than 2 minutes
          if (timeLeft <= 120) {
            timerElement.classList.add('warning');
          } else {
            timerElement.classList.remove('warning');
          }
          
          if (timeLeft <= 0) {
            clearInterval(otpTimer);
            timerElement.textContent = 'OTP has expired';
            timerElement.classList.remove('warning');
            timerElement.style.color = '#dc3545';
            resendLink.style.display = 'inline';
          }
          timeLeft--;
        }, 1000);
      }

      // Start timer on page load
      startOtpTimer();

      // OTP Verification Form Handler
      document.getElementById('otpVerificationForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('resetPasswordBtn');
        const originalText = submitBtn.innerHTML;
        
        // Validate password confirmation
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('password_confirmation').value;
        
        if (password !== confirmPassword) {
          showAlert('Passwords do not match.');
          return;
        }
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Resetting Password...';
        
        try {
          const formData = new FormData(this);
          const response = await fetch('/password-reset/verify-otp', {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': csrfToken
            }
          });
          
          const data = await response.json();
          
          if (data.success) {
            // Clear timer
            if (otpTimer) {
              clearInterval(otpTimer);
            }
            
            // Show success message
            showAlert(data.message, 'success');
            
            // Redirect to success page or login page
            setTimeout(() => {
              if (data.redirect) {
                window.location.href = data.redirect;
              } else {
                window.location.href = '/login?reset=success';
              }
            }, 1500);
          } else {
            showAlert(data.message);
          }
        } catch (error) {
          showAlert('Network error. Please try again.');
        } finally {
          // Reset button state
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalText;
        }
      });

      // Resend OTP Handler
      document.getElementById('resendOtpLink').addEventListener('click', async function(e) {
        e.preventDefault();
        
        const originalText = this.innerHTML;
        this.innerHTML = '<i class="spinner-border spinner-border-sm me-1"></i>Sending...';
        
        try {
          const formData = new FormData();
          formData.append('email', email);
          
          const response = await fetch('/password-reset/resend-otp', {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': csrfToken
            }
          });
          
          const data = await response.json();
          
          if (data.success) {
            showAlert('New OTP sent successfully! Please check your email.', 'success');
            startOtpTimer();
          } else {
            showAlert(data.message);
          }
        } catch (error) {
          showAlert('Failed to resend OTP. Please try again.');
        } finally {
          this.innerHTML = originalText;
        }
      });

      // Auto-format OTP input (numbers only)
      document.getElementById('otp').addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
      });
    });
  </script>
</body>
</html>
