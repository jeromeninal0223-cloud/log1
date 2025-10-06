<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first admin user or create one if none exists
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::first(); // Fallback to first user
        }

        if (!$admin) {
            $this->command->info('No users found. Please create users first.');
            return;
        }

        $projects = [
            [
                'project_code' => 'PROJ-2024-001',
                'project_title' => 'Office Equipment Deployment',
                'project_description' => 'Deploy new office equipment including computers, printers, and furniture across all departments. This project aims to modernize the workplace and improve productivity.',
                'start_date' => Carbon::now()->addDays(5),
                'expected_end_date' => Carbon::now()->addDays(35),
                'estimated_budget' => 150000.00,
                'status' => 'Planning',
                'responsible_person' => 'John Doe',
                'department' => 'IT Department',
                'created_by' => $admin->id,
            ],
            [
                'project_code' => 'PROJ-2024-002',
                'project_title' => 'IT Infrastructure Maintenance',
                'project_description' => 'Comprehensive maintenance of IT infrastructure including server updates, network optimization, and security enhancements.',
                'start_date' => Carbon::now()->subDays(10),
                'expected_end_date' => Carbon::now()->addDays(20),
                'estimated_budget' => 75000.00,
                'actual_budget' => 45000.00,
                'status' => 'Active',
                'responsible_person' => 'Jane Smith',
                'department' => 'IT Department',
                'created_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(15),
                'approved_by' => $admin->id,
            ],
            [
                'project_code' => 'PROJ-2024-003',
                'project_title' => 'Logistics Coordination System',
                'project_description' => 'Implementation of a new logistics coordination system to streamline supply chain management and improve delivery tracking.',
                'start_date' => Carbon::now()->subDays(30),
                'expected_end_date' => Carbon::now()->subDays(5),
                'actual_end_date' => Carbon::now()->subDays(3),
                'estimated_budget' => 200000.00,
                'actual_budget' => 185000.00,
                'status' => 'Completed',
                'responsible_person' => 'Mike Wilson',
                'department' => 'Logistics',
                'created_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(35),
                'approved_by' => $admin->id,
            ],
            [
                'project_code' => 'PROJ-2024-004',
                'project_title' => 'Employee Training Program',
                'project_description' => 'Comprehensive training program for all employees covering new technologies, safety protocols, and professional development.',
                'start_date' => Carbon::now()->addDays(15),
                'expected_end_date' => Carbon::now()->addDays(60),
                'estimated_budget' => 50000.00,
                'status' => 'Draft',
                'responsible_person' => 'Sarah Johnson',
                'department' => 'Human Resources',
                'created_by' => $admin->id,
            ],
            [
                'project_code' => 'PROJ-2024-005',
                'project_title' => 'Facility Security Upgrade',
                'project_description' => 'Upgrade facility security systems including CCTV installation, access control systems, and alarm systems.',
                'start_date' => Carbon::now()->subDays(5),
                'expected_end_date' => Carbon::now()->addDays(10),
                'estimated_budget' => 120000.00,
                'actual_budget' => 95000.00,
                'status' => 'On Hold',
                'responsible_person' => 'Robert Brown',
                'department' => 'Security',
                'notes' => 'Project on hold due to budget approval delays.',
                'created_by' => $admin->id,
                'approved_at' => Carbon::now()->subDays(10),
                'approved_by' => $admin->id,
            ]
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        $this->command->info('Project seeder completed successfully!');
    }
}
