<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'action',
        'module',
        'resource_type',
        'resource_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'session_id',
        'status',
        'error_message',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'success' => 'bg-success',
            'failed' => 'bg-danger',
            'warning' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'success' => 'bi-check-circle',
            'failed' => 'bi-x-circle',
            'warning' => 'bi-exclamation-triangle',
            default => 'bi-info-circle'
        };
    }

    /**
     * Get the action badge class
     */
    public function getActionBadgeClassAttribute(): string
    {
        return match($this->action) {
            'login' => 'bg-success',
            'logout' => 'bg-secondary',
            'create' => 'bg-primary',
            'update' => 'bg-info',
            'delete' => 'bg-danger',
            'view' => 'bg-light text-dark',
            'download' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Get the module badge class
     */
    public function getModuleBadgeClassAttribute(): string
    {
        return match($this->module) {
            'AUTH' => 'text-bg-success',
            'DTRS' => 'text-bg-secondary',
            'PSM' => 'text-bg-primary',
            'PLT' => 'text-bg-info',
            'SWS' => 'text-bg-warning',
            'ALMS' => 'text-bg-danger',
            'DASHBOARD' => 'text-bg-light text-dark',
            'VENDOR_PORTAL' => 'text-bg-secondary',
            'API' => 'text-bg-dark',
            'SYSTEM' => 'text-bg-secondary',
            default => 'text-bg-dark'
        };
    }

    /**
     * Get user avatar color based on role
     */
    public function getUserAvatarColorAttribute(): string
    {
        return match($this->user_role) {
            'admin' => 'bg-danger',
            'procurement_officer' => 'bg-primary',
            'logistics_staff' => 'bg-info',
            'driver' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Scope for filtering by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for filtering by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for filtering by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for filtering by module
     */
    public function scopeByModule($query, $module)
    {
        return $query->where('module', $module);
    }

    /**
     * Static method to log activities
     */
    public static function logActivity(array $data): self
    {
        // Get current request data
        $request = request();
        
        return self::create(array_merge([
            'user_id' => auth()->id(),
            'user_name' => auth()->user()?->name,
            'user_role' => auth()->user()?->role,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'session_id' => $request->session()->getId(),
            'status' => 'success',
        ], $data));
    }
}
