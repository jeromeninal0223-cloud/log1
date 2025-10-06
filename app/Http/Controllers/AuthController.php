<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Check if user is already logged in with a different role
        if (Auth::check()) {
            $currentUser = Auth::user();
            $attemptedUser = User::where('email', $credentials['email'])->first();
            
            if ($attemptedUser && $currentUser->id !== $attemptedUser->id) {
                // Different user trying to login - show warning
                return back()->withErrors([
                    'email' => 'Another user is already logged in. Please logout first or use a different browser/incognito window.',
                ])->onlyInput('email')->with('warning', 'Session conflict detected. Please use separate browser sessions for different user accounts.');
            }
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Redirect procurement officers to Officer dashboard, others to main dashboard
            $user = Auth::user();
            
            // Debug logging to check user role
            \Log::info('User login redirect debug', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'user_name' => $user->name
            ]);
            
            if ($user->role === 'procurement_officer') {
                \Log::info('Redirecting procurement officer to /officer/dashboard');
                return redirect('/officer/dashboard');
            } elseif ($user->role === 'admin' || $user->role === 'logistics_staff') {
                \Log::info('Redirecting admin/logistics to main dashboard');
                return redirect('/dashboard');
            }
            
            // Default fallback for unknown roles
            \Log::info('Unknown role, redirecting to officer dashboard');
            return redirect('/officer/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'role' => 'required|in:admin,procurement_officer,logistics_staff',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login')->with('success', 'Account created successfully! Please login with your credentials.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
