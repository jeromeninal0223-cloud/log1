<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - Jetlouge Travels Staff Portal</title>

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
                <p class="brand-subtitle">Staff Portal</p>
              </div>
              
              <h2 class="welcome-text">Welcome Back!</h2>
              <p class="welcome-subtitle">
                Access your staff dashboard to manage procurement, logistics, 
                and administrative tasks for travel operations.
              </p>
              
              <ul class="feature-list">
                <li>
                  <i class="bi bi-check"></i>
                  <span>Manage procurement & bidding</span>
                </li>
                <li>
                  <i class="bi bi-check"></i>
                  <span>Handle logistics operations</span>
                </li>
                <li>
                  <i class="bi bi-check"></i>
                  <span>Monitor system analytics</span>
                </li>
                <li>
                  <i class="bi bi-check"></i>
                  <span>Secure staff access</span>
                </li>
              </ul>
            </div>
            
            <!-- Right Side - Login Form -->
            <div class="col-lg-6 login-right">
              <h3 class="text-center mb-4" style="color: var(--jetlouge-primary); font-weight: 700;">
                Sign In to Your Account
              </h3>
              
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
              
              <form id="loginForm" method="POST" action="{{ url('/login') }}">
                @csrf
                <div class="mb-3">
                  <label for="email" class="form-label fw-semibold">Email Address</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-envelope"></i>
                    </span>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="{{ old('email') }}" required>
                  </div>
                </div>
                
                <div class="mb-3">
                  <label for="password" class="form-label fw-semibold">Password</label>
                  <div class="input-group">
                    <span class="input-group-text">
                      <i class="bi bi-lock"></i>
                    </span>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                </div>
                
                <div class="mb-3 form-check">
                  <input type="checkbox" class="form-check-input" id="rememberMe">
                  <label class="form-check-label" for="rememberMe">
                    Remember me
                  </label>
                </div>
                
                <button type="submit" class="btn btn-login mb-3">
                  <i class="bi bi-box-arrow-in-right me-2"></i>
                  Sign In
                </button>
                
                <div class="text-center">
                  <a href="#" class="btn-forgot">Forgot your password?</a>
                </div>

                <hr class="my-4">

                <div class="text-center">
                  <p class="mb-2">Don't have an account?</p>
                  <a href="{{ url('/register') }}" class="btn btn-outline-primary">
                    <i class="bi bi-person-plus me-2"></i>
                    Create New Account
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
      
      // Form submission is now handled by Laravel - no JavaScript needed

      // Add floating animation to shapes
      const shapes = document.querySelectorAll('.shape');
      shapes.forEach((shape, index) => {
        shape.style.animationDelay = `${index * 2}s`;
      });
    });
  </script>
</body>
</html>
