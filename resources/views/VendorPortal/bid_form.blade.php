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
          
          if (!amount || parseFloat(amount) <= 0) {
            e.preventDefault();
            alert('Please enter a valid bid amount.');
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
