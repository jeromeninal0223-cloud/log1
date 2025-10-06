<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Require2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $vendor = Auth::guard('vendor')->user();
        
        // If vendor is not authenticated, let other middleware handle it
        if (!$vendor) {
            return $next($request);
        }
        
        // If 2FA is not enabled for this vendor, proceed normally
        if (!$vendor->two_factor_enabled) {
            return $next($request);
        }
        
        // If 2FA is enabled but not verified in this session
        if (!$request->session()->get('2fa_verified', false)) {
            // Allow access to 2FA verification routes
            $allowedRoutes = [
                'vendor.2fa.verify',
                'vendor.logout'
            ];
            
            if (in_array($request->route()->getName(), $allowedRoutes)) {
                return $next($request);
            }
            
            // Redirect to 2FA verification page
            return redirect()->route('vendor.2fa.challenge');
        }
        
        return $next($request);
    }
}
