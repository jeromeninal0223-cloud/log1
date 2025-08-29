<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckVendorStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if vendor is authenticated
        if (Auth::guard('vendor')->check()) {
            $vendor = Auth::guard('vendor')->user();
            
            // Check if vendor status is not active/approved
            $statusLower = strtolower((string) $vendor->status);
            if (!in_array($statusLower, ['active', 'approved'], true)) {
                // Force logout
                Auth::guard('vendor')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                $message = match ($statusLower) {
                    'pending' => 'Your account is pending approval. Please wait for admin approval.',
                    'suspended' => 'Your account has been suspended. Please contact support.',
                    default => 'Your account is not active. Please contact support.'
                };
                
                // Redirect to login with error message
                return redirect()->route('vendor.login')->withErrors([
                    'email' => $message,
                ]);
            }
        }

        return $next($request);
    }
}
