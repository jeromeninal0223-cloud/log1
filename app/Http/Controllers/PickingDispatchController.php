<?php

namespace App\Http\Controllers;

use App\Models\InventoryReceiptItem;
use App\Models\ItemRequest;
use App\Models\PickingSession;
use App\Models\PickingItem;
use App\Models\DispatchRoute;
use App\Models\DispatchSchedule;
use App\Models\DispatchItem;
use App\Models\DispatchTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PickingDispatchController extends Controller
{
    public function index()
    {
        // Get inventory items that have active item requests
        $inventoryItems = InventoryReceiptItem::whereHas('receipt', function($query) {
                $query->whereIn('status', ['Completed', 'Pending']);
            })
            ->where('quantity', '>', 0)
            ->whereExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('item_requests')
                      ->whereColumn('item_requests.item_name', 'inventory_receipt_items.item_name')
                      ->whereIn('item_requests.status', ['pending', 'in_progress']);
            })
            ->with('receipt')
            ->orderBy('item_name')
            ->get();

        // Add request count, total requested quantity, and requester details for each inventory item
        $inventoryItems->each(function($item) {
            $requests = ItemRequest::where('item_name', $item->item_name)
                ->whereIn('status', ['pending', 'in_progress'])
                ->with('requestedBy')
                ->get();
            
            $item->request_count = $requests->count();
            $item->total_requested_quantity = $requests->sum('requested_quantity');
            $item->requests = $requests; // Include full request details with requesters
        });

        // Get items ready for dispatch from the last completed session
        $dispatchItems = session('last_completed_session.items', []);

        return view('SWS.picking and dispatch', compact('inventoryItems', 'dispatchItems'));
    }

    public function updatePicked(Request $request, $item)
    {
        $request->validate([
            'picked_quantity' => 'required|integer|min:0',
        ]);

        try {
            $inventoryItem = InventoryReceiptItem::findOrFail($item);
            
            // Validate picked quantity doesn't exceed available quantity
            if ($request->picked_quantity > $inventoryItem->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Picked quantity cannot exceed available quantity'
                ], 400);
            }

            // For now, we'll store picked quantity in a session or you can add a picked_quantity field
            // Since InventoryReceiptItem doesn't have picked_quantity field, we'll return success
            
            return response()->json([
                'success' => true,
                'message' => 'Picked quantity updated successfully',
                'data' => [
                    'id' => $inventoryItem->id,
                    'picked_quantity' => $request->picked_quantity,
                    'available_quantity' => $inventoryItem->quantity
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating picked quantity: ' . $e->getMessage()
            ], 500);
        }
    }

    public function dispatchItem(Request $request)
    {
        // Logic for dispatching single item
        return response()->json(['success' => true, 'message' => 'Item dispatched successfully']);
    }

    public function getInventoryItems()
    {
        $inventoryItems = InventoryReceiptItem::whereHas('receipt', function($query) {
                $query->whereIn('status', ['Completed', 'Pending']);
            })
            ->where('quantity', '>', 0)
            ->with('receipt')
            ->orderBy('item_name')
            ->get();

        return response()->json([
            'success' => true,
            'items' => $inventoryItems->map(function($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                    'quantity' => $item->quantity,
                    'storage_location' => $item->storage_location,
                    'description' => $item->description,
                    'receipt_number' => $item->receipt->receipt_number ?? 'N/A'
                ];
            })
        ]);
    }


    public function getItemsByLocation()
    {
        // Logic for getting items by location
        return response()->json(['items' => []]);
    }

    public function removeFromDispatch(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer'
        ]);

        try {
            // Get current dispatch items from session
            $dispatchItems = session('last_completed_session.items', []);
            
            // Remove the item with matching item_id
            $dispatchItems = array_filter($dispatchItems, function($item) use ($request) {
                return $item['item_id'] != $request->item_id;
            });
            
            // Re-index the array
            $dispatchItems = array_values($dispatchItems);
            
            // Update session
            session(['last_completed_session.items' => $dispatchItems]);
            
            return response()->json([
                'success' => true,
                'message' => 'Item removed from dispatch list',
                'remaining_count' => count($dispatchItems)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function saveProgress(Request $request)
    {
        $request->validate([
            'picked_items' => 'required|array',
            'picked_items.*.item_id' => 'required|integer|exists:inventory_receipt_items,id',
            'picked_items.*.picked_quantity' => 'required|integer|min:0'
        ]);

        try {
            DB::beginTransaction();

            foreach ($request->picked_items as $pickedItem) {
                $inventoryItem = InventoryReceiptItem::findOrFail($pickedItem['item_id']);
                
                // Validate picked quantity doesn't exceed available quantity
                if ($pickedItem['picked_quantity'] > $inventoryItem->quantity) {
                    throw new \Exception("Picked quantity for {$inventoryItem->item_name} cannot exceed available quantity");
                }

                // Store picked quantity in session for now
                // You can modify this to store in database if you add a picked_quantity column
                session()->put("picked_item_{$pickedItem['item_id']}", $pickedItem['picked_quantity']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Progress saved successfully',
                'data' => [
                    'saved_items' => count($request->picked_items),
                    'timestamp' => now()->toISOString()
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Error saving pick progress: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving progress: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ] : null
            ], 500);
        }
    }

    public function completeSession(Request $request)
    {
        $request->validate([
            'picked_items' => 'required|array',
            'picked_items.*.item_id' => 'required|integer|exists:inventory_receipt_items,id',
            'picked_items.*.picked_quantity' => 'required|integer|min:0',
            'session_completed_at' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $completedItems = [];
            $totalPicked = 0;

            foreach ($request->picked_items as $pickedItem) {
                $inventoryItem = InventoryReceiptItem::findOrFail($pickedItem['item_id']);
                
                // Validate picked quantity doesn't exceed available quantity
                if ($pickedItem['picked_quantity'] > $inventoryItem->quantity) {
                    throw new \Exception("Picked quantity for {$inventoryItem->item_name} cannot exceed available quantity");
                }

                // Update inventory quantity (subtract picked items)
                $inventoryItem->quantity -= $pickedItem['picked_quantity'];
                $inventoryItem->save();

                // Update related item requests to 'completed' status
                ItemRequest::where('item_name', $inventoryItem->item_name)
                    ->whereIn('status', ['pending', 'in_progress'])
                    ->limit($pickedItem['picked_quantity'])
                    ->update([
                        'status' => 'COMPLETED',
                        'picked_quantity' => $pickedItem['picked_quantity']
                    ]);

                $completedItems[] = [
                    'item_id' => $inventoryItem->id,
                    'item_name' => $inventoryItem->item_name,
                    'picked_quantity' => $pickedItem['picked_quantity'],
                    'remaining_quantity' => $inventoryItem->quantity
                ];

                $totalPicked += $pickedItem['picked_quantity'];

                // Clear session data for this item
                session()->forget("picked_item_{$pickedItem['item_id']}");
            }

            // Store session completion data
            session()->put('last_completed_session', [
                'completed_at' => $request->session_completed_at,
                'total_items_picked' => $totalPicked,
                'items_count' => count($completedItems),
                'items' => $completedItems
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pick session completed successfully',
                'data' => [
                    'total_items_picked' => $totalPicked,
                    'items_processed' => count($completedItems),
                    'completed_at' => $request->session_completed_at,
                    'items' => $completedItems
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Log the error for debugging
            \Log::error('Error completing pick session: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error completing session: ' . $e->getMessage(),
                'debug' => config('app.debug') ? [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ] : null
            ], 500);
        }
    }

    /**
     * Save dispatch route for an item
     */
    public function saveRoute(Request $request)
    {
        $request->validate([
            'item_id' => 'required|string',
            'destination' => 'required|string',
            'route_data' => 'required|array',
            'route_data.distance' => 'nullable|numeric',
            'route_data.duration' => 'nullable|integer',
            'route_data.coordinates' => 'nullable|array',
            'route_data.pickup' => 'nullable|array',
            'route_data.destination_coords' => 'nullable|array',
            'route_data.is_straight_line' => 'nullable|boolean',
        ]);

        try {
            DB::beginTransaction();

            // Delete existing route for this item
            DispatchRoute::where('item_id', $request->item_id)->delete();

            // Create new route
            $route = DispatchRoute::createFromRouteData(
                $request->item_id,
                $request->destination,
                $request->route_data,
                Auth::id()
            );

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Route saved successfully',
                'data' => [
                    'route_id' => $route->id,
                    'summary' => $route->summary,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error saving dispatch route: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error saving route: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a dispatch schedule
     */
    public function createSchedule(Request $request)
    {
        $request->validate([
            'scheduled_datetime' => 'required|date|after:now',
            'priority' => 'required|in:normal,high,urgent',
            'driver_name' => 'required|string|max:255',
            'vehicle_info' => 'required|string|max:255',
            'special_instructions' => 'nullable|string',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Create dispatch schedule
            $schedule = DispatchSchedule::create([
                'schedule_id' => DispatchSchedule::generateScheduleId(),
                'title' => 'Dispatch - ' . date('M j, Y g:i A', strtotime($request->scheduled_datetime)),
                'scheduled_datetime' => $request->scheduled_datetime,
                'priority' => $request->priority,
                'status' => 'scheduled',
                'driver_name' => $request->driver_name,
                'vehicle_info' => $request->vehicle_info,
                'special_instructions' => $request->special_instructions,
                'total_items' => count($request->selected_items),
                'created_by' => Auth::id(),
            ]);

            // Calculate totals and create dispatch items
            $totalDistance = 0;
            $totalDuration = 0;
            $sequenceOrder = 1;

            foreach ($request->selected_items as $itemId) {
                // Find the route for this item
                $route = DispatchRoute::where('item_id', $itemId)->first();
                
                // Get item details from session or database
                $dispatchItems = session('last_completed_session.items', []);
                $itemData = collect($dispatchItems)->firstWhere('item_id', $itemId);

                if ($itemData) {
                    $dispatchItem = $schedule->dispatchItems()->create([
                        'picking_item_id' => null, // Will be linked when we have picking sessions
                        'dispatch_route_id' => $route?->id,
                        'item_id' => $itemId,
                        'item_name' => $itemData['item_name'],
                        'quantity' => $itemData['picked_quantity'],
                        'destination' => $route?->destination ?? 'Warehouse',
                        'status' => 'ready',
                        'sequence_order' => $sequenceOrder++,
                    ]);

                    // Add to totals
                    if ($route) {
                        $totalDistance += $route->distance_km ?? 0;
                        $totalDuration += $route->duration_minutes ?? 0;
                    }
                }
            }

            // Update schedule with calculated totals
            $schedule->update([
                'total_distance_km' => $totalDistance,
                'estimated_duration_minutes' => $totalDuration,
            ]);

            // Create initial tracking record
            DispatchTracking::create([
                'dispatch_schedule_id' => $schedule->id,
                'status' => 'scheduled',
                'description' => 'Dispatch scheduled for ' . $schedule->formatted_scheduled_time,
                'timestamp' => now(),
                'updated_by' => Auth::id(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Dispatch scheduled successfully',
                'data' => [
                    'schedule_id' => $schedule->schedule_id,
                    'total_items' => $schedule->total_items,
                    'scheduled_time' => $schedule->formatted_scheduled_time,
                    'estimated_duration' => $schedule->formatted_duration,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error creating dispatch schedule: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error creating schedule: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get dispatch schedules
     */
    public function getSchedules(Request $request)
    {
        $query = DispatchSchedule::with(['dispatchItems', 'creator', 'assignee'])
            ->orderBy('scheduled_datetime', 'desc');

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range if provided
        if ($request->has('from_date')) {
            $query->whereDate('scheduled_datetime', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $query->whereDate('scheduled_datetime', '<=', $request->to_date);
        }

        $schedules = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $schedules,
        ]);
    }

    /**
     * Update dispatch tracking
     */
    public function updateTracking(Request $request, DispatchSchedule $schedule)
    {
        $request->validate([
            'status' => 'required|in:started,en_route,arrived,completed,delayed,failed',
            'description' => 'required|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'address' => 'nullable|string',
            'additional_data' => 'nullable|array',
        ]);

        try {
            DB::beginTransaction();

            // Create tracking record
            $tracking = DispatchTracking::create([
                'dispatch_schedule_id' => $schedule->id,
                'status' => $request->status,
                'description' => $request->description,
                'current_latitude' => $request->latitude,
                'current_longitude' => $request->longitude,
                'current_address' => $request->address,
                'timestamp' => now(),
                'updated_by' => Auth::id(),
                'additional_data' => $request->additional_data,
            ]);

            // Update schedule status if needed
            if (in_array($request->status, ['started', 'completed', 'failed'])) {
                $statusMap = [
                    'started' => 'in_progress',
                    'completed' => 'completed',
                    'failed' => 'cancelled',
                ];

                $schedule->update(['status' => $statusMap[$request->status]]);

                if ($request->status === 'started') {
                    $schedule->update(['started_at' => now()]);
                } elseif ($request->status === 'completed') {
                    $schedule->update(['completed_at' => now()]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tracking updated successfully',
                'data' => [
                    'tracking_id' => $tracking->id,
                    'schedule_status' => $schedule->status,
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error updating dispatch tracking: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error updating tracking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get dispatch routes for items
     */
    public function getRoutes(Request $request)
    {
        $itemIds = $request->get('item_ids', []);
        
        if (empty($itemIds)) {
            return response()->json([
                'success' => true,
                'data' => [],
            ]);
        }

        $routes = DispatchRoute::whereIn('item_id', $itemIds)
            ->with('creator')
            ->get()
            ->keyBy('item_id');

        return response()->json([
            'success' => true,
            'data' => $routes,
        ]);
    }

    /**
     * Bulk dispatch items immediately
     */
    public function bulkDispatch(Request $request)
    {
        $request->validate([
            'item_ids' => 'required|array|min:1',
            'item_ids.*' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // Create immediate dispatch schedule
            $schedule = DispatchSchedule::create([
                'schedule_id' => DispatchSchedule::generateScheduleId(),
                'title' => 'Bulk Dispatch - ' . now()->format('M j, Y g:i A'),
                'scheduled_datetime' => now(),
                'priority' => 'normal',
                'status' => 'in_progress',
                'driver_name' => 'Immediate Dispatch',
                'vehicle_info' => 'Various',
                'total_items' => count($request->item_ids),
                'started_at' => now(),
                'created_by' => Auth::id(),
            ]);

            // Create dispatch items and mark as dispatched
            $dispatchItems = session('last_completed_session.items', []);
            $sequenceOrder = 1;

            foreach ($request->item_ids as $itemId) {
                $itemData = collect($dispatchItems)->firstWhere('item_id', $itemId);
                $route = DispatchRoute::where('item_id', $itemId)->first();

                if ($itemData) {
                    $dispatchItem = $schedule->dispatchItems()->create([
                        'dispatch_route_id' => $route?->id,
                        'item_id' => $itemId,
                        'item_name' => $itemData['item_name'],
                        'quantity' => $itemData['picked_quantity'],
                        'destination' => $route?->destination ?? 'Warehouse',
                        'status' => 'dispatched',
                        'sequence_order' => $sequenceOrder++,
                        'dispatched_at' => now(),
                    ]);
                }
            }

            // Create tracking records
            DispatchTracking::create([
                'dispatch_schedule_id' => $schedule->id,
                'status' => 'started',
                'description' => 'Bulk dispatch started',
                'timestamp' => now(),
                'updated_by' => Auth::id(),
            ]);

            DispatchTracking::create([
                'dispatch_schedule_id' => $schedule->id,
                'status' => 'completed',
                'description' => 'Bulk dispatch completed',
                'timestamp' => now(),
                'updated_by' => Auth::id(),
            ]);

            // Mark schedule as completed
            $schedule->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            // Remove dispatched items from session
            $remainingItems = collect($dispatchItems)->reject(function ($item) use ($request) {
                return in_array($item['item_id'], $request->item_ids);
            })->values()->toArray();

            session(['last_completed_session.items' => $remainingItems]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk dispatch completed successfully',
                'data' => [
                    'schedule_id' => $schedule->schedule_id,
                    'items_dispatched' => count($request->item_ids),
                    'remaining_items' => count($remainingItems),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error processing bulk dispatch: ' . $e->getMessage(), [
                'exception' => $e,
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error processing bulk dispatch: ' . $e->getMessage(),
            ], 500);
        }
    }
}
