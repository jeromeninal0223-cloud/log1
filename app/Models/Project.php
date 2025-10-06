<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_code',
        'project_title',
        'project_description',
        'start_date',
        'expected_end_date',
        'actual_end_date',
        'estimated_budget',
        'actual_budget',
        'status',
        'responsible_person',
        'department',
        'notes',
        'created_by',
        'updated_by',
        'approved_at',
        'approved_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'estimated_budget' => 'decimal:2',
        'actual_budget' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Generate unique project code
    public static function generateProjectCode()
    {
        $year = date('Y');
        $lastProject = self::whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($lastProject) {
            $lastNumber = (int) substr($lastProject->project_code, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return 'PROJ-' . $year . '-' . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    // Get status badge color
    public function getStatusColor()
    {
        return match($this->status) {
            'Draft' => 'secondary',
            'Planning' => 'warning',
            'Active' => 'success',
            'On Hold' => 'warning',
            'Completed' => 'success',
            'Cancelled' => 'danger',
            default => 'secondary'
        };
    }


    // Calculate project duration in days
    public function getDurationInDays()
    {
        $endDate = $this->actual_end_date ?? $this->expected_end_date;
        return $this->start_date->diffInDays($endDate);
    }

    // Calculate project progress percentage
    public function getProgressPercentage()
    {
        if ($this->status === 'Completed') {
            return 100;
        }
        
        if ($this->status === 'Cancelled' || $this->status === 'Draft') {
            return 0;
        }

        $totalDays = $this->start_date->diffInDays($this->expected_end_date);
        $elapsedDays = $this->start_date->diffInDays(Carbon::now());
        
        if ($elapsedDays <= 0) {
            return 0;
        }
        
        if ($elapsedDays >= $totalDays) {
            return 100;
        }
        
        return round(($elapsedDays / $totalDays) * 100);
    }

    // Check if project is overdue
    public function isOverdue()
    {
        return $this->status !== 'Completed' && 
               $this->status !== 'Cancelled' && 
               Carbon::now()->isAfter($this->expected_end_date);
    }

    // Get budget utilization percentage
    public function getBudgetUtilization()
    {
        if (!$this->actual_budget || $this->estimated_budget == 0) {
            return 0;
        }
        
        return round(($this->actual_budget / $this->estimated_budget) * 100, 2);
    }

    // Scope for active projects
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Scope for completed projects
    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    // Scope for projects by user
    public function scopeByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    // Scope for overdue projects
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'Completed')
                    ->where('status', '!=', 'Cancelled')
                    ->where('expected_end_date', '<', Carbon::now());
    }
}
