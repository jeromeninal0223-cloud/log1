<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PickingSession;
use App\Models\PickingItem;
use App\Models\DispatchRoute;
use App\Models\DispatchSchedule;
use App\Models\DispatchItem;
use App\Models\DispatchTracking;

class PickingDispatchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a user for testing
        $user = User::first() ?? User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create a picking session
        $session = PickingSession::create([
            'session_id' => PickingSession::generateSessionId(),
            'user_id' => $user->id,
            'status' => 'active',
            'started_at' => now()->subHours(2),
            'metadata' => [
                'warehouse' => 'Main Warehouse',
                'shift' => 'Morning',
            ],
        ]);

        // Create picking items
        $items = [
            [
                'item_id' => '22',
                'item_name' => 'ADADAD',
                'item_code' => 'ITM-001',
                'description' => 'Sample item for testing',
                'requested_quantity' => 5,
                'picked_quantity' => 5,
                'unit' => 'pcs',
                'location' => 'A1-B2-C3',
                'status' => 'picked',
                'priority' => 'normal',
                'picked_at' => now()->subHour(),
                'picked_by' => $user->id,
            ],
            [
                'item_id' => '23',
                'item_name' => 'Office Chair',
                'item_code' => 'ITM-002',
                'description' => 'Ergonomic office chair',
                'requested_quantity' => 3,
                'picked_quantity' => 3,
                'unit' => 'pcs',
                'location' => 'B2-C3-D4',
                'status' => 'picked',
                'priority' => 'high',
                'picked_at' => now()->subMinutes(30),
                'picked_by' => $user->id,
            ],
            [
                'item_id' => '24',
                'item_name' => 'Laptop Computer',
                'item_code' => 'ITM-003',
                'description' => 'Business laptop for office use',
                'requested_quantity' => 2,
                'picked_quantity' => 2,
                'unit' => 'pcs',
                'location' => 'C3-D4-E5',
                'status' => 'picked',
                'priority' => 'urgent',
                'picked_at' => now()->subMinutes(15),
                'picked_by' => $user->id,
            ],
        ];

        foreach ($items as $itemData) {
            $pickingItem = $session->pickingItems()->create($itemData);

            // Create dispatch routes for picked items
            if ($pickingItem->status === 'picked') {
                $destinations = ['IT Department', 'Admin Office', 'Maintenance Department'];
                $destination = $destinations[array_rand($destinations)];

                DispatchRoute::create([
                    'item_id' => $pickingItem->item_id,
                    'destination' => $destination,
                    'pickup_latitude' => 14.5995 + (rand(-100, 100) / 10000),
                    'pickup_longitude' => 120.9842 + (rand(-100, 100) / 10000),
                    'pickup_address' => 'Main Warehouse, Manila',
                    'destination_latitude' => 14.5995 + (rand(-500, 500) / 10000),
                    'destination_longitude' => 120.9842 + (rand(-500, 500) / 10000),
                    'destination_address' => $destination . ', Manila',
                    'distance_km' => rand(50, 200) / 10, // 5.0 to 20.0 km
                    'duration_minutes' => rand(15, 60),
                    'is_straight_line' => rand(0, 1) === 1,
                    'route_coordinates' => [
                        [14.5995, 120.9842],
                        [14.5995 + (rand(-500, 500) / 10000), 120.9842 + (rand(-500, 500) / 10000)],
                    ],
                    'route_type' => 'road',
                    'calculated_at' => now(),
                    'created_by' => $user->id,
                ]);
            }
        }

        // Create a dispatch schedule
        $schedule = DispatchSchedule::create([
            'schedule_id' => DispatchSchedule::generateScheduleId(),
            'title' => 'Morning Delivery Run',
            'scheduled_datetime' => now()->addHours(2),
            'priority' => 'normal',
            'status' => 'scheduled',
            'driver_name' => 'John Doe',
            'vehicle_info' => 'Truck A (TRK-001)',
            'special_instructions' => 'Handle with care. Contact recipient before delivery.',
            'total_items' => 3,
            'estimated_duration_minutes' => 120,
            'total_distance_km' => 25.5,
            'created_by' => $user->id,
            'metadata' => [
                'route_optimized' => true,
                'fuel_estimate' => '15L',
            ],
        ]);

        // Create dispatch items for the schedule
        foreach ($session->pickingItems as $index => $pickingItem) {
            $dispatchItem = $schedule->dispatchItems()->create([
                'picking_item_id' => $pickingItem->id,
                'dispatch_route_id' => $pickingItem->dispatchRoute?->id,
                'item_id' => $pickingItem->item_id,
                'item_name' => $pickingItem->item_name,
                'quantity' => $pickingItem->picked_quantity,
                'destination' => $pickingItem->dispatchRoute?->destination ?? 'Warehouse',
                'status' => 'ready',
                'sequence_order' => $index + 1,
            ]);
        }

        // Create some tracking records
        $trackingStatuses = [
            ['status' => 'started', 'description' => 'Dispatch started from warehouse'],
            ['status' => 'en_route', 'description' => 'Vehicle en route to first destination'],
            ['status' => 'arrived', 'description' => 'Arrived at IT Department'],
        ];

        foreach ($trackingStatuses as $index => $tracking) {
            DispatchTracking::create([
                'dispatch_schedule_id' => $schedule->id,
                'status' => $tracking['status'],
                'description' => $tracking['description'],
                'current_latitude' => 14.5995 + ($index * 0.001),
                'current_longitude' => 120.9842 + ($index * 0.001),
                'current_address' => 'Location ' . ($index + 1) . ', Manila',
                'timestamp' => now()->subMinutes(60 - ($index * 20)),
                'updated_by' => $user->id,
                'additional_data' => [
                    'vehicle_speed' => rand(20, 60),
                    'fuel_level' => rand(50, 100),
                ],
            ]);
        }

        $this->command->info('Picking and Dispatch sample data created successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 Picking Session');
        $this->command->info('- 3 Picking Items');
        $this->command->info('- 3 Dispatch Routes');
        $this->command->info('- 1 Dispatch Schedule');
        $this->command->info('- 3 Dispatch Items');
        $this->command->info('- 3 Tracking Records');
    }
}
