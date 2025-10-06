<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Forgot Password - Jetlouge Travels Staff Portal</title>

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
               <img src="{{ asset('assets/images/jetlouge_logo.png') }}" alt="Jetlouge Travels">
                </div>
                <h1 class="brand-text">Jetlouge Travels</h1>
                <p class="brand-subtitle">Admin Portal</p>
              </div>
              
              <h2 class="welcome-text">Reset Your Password</h2>
              <p class="welcome-subtitle">
                Enter your email address and we'll send you a secure OTP 
                to reset your password safely.
              </p>
              
              <ul class="feature-list">
                <li>
                  <i class="bi bi-shield-check"></i>
                  <span>Secure OTP verification</span>
                </li>
                <li>
                  <i class="bi bi-clock"></i>
                  <span>10-minute expiration</span>
                </li>
                <li>
                  <i class="bi bi-envelope"></i>
                  <span>Email-based authentication</span>
                </li>
                <li>
                  <i class="bi bi-key"></i>
                  <span>One-time use security</span>
                </li>
              </ul>
            </div>
            
            <!-- Right Side - Forgot Password Form -->
            <div class="col-lg-6 login-right">
              <div class="text-center mb-4">
                <i class="bi bi-key-fill text-primary" style="font-size: 3rem;"></i>
                <h3 class="mt-3" style="color: var(--jetlouge-primary); font-weight: 700;">
                  Forgot Your Password?
                </h3>
                <p class="text-muted">No worries! We'll help you reset it securely.</p>
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

              <div id="forgotPasswordAlert"></div>
              
              <form id="forgotPasswordForm">
                @csrf
                <div class="mb-4">
                  <label for="email" class="form-label fw-semibold">Email Address</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" 
                           placeholder="Enter your registered email address" 
                           value="{{ old('email') }}" required>
                  </div>
                  <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>
                    We'll send a 6-digit OTP to this email address
                  </div>
                </div>
                
                <button type="submit" class="btn btn-login mb-3" id="sendOtpBtn">
                  <i class="bi bi-send me-2"></i>
                  Send OTP
                </button>
                
                <div class="text-center">
                  <a href="{{ route('login') }}" class="btn-forgot">
                    <i class="bi bi-arrow-left me-1"></i>Back to Login
                  </a>
                </div>

                <hr class="my-4">
                
                <div class="alert alert-info">
                  <h6 class="alert-heading">
                    <i class="bi bi-lightbulb me-2"></i>Security Tips
                  </h6>
                  <ul class="mb-0 small">
                    <li>OTP will expire in 10 minutes</li>
                    <li>Don't share your OTP with anyone</li>
                    <li>Check your spam folder if you don't receive the email</li>
                    <li>Contact IT support if you continue having issues</li>
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
      
      // Add floating animation to shapes
      const shapes = document.querySelectorAll('.shape');
      shapes.forEach((shape, index) => {
        shape.style.animationDelay = `${index * 2}s`;
      });

      // Utility function to show alerts
      function showAlert(message, type = 'danger') {
        const container = document.getElementById('forgotPasswordAlert');
        container.innerHTML = `
          <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        `;
      }

      // Forgot Password Form Handler
      document.getElementById('forgotPasswordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('sendOtpBtn');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Sending OTP...';
        
        try {
          const formData = new FormData(this);
          const response = await fetch('/password-reset/send-otp', {
            method: 'POST',
            body: formData,
            headers: {
              'X-CSRF-TOKEN': csrfToken
            }
          });
          
          const data = await response.json();
          
          if (data.success) {
            // Redirect to OTP verification page
            const email = formData.get('email');
            window.location.href = `/verify-otp?email=${encodeURIComponent(email)}`;
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
    });
  </script>
</body>
</html>
