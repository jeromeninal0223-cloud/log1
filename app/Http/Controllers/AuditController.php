<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AuditController extends Controller
{
    /**
     * Display the audit trail page
     */
    public function index(Request $request)
    {
        // Get filter parameters
        $startDate = $request->get('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $userId = $request->get('user_id');
        $action = $request->get('action');
        $module = $request->get('module');
        $search = $request->get('search');

        // Build query with filters
        $query = AuditLog::with('user')
            ->dateRange($startDate . ' 00:00:00', $endDate . ' 23:59:59')
            ->orderBy('created_at', 'desc');

        if ($userId) {
            $query->byUser($userId);
        }

        if ($action) {
            $query->byAction($action);
        }

        if ($module) {
            $query->byModule($module);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('user_name', 'like', "%{$search}%")
                  ->orWhere('resource_type', 'like', "%{$search}%");
            });
        }

        // Get paginated results
        $auditLogs = $query->paginate(10);

        // Get statistics for today
        $todayStats = $this->getTodayStatistics();

        // Get all users for filter dropdown
        $users = User::select('id', 'name', 'role')->orderBy('name')->get();

        return view('DTRS.audits', compact(
            'auditLogs',
            'todayStats',
            'users',
            'startDate',
            'endDate',
            'userId',
            'action',
            'module',
            'search'
        ));
    }

    /**
     * Get today's statistics
     */
    private function getTodayStatistics()
    {
        $today = Carbon::today();
        
        return [
            'total_activities' => AuditLog::whereDate('created_at', $today)->count(),
            'active_users' => AuditLog::whereDate('created_at', $today)
                ->distinct('user_id')
                ->count('user_id'),
            'security_alerts' => AuditLog::whereDate('created_at', $today)
                ->where('status', 'failed')
                ->count(),
            'compliance_score' => $this->calculateComplianceScore(),
        ];
    }

    /**
     * Calculate compliance score based on recent activities
     */
    private function calculateComplianceScore()
    {
        $totalActivities = AuditLog::whereDate('created_at', '>=', Carbon::now()->subDays(7))->count();
        $failedActivities = AuditLog::whereDate('created_at', '>=', Carbon::now()->subDays(7))
            ->where('status', 'failed')
            ->count();

        if ($totalActivities === 0) {
            return 100;
        }

        return max(0, 100 - (($failedActivities / $totalActivities) * 100));
    }

    /**
     * Export audit logs
     */
    public function export(Request $request)
    {
        // Implementation for exporting audit logs
        // This would generate Excel/CSV/PDF reports
        
        return response()->json([
            'success' => true,
            'message' => 'Export functionality will be implemented based on requirements'
        ]);
    }

    /**
     * Get audit data for AJAX requests
     */
    public function getData(Request $request)
    {
        $startDate = $request->get('start_date', Carbon::now()->subDays(7)->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $userId = $request->get('user_id');
        $action = $request->get('action');
        $module = $request->get('module');

        $query = AuditLog::with('user')
            ->dateRange($startDate . ' 00:00:00', $endDate . ' 23:59:59')
            ->orderBy('created_at', 'desc');

        if ($userId) $query->byUser($userId);
        if ($action) $query->byAction($action);
        if ($module) $query->byModule($module);

        $auditLogs = $query->limit(100)->get();

        return response()->json([
            'success' => true,
            'data' => $auditLogs->map(function($log) {
                return [
                    'id' => $log->id,
                    'timestamp' => $log->created_at->format('Y-m-d H:i:s'),
                    'user_name' => $log->user_name,
                    'user_role' => $log->user_role,
                    'action' => $log->action,
                    'module' => $log->module,
                    'description' => $log->description,
                    'ip_address' => $log->ip_address,
                    'status' => $log->status,
                    'status_icon' => $log->status_icon,
                    'action_badge_class' => $log->action_badge_class,
                    'module_badge_class' => $log->module_badge_class,
                    'user_avatar_color' => $log->user_avatar_color,
                ];
            }),
            'total' => $query->count()
        ]);
    }
}
