<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Submit Bid - JetLogue Travels</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">

</head>
<body style="background-color: #f8f9fa !important;">

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top" style="background-color: var(--jetlouge-primary);">
    <div class="container-fluid">
      <button class="sidebar-toggle desktop-toggle me-3" id="desktop-toggle" title="Toggle Sidebar">
        <i class="bi bi-list fs-5"></i>
      </button>
      <a class="navbar-brand fw-bold" href="#">
        <img src="{{ asset('assets/images/jetlouge_logo.png') }}" alt="JetLogue Travels Logo" style="height: 32px; width: auto; margin-right: 8px;">
        JetLogue Travels
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
        <a href="{{ route('vendor.profile') }}" class="nav-link text-dark">
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
            <i class="bi bi-file-earmark-plus fs-1 text-primary"></i>
          </div>
          <div>
            <h2 class="fw-bold mb-1">Submit Bid</h2>
            <p class="text-muted mb-0">Submit your proposal for this opportunity.</p>
          </div>
        </div>
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('vendor.dashboard') }}" class="text-decoration-none">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('vendor.bidding.landing') }}" class="text-decoration-none">Opportunities</a></li>
            <li class="breadcrumb-item active" aria-current="page">Submit Bid</li>
          </ol>
        </nav>
      </div>
    </div>

    <!-- Opportunity Details -->
    <div class="row g-4 mb-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom bg-light">
            <h5 class="card-title mb-0">
              <i class="bi bi-info-circle me-2 text-primary"></i>Opportunity Details
            </h5>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <h4 class="fw-bold text-primary mb-2">{{ $opportunity['title'] }}</h4>
                <p class="text-muted mb-3">{{ $opportunity['bid_number'] }}</p>
                
                @if(isset($opportunity['description']) && !empty($opportunity['description']))
                <div class="mb-4">
                  <strong>Description:</strong>
                  <p class="mt-2 text-dark">{{ $opportunity['description'] }}</p>
                </div>
                @endif
                
                <div class="row g-3">
                  <div class="col-sm-6">
                    <strong>Category:</strong>
                    <span class="badge bg-secondary ms-2">{{ $opportunity['category'] }}</span>
                  </div>
                  <div class="col-sm-6">
                    <strong>Budget Range:</strong>
                    <span class="text-success fw-bold ms-2">₱{{ number_format($opportunity['budget']) }}</span>
                  </div>
                  <div class="col-sm-6">
                    <strong>Start Date:</strong>
                    <span class="ms-2">{{ date('M d, Y', strtotime($opportunity['start_date'])) }}</span>
                  </div>
                  <div class="col-sm-6">
                    <strong>End Date:</strong>
                    <span class="ms-2">{{ date('M d, Y', strtotime($opportunity['end_date'])) }}</span>
                  </div>
                </div>
              </div>
              <div class="col-md-4 text-end">
                <div class="bg-light p-3 rounded">
                  <h6 class="fw-bold mb-2">Submission Status</h6>
                  <p class="mb-1"><strong>{{ $opportunity['submission_count'] }}</strong> bids submitted</p>
                  <span class="badge bg-success">{{ $opportunity['current_status'] }}</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bid Submission Form -->
    <div class="row g-4">
      <div class="col-12">
        <div class="card shadow-sm border-0">
          <div class="card-header border-bottom">
            <h5 class="card-title mb-0">
              <i class="bi bi-file-earmark-plus me-2 text-primary"></i>Your Bid Submission
            </h5>
          </div>
          <div class="card-body">
            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form action="{{ route('vendor.bid.submit', $opportunity['id']) }}" method="POST" enctype="multipart/form-data">
              @csrf
              
              <div class="row g-4">
                <!-- Bid Amount -->
                <div class="col-md-6">
                  <label for="amount" class="form-label fw-bold">
                    <i class="bi bi-currency-dollar me-1"></i>Bid Amount (₱) <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <span class="input-group-text">₱</span>
                    <input type="number" class="form-control" id="amount" name="amount" 
                           value="{{ old('amount') }}" step="0.01" min="1" required
                           placeholder="Enter your bid amount">
                  </div>
                  <small class="text-muted">Budget range: ₱{{ number_format($opportunity['budget']) }}</small>
                </div>

                <!-- Estimated Completion -->
                <div class="col-md-6">
                  <label for="completion_date" class="form-label fw-bold">
                    <i class="bi bi-calendar-check me-1"></i>Estimated Completion Date
                  </label>
                  <input type="date" class="form-control" id="completion_date" name="completion_date" 
                         value="{{ old('completion_date') }}" min="{{ date('Y-m-d') }}">
                  <small class="text-muted">When do you expect to complete this project?</small>
                </div>

                <!-- Warranty Period -->
                <div class="col-md-6">
                  <label for="warranty_period" class="form-label fw-bold">
                    <i class="bi bi-shield-check me-1"></i>Warranty Period <span class="text-danger">*</span>
                  </label>
                  <select class="form-select" id="warranty_period" name="warranty_period" required>
                    <option value="">Select warranty period</option>
                    <option value="3_months" {{ old('warranty_period') == '3_months' ? 'selected' : '' }}>3 months</option>
                    <option value="6_months" {{ old('warranty_period') == '6_months' ? 'selected' : '' }}>6 months</option>
                    <option value="12_months" {{ old('warranty_period') == '12_months' ? 'selected' : '' }}>12 months (Standard)</option>
                    <option value="18_months" {{ old('warranty_period') == '18_months' ? 'selected' : '' }}>18 months</option>
                    <option value="24_months" {{ old('warranty_period') == '24_months' ? 'selected' : '' }}>24 months</option>
                    <option value="36_months" {{ old('warranty_period') == '36_months' ? 'selected' : '' }}>36 months</option>
                    <option value="custom" {{ old('warranty_period') == 'custom' ? 'selected' : '' }}>Custom period</option>
                  </select>
                  <small class="text-muted">Select the warranty period you're offering</small>
                </div>

                <!-- Custom Warranty Period (Hidden by default) -->
                <div class="col-md-6" id="custom_warranty_section" style="display: none;">
                  <label for="custom_warranty" class="form-label fw-bold">
                    <i class="bi bi-pencil me-1"></i>Custom Warranty Period
                  </label>
                  <input type="text" class="form-control" id="custom_warranty" name="custom_warranty" 
                         value="{{ old('custom_warranty') }}" placeholder="e.g., 5 years for structural work">
                  <small class="text-muted">Specify your custom warranty terms</small>
                </div>

                <!-- Payment Terms -->
                <div class="col-12">
                  <label for="payment_terms" class="form-label fw-bold">
                    <i class="bi bi-credit-card me-1"></i>Payment Terms & Conditions <span class="text-danger">*</span>
                  </label>
                  <select class="form-select mb-3" id="payment_terms_type" name="payment_terms_type" required>
                    <option value="">Select payment structure</option>
                    <option value="full_advance" {{ old('payment_terms_type') == 'full_advance' ? 'selected' : '' }}>100% Advance Payment</option>
                    <option value="full_delivery" {{ old('payment_terms_type') == 'full_delivery' ? 'selected' : '' }}>100% Upon Delivery</option>
                    <option value="cod" {{ old('payment_terms_type') == 'cod' ? 'selected' : '' }}>Cash on Delivery (COD)</option>
                    <option value="50_50" {{ old('payment_terms_type') == '50_50' ? 'selected' : '' }}>50% Advance, 50% Upon Delivery</option>
                    <option value="30_70" {{ old('payment_terms_type') == '30_70' ? 'selected' : '' }}>30% Advance, 70% Upon Delivery</option>
                    <option value="milestone" {{ old('payment_terms_type') == 'milestone' ? 'selected' : '' }}>Milestone-based Payments</option>
                    <option value="net_30" {{ old('payment_terms_type') == 'net_30' ? 'selected' : '' }}>Net 30 Days</option>
                    <option value="net_15" {{ old('payment_terms_type') == 'net_15' ? 'selected' : '' }}>Net 15 Days</option>
                    <option value="custom" {{ old('payment_terms_type') == 'custom' ? 'selected' : '' }}>Custom Terms</option>
                  </select>
                  <textarea class="form-control" id="payment_terms_details" name="payment_terms_details" rows="4" required
                            placeholder="Provide detailed payment terms and conditions. Include payment methods accepted, late payment penalties, and any specific requirements.">{{ old('payment_terms_details') }}</textarea>
                  <small class="text-muted">Specify your complete payment terms, conditions, and accepted payment methods</small>
                </div>

                <!-- Proposal -->
                <div class="col-12">
                  <label for="proposal" class="form-label fw-bold">
                    <i class="bi bi-file-text me-1"></i>Proposal Description <span class="text-danger">*</span>
                  </label>
                  <textarea class="form-control" id="proposal" name="proposal" rows="8" required
                            placeholder="Describe your approach, methodology, timeline, and why you're the best choice for this project. Minimum 50 characters.">{{ old('proposal') }}</textarea>
                  <div class="d-flex justify-content-between">
                    <small class="text-muted">Minimum 50 characters required</small>
                    <small class="text-muted" id="char-count">0 characters</small>
                  </div>
                </div>

                <!-- File Attachments -->
                <div class="col-12">
                  <label for="attachments" class="form-label fw-bold">
                    <i class="bi bi-paperclip me-1"></i>Supporting Documents
                  </label>
                  <input type="file" class="form-control" id="attachments" name="attachments[]" 
                         multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                  <small class="text-muted">
                    Upload supporting documents (PDF, DOC, DOCX, JPG, PNG). Maximum 5MB per file.
                    You can select multiple files.
                  </small>
                  <div id="file-list" class="mt-2"></div>
                </div>

                <!-- Terms Agreement -->
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                    <label class="form-check-label" for="terms">
                      I agree to the <a href="#" class="text-primary">terms and conditions</a> and confirm that all information provided is accurate. <span class="text-danger">*</span>
                    </label>
                  </div>
                </div>

                <!-- Submit Buttons -->
                <div class="col-12">
                  <div class="d-flex gap-3 justify-content-end">
                    <a href="{{ route('vendor.bidding.landing') }}" class="btn btn-outline-secondary">
                      <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                      <i class="bi bi-send me-2"></i>Submit Bid
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

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

      // Character counter for proposal
      const proposalTextarea = document.getElementById('proposal');
      const charCount = document.getElementById('char-count');
      
      if (proposalTextarea && charCount) {
        proposalTextarea.addEventListener('input', function() {
          const count = this.value.length;
          charCount.textContent = count + ' characters';
          
          if (count < 50) {
            charCount.classList.add('text-danger');
            charCount.classList.remove('text-success');
          } else {
            charCount.classList.add('text-success');
            charCount.classList.remove('text-danger');
          }
        });
      }

      // Handle warranty period custom option
      const warrantySelect = document.getElementById('warranty_period');
      const customWarrantySection = document.getElementById('custom_warranty_section');
      const customWarrantyInput = document.getElementById('custom_warranty');
      
      if (warrantySelect && customWarrantySection) {
        warrantySelect.addEventListener('change', function() {
          if (this.value === 'custom') {
            customWarrantySection.style.display = 'block';
            customWarrantyInput.required = true;
          } else {
            customWarrantySection.style.display = 'none';
            customWarrantyInput.required = false;
            customWarrantyInput.value = '';
          }
        });
        
        // Check initial state
        if (warrantySelect.value === 'custom') {
          customWarrantySection.style.display = 'block';
          customWarrantyInput.required = true;
        }
      }

      // Auto-populate payment terms details based on selection
      const paymentTermsType = document.getElementById('payment_terms_type');
      const paymentTermsDetails = document.getElementById('payment_terms_details');
      
      if (paymentTermsType && paymentTermsDetails) {
        paymentTermsType.addEventListener('change', function() {
          const templates = {
            'full_advance': 'Payment Terms: 100% advance payment required before project commencement.\n\nAccepted Payment Methods: Bank transfer, check, or online payment.\n\nPayment Schedule: Full payment due upon contract signing.\n\nLate Payment: N/A (advance payment required).',
            
            'full_delivery': 'Payment Terms: 100% payment upon successful delivery and acceptance.\n\nAccepted Payment Methods: Bank transfer, check, or online payment.\n\nPayment Schedule: Full payment due within 7 days of delivery confirmation.\n\nLate Payment: 2% monthly interest on overdue amounts.',
            
            'cod': 'Payment Terms: Cash on Delivery (COD) - Payment collected upon delivery.\n\nAccepted Payment Methods: Cash, check, or card payment upon delivery.\n\nPayment Schedule: Full payment due at time of delivery/service completion.\n\nDelivery Requirements: Valid ID and authorized recipient required for COD transactions.\n\nLate Payment: N/A (payment collected on delivery).',
            
            '50_50': 'Payment Terms: 50% advance payment, 50% upon delivery and acceptance.\n\nAccepted Payment Methods: Bank transfer, check, or online payment.\n\nPayment Schedule:\n- 50% upon contract signing\n- 50% within 7 days of delivery confirmation\n\nLate Payment: 2% monthly interest on overdue amounts.',
            
            '30_70': 'Payment Terms: 30% advance payment, 70% upon delivery and acceptance.\n\nAccepted Payment Methods: Bank transfer, check, or online payment.\n\nPayment Schedule:\n- 30% upon contract signing\n- 70% within 7 days of delivery confirmation\n\nLate Payment: 2% monthly interest on overdue amounts.',
            
            'milestone': 'Payment Terms: Milestone-based payment structure.\n\nAccepted Payment Methods: Bank transfer, check, or online payment.\n\nPayment Schedule:\n- 25% upon contract signing\n- 25% at 25% project completion\n- 25% at 75% project completion\n- 25% upon final delivery and acceptance\n\nLate Payment: 2% monthly interest on overdue amounts.',
            
            'net_30': 'Payment Terms: Net 30 days from invoice date.\n\nAccepted Payment Methods: Bank transfer, check, or online payment.\n\nPayment Schedule: Full payment due within 30 days of invoice.\n\nLate Payment: 2% monthly interest on overdue amounts after 30 days.',
            
            'net_15': 'Payment Terms: Net 15 days from invoice date.\n\nAccepted Payment Methods: Bank transfer, check, or online payment.\n\nPayment Schedule: Full payment due within 15 days of invoice.\n\nLate Payment: 2% monthly interest on overdue amounts after 15 days.'
          };
          
          if (this.value && this.value !== 'custom' && templates[this.value]) {
            paymentTermsDetails.value = templates[this.value];
          } else if (this.value === 'custom') {
            paymentTermsDetails.value = 'Please specify your custom payment terms and conditions here...';
          }
        });
      }

      // File upload preview
      const fileInput = document.getElementById('attachments');
      const fileList = document.getElementById('file-list');
      
      if (fileInput && fileList) {
        fileInput.addEventListener('change', function() {
          fileList.innerHTML = '';
          
          if (this.files.length > 0) {
            const listGroup = document.createElement('div');
            listGroup.className = 'list-group list-group-flush';
            
            Array.from(this.files).forEach((file, index) => {
              const listItem = document.createElement('div');
              listItem.className = 'list-group-item d-flex justify-content-between align-items-center py-2';
              
              const fileInfo = document.createElement('div');
              fileInfo.innerHTML = `
                <i class="bi bi-file-earmark me-2"></i>
                <strong>${file.name}</strong>
                <small class="text-muted ms-2">(${(file.size / 1024 / 1024).toFixed(2)} MB)</small>
              `;
              
              const removeBtn = document.createElement('button');
              removeBtn.type = 'button';
              removeBtn.className = 'btn btn-sm btn-outline-danger';
              removeBtn.innerHTML = '<i class="bi bi-x"></i>';
              removeBtn.onclick = () => removeFile(index);
              
              listItem.appendChild(fileInfo);
              listItem.appendChild(removeBtn);
              listGroup.appendChild(listItem);
            });
            
            fileList.appendChild(listGroup);
          }
        });
      }

      // Form validation
      const form = document.querySelector('form');
      if (form) {
        form.addEventListener('submit', function(e) {
          const amount = document.getElementById('amount').value;
          const proposal = document.getElementById('proposal').value;
          const terms = document.getElementById('terms').checked;
          const warrantyPeriod = document.getElementById('warranty_period').value;
          const paymentTermsType = document.getElementById('payment_terms_type').value;
          const paymentTermsDetails = document.getElementById('payment_terms_details').value;
          const customWarranty = document.getElementById('custom_warranty').value;
          
          if (!amount || parseFloat(amount) <= 0) {
            e.preventDefault();
            alert('Please enter a valid bid amount.');
            return;
          }
          
          if (!warrantyPeriod) {
            e.preventDefault();
            alert('Please select a warranty period.');
            return;
          }
          
          if (warrantyPeriod === 'custom' && !customWarranty.trim()) {
            e.preventDefault();
            alert('Please specify your custom warranty period.');
            return;
          }
          
          if (!paymentTermsType) {
            e.preventDefault();
            alert('Please select payment terms.');
            return;
          }
          
          if (!paymentTermsDetails.trim()) {
            e.preventDefault();
            alert('Please provide detailed payment terms and conditions.');
            return;
          }
          
          if (!proposal || proposal.length < 50) {
            e.preventDefault();
            alert('Please provide a proposal description of at least 50 characters.');
            return;
          }
          
          if (!terms) {
            e.preventDefault();
            alert('Please agree to the terms and conditions.');
            return;
          }
        });
      }
    });

    function removeFile(index) {
      const fileInput = document.getElementById('attachments');
      const dt = new DataTransfer();
      
      Array.from(fileInput.files).forEach((file, i) => {
        if (i !== index) {
          dt.items.add(file);
        }
      });
      
      fileInput.files = dt.files;
      fileInput.dispatchEvent(new Event('change'));
    }
  </script>
</body>
</html>
