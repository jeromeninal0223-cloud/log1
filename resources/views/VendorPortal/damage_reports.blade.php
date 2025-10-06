<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Damage Reports - Vendor Portal</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('assets/css/dash-style-fixed.css') }}">
  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --jetlouge-primary: #1e3a8a;
      --jetlouge-secondary: #3b82f6;
    }


    /* Damage Reports Specific Styles */
    .damage-card {
      border-left: 4px solid #dc3545;
      transition: all 0.3s ease;
    }
    .damage-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .status-badge {
      font-size: 0.75rem;
      padding: 0.25rem 0.5rem;
    }
    .damage-stats {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
      border-radius: 10px;
    }
    .replacement-stats {
      background: linear-gradient(135deg, #28a745 0%, #218838 100%);
      color: white;
      border-radius: 10px;
    }
    .pending-stats {
      background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
      color: white;
      border-radius: 10px;
    }

    /* Notification Styles */
    .notification-dropdown {
      width: 350px;
      max-height: 400px;
    }

    .notification-item {
      padding: 0.75rem 1rem;
      border-bottom: 1px solid #f1f3f4;
    }

    .notification-item:hover {
      background-color: #f8f9fa;
    }

    .notification-item.unread {
      background-color: #e3f2fd;
    }

    .unread-indicator {
      width: 8px;
      height: 8px;
      background-color: #007bff;
      border-radius: 50%;
    }

    .pulse {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
  </style>
</head>
<body style="background-color: #f8f9fa !important;">

  @include('VendorPortal.partials.navbar')
  @include('VendorPortal.partials.sidebar')

  <!-- Main Content -->
  <main id="main-content">
      
      <!-- Header -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h2 class="fw-bold text-dark mb-1">
                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                Damage Reports
              </h2>
              <p class="text-muted mb-0">Monitor and manage damage reports for your delivered items</p>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-outline-primary" id="refreshBtn">
                <i class="bi bi-arrow-clockwise me-1"></i>Refresh
              </button>
              <button class="btn btn-primary" id="exportBtn">
                <i class="bi bi-download me-1"></i>Export Report
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Statistics Cards -->
      <div class="row mb-4">
        <div class="col-md-3">
          <div class="card damage-stats p-3 text-center">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h3 class="mb-0 fw-bold" id="totalDamaged">{{ $damageStats['total_damaged'] ?? 0 }}</h3>
                <small class="opacity-75">Total Damaged Items</small>
              </div>
              <i class="bi bi-exclamation-circle fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card pending-stats p-3 text-center">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h3 class="mb-0 fw-bold" id="pendingReplacements">{{ $damageStats['pending_replacements'] ?? 0 }}</h3>
                <small class="opacity-75">Pending Replacements</small>
              </div>
              <i class="bi bi-clock fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card replacement-stats p-3 text-center">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h3 class="mb-0 fw-bold" id="completedReplacements">{{ $damageStats['completed_replacements'] ?? 0 }}</h3>
                <small class="opacity-75">Completed Replacements</small>
              </div>
              <i class="bi bi-check-circle fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <div class="card bg-info text-white p-3 text-center">
            <div class="d-flex align-items-center justify-content-between">
              <div>
                <h3 class="mb-0 fw-bold" id="damageRate">{{ number_format($damageStats['damage_rate'] ?? 0, 2) }}%</h3>
                <small class="opacity-75">Damage Rate</small>
              </div>
              <i class="bi bi-graph-down fs-1 opacity-50"></i>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="row mb-4">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row g-3">
                <div class="col-md-3">
                  <label class="form-label">Date Range</label>
                  <select class="form-select" id="dateRange">
                    <option value="7">Last 7 days</option>
                    <option value="30" selected>Last 30 days</option>
                    <option value="90">Last 90 days</option>
                    <option value="365">Last year</option>
                    <option value="all">All time</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Status</label>
                  <select class="form-select" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="reported">Reported</option>
                    <option value="acknowledged">Acknowledged</option>
                    <option value="replacement_sent">Replacement Sent</option>
                    <option value="resolved">Resolved</option>
                  </select>
                </div>
                <div class="col-md-3">
                  <label class="form-label">Item Name</label>
                  <input type="text" class="form-control" id="itemSearch" placeholder="Search by item name...">
                </div>
                <div class="col-md-3">
                  <label class="form-label">&nbsp;</label>
                  <button class="btn btn-primary w-100" id="applyFilters">
                    <i class="bi bi-funnel me-1"></i>Apply Filters
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Damage Reports Table -->
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-header bg-white">
              <h5 class="mb-0">
                <i class="bi bi-table me-2"></i>Damage Reports
                <span class="badge bg-danger ms-2" id="totalReports">{{ count($damageReports ?? []) }}</span>
              </h5>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover mb-0" id="damageReportsTable">
                  <thead class="table-light">
                    <tr>
                      <th>Report ID</th>
                      <th>Receipt Number</th>
                      <th>Item Name</th>
                      <th>Received Qty</th>
                      <th>Damaged Qty</th>
                      <th>Damage Rate</th>
                      <th>Images</th>
                      <th>Reported Date</th>
                      <th>Status</th>
                      <th>Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    @forelse($damageReports ?? [] as $report)
                    <tr class="damage-report-row" data-report-id="{{ $report->id }}">
                      <td>
                        <strong class="text-primary">#{{ str_pad($report->id, 6, '0', STR_PAD_LEFT) }}</strong>
                      </td>
                      <td>
                        <span class="badge bg-secondary">{{ $report->receipt->receipt_number ?? 'N/A' }}</span>
                      </td>
                      <td>
                        <div>
                          <strong>{{ $report->item_name }}</strong>
                          @if($report->description)
                          <br><small class="text-muted">{{ $report->description }}</small>
                          @endif
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info">{{ $report->quantity }} {{ $report->unit }}</span>
                      </td>
                      <td>
                        <span class="badge bg-danger">{{ $report->damaged_quantity }} {{ $report->unit }}</span>
                      </td>
                      <td>
                        @php
                          $damageRate = $report->quantity > 0 ? round(($report->damaged_quantity / $report->quantity) * 100, 1) : 0;
                          $rateClass = $damageRate > 50 ? 'danger' : ($damageRate > 20 ? 'warning' : 'success');
                        @endphp
                        <span class="badge bg-{{ $rateClass }}">{{ $damageRate }}%</span>
                      </td>
                      <td>
                        <div class="d-flex flex-column gap-1 align-items-start">
                          <div class="d-flex gap-1">
                            @if($report->image_path)
                              <span class="badge bg-info" title="Item Photo: {{ basename($report->image_path) }}">
                                <i class="bi bi-camera"></i>
                              </span>
                            @endif
                            @if($report->damage_image_path)
                              <span class="badge bg-warning" title="Damage Evidence: {{ basename($report->damage_image_path) }}">
                                <i class="bi bi-exclamation-triangle"></i>
                              </span>
                            @endif
                          </div>
                          @if(!$report->image_path && !$report->damage_image_path)
                            <small class="text-muted">No images</small>
                          @else
                            <small class="text-success">{{ ($report->image_path ? 1 : 0) + ($report->damage_image_path ? 1 : 0) }} image(s)</small>
                          @endif
                        </div>
                      </td>
                      <td>
                        <small class="text-muted">
                          {{ $report->created_at->format('M d, Y') }}<br>
                          {{ $report->created_at->format('h:i A') }}
                        </small>
                      </td>
                      <td>
                        @php
                          $status = $report->return_to_vendor ? 'reported' : 'resolved';
                          $statusClass = match($status) {
                            'reported' => 'danger',
                            'acknowledged' => 'warning',
                            'replacement_sent' => 'info',
                            'resolved' => 'success',
                            default => 'secondary'
                          };
                        @endphp
                        <span class="badge bg-{{ $statusClass }} status-badge">
                          {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </span>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <button class="btn btn-sm btn-outline-primary view-details-btn" 
                                  data-report-id="{{ $report->id }}" 
                                  title="View Details">
                            <i class="bi bi-eye"></i>
                          </button>
                          @if($report->return_to_vendor)
                          <button class="btn btn-sm btn-outline-success acknowledge-btn" 
                                  data-report-id="{{ $report->id }}" 
                                  title="Acknowledge">
                            <i class="bi bi-check"></i>
                          </button>
                          <button class="btn btn-sm btn-outline-info replacement-btn" 
                                  data-report-id="{{ $report->id }}" 
                                  title="Send Replacement">
                            <i class="bi bi-box-seam"></i>
                          </button>
                          @endif
                        </div>
                      </td>
                    </tr>
                    @empty
                    <tr>
                      <td colspan="10" class="text-center py-4">
                        <div class="text-muted">
                          <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                          <p class="mb-0">No damage reports found</p>
                          <small>Great job! Your deliveries have been damage-free.</small>
                        </div>
                      </td>
                    </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

  </main>

  <!-- Damage Details Modal -->
  <div class="modal fade" id="damageDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            Damage Report Details
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="damageDetailsContent">
          <!-- Content will be loaded dynamically -->
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success" id="acknowledgeFromModal">
            <i class="bi bi-check-circle me-1"></i>Acknowledge
          </button>
          <button type="button" class="btn btn-primary" id="sendReplacementFromModal">
            <i class="bi bi-box-seam me-1"></i>Send Replacement
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Replacement Modal -->
  <div class="modal fade" id="replacementModal" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            <i class="bi bi-box-seam me-2"></i>
            Send Replacement
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="replacementForm">
            <div class="mb-3">
              <label class="form-label">Replacement Quantity</label>
              <input type="number" class="form-control" id="replacementQty" min="1" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Expected Delivery Date</label>
              <input type="date" class="form-control" id="deliveryDate" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Tracking Number (Optional)</label>
              <input type="text" class="form-control" id="trackingNumber" placeholder="Enter tracking number">
            </div>
            <div class="mb-3">
              <label class="form-label">Notes</label>
              <textarea class="form-control" id="replacementNotes" rows="3" placeholder="Additional notes about the replacement..."></textarea>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary" id="submitReplacement">
            <i class="bi bi-send me-1"></i>Send Replacement
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      let currentReportId = null;

      // Sidebar functionality
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

      // View Details
      document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const reportId = this.dataset.reportId;
          loadDamageDetails(reportId);
        });
      });

      // Acknowledge Damage
      document.querySelectorAll('.acknowledge-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          const reportId = this.dataset.reportId;
          acknowledgeDamage(reportId);
        });
      });

      // Send Replacement
      document.querySelectorAll('.replacement-btn').forEach(btn => {
        btn.addEventListener('click', function() {
          currentReportId = this.dataset.reportId;
          const modal = new bootstrap.Modal(document.getElementById('replacementModal'));
          modal.show();
        });
      });

      // Submit Replacement
      document.getElementById('submitReplacement').addEventListener('click', function() {
        if (currentReportId) {
          submitReplacement(currentReportId);
        }
      });

      // Apply Filters
      document.getElementById('applyFilters').addEventListener('click', function() {
        applyFilters();
      });

      // Refresh Data
      document.getElementById('refreshBtn').addEventListener('click', function() {
        location.reload();
      });

      // Export Report
      document.getElementById('exportBtn').addEventListener('click', function() {
        exportDamageReport();
      });

      function loadDamageDetails(reportId) {
        fetch(`/vendor/damage-reports/${reportId}`, {
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            displayDamageDetails(data.report);
          } else {
            Swal.fire('Error', 'Failed to load damage details', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error', 'Failed to load damage details', 'error');
        });
      }

      function displayDamageDetails(report) {
        const content = `
          <div class="row">
            <div class="col-md-6">
              <h6 class="text-muted">Receipt Information</h6>
              <p><strong>Receipt Number:</strong> ${report.receipt_number}</p>
              <p><strong>Supplier:</strong> ${report.supplier_name}</p>
              <p><strong>Delivery Date:</strong> ${report.delivery_date}</p>
            </div>
            <div class="col-md-6">
              <h6 class="text-muted">Item Information</h6>
              <p><strong>Item Name:</strong> ${report.item_name}</p>
              <p><strong>Description:</strong> ${report.description || 'N/A'}</p>
              <p><strong>Unit:</strong> ${report.unit}</p>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-md-4">
              <div class="text-center p-3 bg-light rounded">
                <h4 class="text-info">${report.quantity}</h4>
                <small class="text-muted">Total Received</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="text-center p-3 bg-light rounded">
                <h4 class="text-danger">${report.damaged_quantity}</h4>
                <small class="text-muted">Damaged</small>
              </div>
            </div>
            <div class="col-md-4">
              <div class="text-center p-3 bg-light rounded">
                <h4 class="text-success">${report.quantity - report.damaged_quantity}</h4>
                <small class="text-muted">Good Items</small>
              </div>
            </div>
          </div>
          ${report.damage_reason ? `
          <hr>
          <h6 class="text-muted">Damage Reason</h6>
          <p class="alert alert-warning">${report.damage_reason}</p>
          ` : ''}
          ${report.image_path ? `
          <hr>
          <h6 class="text-muted">Item Photo</h6>
          <img src="/storage/${report.image_path}" class="img-fluid rounded mb-3" alt="Item photo" style="max-height: 300px;">
          ` : ''}
          ${report.damage_image_path ? `
          <hr>
          <h6 class="text-muted">Damage Evidence</h6>
          <img src="/storage/${report.damage_image_path}" class="img-fluid rounded" alt="Damage evidence" style="max-height: 300px;">
          ` : ''}
        `;
        
        document.getElementById('damageDetailsContent').innerHTML = content;
        const modal = new bootstrap.Modal(document.getElementById('damageDetailsModal'));
        modal.show();
      }

      function acknowledgeDamage(reportId) {
        Swal.fire({
          title: 'Acknowledge Damage Report',
          text: 'This will mark the damage report as acknowledged.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonText: 'Yes, Acknowledge',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed) {
            fetch(`/vendor/damage-reports/${reportId}/acknowledge`, {
              method: 'POST',
              headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
              }
            })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                Swal.fire('Success', 'Damage report acknowledged successfully', 'success')
                  .then(() => location.reload());
              } else {
                Swal.fire('Error', data.message || 'Failed to acknowledge damage report', 'error');
              }
            })
            .catch(error => {
              console.error('Error:', error);
              Swal.fire('Error', 'Failed to acknowledge damage report', 'error');
            });
          }
        });
      }

      function submitReplacement(reportId) {
        const formData = {
          quantity: document.getElementById('replacementQty').value,
          delivery_date: document.getElementById('deliveryDate').value,
          tracking_number: document.getElementById('trackingNumber').value,
          notes: document.getElementById('replacementNotes').value
        };

        fetch(`/vendor/damage-reports/${reportId}/replacement`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': csrfToken,
            'Content-Type': 'application/json',
            'Accept': 'application/json'
          },
          body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            Swal.fire('Success', 'Replacement information submitted successfully', 'success')
              .then(() => {
                bootstrap.Modal.getInstance(document.getElementById('replacementModal')).hide();
                location.reload();
              });
          } else {
            Swal.fire('Error', data.message || 'Failed to submit replacement', 'error');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          Swal.fire('Error', 'Failed to submit replacement', 'error');
        });
      }

      function applyFilters() {
        const filters = {
          date_range: document.getElementById('dateRange').value,
          status: document.getElementById('statusFilter').value,
          item_search: document.getElementById('itemSearch').value
        };

        const params = new URLSearchParams(filters);
        window.location.href = `${window.location.pathname}?${params.toString()}`;
      }

      function exportDamageReport() {
        const filters = {
          date_range: document.getElementById('dateRange').value,
          status: document.getElementById('statusFilter').value,
          item_search: document.getElementById('itemSearch').value
        };

        const params = new URLSearchParams(filters);
        window.open(`/vendor/damage-reports/export?${params.toString()}`, '_blank');
      }
    });
  </script>

</body>
</html>
