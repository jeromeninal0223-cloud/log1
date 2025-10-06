<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Password Reset Successful - Jetlouge Travels Staff Portal</title>

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
              
              <h2 class="welcome-text">Success!</h2>
              <p class="welcome-subtitle">
                Your password has been reset successfully. You can now 
                access your account with your new password.
              </p>
              
              <ul class="feature-list">
                <li>
                  <i class="bi bi-check-circle"></i>
                  <span>Password updated securely</span>
                </li>
                <li>
                  <i class="bi bi-shield-check"></i>
                  <span>Account security maintained</span>
                </li>
                <li>
                  <i class="bi bi-key"></i>
                  <span>Ready to login</span>
                </li>
                <li>
                  <i class="bi bi-arrow-right"></i>
                  <span>Continue to dashboard</span>
                </li>
              </ul>
            </div>
            
            <!-- Right Side - Success Message -->
            <div class="col-lg-6 login-right">
              <div class="text-center mb-4">
                <div class="mb-4">
                  <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h3 class="mt-3" style="color: var(--jetlouge-primary); font-weight: 700;">
                  Password Reset Complete!
                </h3>
                <p class="text-muted">Your password has been successfully updated.</p>
              </div>
              
              <div class="alert alert-success">
                <h5 class="alert-heading">
                  <i class="bi bi-check-circle me-2"></i>What's Next?
                </h5>
                <p class="mb-3">Your password has been securely updated. You can now:</p>
                <ul class="mb-0">
                  <li>Login with your new password</li>
                  <li>Access all your account features</li>
                  <li>Continue with your work seamlessly</li>
                </ul>
              </div>
              
              <div class="d-grid gap-2">
                <a href="{{ route('login') }}" class="btn btn-login">
                  <i class="bi bi-box-arrow-in-right me-2"></i>
                  Login Now
                </a>
              </div>

              <hr class="my-4">
              
              <div class="alert alert-info">
                <h6 class="alert-heading">
                  <i class="bi bi-lightbulb me-2"></i>Security Reminders
                </h6>
                <ul class="mb-0 small">
                  <li>Keep your password confidential</li>
                  <li>Use a unique password for this account</li>
                  <li>Consider using a password manager</li>
                  <li>Contact IT support if you notice any suspicious activity</li>
                </ul>
              </div>
              
              <div class="text-center mt-4">
                <p class="text-muted small">
                  <i class="bi bi-clock me-1"></i>
                  Redirecting to login in <span id="countdown">10</span> seconds...
                </p>
              </div>
            </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Add floating animation to shapes
      const shapes = document.querySelectorAll('.shape');
      shapes.forEach((shape, index) => {
        shape.style.animationDelay = `${index * 2}s`;
      });

      // Auto-redirect countdown
      let countdown = 10;
      const countdownElement = document.getElementById('countdown');
      
      const timer = setInterval(() => {
        countdown--;
        countdownElement.textContent = countdown;
        
        if (countdown <= 0) {
          clearInterval(timer);
          window.location.href = '{{ route("login") }}?reset=success';
        }
      }, 1000);
    });
  </script>
</body>
</html>
