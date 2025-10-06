<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Version Comparison - Document Version History</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .comparison-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem 0;
        }
        
        .version-card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        
        .version-card:hover {
            transform: translateY(-2px);
        }
        
        .feature-coming-soon {
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="comparison-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="bi bi-file-diff me-3"></i>
                        Version Comparison
                    </h1>
                    <p class="mb-0 opacity-75">
                        @if(isset($document))
                            Comparing versions of "{{ $document->title }}"
                        @else
                            Document Version Analysis
                        @endif
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <button class="btn btn-light" onclick="window.close()">
                        <i class="bi bi-x-lg me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        @if(isset($version) && isset($currentVersion))
        <!-- Version Comparison for Regular Documents -->
        <div class="row g-4">
            <!-- Selected Version -->
            <div class="col-md-6">
                <div class="card version-card h-100">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-clock-history me-2"></i>
                            Version {{ $version->version_number }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Modified By:</strong> {{ $version->modified_by_name }}<br>
                            <strong>Date:</strong> {{ $version->created_at->format('M d, Y \a\t g:i A') }}<br>
                            <strong>Status:</strong> 
                            <span class="badge {{ $version->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($version->status) }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Changes Summary:</strong>
                            <p class="text-muted mt-1">{{ $version->changes_summary ?? 'No changes recorded' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <strong>File Size:</strong> {{ $version->formatted_file_size }}<br>
                            <strong>File Path:</strong> <code>{{ basename($version->file_path) }}</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Current Version -->
            <div class="col-md-6">
                <div class="card version-card h-100">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-check-circle me-2"></i>
                            Current Version {{ $currentVersion->version_number }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Modified By:</strong> {{ $currentVersion->modified_by_name }}<br>
                            <strong>Date:</strong> {{ $currentVersion->created_at->format('M d, Y \a\t g:i A') }}<br>
                            <strong>Status:</strong> 
                            <span class="badge bg-success">Active</span>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Changes Summary:</strong>
                            <p class="text-muted mt-1">{{ $currentVersion->changes_summary ?? 'No changes recorded' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <strong>File Size:</strong> {{ $currentVersion->formatted_file_size }}<br>
                            <strong>File Path:</strong> <code>{{ basename($currentVersion->file_path) }}</code>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comparison Analysis -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>
                            Comparison Analysis
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-calendar-event fs-1 text-primary mb-2"></i>
                                    <h6>Time Difference</h6>
                                    <p class="mb-0">{{ $currentVersion->created_at->diffForHumans($version->created_at) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-file-earmark-arrow-up fs-1 text-success mb-2"></i>
                                    <h6>Size Change</h6>
                                    <p class="mb-0">
                                        @php
                                            $sizeDiff = $currentVersion->file_size - $version->file_size;
                                            $sizeChange = $sizeDiff > 0 ? '+' . number_format($sizeDiff) . ' bytes' : number_format($sizeDiff) . ' bytes';
                                        @endphp
                                        {{ $sizeChange }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <i class="bi bi-person-check fs-1 text-info mb-2"></i>
                                    <h6>Modified By</h6>
                                    <p class="mb-0">
                                        @if($version->modified_by_name === $currentVersion->modified_by_name)
                                            Same User
                                        @else
                                            Different Users
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Feature Coming Soon -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="feature-coming-soon">
                    <i class="bi bi-tools fs-1 mb-3"></i>
                    <h3>{{ $message }}</h3>
                    <p class="mb-4">{{ $reason }}</p>
                    
                    <div class="row g-3 mt-4">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-file-diff fs-4 me-3"></i>
                                <div>
                                    <strong>Side-by-Side View</strong>
                                    <br><small>Compare document content line by line</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-highlighter fs-4 me-3"></i>
                                <div>
                                    <strong>Change Highlighting</strong>
                                    <br><small>Visual indicators for additions and deletions</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-download fs-4 me-3"></i>
                                <div>
                                    <strong>Export Comparison</strong>
                                    <br><small>Download comparison reports</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-12 text-center">
                <button class="btn btn-secondary me-2" onclick="window.history.back()">
                    <i class="bi bi-arrow-left me-2"></i>Go Back
                </button>
                <button class="btn btn-primary" onclick="window.close()">
                    <i class="bi bi-x-lg me-2"></i>Close Window
                </button>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
