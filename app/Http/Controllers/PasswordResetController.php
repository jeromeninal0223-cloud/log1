<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\PasswordResetOtp;
use App\Mail\PasswordResetOtpMail;

class PasswordResetController extends Controller
{
    /**
     * Send OTP to user's email for password reset
     */
    public function sendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address that exists in our system.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $email = $request->email;
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No account found with this email address.'
                ], 404);
            }

            // Generate OTP
            $otpRecord = PasswordResetOtp::generateOtp($email);

            // Send OTP via email
            try {
                Mail::to($email)->send(new PasswordResetOtpMail($otpRecord->otp, $user->name));
                
                Log::info('Password reset OTP sent', [
                    'email' => $email,
                    'otp_id' => $otpRecord->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'OTP has been sent to your email address. Please check your inbox.',
                    'expires_in' => 10 // minutes
                ]);

            } catch (\Exception $e) {
                Log::error('Failed to send OTP email', [
                    'email' => $email,
                    'error' => $e->getMessage()
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send OTP. Please try again later.'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('OTP generation failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Verify OTP and reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|size:6',
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please check your input and try again.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $email = $request->email;
            $otp = $request->otp;
            $password = $request->password;

            // Verify OTP
            if (!PasswordResetOtp::verifyOtp($email, $otp)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid or expired OTP. Please request a new one.'
                ], 400);
            }

            // Find user and update password
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not found.'
                ], 404);
            }

            // Update password
            $user->update([
                'password' => Hash::make($password)
            ]);

            Log::info('Password reset successful', [
                'email' => $email,
                'user_id' => $user->id
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Password has been reset successfully. You can now login with your new password.',
                'redirect' => route('password.reset.success')
            ]);

        } catch (\Exception $e) {
            Log::error('Password reset failed', [
                'email' => $request->email,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong. Please try again later.'
            ], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please enter a valid email address.',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if there's a recent OTP request (prevent spam)
        $recentOtp = PasswordResetOtp::where('email', $request->email)
            ->where('created_at', '>', now()->subMinute())
            ->first();

        if ($recentOtp) {
            return response()->json([
                'success' => false,
                'message' => 'Please wait at least 1 minute before requesting a new OTP.'
            ], 429);
        }

        // Use the same logic as sendOtp
        return $this->sendOtp($request);
    }
}
