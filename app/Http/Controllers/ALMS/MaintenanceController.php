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
            'title' => ['required', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'string', 'max:255'],
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
            'title' => ['required', 'string', 'max:255'],
            'scheduled_date' => ['required', 'date'],
            'status' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $schedule->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $schedule->load('asset')]);
        }

        return redirect()->back()->with('success', 'Maintenance updated.');
    }
}


