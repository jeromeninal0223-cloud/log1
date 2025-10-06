<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PasswordResetOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'otp',
        'expires_at',
        'is_used'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean'
    ];

    /**
     * Generate a new OTP for the given email
     */
    public static function generateOtp($email)
    {
        // Invalidate any existing OTPs for this email
        self::where('email', $email)->update(['is_used' => true]);

        // Generate 6-digit OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Create new OTP record (expires in 10 minutes)
        return self::create([
            'email' => $email,
            'otp' => $otp,
            'expires_at' => Carbon::now()->addMinutes(10),
            'is_used' => false
        ]);
    }

    /**
     * Verify OTP for the given email
     */
    public static function verifyOtp($email, $otp)
    {
        $otpRecord = self::where('email', $email)
            ->where('otp', $otp)
            ->where('is_used', false)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if ($otpRecord) {
            // Mark as used
            $otpRecord->update(['is_used' => true]);
            return true;
        }

        return false;
    }

    /**
     * Check if OTP is expired
     */
    public function isExpired()
    {
        return $this->expires_at < Carbon::now();
    }

    /**
     * Check if OTP is valid (not used and not expired)
     */
    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }
}
