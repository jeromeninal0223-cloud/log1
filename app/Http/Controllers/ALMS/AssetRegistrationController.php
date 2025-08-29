<?php

namespace App\Http\Controllers\ALMS;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;

class AssetRegistrationController extends Controller
{
    public function index()
    {
        $assets = Asset::latest()->take(50)->get();
        return view('ALMS.assetregistration', compact('assets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'string', 'max:255'],
            'registration_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $asset = Asset::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $asset], 201);
        }

        return redirect()->back()->with('success', 'Vehicle registered successfully!');
    }

    public function show(Asset $asset)
    {
        return response()->json(['data' => $asset]);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'plate_number' => ['required', 'string', 'max:255'],
            'vehicle_type' => ['required', 'string', 'max:255'],
            'brand' => ['nullable', 'string', 'max:255'],
            'model' => ['nullable', 'string', 'max:255'],
            'year' => ['nullable', 'integer', 'min:1900', 'max:2100'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'string', 'max:255'],
            'registration_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $asset->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $asset]);
        }

        return redirect()->back()->with('success', 'Vehicle updated successfully!');
    }
}


