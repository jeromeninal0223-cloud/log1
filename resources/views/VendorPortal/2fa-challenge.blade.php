<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Two-Factor Authentication - {{ config('app.name') }}</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    body {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .auth-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      max-width: 450px;
      width: 100%;
    }
    
    .auth-header {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      padding: 2rem;
      text-align: center;
    }
    
    .auth-body {
      padding: 2rem;
    }
    
    .verification-input {
      font-size: 1.5rem;
      letter-spacing: 0.5rem;
      text-align: center;
      border: 2px solid #e9ecef;
      border-radius: 10px;
      padding: 1rem;
      transition: all 0.3s ease;
    }
    
    .verification-input:focus {
      border-color: #667eea;
      box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .btn-verify {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      border: none;
      padding: 0.75rem 2rem;
      border-radius: 10px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-verify:hover {
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }
    
    .backup-code-toggle {
      color: #667eea;
      text-decoration: none;
      font-size: 0.9rem;
    }
    
    .backup-code-toggle:hover {
      color: #764ba2;
      text-decoration: underline;
    }
    
    .security-notice {
      background: #f8f9fa;
      border-left: 4px solid #667eea;
      padding: 1rem;
      border-radius: 5px;
      margin-top: 1rem;
    }
    
    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }
    
    .shake {
      animation: shake 0.5s ease-in-out;
    }
  </style>
</head>
<body>
  <div class="auth-card">
    <div class="auth-header">
      <i class="bi bi-shield-check" style="font-size: 3rem; margin-bottom: 1rem;"></i>
      <h4 class="mb-0">Two-Factor Authentication</h4>
      <p class="mb-0 opacity-75">Enter your verification code</p>
    </div>
    
    <div class="auth-body">
      <div class="text-center mb-4">
        <h6>Welcome back, {{ Auth::guard('vendor')->user()->name }}!</h6>
        <p class="text-muted mb-0">Please enter the 6-digit code from your authenticator app to continue.</p>
      </div>
      
      <!-- Alert Container -->
      <div id="alertContainer" class="mb-3"></div>
      
      <form id="verify2FAForm">
        @csrf
        <div class="mb-4">
          <label for="verification_code" class="form-label">Verification Code</label>
          <input type="text" 
                 class="form-control verification-input" 
                 id="verification_code" 
                 name="verification_code" 
                 maxlength="6" 
                 pattern="[0-9]{6}" 
                 placeholder="000000"
                 autocomplete="off"
                 required>
          <div class="invalid-feedback" id="verification_error"></div>
        </div>
        
        <div class="d-grid mb-3">
          <button type="submit" class="btn btn-primary btn-verify" id="verifyBtn">
            <i class="bi bi-shield-check me-2"></i>Verify & Continue
          </button>
        </div>
      </form>
      
      <div class="text-center">
        <a href="#" class="backup-code-toggle" onclick="toggleBackupCodeForm()">
          <i class="bi bi-key me-1"></i>Use backup code instead
        </a>
      </div>
      
      <!-- Backup Code Form (Hidden by default) -->
      <div id="backupCodeForm" class="d-none mt-4">
        <hr>
        <h6 class="text-center mb-3">Enter Backup Code</h6>
        <form id="backupCodeVerifyForm">
          @csrf
          <div class="mb-3">
            <label for="backup_code" class="form-label">Backup Code</label>
            <input type="text" 
                   class="form-control text-center" 
                   id="backup_code" 
                   name="backup_code" 
                   placeholder="XXXX-XXXX"
                   style="font-family: monospace; font-size: 1.2rem;">
            <small class="text-muted">Enter one of your saved backup codes</small>
          </div>
          <div class="d-grid">
            <button type="submit" class="btn btn-outline-primary">
              <i class="bi bi-key me-2"></i>Verify Backup Code
            </button>
          </div>
        </form>
        
        <div class="text-center mt-3">
          <a href="#" class="backup-code-toggle" onclick="toggleBackupCodeForm()">
            <i class="bi bi-arrow-left me-1"></i>Back to authenticator code
          </a>
        </div>
      </div>
      
      <div class="security-notice">
        <div class="d-flex align-items-start">
          <i class="bi bi-info-circle text-primary me-2 mt-1"></i>
          <div>
            <small class="fw-semibold">Security Notice</small>
            <br>
            <small class="text-muted">
              This extra security step helps protect your account. If you're having trouble, 
              contact support or use one of your backup codes.
            </small>
          </div>
        </div>
      </div>
      
      <div class="text-center mt-4">
        <form action="{{ route('vendor.logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-link text-muted">
            <i class="bi bi-box-arrow-left me-1"></i>Sign out
          </button>
        </form>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const verificationInput = document.getElementById('verification_code');
      const verifyForm = document.getElementById('verify2FAForm');
      const backupForm = document.getElementById('backupCodeVerifyForm');
      
      // Auto-focus on verification input
      verificationInput.focus();
      
      // Only allow numbers in verification code
      verificationInput.addEventListener('input', function(e) {
        this.value = this.value.replace(/[^0-9]/g, '');
        
        // Auto-submit when 6 digits are entered
        if (this.value.length === 6) {
          setTimeout(() => {
            verifyForm.dispatchEvent(new Event('submit'));
          }, 500);
        }
      });
      
      // Handle main verification form
      verifyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const code = verificationInput.value;
        if (code.length !== 6) {
          showAlert('Please enter a valid 6-digit code.', 'danger');
          shakeInput(verificationInput);
          return;
        }
        
        submitVerification(code, false);
      });
      
      // Handle backup code form
      backupForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const backupCode = document.getElementById('backup_code').value.trim();
        if (!backupCode) {
          showAlert('Please enter a backup code.', 'danger');
          return;
        }
        
        submitVerification(backupCode, true);
      });
    });
    
    function submitVerification(code, isBackupCode) {
      const btn = document.getElementById('verifyBtn');
      const originalText = btn.innerHTML;
      
      // Show loading state
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Verifying...';
      
      fetch('{{ route("vendor.2fa.verify") }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          verification_code: code
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          showAlert('Verification successful! Redirecting...', 'success');
          setTimeout(() => {
            window.location.href = data.redirect || '{{ route("vendor.dashboard") }}';
          }, 1500);
        } else {
          throw new Error(data.message || 'Verification failed');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        showAlert(error.message || 'Verification failed. Please try again.', 'danger');
        
        // Shake the input on error
        const input = isBackupCode ? document.getElementById('backup_code') : document.getElementById('verification_code');
        shakeInput(input);
        
        // Clear the input
        input.value = '';
        input.focus();
      })
      .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
      });
    }
    
    function toggleBackupCodeForm() {
      const backupForm = document.getElementById('backupCodeForm');
      const isHidden = backupForm.classList.contains('d-none');
      
      if (isHidden) {
        backupForm.classList.remove('d-none');
        document.getElementById('backup_code').focus();
      } else {
        backupForm.classList.add('d-none');
        document.getElementById('verification_code').focus();
      }
    }
    
    function showAlert(message, type) {
      const alertContainer = document.getElementById('alertContainer');
      const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
      const icon = type === 'success' ? 'bi-check-circle' : 'bi-exclamation-triangle';
      
      alertContainer.innerHTML = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
          <i class="bi ${icon} me-2"></i>${message}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      `;
    }
    
    function shakeInput(input) {
      input.classList.add('shake');
      setTimeout(() => {
        input.classList.remove('shake');
      }, 500);
    }
  </script>
</body>
</html>
