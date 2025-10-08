<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vehicle Request Management - Jetlouge Travels</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --primary-color: #0f3d64;
            --secondary-color: #f8f9fa;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        body {
            background-color: var(--secondary-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .sidebar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a5490 100%);
            min-height: 100vh;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        
        .main-content {
            padding: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a5490 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 1.5rem;
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .status-approved {
            background-color: #d1e7dd;
            color: #0f5132;
            border: 1px solid #a3cfbb;
        }
        
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f1aeb5;
        }
        
        .btn-action {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            margin: 0.2rem;
        }
        
        .table th {
            background-color: #f8f9fa;
            border-top: none;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(15, 61, 100, 0.05);
        }
        
        .loading-spinner {
            display: none;
        }
        
        .filter-tabs .nav-link {
            border-radius: 10px;
            margin-right: 0.5rem;
            font-weight: 600;
        }
        
        .filter-tabs .nav-link.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar p-0">
                <div class="p-3">
                    <h5 class="text-white mb-4">
                        <i class="bi bi-truck me-2"></i>
                        Vehicle Requests
                    </h5>
                    <nav class="nav flex-column">
                        <a href="{{ route('dashboard') }}" class="nav-link text-white-50 mb-2">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                        <a href="{{ url('/alms/assetregistration') }}" class="nav-link text-white-50 mb-2">
                            <i class="bi bi-clipboard-data me-2"></i> Asset Registration
                        </a>
                        <a href="{{ route('alms.vehicle-requests') }}" class="nav-link text-white active">
                            <i class="bi bi-truck me-2"></i> Vehicle Requests
                        </a>
                        <a href="{{ url('/alms/maintenance') }}" class="nav-link text-white-50 mb-2">
                            <i class="bi bi-tools me-2"></i> Maintenance
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 main-content">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h2 class="mb-1">Vehicle Request Management</h2>
                        <p class="text-muted">Manage vehicle requests from external departments</p>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" id="testConnectionBtn">
                            <i class="bi bi-wifi me-2"></i>Test Connection
                        </button>
                        <button class="btn btn-primary" id="refreshBtn">
                            <i class="bi bi-arrow-clockwise me-2"></i>Refresh
                        </button>
                    </div>
                </div>

                <!-- Status Filter Tabs -->
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-pills filter-tabs mb-3">
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" 
                                   href="{{ route('alms.vehicle-requests', ['status' => 'pending']) }}">
                                    <i class="bi bi-clock me-2"></i>Pending
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" 
                                   href="{{ route('alms.vehicle-requests', ['status' => 'approved']) }}">
                                    <i class="bi bi-check-circle me-2"></i>Approved
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" 
                                   href="{{ route('alms.vehicle-requests', ['status' => 'rejected']) }}">
                                    <i class="bi bi-x-circle me-2"></i>Rejected
                                </a>
                            </li>
                        </ul>

                        <!-- Loading Spinner -->
                        <div class="loading-spinner text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Fetching requests...</p>
                        </div>

                        <!-- Requests Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Requester</th>
                                        <th>Vehicle Type</th>
                                        <th>Justification</th>
                                        <th>Request Date</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="requestsTableBody">
                                    @if(isset($requests) && count($requests) > 0)
                                        @foreach($requests as $request)
                                        <tr data-request-id="{{ $request['id'] }}">
                                            <td><strong>#{{ $request['id'] }}</strong></td>
                                            <td>{{ $request['requester_name'] ?? 'ID #' . ($request['requester_id'] ?? 'Unknown') }}</td>
                                            <td>{{ $request['vehicle_type'] ?? 'Not specified' }}</td>
                                            <td>
                                                <span class="text-truncate d-inline-block" style="max-width: 200px;" 
                                                      title="{{ $request['justification'] ?? 'No justification provided' }}">
                                                    {{ $request['justification'] ?? 'No justification provided' }}
                                                </span>
                                            </td>
                                            <td>{{ $request['request_date'] ?? 'Unknown' }}</td>
                                            <td>
                                                <span class="status-badge status-{{ strtolower($request['status'] ?? 'pending') }}">
                                                    {{ ucfirst($request['status'] ?? 'pending') }}
                                                </span>
                                            </td>
                                            <td>
                                                @if(($request['status'] ?? 'pending') === 'pending')
                                                    <button class="btn btn-success btn-sm btn-action approve-btn" 
                                                            data-id="{{ $request['id'] }}">
                                                        <i class="bi bi-check me-1"></i>Approve
                                                    </button>
                                                    <button class="btn btn-danger btn-sm btn-action reject-btn" 
                                                            data-id="{{ $request['id'] }}">
                                                        <i class="bi bi-x me-1"></i>Reject
                                                    </button>
                                                @else
                                                    <span class="text-muted small">No actions available</span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox display-4 d-block mb-3"></i>
                                                No {{ $status }} requests found
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.32/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        console.log('CSRF Token:', csrfToken);
        
        // Elements
        const refreshBtn = document.getElementById('refreshBtn');
        const testConnectionBtn = document.getElementById('testConnectionBtn');
        const loadingSpinner = document.querySelector('.loading-spinner');
        const tableBody = document.getElementById('requestsTableBody');

        // Event Listeners
        refreshBtn.addEventListener('click', refreshRequests);
        testConnectionBtn.addEventListener('click', testConnection);
        
        // Approve/Reject button handlers
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('approve-btn') || e.target.closest('.approve-btn')) {
                const btn = e.target.classList.contains('approve-btn') ? e.target : e.target.closest('.approve-btn');
                const requestId = btn.dataset.id;
                handleDecision(requestId, 'approve');
            }
            
            if (e.target.classList.contains('reject-btn') || e.target.closest('.reject-btn')) {
                const btn = e.target.classList.contains('reject-btn') ? e.target : e.target.closest('.reject-btn');
                const requestId = btn.dataset.id;
                handleDecision(requestId, 'reject');
            }
        });

        // Functions
        async function refreshRequests() {
            showLoading(true);
            try {
                const response = await fetch(window.location.href, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                if (response.ok) {
                    location.reload();
                } else {
                    throw new Error('Failed to refresh requests');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to refresh requests: ' + error.message
                });
            } finally {
                showLoading(false);
            }
        }

        async function testConnection() {
            showLoading(true);
            try {
                const response = await fetch('/alms/vehicle-requests/test/connection', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Connection Successful',
                        text: `API connection is working. Found ${data.request_count} requests.`
                    });
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Connection Failed',
                    text: error.message
                });
            } finally {
                showLoading(false);
            }
        }

        async function handleDecision(requestId, decision) {
            const { value: note } = await Swal.fire({
                title: `${decision.charAt(0).toUpperCase() + decision.slice(1)} Request`,
                text: `Are you sure you want to ${decision} this vehicle request?`,
                input: 'textarea',
                inputLabel: 'Note (optional)',
                inputPlaceholder: 'Add a note about your decision...',
                showCancelButton: true,
                confirmButtonText: `Yes, ${decision}`,
                confirmButtonColor: decision === 'approve' ? '#198754' : '#dc3545',
                cancelButtonText: 'Cancel'
            });

            if (note !== undefined) {
                try {
                    console.log('Sending request with CSRF token:', csrfToken);
                    
                    const formData = new FormData();
                    formData.append('request_id', requestId);
                    formData.append('decision', decision);
                    formData.append('note', note || '');
                    formData.append('_token', csrfToken);
                    
                    const response = await fetch('/alms/vehicle-requests/decide', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json'
                        },
                        body: formData
                    });
                    
                    console.log('Response status:', response.status);

                    const data = await response.json();

                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: data.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update request: ' + error.message
                    });
                }
            }
        }

        function showLoading(show) {
            if (show) {
                loadingSpinner.style.display = 'block';
                tableBody.style.opacity = '0.5';
            } else {
                loadingSpinner.style.display = 'none';
                tableBody.style.opacity = '1';
            }
        }
    </script>
</body>
</html>
