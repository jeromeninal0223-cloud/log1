<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects
     */
    public function index()
    {
        $projects = Project::with(['createdBy', 'approvedBy'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => Project::count(),
            'active' => Project::where('status', 'Active')->count(),
            'draft' => Project::where('status', 'Draft')->count(),
            'completed' => Project::where('status', 'Completed')->count(),
            'overdue' => Project::overdue()->count(),
            'total_budget' => Project::sum('estimated_budget'),
            'utilized_budget' => Project::sum('actual_budget')
        ];

        return view('PLT.Project', compact('projects', 'stats'));
    }

    /**
     * Store a newly created project
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'start_date' => 'required|date|after_or_equal:today',
            'expected_end_date' => 'required|date|after:start_date',
            'estimated_budget' => 'required|numeric|min:0',
            'responsible_person' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            DB::beginTransaction();

            $project = Project::create([
                'project_code' => Project::generateProjectCode(),
                'project_title' => $validated['project_title'],
                'project_description' => $validated['project_description'],
                'start_date' => $validated['start_date'],
                'expected_end_date' => $validated['expected_end_date'],
                'estimated_budget' => $validated['estimated_budget'],
                'responsible_person' => $validated['responsible_person'] ?? null,
                'department' => $validated['department'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'Draft',
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Project created successfully!',
                    'project' => $project->load(['createdBy'])
                ]);
            }

            // Regular form submission - redirect with success message
            return redirect()->back()->with('success', 'Project created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Check if request expects JSON (AJAX request)
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create project: ' . $e->getMessage()
                ], 500);
            }

            // Regular form submission - redirect with error message
            return redirect()->back()->withErrors(['error' => 'Failed to create project: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified project
     */
    public function show(Project $project)
    {
        $project->load(['createdBy', 'updatedBy', 'approvedBy']);
        
        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    /**
     * Update the specified project
     */
    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'project_description' => 'required|string',
            'start_date' => 'required|date',
            'expected_end_date' => 'required|date|after:start_date',
            'estimated_budget' => 'required|numeric|min:0',
            'actual_budget' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:Draft,Planning,Active,On Hold,Completed,Cancelled',
            'responsible_person' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'actual_end_date' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();

            $project->update(array_merge($validated, [
                'updated_by' => Auth::id()
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully!',
                'project' => $project->fresh()->load(['createdBy', 'updatedBy'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified project
     */
    public function destroy(Project $project)
    {
        try {
            // Only allow deletion of draft projects
            if ($project->status !== 'Draft') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only draft projects can be deleted.'
                ], 400);
            }

            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Approve a project
     */
    public function approve(Request $request, Project $project)
    {
        try {
            $project->update([
                'status' => 'Planning',
                'approved_at' => Carbon::now(),
                'approved_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Project approved successfully!',
                'project' => $project->fresh()->load(['approvedBy'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Start a project (change status to Active)
     */
    public function start(Project $project)
    {
        try {
            if (!in_array($project->status, ['Planning', 'On Hold'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project must be in Planning or On Hold status to start.'
                ], 400);
            }

            $project->update([
                'status' => 'Active',
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Project started successfully!',
                'project' => $project->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to start project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Complete a project
     */
    public function complete(Project $project)
    {
        try {
            $project->update([
                'status' => 'Completed',
                'actual_end_date' => Carbon::now(),
                'updated_by' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Project completed successfully!',
                'project' => $project->fresh()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to complete project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get project statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => Project::count(),
            'active' => Project::where('status', 'Active')->count(),
            'draft' => Project::where('status', 'Draft')->count(),
            'completed' => Project::where('status', 'Completed')->count(),
            'overdue' => Project::overdue()->count(),
            'total_budget' => Project::sum('estimated_budget'),
            'utilized_budget' => Project::sum('actual_budget'),
            'success_rate' => Project::count() > 0 ? 
                round((Project::where('status', 'Completed')->count() / Project::count()) * 100, 2) : 0
        ];

        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Save project as draft
     */
    public function saveDraft(Request $request)
    {
        $validated = $request->validate([
            'project_title' => 'nullable|string|max:255',
            'project_description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'expected_end_date' => 'nullable|date|after:start_date',
            'estimated_budget' => 'nullable|numeric|min:0',
            'responsible_person' => 'nullable|string|max:255',
            'department' => 'nullable|string|max:255',
            'notes' => 'nullable|string'
        ]);

        try {
            $project = Project::create([
                'project_code' => Project::generateProjectCode(),
                'project_title' => $validated['project_title'] ?? 'Untitled Project',
                'project_description' => $validated['project_description'] ?? '',
                'start_date' => $validated['start_date'] ?? Carbon::now()->addDays(1),
                'expected_end_date' => $validated['expected_end_date'] ?? Carbon::now()->addDays(30),
                'estimated_budget' => $validated['estimated_budget'] ?? 0,
                'responsible_person' => $validated['responsible_person'],
                'department' => $validated['department'],
                'notes' => $validated['notes'],
                'status' => 'Draft',
                'created_by' => Auth::id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Project saved as draft successfully!',
                'project' => $project->load(['createdBy'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save draft: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the reports page with movement analytics
     */
    public function reports()
    {
        // Get movement statistics
        $stats = [
            'total_reports' => 47,
            'completed_movements' => Project::where('status', 'Completed')->count(),
            'incidents_reported' => 3,
            'avg_completion_time' => '3.2'
        ];

        // Get analytics data
        $analytics = [
            'success_rate' => 94,
            'efficiency' => 87
        ];

        // Get summary data
        $summary = [
            'reports_generated' => 15,
            'movements_analyzed' => Project::count(),
            'issues_identified' => 8,
            'avg_report_time' => '2.5'
        ];

        return view('PLT.Reports', compact('stats', 'analytics', 'summary'));
    }

    /**
     * Display the execution/tracking page
     */
    public function execution()
    {
        $movements = Project::with(['createdBy'])
            ->whereIn('status', ['Active', 'In Progress', 'Scheduled'])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'active_movements' => Project::where('status', 'Active')->count(),
            'completed_movements' => Project::where('status', 'Completed')->count(),
            'delayed_movements' => Project::overdue()->count(),
            'avg_progress' => 75
        ];

        return view('PLT.execution', compact('movements', 'stats'));
    }
}
