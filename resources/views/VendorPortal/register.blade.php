<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Vendor Registration - JetLouge Travels</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Register Page Styles -->
  <link rel="stylesheet" href="{{ asset('assets/css/register-style.css') }}">
  
  <style>
    .file-upload-wrapper {
      position: relative;
      border: 2px dashed #dee2e6;
      border-radius: 8px;
      padding: 20px;
      text-align: center;
      transition: all 0.3s ease;
      cursor: pointer;
      background-color: #f8f9fa;
    }
    
    .file-upload-wrapper:hover {
      border-color: var(--jetlouge-primary);
      background-color: #f0f8ff;
    }
    
    .file-upload-wrapper.has-file {
      border-color: #28a745;
      background-color: #f8fff8;
    }
    
    .file-input {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      opacity: 0;
      cursor: pointer;
    }
    
    .file-upload-info {
      pointer-events: none;
    }
    
    .file-upload-info i {
      font-size: 2rem;
      color: #6c757d;
      margin-bottom: 10px;
      display: block;
    }
    
    .file-upload-wrapper:hover .file-upload-info i {
      color: var(--jetlouge-primary);
    }
    
    .file-upload-wrapper.has-file .file-upload-info i {
      color: #28a745;
    }
    
    .file-name {
      font-weight: 500;
      color: #495057;
    }
    
    .file-size {
      font-size: 0.875rem;
      color: #6c757d;
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div class="register-container">
    <div class="row g-0">
      <!-- Left Side - Welcome -->
      <div class="col-lg-6 register-left">
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

        <h2 class="welcome-text">Join Our Vendor Network!</h2>
        <p class="welcome-subtitle">
          Register as a vendor to access exclusive business opportunities,
          submit competitive bids, and grow your business with JetLouge Travels.
        </p>

        <ul class="feature-list">
          <li>
            <i class="bi bi-check"></i>
            <span>Access to bidding opportunities</span>
          </li>
          <li>
            <i class="bi bi-check"></i>
            <span>Secure payment processing</span>
          </li>
          <li>
            <i class="bi bi-check"></i>
            <span>Order management system</span>
          </li>
          <li>
            <i class="bi bi-check"></i>
            <span>24/7 vendor support</span>
          </li>
        </ul>
      </div>
      
      <!-- Right Side - Register Form -->
      <div class="col-lg-6 register-right">
        <h3 class="text-center mb-4" style="color: var(--jetlouge-primary); font-weight: 700;">
          Create Your Vendor Account
        </h3>
        
        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        
        @if ($errors->any())
          <div class="alert alert-danger">
            <ul class="mb-0">
              @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif
        
        <form method="POST" action="{{ route('vendor.register.submit') }}" id="registerForm" enctype="multipart/form-data">
          @csrf
          
          <!-- Step 1: Personal Information -->
          <div id="step1" class="registration-step">
            <h5 class="mb-3 text-primary">Step 1: Personal Information</h5>
            
            <div class="mb-3">
              <label for="name" class="form-label fw-semibold">Full Name *</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-person"></i>
                </span>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                       id="name" placeholder="Enter your full name" value="{{ old('name') }}" required>
                @error('name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email Address *</label>
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
              <label for="password" class="form-label fw-semibold">Password *</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-lock"></i>
                </span>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" 
                       id="password" placeholder="Create a password" required>
                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                  <i class="bi bi-eye"></i>
                </button>
                @error('password')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
              <div class="password-strength mt-2">
                <div class="strength-bar">
                  <div class="strength-fill" id="strengthFill"></div>
                </div>
                <small class="text-muted" id="strengthText">Password strength</small>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="password_confirmation" class="form-label fw-semibold">Confirm Password *</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-lock-fill"></i>
                </span>
                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" 
                       id="password_confirmation" placeholder="Confirm your password" required>
                <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                  <i class="bi bi-eye"></i>
                </button>
                @error('password_confirmation')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="d-flex justify-content-end">
              <button type="button" class="btn btn-primary" onclick="nextStep()">
                Next Step <i class="bi bi-arrow-right ms-2"></i>
              </button>
            </div>
          </div>
          
          <!-- Step 2: Company Information -->
          <div id="step2" class="registration-step" style="display: none;">
            <h5 class="mb-3 text-primary">Step 2: Company Information</h5>
            
            <div class="mb-3">
              <label for="company_name" class="form-label fw-semibold">Company Name *</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-building"></i>
                </span>
                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror" 
                       id="company_name" placeholder="Enter your company name" value="{{ old('company_name') }}" required>
                @error('company_name')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="mb-3">
              <label for="phone" class="form-label fw-semibold">Phone Number *</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-telephone"></i>
                </span>
                <input type="tel" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                       id="phone" placeholder="Enter your phone number" value="{{ old('phone') }}" required>
                @error('phone')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="mb-3">
              <label for="business_type" class="form-label fw-semibold">Business Type *</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-briefcase"></i>
                </span>
                <select name="business_type" class="form-select @error('business_type') is-invalid @enderror" 
                        id="business_type" required>
                  <option value="">Select your business type</option>
                  <option value="Logistics & Transportation" {{ old('business_type') == 'Logistics & Transportation' ? 'selected' : '' }}>Logistics & Transportation</option>
                  <option value="Technology & Software" {{ old('business_type') == 'Technology & Software' ? 'selected' : '' }}>Technology & Software</option>
                  <option value="Vehicle Services" {{ old('business_type') == 'Vehicle Services' ? 'selected' : '' }}>Vehicle Services</option>
                  <option value="International Logistics" {{ old('business_type') == 'International Logistics' ? 'selected' : '' }}>International Logistics</option>
                  <option value="Consulting" {{ old('business_type') == 'Consulting' ? 'selected' : '' }}>Consulting</option>
                  <option value="Manufacturing" {{ old('business_type') == 'Manufacturing' ? 'selected' : '' }}>Manufacturing</option>
                  <option value="Retail" {{ old('business_type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                  <option value="Other" {{ old('business_type') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
                @error('business_type')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <div class="mb-3">
              <label for="address" class="form-label fw-semibold">Business Address *</label>
              <div class="input-group">
                <span class="input-group-text">
                  <i class="bi bi-geo-alt"></i>
                </span>
                <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                          id="address" rows="3" placeholder="Enter your business address" required>{{ old('address') }}</textarea>
                @error('address')
                  <div class="invalid-feedback">{{ $message }}</div>
                @enderror
              </div>
            </div>
            
            <!-- Document Upload Section -->
            <div class="mb-4">
              <h6 class="text-primary mb-3">Required Documents</h6>
              <p class="text-muted small mb-3">Please upload the following documents to verify your business legitimacy. Accepted formats: PDF, JPG, PNG (Max 5MB each)</p>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="business_license" class="form-label fw-semibold">Business License *</label>
                  <div class="file-upload-wrapper">
                    <input type="file" name="business_license" class="form-control file-input @error('business_license') is-invalid @enderror" 
                           id="business_license" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="file-upload-info">
                      <i class="bi bi-file-earmark-text"></i>
                      <span class="file-name">Choose business license file</span>
                    </div>
                    @error('business_license')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="tax_certificate" class="form-label fw-semibold">Tax Certificate *</label>
                  <div class="file-upload-wrapper">
                    <input type="file" name="tax_certificate" class="form-control file-input @error('tax_certificate') is-invalid @enderror" 
                           id="tax_certificate" accept=".pdf,.jpg,.jpeg,.png" required>
                    <div class="file-upload-info">
                      <i class="bi bi-file-earmark-text"></i>
                      <span class="file-name">Choose tax certificate file</span>
                    </div>
                    @error('tax_certificate')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="insurance_certificate" class="form-label fw-semibold">Insurance Certificate</label>
                  <div class="file-upload-wrapper">
                    <input type="file" name="insurance_certificate" class="form-control file-input @error('insurance_certificate') is-invalid @enderror" 
                           id="insurance_certificate" accept=".pdf,.jpg,.jpeg,.png">
                    <div class="file-upload-info">
                      <i class="bi bi-file-earmark-text"></i>
                      <span class="file-name">Choose insurance certificate (optional)</span>
                    </div>
                    @error('insurance_certificate')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                </div>
                
                <div class="col-md-6 mb-3">
                  <label for="additional_documents" class="form-label fw-semibold">Additional Documents</label>
                  <div class="file-upload-wrapper">
                    <input type="file" name="additional_documents[]" class="form-control file-input @error('additional_documents') is-invalid @enderror" 
                           id="additional_documents" accept=".pdf,.jpg,.jpeg,.png" multiple>
                    <div class="file-upload-info">
                      <i class="bi bi-files"></i>
                      <span class="file-name">Choose additional files (optional)</span>
                    </div>
                    @error('additional_documents')
                      <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                  </div>
                  <small class="text-muted">You can select multiple files. Examples: certifications, permits, references</small>
                </div>
              </div>
            </div>
            
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="agreeTerms" required>
              <label class="form-check-label" for="agreeTerms">
                I agree to the <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service</a> and <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacy Policy</a>
              </label>
            </div>
            
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-outline-secondary" onclick="prevStep()">
                <i class="bi bi-arrow-left me-2"></i>Previous Step
              </button>
              <button type="submit" class="btn btn-register" id="submitBtn">
                <i class="bi bi-person-plus me-2"></i>Create Account
              </button>
            </div>
          </div>
        </form>
        
        <hr class="my-4">
        
        <div class="text-center">
          <p class="mb-2">Already have a vendor account?</p>
          <a href="{{ route('vendor.login') }}" class="btn-back-login">
            <i class="bi bi-arrow-left me-2"></i>
            Back to Login
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- Terms of Service Modal -->
  <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="termsModalLabel">Terms of Service</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h6>1. Acceptance of Terms</h6>
          <p>By registering as a vendor with JetLouge Travels, you agree to be bound by these Terms of Service and all applicable laws and regulations.</p>
          
          <h6>2. Vendor Registration</h6>
          <p>You must provide accurate, current, and complete information during the registration process. You are responsible for maintaining the confidentiality of your account credentials.</p>
          
          <h6>3. Vendor Obligations</h6>
          <ul>
            <li>Provide accurate business information and documentation</li>
            <li>Maintain valid licenses and certifications</li>
            <li>Deliver services as agreed in contracts</li>
            <li>Comply with all applicable laws and regulations</li>
            <li>Maintain professional standards in all communications</li>
          </ul>
          
          <h6>4. Bidding Process</h6>
          <p>Vendors may submit bids for available opportunities. All bids must be accurate and reflect true costs. JetLouge Travels reserves the right to accept or reject any bid at its discretion.</p>
          
          <h6>5. Payment Terms</h6>
          <p>Payment terms will be specified in individual contracts. Standard payment terms are Net 30 days from invoice approval. Late payments may incur additional charges.</p>
          
          <h6>6. Quality Standards</h6>
          <p>All services must meet JetLouge Travels' quality standards. Failure to meet standards may result in contract termination and removal from the vendor network.</p>
          
          <h6>7. Confidentiality</h6>
          <p>Vendors must maintain strict confidentiality regarding all business information, client data, and proprietary processes of JetLouge Travels.</p>
          
          <h6>8. Termination</h6>
          <p>Either party may terminate the vendor relationship with 30 days written notice. JetLouge Travels may terminate immediately for breach of terms.</p>
          
          <h6>9. Limitation of Liability</h6>
          <p>JetLouge Travels' liability is limited to the contract value. Vendors are responsible for their own insurance and liability coverage.</p>
          
          <h6>10. Governing Law</h6>
          <p>These terms are governed by the laws of the jurisdiction where JetLouge Travels is incorporated.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Privacy Policy Modal -->
  <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="privacyModalLabel">Privacy Policy</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <h6>1. Information We Collect</h6>
          <p>We collect information you provide during registration including:</p>
          <ul>
            <li>Personal information (name, email, phone)</li>
            <li>Business information (company name, address, business type)</li>
            <li>Financial information for payment processing</li>
            <li>Communication records and transaction history</li>
          </ul>
          
          <h6>2. How We Use Your Information</h6>
          <p>Your information is used to:</p>
          <ul>
            <li>Process your vendor registration and maintain your account</li>
            <li>Facilitate bidding opportunities and contract management</li>
            <li>Process payments and maintain financial records</li>
            <li>Communicate about business opportunities and updates</li>
            <li>Comply with legal and regulatory requirements</li>
          </ul>
          
          <h6>3. Information Sharing</h6>
          <p>We do not sell your personal information. We may share information with:</p>
          <ul>
            <li>Service providers who assist in business operations</li>
            <li>Legal authorities when required by law</li>
            <li>Business partners for specific project collaborations (with consent)</li>
          </ul>
          
          <h6>4. Data Security</h6>
          <p>We implement appropriate security measures to protect your information including encryption, secure servers, and access controls. However, no system is 100% secure.</p>
          
          <h6>5. Data Retention</h6>
          <p>We retain your information for as long as your vendor account is active and for a reasonable period thereafter for legal and business purposes.</p>
          
          <h6>6. Your Rights</h6>
          <p>You have the right to:</p>
          <ul>
            <li>Access and update your personal information</li>
            <li>Request deletion of your data (subject to legal requirements)</li>
            <li>Opt-out of non-essential communications</li>
            <li>Request a copy of your data</li>
          </ul>
          
          <h6>7. Cookies and Tracking</h6>
          <p>We use cookies to improve your experience on our platform. You can control cookie settings through your browser preferences.</p>
          
          <h6>8. Third-Party Links</h6>
          <p>Our platform may contain links to third-party websites. We are not responsible for the privacy practices of these external sites.</p>
          
          <h6>9. Updates to This Policy</h6>
          <p>We may update this privacy policy periodically. We will notify you of significant changes via email or platform notifications.</p>
          
          <h6>10. Contact Us</h6>
          <p>For privacy-related questions or concerns, contact us at privacy@jetlougetravels.com or through our vendor support portal.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
      const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
      const confirmPasswordInput = document.getElementById('password_confirmation');
      
      togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
      });
      
      toggleConfirmPassword.addEventListener('click', function() {
        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        confirmPasswordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('bi-eye');
        icon.classList.toggle('bi-eye-slash');
      });
      
      // Password strength checker
      passwordInput.addEventListener('input', function() {
        const password = this.value;
        const strengthFill = document.getElementById('strengthFill');
        const strengthText = document.getElementById('strengthText');
        
        let strength = 0;
        let text = 'Weak';
        let color = '#dc3545';
        
        if (password.length >= 8) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        if (strength >= 4) {
          text = 'Strong';
          color = '#28a745';
        } else if (strength >= 2) {
          text = 'Medium';
          color = '#ffc107';
        }
        
        strengthFill.style.width = (strength * 20) + '%';
        strengthFill.style.backgroundColor = color;
        strengthText.textContent = text;
        strengthText.style.color = color;
      });
      
      // Password confirmation validation
      confirmPasswordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        const confirmPassword = this.value;
        
        if (confirmPassword && password !== confirmPassword) {
          this.classList.add('is-invalid');
          this.classList.remove('is-valid');
        } else if (confirmPassword) {
          this.classList.add('is-valid');
          this.classList.remove('is-invalid');
        }
      });
      
      // Form submission
      const registerForm = document.getElementById('registerForm');
      registerForm.addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = '<i class="bi bi-arrow-clockwise me-2"></i>Creating Account...';
        submitBtn.disabled = true;
      });

      // Add floating animation to shapes
      const shapes = document.querySelectorAll('.shape');
      shapes.forEach((shape, index) => {
        shape.style.animationDelay = `${index * 2}s`;
      });
      
      // File upload handling
      const fileInputs = document.querySelectorAll('.file-input');
      fileInputs.forEach(input => {
        const wrapper = input.closest('.file-upload-wrapper');
        const fileNameSpan = wrapper.querySelector('.file-name');
        const originalText = fileNameSpan.textContent;
        
        input.addEventListener('change', function() {
          const files = this.files;
          if (files.length > 0) {
            wrapper.classList.add('has-file');
            
            if (files.length === 1) {
              const file = files[0];
              const fileSize = (file.size / 1024 / 1024).toFixed(2);
              fileNameSpan.innerHTML = `${file.name}<br><span class="file-size">${fileSize} MB</span>`;
            } else {
              fileNameSpan.innerHTML = `${files.length} files selected<br><span class="file-size">Multiple files</span>`;
            }
          } else {
            wrapper.classList.remove('has-file');
            fileNameSpan.textContent = originalText;
          }
        });
        
        // File size validation
        input.addEventListener('change', function() {
          const maxSize = 5 * 1024 * 1024; // 5MB
          let hasError = false;
          
          Array.from(this.files).forEach(file => {
            if (file.size > maxSize) {
              hasError = true;
              alert(`File "${file.name}" is too large. Maximum size is 5MB.`);
            }
          });
          
          if (hasError) {
            this.value = '';
            wrapper.classList.remove('has-file');
            fileNameSpan.textContent = originalText;
          }
        });
        
        // Drag and drop functionality
        wrapper.addEventListener('dragover', function(e) {
          e.preventDefault();
          this.style.borderColor = 'var(--jetlouge-primary)';
          this.style.backgroundColor = '#f0f8ff';
        });
        
        wrapper.addEventListener('dragleave', function(e) {
          e.preventDefault();
          this.style.borderColor = '#dee2e6';
          this.style.backgroundColor = '#f8f9fa';
        });
        
        wrapper.addEventListener('drop', function(e) {
          e.preventDefault();
          this.style.borderColor = '#dee2e6';
          this.style.backgroundColor = '#f8f9fa';
          
          const files = e.dataTransfer.files;
          if (files.length > 0) {
            input.files = files;
            input.dispatchEvent(new Event('change'));
          }
        });
      });
    });

    // Step navigation functions
    function nextStep() {
      // Validate step 1
      const name = document.getElementById('name').value;
      const email = document.getElementById('email').value;
      const password = document.getElementById('password').value;
      const passwordConfirmation = document.getElementById('password_confirmation').value;
      
      if (!name || !email || !password || !passwordConfirmation) {
        alert('Please fill in all required fields in Step 1.');
        return;
      }
      
      if (password !== passwordConfirmation) {
        alert('Passwords do not match!');
        return;
      }
      
      if (password.length < 8) {
        alert('Password must be at least 8 characters long.');
        return;
      }
      
      // Show step 2
      document.getElementById('step1').style.display = 'none';
      document.getElementById('step2').style.display = 'block';
    }
    
    function prevStep() {
      // Show step 1
      document.getElementById('step2').style.display = 'none';
      document.getElementById('step1').style.display = 'block';
    }
  </script>
</body>
</html>
