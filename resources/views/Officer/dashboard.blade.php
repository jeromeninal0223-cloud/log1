@php
// Access Control: Only procurement officers and admin can access Officer dashboard
if (!auth()->check()) {
    header('Location: /login');
    exit();
}

$userRole = auth()->user()->role;
if (!in_array($userRole, ['procurement_officer', 'admin'])) {
    // Redirect unauthorized users to main dashboard
    header('Location: /dashboard');
    exit();
}
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procurement Officer Dashboard</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        .dashboard-card {
            transition: transform 0.2s ease-in-out;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        
        .psm-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-3">
                        <i class="bi bi-person-badge me-3"></i>
                        Welcome, {{ auth()->user()->name }}
                    </h1>
                    <p class="lead mb-0">Procurement Officer Dashboard</p>
                    <p class="mb-0">Access your Procurement & Sourcing Management tools</p>
                </div>
                <div class="col-md-4 text-end">
                    <i class="bi bi-cart-plus" style="font-size: 4rem; opacity: 0.7;"></i>
                </div>
            </div>
        </div>

        <!-- PSM Access Cards -->
        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="psm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-file-earmark-text" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="card-title">Purchase Requests</h5>
                        <p class="card-text text-muted">Create and manage purchase requests</p>
                        <a href="{{ route('psm.request') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Access
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="psm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-people" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="card-title">Vendor Management</h5>
                        <p class="card-text text-muted">Manage vendors and suppliers</p>
                        <a href="{{ route('psm.vendor') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Access
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="psm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-trophy" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="card-title">Bidding & RFQ</h5>
                        <p class="card-text text-muted">Manage bidding opportunities</p>
                        <a href="{{ route('psm.bidding') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Access
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="psm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-file-earmark-check" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="card-title">Contracts</h5>
                        <p class="card-text text-muted">Contract management and approval</p>
                        <a href="/psm/contract" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Access
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="psm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-box-seam" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="card-title">Purchase Orders</h5>
                        <p class="card-text text-muted">Create and track purchase orders</p>
                        <a href="{{ route('psm.order.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Access
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="psm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-truck" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="card-title">Delivery Tracking</h5>
                        <p class="card-text text-muted">Track deliveries and shipments</p>
                        <a href="{{ route('psm.delivery') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Access
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="psm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-receipt" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="card-title">Invoice Management</h5>
                        <p class="card-text text-muted">Process and manage invoices</p>
                        <a href="{{ route('psm.invoice.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-right me-2"></i>Access
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-4">
                <div class="card dashboard-card h-100">
                    <div class="card-body text-center p-4">
                        <div class="psm-gradient rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-box-arrow-right" style="font-size: 2rem;"></i>
                        </div>
                        <h5 class="card-title">Logout</h5>
                        <p class="card-text text-muted">Sign out of your account</p>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Access Info -->
        <div class="row mt-5">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="bi bi-info-circle me-2"></i>
                            Procurement Officer Access
                        </h5>
                        <p class="card-text">
                            As a Procurement Officer, you have access to all Procurement & Sourcing Management (PSM) modules. 
                            Use the cards above to navigate to different PSM functions. All your procurement activities, 
                            from purchase requests to invoice management, are centralized in the PSM system.
                        </p>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="bi bi-shield-check me-1"></i>
                                    <strong>Secure Access:</strong> Role-based permissions
                                </small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    <strong>Real-time:</strong> Live data updates
                                </small>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">
                                    <i class="bi bi-graph-up me-1"></i>
                                    <strong>Analytics:</strong> Performance tracking
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
