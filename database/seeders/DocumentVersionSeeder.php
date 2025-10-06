<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use App\Models\DocumentVersion;
use App\Models\User;
use Carbon\Carbon;

class DocumentVersionSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get or create some users for testing
        $admin = User::where('role', 'admin')->first();
        $logisticsStaff = User::where('role', 'logistics_staff')->first();
        
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@jetlouge.com',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
        }
        
        if (!$logisticsStaff) {
            $logisticsStaff = User::create([
                'name' => 'Logistics Staff',
                'email' => 'logistics@jetlouge.com',
                'password' => bcrypt('password'),
                'role' => 'logistics_staff'
            ]);
        }

        // Create sample documents
        $documents = [
            [
                'title' => 'Supply Chain Contract 2024',
                'document_type' => 'Contract',
                'description' => 'Main supply chain contract for 2024 operations',
                'current_version' => '3.2',
                'created_by_id' => $admin->id,
                'created_by_name' => $admin->name,
                'status' => 'active'
            ],
            [
                'title' => 'Logistics Operations Manual',
                'document_type' => 'Manual',
                'description' => 'Comprehensive guide for logistics operations',
                'current_version' => '2.1',
                'created_by_id' => $logisticsStaff->id,
                'created_by_name' => $logisticsStaff->name,
                'status' => 'active'
            ],
            [
                'title' => 'Procurement Policy Document',
                'document_type' => 'Policy',
                'description' => 'Official procurement policies and procedures',
                'current_version' => '4.0',
                'created_by_id' => $admin->id,
                'created_by_name' => $admin->name,
                'status' => 'active'
            ],
            [
                'title' => 'Vendor Agreement Template',
                'document_type' => 'Template',
                'description' => 'Standard template for vendor agreements',
                'current_version' => '1.3',
                'created_by_id' => $admin->id,
                'created_by_name' => $admin->name,
                'status' => 'active'
            ],
            [
                'title' => 'Quality Assurance Guidelines',
                'document_type' => 'Guidelines',
                'description' => 'Quality assurance procedures and standards',
                'current_version' => '2.0',
                'created_by_id' => $logisticsStaff->id,
                'created_by_name' => $logisticsStaff->name,
                'status' => 'active'
            ]
        ];

        foreach ($documents as $docData) {
            $document = Document::create($docData);
            
            // Create version history for each document
            $this->createVersionHistory($document, $admin, $logisticsStaff);
        }
    }

    /**
     * Create version history for a document
     */
    private function createVersionHistory(Document $document, User $admin, User $logisticsStaff): void
    {
        $versions = [];
        $currentVersion = $document->current_version;
        
        // Generate version history based on document type
        switch ($document->document_type) {
            case 'Contract':
                $versions = [
                    ['1.0', $admin, 'Initial contract draft', 35, 1654321],
                    ['1.1', $logisticsStaff, 'Added logistics clauses and delivery terms', 28, 1723456],
                    ['2.0', $admin, 'Major revision with updated legal terms', 21, 1876543],
                    ['2.1', $logisticsStaff, 'Minor corrections and formatting updates', 14, 1890123],
                    ['3.0', $admin, 'Added compliance requirements and new sections', 7, 1987654],
                    ['3.1', $logisticsStaff, 'Fixed formatting issues and typos', 3, 2045678],
                    ['3.2', $admin, 'Updated terms and added new compliance clauses', 1, 2048576]
                ];
                break;
                
            case 'Manual':
                $versions = [
                    ['1.0', $logisticsStaff, 'Initial operations manual', 45, 2234567],
                    ['1.5', $admin, 'Added safety procedures and protocols', 30, 2456789],
                    ['2.0', $logisticsStaff, 'Complete restructure with new processes', 15, 2567890],
                    ['2.1', $admin, 'Updated with latest regulatory requirements', 5, 2678901]
                ];
                break;
                
            case 'Policy':
                $versions = [
                    ['1.0', $admin, 'Initial policy framework', 60, 1234567],
                    ['2.0', $admin, 'Major policy updates and new guidelines', 40, 1456789],
                    ['3.0', $logisticsStaff, 'Added operational procedures', 25, 1678901],
                    ['3.5', $admin, 'Compliance updates and legal revisions', 10, 1789012],
                    ['4.0', $admin, 'Complete policy overhaul for 2024', 2, 1890123]
                ];
                break;
                
            case 'Template':
                $versions = [
                    ['1.0', $admin, 'Basic vendor agreement template', 20, 987654],
                    ['1.1', $logisticsStaff, 'Added logistics-specific clauses', 12, 1098765],
                    ['1.2', $admin, 'Legal review and updates', 8, 1109876],
                    ['1.3', $admin, 'Final template with all requirements', 3, 1120987]
                ];
                break;
                
            case 'Guidelines':
                $versions = [
                    ['1.0', $logisticsStaff, 'Initial QA guidelines', 30, 1567890],
                    ['1.5', $admin, 'Enhanced quality standards', 18, 1678901],
                    ['2.0', $logisticsStaff, 'Updated procedures and checklists', 6, 1789012]
                ];
                break;
        }

        // Create the versions
        foreach ($versions as [$versionNumber, $user, $changesSummary, $daysAgo, $fileSize]) {
            $status = $versionNumber === $currentVersion ? 'active' : 'archived';
            
            DocumentVersion::create([
                'document_id' => $document->id,
                'version_number' => $versionNumber,
                'modified_by_id' => $user->id,
                'modified_by_name' => $user->name,
                'user_role' => $user->role,
                'changes_summary' => $changesSummary,
                'file_path' => "documents/versions/{$document->id}/v{$versionNumber}.pdf",
                'file_size' => $fileSize,
                'status' => $status,
                'metadata' => [
                    'file_type' => 'pdf',
                    'original_name' => str_replace(' ', '_', $document->title) . "_v{$versionNumber}.pdf"
                ],
                'created_at' => Carbon::now()->subDays($daysAgo),
                'updated_at' => Carbon::now()->subDays($daysAgo)
            ]);
        }
    }
}
