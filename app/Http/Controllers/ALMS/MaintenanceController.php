<?php

namespace App\Http\Controllers\ALMS;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\MaintenanceSchedule;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    public function index()
    {
        $assets = Asset::orderBy('plate_number')->get();
        $schedules = MaintenanceSchedule::with('asset')->latest()->take(100)->get();
        return view('ALMS.maintenance', compact('assets', 'schedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'category' => ['required', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['required', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'string', 'max:255'],
            'estimated_duration' => ['nullable', 'string', 'max:255'],
            'assigned_technician' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $schedule = MaintenanceSchedule::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $schedule->load('asset')], 201);
        }

        return redirect()->back()->with('success', 'Maintenance scheduled.');
    }

    public function show(MaintenanceSchedule $schedule)
    {
        return response()->json(['data' => $schedule->load('asset')]);
    }

    public function update(Request $request, MaintenanceSchedule $schedule)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'category' => ['sometimes', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:255'],
            'priority' => ['sometimes', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'string', 'max:255'],
            'estimated_duration' => ['nullable', 'string', 'max:255'],
            'assigned_technician' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $schedule->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $schedule->load('asset')]);
        }

        return redirect()->back()->with('success', 'Maintenance updated.');
    }

    public function destroy(MaintenanceSchedule $schedule)
    {
        try {
            $schedule->delete();

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Maintenance schedule deleted successfully.'
                ]);
            }

            return redirect()->back()->with('success', 'Maintenance schedule deleted successfully.');
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete maintenance schedule', [
                'schedule_id' => $schedule->id,
                'error' => $e->getMessage()
            ]);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Failed to delete maintenance schedule. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete maintenance schedule. Please try again.');
        }
    }

    public function bulkReschedule(Request $request)
    {
        $validated = $request->validate([
            'schedule_ids' => ['required', 'array'],
            'schedule_ids.*' => ['required', 'exists:maintenance_schedules,id'],
            'new_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $scheduleIds = $validated['schedule_ids'];
            $newDate = $validated['new_date'];
            $reason = $validated['reason'];

            // Update all selected schedules
            $updatedCount = MaintenanceSchedule::whereIn('id', $scheduleIds)
                ->update([
                    'scheduled_date' => $newDate,
                    'notes' => $reason ? "Rescheduled: {$reason}" : null,
                    'updated_at' => now()
                ]);

            \Log::info('Bulk reschedule completed', [
                'schedule_ids' => $scheduleIds,
                'new_date' => $newDate,
                'reason' => $reason,
                'updated_count' => $updatedCount,
                'user_id' => auth()->id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully rescheduled {$updatedCount} maintenance schedule(s) to {$newDate}.",
                    'updated_count' => $updatedCount
                ]);
            }

            return redirect()->back()->with('success', "Successfully rescheduled {$updatedCount} maintenance schedule(s).");

        } catch (\Exception $e) {
            \Log::error('Failed to bulk reschedule maintenance schedules', [
                'schedule_ids' => $validated['schedule_ids'] ?? [],
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to reschedule maintenance schedules. Please try again.'
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to reschedule maintenance schedules. Please try again.');
        }
    }
}


