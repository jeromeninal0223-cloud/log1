<?php

namespace Database\Seeders;

use App\Models\ItemRequest;
use App\Models\User;
use Illuminate\Database\Seeder;

class ItemRequestSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first user or create a default one
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'System Admin',
                'email' => 'admin@jetlouge.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
        }

        $requests = [
            [
                'item_name' => 'Office Chair Ergonomic',
                'asset_code' => 'AST-002',
                'category' => 'Furniture',
                'storage_location' => 'Warehouse B - C1',
                'requested_quantity' => 2,
                'available_quantity' => 3,
                'picked_quantity' => 0,
                'priority' => 'HIGH',
                'status' => 'PENDING',
                'requested_by' => $user->id,
                'notes' => 'Ergonomic chairs needed for new workstations'
            ],
            [
                'item_name' => 'Dell XPS 13 Laptop',
                'asset_code' => 'AST-001',
                'category' => 'Electronics',
                'storage_location' => 'Warehouse A - B2',
                'requested_quantity' => 5,
                'available_quantity' => 15,
                'picked_quantity' => 0,
                'priority' => 'HIGH',
                'status' => 'PENDING',
                'requested_by' => $user->id,
                'notes' => 'High-performance laptops for development team'
            ],
            [
                'item_name' => 'HP ProBook 450 G9',
                'asset_code' => 'AST-003',
                'category' => 'Electronics',
                'storage_location' => 'Warehouse A - B3',
                'requested_quantity' => 3,
                'available_quantity' => 8,
                'picked_quantity' => 0,
                'priority' => 'MEDIUM',
                'status' => 'PENDING',
                'requested_by' => $user->id,
                'notes' => 'Standard laptops for administrative staff'
            ],
            [
                'item_name' => 'Logitech Wireless Mouse',
                'asset_code' => 'ACC-003',
                'category' => 'Accessories',
                'storage_location' => 'Warehouse A - C2',
                'requested_quantity' => 10,
                'available_quantity' => 25,
                'picked_quantity' => 0,
                'priority' => 'MEDIUM',
                'status' => 'PENDING',
                'requested_by' => $user->id,
                'notes' => 'Wireless mice for all workstations'
            ],
            [
                'item_name' => '24" LED Monitor',
                'asset_code' => 'MON-001',
                'category' => 'Electronics',
                'storage_location' => 'Warehouse A - B1',
                'requested_quantity' => 4,
                'available_quantity' => 3,
                'picked_quantity' => 0,
                'priority' => 'HIGH',
                'status' => 'PENDING',
                'requested_by' => $user->id,
                'notes' => 'Additional monitors needed - insufficient stock'
            ],
            [
                'item_name' => 'HP LaserJet Printer',
                'asset_code' => 'PRT-001',
                'category' => 'Electronics',
                'storage_location' => 'Warehouse B - A3',
                'requested_quantity' => 1,
                'available_quantity' => 6,
                'picked_quantity' => 0,
                'priority' => 'LOW',
                'status' => 'PENDING',
                'requested_by' => $user->id,
                'notes' => 'Replacement printer for conference room'
            ]
        ];

        foreach ($requests as $request) {
            ItemRequest::create($request);
        }
    }
}
