<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $message }} - Document Version History</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .placeholder-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
            margin: 2rem;
        }
        
        .placeholder-header {
            background: linear-gradient(45deg, #ff6b6b, #ee5a52);
            color: white;
            padding: 2rem;
            border-radius: 20px 20px 0 0;
            text-align: center;
        }
        
        .placeholder-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        
        .suggestion-item {
            background: #f8f9fa;
            border-left: 4px solid #007bff;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 0 8px 8px 0;
        }
        
        .action-buttons {
            background: #f8f9fa;
            padding: 1.5rem 2rem;
            border-radius: 0 0 20px 20px;
            text-align: center;
        }
        
        .document-info {
            background: #e3f2fd;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="placeholder-card">
        <!-- Header -->
        <div class="placeholder-header">
            <i class="bi bi-file-earmark-x placeholder-icon"></i>
            <h2 class="mb-2">{{ $message }}</h2>
            <p class="mb-0 opacity-75">{{ ucfirst($action) }} Request Failed</p>
        </div>

        <!-- Content -->
        <div class="p-4">
            <!-- Document Information -->
            @if(isset($vendor))
            <div class="document-info">
                <h6 class="text-primary mb-2">
                    <i class="bi bi-building me-2"></i>Vendor Document Details
                </h6>
                <div class="row g-2">
                    <div class="col-sm-4"><strong>Company:</strong></div>
                    <div class="col-sm-8">{{ $vendor->company_name }}</div>
                    <div class="col-sm-4"><strong>Document Type:</strong></div>
                    <div class="col-sm-8">{{ ucfirst(str_replace('_', ' ', $documentType)) }}</div>
                    <div class="col-sm-4"><strong>Vendor ID:</strong></div>
                    <div class="col-sm-8">#{{ $vendor->id }}</div>
                </div>
            </div>
            @elseif(isset($version))
            <div class="document-info">
                <h6 class="text-primary mb-2">
                    <i class="bi bi-file-earmark-text me-2"></i>Document Version Details
                </h6>
                <div class="row g-2">
                    <div class="col-sm-4"><strong>Document:</strong></div>
                    <div class="col-sm-8">{{ $document->title }}</div>
                    <div class="col-sm-4"><strong>Version:</strong></div>
                    <div class="col-sm-8">{{ $version->version_number }}</div>
                    <div class="col-sm-4"><strong>Modified By:</strong></div>
                    <div class="col-sm-8">{{ $version->modified_by_name }}</div>
                    <div class="col-sm-4"><strong>Date:</strong></div>
                    <div class="col-sm-8">{{ $version->created_at->format('M d, Y \a\t g:i A') }}</div>
                </div>
            </div>
            @endif

            <!-- Problem Description -->
            <div class="mb-4">
                <h6 class="text-danger mb-3">
                    <i class="bi bi-exclamation-triangle me-2"></i>What Happened?
                </h6>
                <p class="text-muted">{{ $reason }}</p>
            </div>

            <!-- Suggestions -->
            <div class="mb-4">
                <h6 class="text-info mb-3">
                    <i class="bi bi-lightbulb me-2"></i>Possible Solutions
                </h6>
                @foreach($suggestions as $suggestion)
                <div class="suggestion-item">
                    <i class="bi bi-arrow-right me-2 text-primary"></i>{{ $suggestion }}
                </div>
                @endforeach
            </div>

            <!-- Additional Help -->
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Need Help?</strong> 
                @if(isset($vendor))
                    Contact the vendor or system administrator to resolve this issue. The document may need to be re-uploaded through the vendor registration system.
                @else
                    Contact your system administrator to restore the missing document file or check the storage configuration.
                @endif
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="action-buttons">
            <button class="btn btn-secondary me-2" onclick="window.history.back()">
                <i class="bi bi-arrow-left me-2"></i>Go Back
            </button>
            <button class="btn btn-primary me-2" onclick="window.location.reload()">
                <i class="bi bi-arrow-clockwise me-2"></i>Try Again
            </button>
            <button class="btn btn-outline-secondary" onclick="window.close()">
                <i class="bi bi-x-lg me-2"></i>Close
            </button>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Auto-close after 30 seconds if opened in popup
        if (window.opener) {
            setTimeout(() => {
                if (confirm('This window will close automatically. Close now?')) {
                    window.close();
                }
            }, 30000);
        }
    </script>
</body>
</html>
