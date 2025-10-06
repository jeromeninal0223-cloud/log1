<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log for authenticated users
        if (Auth::check()) {
            $this->logRequest($request, $response);
        }

        return $response;
    }

    /**
     * Log the request
     */
    private function logRequest(Request $request, Response $response): void
    {
        try {
            // Skip logging for certain routes/methods
            if ($this->shouldSkipLogging($request)) {
                return;
            }

            $action = $this->determineAction($request);
            $module = $this->determineModule($request);
            $description = $this->generateDescription($request, $action, $module);
            $status = $response->getStatusCode() >= 400 ? 'failed' : 'success';

            AuditLog::create([
                'user_id' => Auth::id(),
                'user_name' => Auth::user()->name,
                'user_role' => Auth::user()->role,
                'action' => $action,
                'module' => $module,
                'resource_type' => $this->getResourceType($request),
                'resource_id' => $this->getResourceId($request),
                'description' => $description,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => $request->session()->getId(),
                'status' => $status,
                'error_message' => $status === 'failed' ? $this->getErrorMessage($response) : null,
            ]);
        } catch (\Exception $e) {
            // Log the error but don't break the application
            \Log::error('Audit logging failed: ' . $e->getMessage());
        }
    }

    /**
     * Determine if we should skip logging for this request
     */
    private function shouldSkipLogging(Request $request): bool
    {
        $skipRoutes = [
            'dtrs.audits',
            'dtrs.audits.data',
            'api.*',
            'logout',
        ];

        $skipMethods = ['HEAD', 'OPTIONS'];
        $skipPaths = [
            '/css/',
            '/js/',
            '/images/',
            '/favicon.ico',
            '/api/audit',
        ];

        // Skip certain HTTP methods
        if (in_array($request->method(), $skipMethods)) {
            return true;
        }

        // Skip asset requests
        foreach ($skipPaths as $path) {
            if (str_contains($request->path(), $path)) {
                return true;
            }
        }

        // Skip certain routes
        $routeName = $request->route()?->getName();
        if ($routeName) {
            foreach ($skipRoutes as $skipRoute) {
                if (str_contains($skipRoute, '*')) {
                    $pattern = str_replace('*', '.*', $skipRoute);
                    if (preg_match('/^' . $pattern . '$/', $routeName)) {
                        return true;
                    }
                } elseif ($routeName === $skipRoute) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine the action based on HTTP method and route
     */
    private function determineAction(Request $request): string
    {
        $method = $request->method();
        $path = $request->path();

        // Special cases based on route patterns
        if (str_contains($path, '/login')) {
            return 'login';
        }
        if (str_contains($path, '/logout')) {
            return 'logout';
        }
        if (str_contains($path, '/download')) {
            return 'download';
        }
        if (str_contains($path, '/view')) {
            return 'view';
        }

        // Default mapping based on HTTP method
        return match($method) {
            'GET' => 'view',
            'POST' => 'create',
            'PUT', 'PATCH' => 'update',
            'DELETE' => 'delete',
            default => 'unknown'
        };
    }

    /**
     * Determine the module based on the route
     */
    private function determineModule(Request $request): string
    {
        $path = $request->path();
        $routeName = $request->route()?->getName();

        // Authentication routes
        if (str_contains($path, '/login') || str_contains($path, '/logout') || str_contains($path, '/register')) {
            return 'AUTH';
        }

        // Document Tracking & Records System
        if (str_contains($path, '/dtrs/') || str_contains($path, 'document')) {
            return 'DTRS';
        }

        // Procurement & Sourcing Management
        if (str_contains($path, '/psm/') || str_contains($path, 'procurement') || 
            str_contains($path, 'vendor') || str_contains($path, 'bidding') || 
            str_contains($path, 'contract') || str_contains($path, 'order') || 
            str_contains($path, 'invoice')) {
            return 'PSM';
        }

        // Project Logistics Tracker
        if (str_contains($path, '/plt/') || str_contains($path, 'logistics') || 
            str_contains($path, 'toursetup') || str_contains($path, 'execution') || 
            str_contains($path, 'closure')) {
            return 'PLT';
        }

        // Smart Warehousing System
        if (str_contains($path, '/sws/') || str_contains($path, 'warehouse') || 
            str_contains($path, 'stock') || str_contains($path, 'inventory')) {
            return 'SWS';
        }

        // Asset Life Cycle & Maintenance System
        if (str_contains($path, '/alms/') || str_contains($path, 'asset') || 
            str_contains($path, 'maintenance')) {
            return 'ALMS';
        }

        // Dashboard and Officer routes
        if (str_contains($path, 'dashboard') || str_contains($path, '/officer/')) {
            return 'DASHBOARD';
        }

        // Vendor Portal
        if (str_contains($path, '/vendor/') || $routeName && str_contains($routeName, 'vendor')) {
            return 'VENDOR_PORTAL';
        }

        // API routes
        if (str_contains($path, '/api/')) {
            return 'API';
        }

        // Default fallback - should be rare now
        return 'SYSTEM';
    }

    /**
     * Generate a human-readable description
     */
    private function generateDescription(Request $request, string $action, string $module): string
    {
        $path = $request->path();
        $method = $request->method();

        // Special descriptions for common actions
        if ($action === 'login') {
            return 'User logged into the system';
        }
        if ($action === 'logout') {
            return 'User logged out of the system';
        }
        if ($action === 'download') {
            return 'Downloaded file or document';
        }

        // Generate description based on module and action
        $moduleNames = [
            'DTRS' => 'Document Tracking',
            'PSM' => 'Procurement & Sourcing',
            'PLT' => 'Project Logistics',
            'SWS' => 'Smart Warehousing',
            'ALMS' => 'Asset Management',
            'AUTH' => 'Authentication',
            'SYSTEM' => 'System',
        ];

        $moduleName = $moduleNames[$module] ?? $module;
        $actionName = ucfirst($action);

        return "{$actionName} action in {$moduleName} module";
    }

    /**
     * Get resource type from route
     */
    private function getResourceType(Request $request): ?string
    {
        $path = $request->path();

        if (str_contains($path, '/document')) return 'Document';
        if (str_contains($path, '/contract')) return 'Contract';
        if (str_contains($path, '/order')) return 'PurchaseOrder';
        if (str_contains($path, '/vendor')) return 'Vendor';
        if (str_contains($path, '/bid')) return 'Bid';
        if (str_contains($path, '/invoice')) return 'Invoice';
        if (str_contains($path, '/asset')) return 'Asset';
        if (str_contains($path, '/user')) return 'User';

        return null;
    }

    /**
     * Get resource ID from route parameters
     */
    private function getResourceId(Request $request): ?int
    {
        $route = $request->route();
        if (!$route) return null;

        $parameters = $route->parameters();
        
        // Common parameter names for IDs
        $idParams = ['id', 'documentId', 'contractId', 'orderId', 'vendorId', 'bidId', 'invoiceId', 'assetId', 'userId'];
        
        foreach ($idParams as $param) {
            if (isset($parameters[$param]) && is_numeric($parameters[$param])) {
                return (int) $parameters[$param];
            }
        }

        return null;
    }

    /**
     * Get error message from response
     */
    private function getErrorMessage(Response $response): ?string
    {
        if ($response->getStatusCode() < 400) {
            return null;
        }

        return "HTTP {$response->getStatusCode()} Error";
    }
}
