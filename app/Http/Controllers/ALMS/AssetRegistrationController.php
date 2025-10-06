<?php

namespace App\Http\Controllers\ALMS;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AssetRegistrationController extends Controller
{
    public function index()
    {
        $assets = Asset::latest()->paginate(15);
        
        // Check image existence for each asset
        foreach($assets as $asset) {
            $asset->image_exists = $asset->image_path && Storage::disk('public')->exists($asset->image_path);
        }
        
        return view('ALMS.assetregistration', compact('assets'));
    }

    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'asset_category' => ['required', 'string', 'in:buildings,vehicles,machinery,furniture,it_equipment,tools,office_equipment'],
            'asset_id' => ['required', 'string', 'max:255'],
            'asset_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
        ];

        // Add category-specific validation rules
        switch ($request->asset_category) {
            case 'buildings':
                $rules += [
                    'building_name' => ['required', 'string', 'max:255'],
                    'location_address' => ['required', 'string'],
                    'floor_area' => ['nullable', 'numeric', 'min:0'],
                    'date_acquired' => ['nullable', 'date'],
                    'acquisition_cost' => ['nullable', 'numeric', 'min:0'],
                    'condition' => ['nullable', 'string'],
                    'current_use' => ['nullable', 'string'],
                    'responsible_department' => ['nullable', 'string'],
                    'custodian' => ['nullable', 'string'],
                    'last_inspection' => ['nullable', 'date'],
                    'remarks' => ['nullable', 'string'],
                ];
                break;
            case 'vehicles':
                $rules += [
                    'vehicle_type' => ['required', 'string', 'max:255'],
                    'plate_number' => ['required', 'string', 'max:255'],
                    'engine_chassis' => ['nullable', 'string'],
                    'date_acquired' => ['nullable', 'date'],
                    'supplier' => ['nullable', 'string'],
                    'acquisition_cost' => ['nullable', 'numeric', 'min:0'],
                    'condition' => ['nullable', 'string'],
                    'operational_status' => ['nullable', 'string'],
                    'assigned_driver' => ['nullable', 'string'],
                    'department_location' => ['nullable', 'string'],
                    'last_maintenance' => ['nullable', 'date'],
                    'next_maintenance' => ['nullable', 'date'],
                    'remarks' => ['nullable', 'string'],
                ];
                break;
            case 'machinery':
                $rules += [
                    'equipment_name' => ['required', 'string', 'max:255'],
                    'brand_model' => ['nullable', 'string'],
                    'serial_number' => ['nullable', 'string'],
                    'date_acquired' => ['nullable', 'date'],
                    'acquisition_cost' => ['nullable', 'numeric', 'min:0'],
                    'condition' => ['nullable', 'string'],
                    'location_site' => ['nullable', 'string'],
                    'assigned_to' => ['nullable', 'string'],
                    'maintenance_frequency' => ['nullable', 'string'],
                    'last_service' => ['nullable', 'date'],
                    'next_service' => ['nullable', 'date'],
                    'remarks' => ['nullable', 'string'],
                ];
                break;
            case 'furniture':
                $rules += [
                    'item_description' => ['required', 'string', 'max:255'],
                    'brand_model' => ['nullable', 'string'],
                    'quantity' => ['nullable', 'integer', 'min:1'],
                    'unit_cost' => ['nullable', 'numeric', 'min:0'],
                    'total_cost' => ['nullable', 'numeric', 'min:0'],
                    'location_office' => ['nullable', 'string'],
                    'condition' => ['nullable', 'string'],
                    'assigned_user' => ['nullable', 'string'],
                    'remarks' => ['nullable', 'string'],
                ];
                break;
            case 'it_equipment':
                $rules += [
                    'item_name' => ['required', 'string', 'max:255'],
                    'serial_number' => ['nullable', 'string'],
                    'brand_model' => ['nullable', 'string'],
                    'date_acquired' => ['nullable', 'date'],
                    'cost' => ['nullable', 'numeric', 'min:0'],
                    'condition' => ['nullable', 'string'],
                    'assigned_to' => ['nullable', 'string'],
                    'department' => ['nullable', 'string'],
                    'warranty_expiry' => ['nullable', 'date'],
                    'status' => ['nullable', 'string'],
                    'remarks' => ['nullable', 'string'],
                ];
                break;
            case 'tools':
                $rules += [
                    'tool_name' => ['required', 'string', 'max:255'],
                    'brand_model' => ['nullable', 'string'],
                    'serial_number' => ['nullable', 'string'],
                    'quantity' => ['nullable', 'integer', 'min:1'],
                    'unit_cost' => ['nullable', 'numeric', 'min:0'],
                    'total_cost' => ['nullable', 'numeric', 'min:0'],
                    'custodian' => ['nullable', 'string'],
                    'condition' => ['nullable', 'string'],
                    'location' => ['nullable', 'string'],
                    'remarks' => ['nullable', 'string'],
                ];
                break;
            case 'office_equipment':
                $rules += [
                    'equipment_name' => ['required', 'string', 'max:255'],
                    'brand_model' => ['nullable', 'string'],
                    'serial_number' => ['nullable', 'string'],
                    'date_acquired' => ['nullable', 'date'],
                    'cost' => ['nullable', 'numeric', 'min:0'],
                    'location' => ['nullable', 'string'],
                    'condition' => ['nullable', 'string'],
                    'assigned_to' => ['nullable', 'string'],
                    'maintenance_schedule' => ['nullable', 'string'],
                    'remarks' => ['nullable', 'string'],
                ];
                break;
        }

        $validated = $request->validate($rules);

        // Handle image upload
        if ($request->hasFile('asset_image')) {
            $image = $request->file('asset_image');
            
            // Fix double extension issue by getting proper extension
            $extension = $image->getClientOriginalExtension();
            $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
            $imageName = time() . '_' . $originalName . '.' . $extension;
            
            // Ensure the assets directory exists
            $assetsPath = storage_path('app/public/assets');
            if (!file_exists($assetsPath)) {
                mkdir($assetsPath, 0755, true);
            }
            
            // Store the file directly using move
            $fullPath = $assetsPath . '/' . $imageName;
            if ($image->move($assetsPath, $imageName)) {
                $validated['image_path'] = 'assets/' . $imageName;
                
                // Debug: Log the storage operation
                \Log::info('Asset image uploaded successfully', [
                    'original_name' => $image->getClientOriginalName(),
                    'stored_name' => $imageName,
                    'full_path' => $fullPath,
                    'file_exists' => file_exists($fullPath)
                ]);
            } else {
                \Log::error('Asset image upload failed', [
                    'original_name' => $image->getClientOriginalName(),
                    'target_path' => $fullPath
                ]);
            }
        }

        // Convert all validated data to JSON for flexible storage
        $validated['asset_data'] = json_encode($validated);
        $validated['created_by'] = auth()->id();
    
        $asset = Asset::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'data' => $asset], 201);
        }

        return redirect()->back()->with('success', 'Physical asset registered successfully!');
    }

    public function show(Asset $asset)
    {
        return response()->json(['data' => $asset]);
    }

    public function update(Request $request, $id)
    {
        try {
            \Log::info('Asset update started', [
                'asset_id' => $id,
                'request_data' => $request->all()
            ]);
            
            $asset = Asset::findOrFail($id);
            
            \Log::info('Asset found', [
                'asset_id' => $asset->id,
                'asset_category' => $asset->asset_category
            ]);

            // Get the asset category from request or existing asset
            $category = $request->input('asset_category') ?? $asset->asset_category;

            // Base validation rules
            $rules = [
                'asset_category' => ['sometimes', 'string', 'in:buildings,vehicles,machinery,furniture,it_equipment,tools,office_equipment'],
                'asset_id' => ['sometimes', 'string', 'max:255'],
                'asset_image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:2048'],
            ];

            // Add category-specific validation rules
            switch ($category) {
                case 'buildings':
                    $rules += [
                        'building_name' => ['sometimes', 'string', 'max:255'],
                        'location_address' => ['sometimes', 'string'],
                        'floor_area' => ['nullable', 'numeric', 'min:0'],
                        'date_acquired' => ['nullable', 'date'],
                        'acquisition_cost' => ['nullable', 'numeric', 'min:0'],
                        'condition' => ['nullable', 'string'],
                        'current_use' => ['nullable', 'string'],
                        'responsible_department' => ['nullable', 'string'],
                        'custodian' => ['nullable', 'string'],
                        'last_inspection' => ['nullable', 'date'],
                        'remarks' => ['nullable', 'string'],
                    ];
                    break;
                case 'vehicles':
                    $rules += [
                        'vehicle_type' => ['sometimes', 'string', 'max:255'],
                        'plate_number' => ['sometimes', 'string', 'max:255'],
                        'engine_chassis' => ['nullable', 'string'],
                        'date_acquired' => ['nullable', 'date'],
                        'supplier' => ['nullable', 'string'],
                        'acquisition_cost' => ['nullable', 'numeric', 'min:0'],
                        'condition' => ['nullable', 'string'],
                        'operational_status' => ['nullable', 'string'],
                        'assigned_driver' => ['nullable', 'string'],
                        'department_location' => ['nullable', 'string'],
                        'last_maintenance' => ['nullable', 'date'],
                        'next_maintenance' => ['nullable', 'date'],
                        'remarks' => ['nullable', 'string'],
                    ];
                    break;
                case 'machinery':
                    $rules += [
                        'equipment_name' => ['sometimes', 'string', 'max:255'],
                        'brand_model' => ['nullable', 'string'],
                        'serial_number' => ['nullable', 'string'],
                        'date_acquired' => ['nullable', 'date'],
                        'acquisition_cost' => ['nullable', 'numeric', 'min:0'],
                        'condition' => ['nullable', 'string'],
                        'location_site' => ['nullable', 'string'],
                        'assigned_to' => ['nullable', 'string'],
                        'maintenance_frequency' => ['nullable', 'string'],
                        'last_service' => ['nullable', 'date'],
                        'next_service' => ['nullable', 'date'],
                        'remarks' => ['nullable', 'string'],
                    ];
                    break;
                case 'furniture':
                    $rules += [
                        'item_description' => ['sometimes', 'string', 'max:255'],
                        'brand_model' => ['nullable', 'string'],
                        'quantity' => ['nullable', 'integer', 'min:1'],
                        'unit_cost' => ['nullable', 'numeric', 'min:0'],
                        'total_cost' => ['nullable', 'numeric', 'min:0'],
                        'location_office' => ['nullable', 'string'],
                        'condition' => ['nullable', 'string'],
                        'assigned_user' => ['nullable', 'string'],
                        'remarks' => ['nullable', 'string'],
                    ];
                    break;
                case 'it_equipment':
                    $rules += [
                        'item_name' => ['sometimes', 'string', 'max:255'],
                        'serial_number' => ['nullable', 'string'],
                        'brand_model' => ['nullable', 'string'],
                        'date_acquired' => ['nullable', 'date'],
                        'cost' => ['nullable', 'numeric', 'min:0'],
                        'condition' => ['nullable', 'string'],
                        'assigned_to' => ['nullable', 'string'],
                        'department' => ['nullable', 'string'],
                        'warranty_expiry' => ['nullable', 'date'],
                        'status' => ['nullable', 'string'],
                        'remarks' => ['nullable', 'string'],
                    ];
                    break;
                case 'tools':
                    $rules += [
                        'tool_name' => ['sometimes', 'string', 'max:255'],
                        'brand_model' => ['nullable', 'string'],
                        'serial_number' => ['nullable', 'string'],
                        'quantity' => ['nullable', 'integer', 'min:1'],
                        'unit_cost' => ['nullable', 'numeric', 'min:0'],
                        'total_cost' => ['nullable', 'numeric', 'min:0'],
                        'custodian' => ['nullable', 'string'],
                        'condition' => ['nullable', 'string'],
                        'location' => ['nullable', 'string'],
                        'remarks' => ['nullable', 'string'],
                    ];
                    break;
                case 'office_equipment':
                    $rules += [
                        'equipment_name' => ['sometimes', 'string', 'max:255'],
                        'brand_model' => ['nullable', 'string'],
                        'serial_number' => ['nullable', 'string'],
                        'date_acquired' => ['nullable', 'date'],
                        'cost' => ['nullable', 'numeric', 'min:0'],
                        'location' => ['nullable', 'string'],
                        'condition' => ['nullable', 'string'],
                        'assigned_to' => ['nullable', 'string'],
                        'maintenance_schedule' => ['nullable', 'string'],
                        'remarks' => ['nullable', 'string'],
                    ];
                    break;
            }

            $validated = $request->validate($rules);

            \Log::info('Validation passed', ['validated_data' => $validated]);

            // Handle image upload
            if ($request->hasFile('asset_image')) {
                // Delete old image if exists
                if ($asset->image_path) {
                    $oldImagePath = storage_path('app/public/' . $asset->image_path);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                
                $image = $request->file('asset_image');
                
                // Fix double extension issue by getting proper extension
                $extension = $image->getClientOriginalExtension();
                $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                $imageName = time() . '_' . $originalName . '.' . $extension;
                
                // Ensure the assets directory exists
                $assetsPath = storage_path('app/public/assets');
                if (!file_exists($assetsPath)) {
                    mkdir($assetsPath, 0755, true);
                }
                
                // Store the file directly using move
                $fullPath = $assetsPath . '/' . $imageName;
                if ($image->move($assetsPath, $imageName)) {
                    $validated['image_path'] = 'assets/' . $imageName;
                    
                    // Debug: Log the storage operation
                    \Log::info('Asset image updated successfully', [
                        'asset_id' => $asset->id,
                        'original_name' => $image->getClientOriginalName(),
                        'stored_name' => $imageName,
                        'full_path' => $fullPath,
                        'file_exists' => file_exists($fullPath)
                    ]);
                } else {
                    \Log::error('Asset image update failed', [
                        'asset_id' => $asset->id,
                        'original_name' => $image->getClientOriginalName(),
                        'target_path' => $fullPath
                    ]);
                }
            }

            // Update asset data JSON
            $validated['asset_data'] = json_encode($validated);

            \Log::info('About to update asset', ['asset_id' => $asset->id]);
            
            $asset->update($validated);
            
            \Log::info('Asset updated successfully', ['asset_id' => $asset->id]);
            
            // Refresh the asset from database to get updated data
            $asset->refresh();

            if ($request->wantsJson()) {
                return response()->json(['success' => true, 'data' => $asset]);
            }
        } catch (\Exception $e) {
            \Log::error('Asset update failed', [
                'asset_id' => $asset->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update asset. Please try again.'
            ], 500);
        }
    }

    // API methods for asset data
    public function apiIndex()
    {
        try {
            $assets = Asset::orderBy('created_at', 'desc')->get();
            return response()->json($assets);
        } catch (\Exception $e) {
            \Log::error('API assets index failed', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to load assets'
            ], 500);
        }
    }

    public function apiShow($id)
    {
        try {
            $asset = Asset::findOrFail($id);
            return response()->json($asset);
        } catch (\Exception $e) {
            \Log::error('API asset show failed', ['asset_id' => $id, 'error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Asset not found'
            ], 404);
        }
    }
}
