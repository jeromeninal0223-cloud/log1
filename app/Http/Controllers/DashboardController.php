<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\{
    PurchaseOrder,
    PurchaseRequest,
    InventoryItem,
    MaintenanceSchedule,
    Asset,
    Bid,
    Opportunity,
    Contract,
    Project,
    Document,
    AuditLog,
    Vendor,
    Invoice
};

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Get dynamic KPI data
        $kpiData = $this->getKPIData($user);
        
        // Get priority tasks based on user role
        $priorityTasks = $this->getPriorityTasks($user);
        
        // Get system health status
        $systemStatus = $this->getSystemHealthStatus();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($user);
        
        // Get role-specific metrics
        $roleMetrics = $this->getRoleSpecificMetrics($user);

        return view('dashboard', array_merge($kpiData, [
            'priorityTasks' => $priorityTasks,
            'systemStatus' => $systemStatus,
            'recentActivities' => $recentActivities,
            'roleMetrics' => $roleMetrics,
            'user' => $user
        ]));
    }

    private function getKPIData($user)
    {
        $currentMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();
        
        try {
            // Pending Orders (Purchase Orders + Purchase Requests)
            $pendingPOs = PurchaseOrder::where('status', 'pending')->count();
            $pendingPRs = PurchaseRequest::where('status', 'pending')->count();
            $pendingOrders = $pendingPOs + $pendingPRs;
        } catch (\Exception $e) {
            $pendingOrders = 0;
        }
        
        try {
            // Warehouse Capacity (based on inventory items vs total capacity)
            $totalItems = InventoryItem::sum('current_stock') ?? 0;
            $maxCapacity = 10000; // This could be configurable
            $warehouseCapacity = $maxCapacity > 0 ? min(100, round(($totalItems / $maxCapacity) * 100)) : 0;
        } catch (\Exception $e) {
            $warehouseCapacity = 0;
        }
        
        try {
            // Maintenance Due (assets requiring maintenance)
            $maintenanceDue = MaintenanceSchedule::where('scheduled_date', '<=', Carbon::now()->addDays(30))
                                               ->where('status', '!=', 'completed')
                                               ->count();
        } catch (\Exception $e) {
            $maintenanceDue = 0;
        }
        
        try {
            // Monthly Revenue (from completed contracts/invoices)
            $monthlyRevenue = Invoice::whereMonth('created_at', Carbon::now()->month)
                                   ->whereYear('created_at', Carbon::now()->year)
                                   ->where('status', 'paid')
                                   ->sum('total_amount') / 1000; // Convert to thousands
            
            $lastMonthRevenue = Invoice::whereMonth('created_at', $lastMonth->month)
                                     ->whereYear('created_at', $lastMonth->year)
                                     ->where('status', 'paid')
                                     ->sum('total_amount') / 1000;
            
            // Calculate revenue growth
            $revenueGrowth = $lastMonthRevenue > 0 ? 
                round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;
        } catch (\Exception $e) {
            $monthlyRevenue = 0;
            $revenueGrowth = 0;
        }

        return [
            'pendingOrders' => $pendingOrders,
            'warehouseCapacity' => $warehouseCapacity,
            'maintenanceDue' => $maintenanceDue,
            'monthlyRevenue' => $monthlyRevenue,
            'revenueGrowth' => $revenueGrowth
        ];
    }

    private function getPriorityTasks($user)
    {
        $tasks = collect();
        
        try {
            // High priority: Overdue maintenance
            $overdueMaintenance = MaintenanceSchedule::with('asset')
                ->where('scheduled_date', '<', Carbon::now())
                ->where('status', '!=', 'completed')
                ->take(3)
                ->get();
                
            foreach ($overdueMaintenance as $maintenance) {
                $tasks->push([
                    'title' => 'Overdue Maintenance: ' . ($maintenance->asset->name ?? 'Asset'),
                    'description' => 'Maintenance was due on ' . Carbon::parse($maintenance->scheduled_date)->format('M d, Y'),
                    'due_date' => Carbon::parse($maintenance->scheduled_date)->format('M d, Y'),
                    'priority' => 'high',
                    'type' => 'maintenance'
                ]);
            }
        } catch (\Exception $e) {
            // Skip maintenance tasks if table doesn't exist
        }
        
        try {
            // Medium priority: Pending purchase requests
            $pendingRequests = PurchaseRequest::where('status', 'pending')
                ->where('created_at', '<', Carbon::now()->subDays(3))
                ->take(3)
                ->get();
                
            foreach ($pendingRequests as $request) {
                $tasks->push([
                    'title' => 'Pending Purchase Request #' . $request->id,
                    'description' => 'Request pending for ' . Carbon::parse($request->created_at)->diffForHumans(),
                    'due_date' => Carbon::parse($request->created_at)->addDays(7)->format('M d, Y'),
                    'priority' => 'medium',
                    'type' => 'procurement'
                ]);
            }
        } catch (\Exception $e) {
            // Skip purchase requests if table doesn't exist
        }
        
        try {
            // Low priority: Upcoming contract renewals
            $upcomingContracts = Contract::where('end_date', '<=', Carbon::now()->addDays(30))
                ->where('end_date', '>', Carbon::now())
                ->where('status', 'active')
                ->take(2)
                ->get();
                
            foreach ($upcomingContracts as $contract) {
                $tasks->push([
                    'title' => 'Contract Renewal Due: ' . $contract->title,
                    'description' => 'Contract expires on ' . Carbon::parse($contract->end_date)->format('M d, Y'),
                    'due_date' => Carbon::parse($contract->end_date)->format('M d, Y'),
                    'priority' => 'low',
                    'type' => 'contract'
                ]);
            }
        } catch (\Exception $e) {
            // Skip contracts if table doesn't exist
        }
        
        return $tasks->sortBy(function($task) {
            $priorities = ['high' => 1, 'medium' => 2, 'low' => 3];
            return $priorities[$task['priority']];
        })->take(5)->values()->all();
    }

    private function getSystemHealthStatus()
    {
        $status = [];
        
        // Check database connectivity
        try {
            DB::connection()->getPdo();
            $status['database'] = 'operational';
        } catch (\Exception $e) {
            $status['database'] = 'error';
        }
        
        // Check recent activity (if there are recent records, system is active)
        $recentActivity = AuditLog::where('created_at', '>', Carbon::now()->subHours(24))->count();
        $status['user_activity'] = $recentActivity > 0 ? 'operational' : 'low_activity';
        
        // Check inventory system (if there are recent inventory updates)
        $recentInventory = InventoryItem::where('updated_at', '>', Carbon::now()->subDays(7))->count();
        $status['inventory_system'] = $recentInventory > 0 ? 'operational' : 'maintenance';
        
        // Check procurement system (if there are active bids/opportunities)
        $activeBids = Bid::where('created_at', '>', Carbon::now()->subDays(30))->count();
        $status['procurement_system'] = $activeBids > 0 ? 'operational' : 'low_activity';
        
        // Check maintenance system
        $scheduledMaintenance = MaintenanceSchedule::where('status', 'scheduled')->count();
        $status['maintenance_system'] = $scheduledMaintenance > 0 ? 'operational' : 'maintenance';
        
        return $status;
    }

    private function getRecentActivities($user)
    {
        $activities = collect();
        
        // Recent purchase orders
        try {
            $recentPOs = PurchaseOrder::with('creator')
                ->latest()
                ->take(3)
                ->get();
                
            foreach ($recentPOs as $po) {
                $activities->push([
                    'title' => 'Purchase Order #' . $po->po_number . ' created',
                    'module' => 'Procurement',
                    'user' => $po->creator->name ?? 'System',
                    'time' => $po->created_at->diffForHumans(),
                    'status' => $po->status,
                    'icon' => 'cart-plus'
                ]);
            }
        } catch (\Exception $e) {
            // Skip purchase orders if table doesn't exist
        }
        
        // Recent inventory receipts
        try {
            $recentReceipts = \App\Models\InventoryReceipt::with('user')
                ->latest()
                ->take(2)
                ->get();
                
            foreach ($recentReceipts as $receipt) {
                $activities->push([
                    'title' => 'Inventory received - ' . $receipt->supplier_name,
                    'module' => 'Warehouse',
                    'user' => $receipt->user->name ?? 'System',
                    'time' => $receipt->created_at->diffForHumans(),
                    'status' => 'completed',
                    'icon' => 'box-arrow-in-down'
                ]);
            }
        } catch (\Exception $e) {
            // Skip inventory receipts if table doesn't exist
        }
        
        // Recent maintenance activities
        try {
            $recentMaintenance = MaintenanceSchedule::with('asset')
                ->where('status', 'completed')
                ->latest('updated_at')
                ->take(2)
                ->get();
                
            foreach ($recentMaintenance as $maintenance) {
                $activities->push([
                    'title' => 'Maintenance completed - ' . ($maintenance->asset->name ?? 'Asset'),
                    'module' => 'Asset Management',
                    'user' => 'Maintenance Team',
                    'time' => $maintenance->updated_at->diffForHumans(),
                    'status' => 'completed',
                    'icon' => 'tools'
                ]);
            }
        } catch (\Exception $e) {
            // Skip maintenance activities if table doesn't exist
        }
        
        // Recent document activities
        try {
            $recentDocs = Document::latest()
                ->take(2)
                ->get();
                
            foreach ($recentDocs as $doc) {
                $activities->push([
                    'title' => 'Document uploaded - ' . $doc->title,
                    'module' => 'Document Management',
                    'user' => 'Document System',
                    'time' => $doc->created_at->diffForHumans(),
                    'status' => 'info',
                    'icon' => 'file-earmark-text'
                ]);
            }
        } catch (\Exception $e) {
            // Skip document activities if table doesn't exist
        }
        
        return $activities->sortByDesc('time')->take(6)->values()->all();
    }

    private function getRoleSpecificMetrics($user)
    {
        $metrics = [];
        
        try {
            switch ($user->role) {
                case 'admin':
                    $metrics = [
                        'total_users' => \App\Models\User::count(),
                        'active_projects' => $this->safeCount(Project::class, ['status' => 'active']),
                        'total_vendors' => $this->safeCount(Vendor::class),
                        'pending_approvals' => $this->safeCount(PurchaseRequest::class, ['status' => 'pending'])
                    ];
                    break;
                    
                case 'procurement_officer':
                    $metrics = [
                        'active_rfqs' => $this->safeCount(Opportunity::class, ['current_status' => 'Open']),
                        'pending_bids' => $this->safeCount(Bid::class, ['status' => 'Under Review']),
                        'contracts_expiring' => $this->safeCountWithDate(Contract::class, 'end_date', '<=', Carbon::now()->addDays(30)),
                        'vendor_evaluations' => $this->safeCount(\App\Models\VendorEvaluation::class, ['status' => 'pending'])
                    ];
                    break;
                    
                case 'logistics_staff':
                    $metrics = [
                        'pending_receipts' => $this->safeCount(\App\Models\InventoryReceipt::class, ['status' => 'pending']),
                        'low_stock_items' => $this->safeLowStockCount(),
                        'scheduled_pickings' => $this->safeCount(\App\Models\PickingSession::class, ['status' => 'scheduled']),
                        'dispatch_routes' => $this->safeCount(\App\Models\DispatchRoute::class, ['status' => 'active'])
                    ];
                    break;
                    
                default:
                    $metrics = [
                        'system_health' => 'operational',
                        'last_login' => $user->updated_at->diffForHumans(),
                        'total_activities' => $this->safeCount(AuditLog::class, ['user_id' => $user->id])
                    ];
            }
        } catch (\Exception $e) {
            $metrics = [
                'system_status' => 'checking',
                'data_loading' => 'in_progress'
            ];
        }
        
        return $metrics;
    }

    private function safeCount($modelClass, $conditions = [])
    {
        try {
            $query = $modelClass::query();
            foreach ($conditions as $field => $value) {
                $query->where($field, $value);
            }
            return $query->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeCountWithDate($modelClass, $dateField, $operator, $date)
    {
        try {
            return $modelClass::where($dateField, $operator, $date)->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function safeLowStockCount()
    {
        try {
            return InventoryItem::where('current_stock', '<=', DB::raw('minimum_stock'))->count();
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function getQuickStats()
    {
        try {
            return response()->json([
                'pending_orders' => $this->safeCount(PurchaseOrder::class, ['status' => 'pending']),
                'low_stock_alerts' => $this->safeLowStockCount(),
                'maintenance_due' => $this->safeCountWithDate(MaintenanceSchedule::class, 'scheduled_date', '<=', Carbon::now()->addDays(7)),
                'active_projects' => $this->safeCount(Project::class, ['status' => 'active'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'pending_orders' => 0,
                'low_stock_alerts' => 0,
                'maintenance_due' => 0,
                'active_projects' => 0,
                'error' => 'Data temporarily unavailable'
            ]);
        }
    }
}
