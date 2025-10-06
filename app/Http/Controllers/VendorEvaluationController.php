<?php

namespace App\Http\Controllers;

use App\Models\VendorEvaluation;
use App\Models\Rfq;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class VendorEvaluationController extends Controller
{
    /**
     * Display a listing of vendor evaluations
     */
    public function index(Request $request): JsonResponse
    {
        $query = VendorEvaluation::with(['rfq', 'vendor', 'evaluator']);

        // Filter by RFQ
        if ($request->has('rfq_id')) {
            $query->where('rfq_id', $request->rfq_id);
        }

        // Filter by vendor
        if ($request->has('vendor_id')) {
            $query->where('vendor_id', $request->vendor_id);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by recommendation
        if ($request->has('recommendation')) {
            $query->where('recommendation', $request->recommendation);
        }

        $evaluations = $query->orderBy('total_score', 'desc')
                            ->paginate($request->get('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $evaluations
        ]);
    }

    /**
     * Store a newly created vendor evaluation
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'rfq_id' => 'required|exists:rfqs,id',
            'vendor_id' => 'required|exists:vendors,id',
            'quoted_price' => 'required|numeric|min:0',
            'proposed_delivery_date' => 'required|date',
            'technical_score' => 'nullable|integer|min:0|max:100',
            'commercial_score' => 'nullable|integer|min:0|max:100',
            'compliance_score' => 'nullable|integer|min:0|max:100',
            'evaluation_notes' => 'nullable|string',
            'evaluation_details' => 'nullable|array',
            'recommendation' => 'nullable|in:award,reject,clarification_needed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if evaluation already exists for this RFQ-Vendor combination
        $existingEvaluation = VendorEvaluation::where('rfq_id', $request->rfq_id)
            ->where('vendor_id', $request->vendor_id)
            ->first();

        if ($existingEvaluation) {
            return response()->json([
                'success' => false,
                'message' => 'Evaluation already exists for this vendor and RFQ'
            ], 422);
        }

        try {
            $evaluation = VendorEvaluation::create([
                'rfq_id' => $request->rfq_id,
                'vendor_id' => $request->vendor_id,
                'quoted_price' => $request->quoted_price,
                'proposed_delivery_date' => $request->proposed_delivery_date,
                'technical_score' => $request->technical_score,
                'commercial_score' => $request->commercial_score,
                'compliance_score' => $request->compliance_score,
                'evaluation_notes' => $request->evaluation_notes,
                'evaluation_details' => $request->evaluation_details,
                'recommendation' => $request->recommendation,
                'status' => 'submitted',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vendor evaluation created successfully',
                'data' => $evaluation->load(['rfq', 'vendor'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create vendor evaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified vendor evaluation
     */
    public function show(VendorEvaluation $vendorEvaluation): JsonResponse
    {
        $vendorEvaluation->load(['rfq.procurementPlan', 'vendor', 'evaluator']);

        return response()->json([
            'success' => true,
            'data' => $vendorEvaluation
        ]);
    }

    /**
     * Update the specified vendor evaluation
     */
    public function update(Request $request, VendorEvaluation $vendorEvaluation): JsonResponse
    {
        // Check if evaluation can be updated
        if (in_array($vendorEvaluation->status, ['awarded', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot update evaluation in current status'
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'quoted_price' => 'sometimes|required|numeric|min:0',
            'proposed_delivery_date' => 'sometimes|required|date',
            'technical_score' => 'nullable|integer|min:0|max:100',
            'commercial_score' => 'nullable|integer|min:0|max:100',
            'compliance_score' => 'nullable|integer|min:0|max:100',
            'evaluation_notes' => 'nullable|string',
            'evaluation_details' => 'nullable|array',
            'recommendation' => 'nullable|in:award,reject,clarification_needed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vendorEvaluation->update($request->only([
                'quoted_price', 'proposed_delivery_date', 'technical_score',
                'commercial_score', 'compliance_score', 'evaluation_notes',
                'evaluation_details', 'recommendation'
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Vendor evaluation updated successfully',
                'data' => $vendorEvaluation->fresh(['rfq', 'vendor', 'evaluator'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update vendor evaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Evaluate a vendor submission
     */
    public function evaluate(Request $request, VendorEvaluation $vendorEvaluation): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'technical_score' => 'required|integer|min:0|max:100',
            'commercial_score' => 'required|integer|min:0|max:100',
            'compliance_score' => 'required|integer|min:0|max:100',
            'evaluation_notes' => 'nullable|string',
            'evaluation_details' => 'nullable|array',
            'recommendation' => 'required|in:award,reject,clarification_needed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $vendorEvaluation->update([
                'technical_score' => $request->technical_score,
                'commercial_score' => $request->commercial_score,
                'compliance_score' => $request->compliance_score,
                'evaluation_notes' => $request->evaluation_notes,
                'evaluation_details' => $request->evaluation_details,
                'recommendation' => $request->recommendation,
                'status' => 'evaluated',
                'evaluated_by' => Auth::id(),
                'evaluated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Vendor evaluation completed successfully',
                'data' => $vendorEvaluation->fresh(['rfq', 'vendor', 'evaluator'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete evaluation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get evaluations for a specific RFQ
     */
    public function getByRfq(Rfq $rfq): JsonResponse
    {
        $evaluations = $rfq->vendorEvaluations()
            ->with(['vendor', 'evaluator'])
            ->orderBy('total_score', 'desc')
            ->get();

        $summary = [
            'total_evaluations' => $evaluations->count(),
            'evaluated_count' => $evaluations->where('status', 'evaluated')->count(),
            'pending_count' => $evaluations->whereIn('status', ['submitted', 'under_evaluation'])->count(),
            'recommended_for_award' => $evaluations->where('recommendation', 'award')->count(),
            'average_score' => $evaluations->where('total_score', '>', 0)->avg('total_score'),
            'highest_score' => $evaluations->max('total_score'),
            'lowest_quoted_price' => $evaluations->min('quoted_price'),
            'highest_quoted_price' => $evaluations->max('quoted_price'),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'evaluations' => $evaluations,
                'summary' => $summary
            ]
        ]);
    }

    /**
     * Get vendor evaluation statistics
     */
    public function getStats(): JsonResponse
    {
        $stats = [
            'total_evaluations' => VendorEvaluation::count(),
            'submitted_evaluations' => VendorEvaluation::where('status', 'submitted')->count(),
            'under_evaluation' => VendorEvaluation::where('status', 'under_evaluation')->count(),
            'evaluated_count' => VendorEvaluation::where('status', 'evaluated')->count(),
            'awarded_count' => VendorEvaluation::where('status', 'awarded')->count(),
            'rejected_count' => VendorEvaluation::where('status', 'rejected')->count(),
            'average_technical_score' => VendorEvaluation::whereNotNull('technical_score')->avg('technical_score'),
            'average_commercial_score' => VendorEvaluation::whereNotNull('commercial_score')->avg('commercial_score'),
            'average_compliance_score' => VendorEvaluation::whereNotNull('compliance_score')->avg('compliance_score'),
            'average_total_score' => VendorEvaluation::whereNotNull('total_score')->avg('total_score'),
            'recommendations' => VendorEvaluation::select('recommendation', DB::raw('count(*) as count'))
                ->whereNotNull('recommendation')
                ->groupBy('recommendation')
                ->get(),
            'top_vendors' => VendorEvaluation::select('vendor_id', DB::raw('AVG(total_score) as avg_score'), DB::raw('COUNT(*) as evaluation_count'))
                ->with('vendor:id,company_name')
                ->whereNotNull('total_score')
                ->groupBy('vendor_id')
                ->having('evaluation_count', '>=', 2)
                ->orderBy('avg_score', 'desc')
                ->limit(10)
                ->get(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}
