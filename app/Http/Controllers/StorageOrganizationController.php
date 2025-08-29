<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\InventoryReceipt;
use App\Models\InventoryReceiptItem;

class StorageOrganizationController extends Controller
{
    public function index()
    {
        // Get unorganized items from inventory receipts (items not yet assigned to storage bins)
        $unorganizedItems = InventoryReceiptItem::whereHas('receipt', function($query) {
                $query->whereIn('status', ['Completed', 'Pending']);
            })
            ->where(function($query) {
                $query->whereNull('storage_location')
                    ->orWhere('storage_location', 'receiving_area')
                    ->orWhere('storage_location', 'LIKE', '%receiving%');
            })
            ->with('receipt')
            ->get();

        // Get organized items (items with proper storage assignments)
        $organizedItems = InventoryReceiptItem::whereHas('receipt', function($query) {
                $query->whereIn('status', ['Completed', 'Pending']);
            })
            ->whereNotNull('storage_location')
            ->where('storage_location', '!=', 'receiving_area')
            ->where('storage_location', 'NOT LIKE', '%receiving%')
            ->with('receipt')
            ->get();

        // Get storage bin data with actual inventory items
        $storageBins = $this->getStorageBinData();

        // Get available zones and bins
        $zones = collect(['A', 'B', 'C', 'D', 'E']);
        $bins = InventoryReceiptItem::whereNotNull('storage_location')
            ->where('storage_location', 'LIKE', '%-%')
            ->distinct()
            ->pluck('storage_location')
            ->merge(['A1-1', 'A1-2', 'A1-3', 'A2-1', 'A2-2', 'A2-3', 'B1-1', 'B1-2', 'B1-3', 'B2-1', 'B2-2', 'B2-3'])
            ->unique()
            ->sort();

        return view('SWS.storageorganization', compact('unorganizedItems', 'organizedItems', 'zones', 'bins', 'storageBins'));
    }

    public function assignLocation(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_receipt_items,id',
            'zone' => 'required|string',
            'bin' => 'required|string',
        ]);

        try {
            $item = InventoryReceiptItem::findOrFail($request->item_id);
            
            // Create storage location string (e.g., "A1-1")
            $storageLocation = $request->zone . $request->bin;
            
            $item->update([
                'storage_location' => $storageLocation,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location assigned successfully',
                'item' => [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'zone' => $request->zone,
                    'bin' => $request->bin,
                    'location' => $storageLocation,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error assigning location: ' . $e->getMessage()
            ], 500);
        }
    }

    public function relocateItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:inventory_receipt_items,id',
            'zone' => 'required|string',
            'bin' => 'required|string',
        ]);

        try {
            $item = InventoryReceiptItem::findOrFail($request->item_id);
            
            // Store old location for reference
            $oldLocation = $item->storage_location;
            
            // Create new storage location string (e.g., "A1-1")
            $newStorageLocation = $request->zone . $request->bin;
            
            $item->update([
                'storage_location' => $newStorageLocation,
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Item relocated successfully',
                'item' => [
                    'id' => $item->id,
                    'name' => $item->item_name,
                    'old_location' => $oldLocation,
                    'new_location' => $newStorageLocation,
                    'zone' => $request->zone,
                    'bin' => $request->bin,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error relocating item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkAssignLocation(Request $request)
    {
        // Logic for bulk assigning locations
        return response()->json(['success' => true, 'message' => 'Bulk assignment completed']);
    }

    public function getItemsByZone()
    {
        // Logic for getting items by zone
        return response()->json(['items' => []]);
    }

    public function getStorageLocations()
    {
        // Logic for getting storage locations
        return response()->json(['locations' => []]);
    }

    private function getStorageBinData()
    {
        $bins = [
            'A1-1' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'A1-2' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'A1-3' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'A2-1' => ['status' => 'maintenance', 'capacity' => 100, 'max_capacity' => 100],
            'A2-2' => ['status' => 'reserved', 'capacity' => 100, 'max_capacity' => 100],
            'A2-3' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'B1-1' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'B1-2' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'B1-3' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'B2-1' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'B2-2' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
            'B2-3' => ['status' => 'available', 'capacity' => 100, 'max_capacity' => 100],
        ];

        // Get actual items in each bin from inventory receipts
        foreach ($bins as $binId => &$binData) {
            $items = InventoryReceiptItem::where('storage_location', $binId)
                ->whereHas('receipt', function($query) {
                    $query->whereIn('status', ['Completed', 'Pending']);
                })
                ->get();

            $binData['items'] = $items;
            $binData['used_capacity'] = $items->sum('quantity');
            $binData['usage_percentage'] = $binData['max_capacity'] > 0 ? 
                round(($binData['used_capacity'] / $binData['max_capacity']) * 100) : 0;
            
            // Update status based on usage
            if ($binData['status'] !== 'maintenance' && $binData['status'] !== 'reserved') {
                if ($binData['used_capacity'] > 0) {
                    $binData['status'] = 'occupied';
                } else {
                    $binData['status'] = 'available';
                }
            }
        }

        // No sample data - only show actual database items

        return $bins;
    }
}
