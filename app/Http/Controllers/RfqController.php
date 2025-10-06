<?php

namespace App\Http\Controllers;

use App\Models\Rfq;
use App\Models\ProcurementPlan;
use App\Models\VendorEvaluation;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RfqController extends Controller
{
    /**
     * Display a listing of RFQs
     */
    public function index(Request $request): JsonResponse
    {
        $query = Rfq::with(['procurementPlan', 'creator', 'vendorEvaluations']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by procurement plan
        if ($request->has('procurement_plan_id')) {
            $query->where('procurement_plan_id', $request->procurement_plan_id);
        }

        // Search by title or RFQ code
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('rfq_code', 'like', "%{$search}%");
            });
        }

        $rfqs = $query->orderBy('created_at', 'desc')
                     ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $rfqs
        ]);
    }

    /**
     * Store a newly created RFQ
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'procurement_plan_id' => 'required|exists:procurement_plans,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'specifications' => 'nullable|string',
            'submission_deadline' => 'required|date|after:today',
            'evaluation_date' => 'nullable|date|after:submission_deadline',
            'budget_range_min' => 'nullable|numeric|min:0',
            'budget_range_max' => 'nullable|numeric|min:0|gte:budget_range_min',
            'evaluation_criteria' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $rfq = Rfq::create([
                'procurement_plan_id' => $request->procurement_plan_id,
                'title' => $request->title,
                'description' => $request->description,
                'specifications' => $request->specifications,
                'submission_deadline' => $request->submission_deadline,
                'evaluation_date' => $request->evaluation_date,
                'budget_range_min' => $request->budget_range_min,
                'budget_range_max' => $request->budget_range_max,
                'evaluation_criteria' => $request->evaluation_criteria,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'RFQ created successfully',
                'data' => $rfq->load(['procurementPlan', 'creator'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create RFQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified RFQ
     */
    public function show(Rfq $rfq): JsonResponse
    {
        $rfq->load(['procurementPlan', 'creator', 'vendorEvaluations.vendor']);

        return response()->json([
            'success' => true,
            'data' => $rfq
        ]);
    }

    /**
     * Update the specified RFQ
     */
    public function update(Request $request, Rfq $rfq): JsonResponse
    {
        // Check if RFQ can be updated
        if (in_array($rfq->status, ['closed', 'awarded', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update RFQ in current status'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'specifications' => 'nullable|string',
            'submission_deadline' => 'sometimes|required|date|after:today',
            'evaluation_date' => 'nullable|date|after:submission_deadline',
            'budget_range_min' => 'nullable|numeric|min:0',
            'budget_range_max' => 'nullable|numeric|min:0|gte:budget_range_min',
            'status' => 'sometimes|required|in:draft,published,closed,awarded,cancelled',
            'evaluation_criteria' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $rfq->update($request->only([
                'title', 'description', 'specifications', 'submission_deadline',
                'evaluation_date', 'budget_range_min', 'budget_range_max',
                'status', 'evaluation_criteria'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'RFQ updated successfully',
                'data' => $rfq->fresh(['procurementPlan', 'creator', 'vendorEvaluations'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update RFQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Publish an RFQ
     */
    public function publish(Rfq $rfq): JsonResponse
    {
        if ($rfq->status !== 'draft') {
            return response()->json([
                'success' => false,
                'message' => 'Only draft RFQs can be published'
            ], 422);
        }

        try {
            $rfq->update(['status' => 'published']);

            return response()->json([
                'success' => true,
                'message' => 'RFQ published successfully',
                'data' => $rfq
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish RFQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Close an RFQ
     */
    public function close(Rfq $rfq): JsonResponse
    {
        if ($rfq->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Only published RFQs can be closed'
            ], 422);
        }

        try {
            $rfq->update(['status' => 'closed']);

            return response()->json([
                'success' => true,
                'message' => 'RFQ closed successfully',
                'data' => $rfq
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to close RFQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Award an RFQ to a vendor
     */
    public function award(Request $request, Rfq $rfq): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vendor_evaluation_id' => 'required|exists:vendor_evaluations,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($rfq->status !== 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Only closed RFQs can be awarded'
            ], 422);
        }

        try {
            $evaluation = VendorEvaluation::findOrFail($request->vendor_evaluation_id);
            
            // Verify the evaluation belongs to this RFQ
            if ($evaluation->rfq_id !== $rfq->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid vendor evaluation for this RFQ'
                ], 422);
            }

            // Update RFQ status
            $rfq->update(['status' => 'awarded']);

            // Update winning evaluation
            $evaluation->update(['status' => 'awarded']);

            // Update other evaluations to rejected
            VendorEvaluation::where('rfq_id', $rfq->id)
                ->where('id', '!=', $evaluation->id)
                ->update(['status' => 'rejected']);

            return response()->json([
                'success' => true,
                'message' => 'RFQ awarded successfully',
                'data' => [
                    'rfq' => $rfq->fresh(),
                    'winning_evaluation' => $evaluation->fresh(['vendor'])
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to award RFQ',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get RFQ statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_rfqs' => Rfq::count(),
            'draft_rfqs' => Rfq::where('status', 'draft')->count(),
            'published_rfqs' => Rfq::where('status', 'published')->count(),
            'closed_rfqs' => Rfq::where('status', 'closed')->count(),
            'awarded_rfqs' => Rfq::where('status', 'awarded')->count(),
            'expired_rfqs' => Rfq::expired()->count(),
            'active_rfqs' => Rfq::active()->count(),
            'avg_evaluation_time' => Rfq::whereNotNull('evaluation_date')
                ->selectRaw('AVG(DATEDIFF(evaluation_date, submission_deadline)) as avg_days')
                ->value('avg_days'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
