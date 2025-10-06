<?php

namespace App\Http\Controllers;

use App\Models\ProcurementPlan;
use App\Models\SourcingStrategy;
use App\Models\Rfq;
use App\Models\VendorEvaluation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProcurementPlanningController extends Controller
{
    /**
     * Display the procurement planning dashboard
     */
    public function index()
    {
        $stats = [
            'procurement_requests' => ProcurementPlan::count(),
            'rfqs_approved' => Rfq::where('status', 'published')->count(),
            'draft_plans' => ProcurementPlan::where('status', 'draft')->count(),
            'active_suppliers' => DB::table('vendors')->where('status', 'active')->count(),
        ];

        $recentPlans = ProcurementPlan::with(['creator', 'sourcingStrategies'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $overduePlans = ProcurementPlan::overdue()
            ->with('creator')
            ->orderBy('required_delivery_date')
            ->limit(5)
            ->get();

        return view('PLT.Toursetup', compact('stats', 'recentPlans', 'overduePlans'));
    }

    /**
     * Store a new procurement plan
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'procurement_title' => 'required|string|max:255',
            'category' => 'required|in:goods,services,construction,technology,consulting',
            'priority' => 'required|in:normal,high,urgent',
            'planning_start_date' => 'required|date',
            'required_delivery_date' => 'required|date|after:planning_start_date',
            'delivery_location' => 'required|string|max:255',
            'requesting_department' => 'nullable|string|max:255',
            'estimated_quantity' => 'nullable|integer|min:1',
            'unit_of_measure' => 'nullable|string|max:50',
            'procurement_officer' => 'nullable|string|max:255',
            'estimated_budget' => 'nullable|numeric|min:0',
            'max_budget' => 'nullable|numeric|min:0|gte:estimated_budget',
            'description' => 'nullable|string',
            'technical_requirements' => 'nullable|string',
            'sourcing_strategy' => 'nullable|array',
            'sourcing_strategy.*.phase_number' => 'required|integer|min:1',
            'sourcing_strategy.*.phase_date' => 'required|date',
            'sourcing_strategy.*.activity' => 'required|string|max:255',
            'sourcing_strategy.*.responsible' => 'required|string|max:255',
            'sourcing_strategy.*.deliverable' => 'required|string|max:255',
            'sourcing_strategy.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $procurementPlan = ProcurementPlan::create([
                'procurement_title' => $request->procurement_title,
                'category' => $request->category,
                'priority' => $request->priority,
                'planning_start_date' => $request->planning_start_date,
                'required_delivery_date' => $request->required_delivery_date,
                'delivery_location' => $request->delivery_location,
                'requesting_department' => $request->requesting_department,
                'estimated_quantity' => $request->estimated_quantity,
                'unit_of_measure' => $request->unit_of_measure,
                'procurement_officer' => $request->procurement_officer,
                'estimated_budget' => $request->estimated_budget,
                'max_budget' => $request->max_budget,
                'description' => $request->description,
                'technical_requirements' => $request->technical_requirements,
                'status' => $request->has('save_as_draft') ? 'draft' : 'under_review',
                'created_by' => Auth::id(),
            ]);

            // Add sourcing strategy phases
            if ($request->has('sourcing_strategy') && is_array($request->sourcing_strategy)) {
                foreach ($request->sourcing_strategy as $strategy) {
                    SourcingStrategy::create([
                        'procurement_plan_id' => $procurementPlan->id,
                        'phase_number' => $strategy['phase_number'],
                        'phase_date' => $strategy['phase_date'],
                        'activity' => $strategy['activity'],
                        'responsible' => $strategy['responsible'],
                        'deliverable' => $strategy['deliverable'],
                        'notes' => $strategy['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Procurement plan created successfully',
                'data' => [
                    'procurement_plan' => $procurementPlan->load('sourcingStrategies'),
                    'procurement_code' => $procurementPlan->procurement_code
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create procurement plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified procurement plan
     */
    public function show(ProcurementPlan $procurementPlan): JsonResponse
    {
        $procurementPlan->load(['creator', 'updater', 'sourcingStrategies', 'rfqs.vendorEvaluations']);

        return response()->json([
            'success' => true,
            'data' => $procurementPlan
        ]);
    }

    /**
     * Update the specified procurement plan
     */
    public function update(Request $request, ProcurementPlan $procurementPlan): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'procurement_title' => 'sometimes|required|string|max:255',
            'category' => 'sometimes|required|in:goods,services,construction,technology,consulting',
            'priority' => 'sometimes|required|in:normal,high,urgent',
            'planning_start_date' => 'sometimes|required|date',
            'required_delivery_date' => 'sometimes|required|date|after:planning_start_date',
            'delivery_location' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|in:draft,under_review,approved,in_progress,completed,cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $procurementPlan->update(array_merge(
                $request->only([
                    'procurement_title', 'category', 'priority', 'planning_start_date',
                    'required_delivery_date', 'delivery_location', 'requesting_department',
                    'estimated_quantity', 'unit_of_measure', 'procurement_officer',
                    'estimated_budget', 'max_budget', 'description', 'technical_requirements', 'status'
                ]),
                ['updated_by' => Auth::id()]
            ));

            return response()->json([
                'success' => true,
                'message' => 'Procurement plan updated successfully',
                'data' => $procurementPlan->fresh(['creator', 'updater', 'sourcingStrategies'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update procurement plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified procurement plan
     */
    public function destroy(ProcurementPlan $procurementPlan): JsonResponse
    {
        try {
            // Check if plan can be deleted (only drafts or cancelled plans)
            if (!in_array($procurementPlan->status, ['draft', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft or cancelled procurement plans can be deleted'
                ], 422);
            }

            $procurementPlan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Procurement plan deleted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete procurement plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get procurement planning statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_plans' => ProcurementPlan::count(),
            'draft_plans' => ProcurementPlan::where('status', 'draft')->count(),
            'approved_plans' => ProcurementPlan::where('status', 'approved')->count(),
            'in_progress_plans' => ProcurementPlan::where('status', 'in_progress')->count(),
            'completed_plans' => ProcurementPlan::where('status', 'completed')->count(),
            'overdue_plans' => ProcurementPlan::overdue()->count(),
            'total_budget' => ProcurementPlan::sum('estimated_budget'),
            'avg_duration' => ProcurementPlan::avg('duration_days'),
            'categories' => ProcurementPlan::select('category', DB::raw('count(*) as count'))
                ->groupBy('category')
                ->get(),
            'monthly_trends' => ProcurementPlan::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
                ->where('created_at', '>=', now()->subMonths(12))
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get()
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Duplicate an existing procurement plan
     */
    public function duplicate(ProcurementPlan $procurementPlan): JsonResponse
    {
        try {
            DB::beginTransaction();

            $newPlan = $procurementPlan->replicate();
            $newPlan->procurement_code = null; // Will be auto-generated
            $newPlan->status = 'draft';
            $newPlan->created_by = Auth::id();
            $newPlan->updated_by = null;
            $newPlan->procurement_title = $newPlan->procurement_title . ' (Copy)';
            $newPlan->save();

            // Duplicate sourcing strategies
            foreach ($procurementPlan->sourcingStrategies as $strategy) {
                $newStrategy = $strategy->replicate();
                $newStrategy->procurement_plan_id = $newPlan->id;
                $newStrategy->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Procurement plan duplicated successfully',
                'data' => $newPlan->load('sourcingStrategies')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate procurement plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
