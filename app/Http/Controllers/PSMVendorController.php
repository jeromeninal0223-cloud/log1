<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class PSMVendorController extends Controller
{
    public function index()
    {
        $vendors = collect();
        $stats = [
            'pending' => 0,
            'approved' => 0,
            'suspended' => 0,
            'total' => 0,
        ];

        if (Schema::hasTable('vendors')) {
            $vendors = Vendor::all();
            $stats = [
                'pending' => $vendors->where('status', 'Pending')->count(),
                'approved' => $vendors->where('status', 'Active')->count(),
                'suspended' => $vendors->where('status', 'Suspended')->count(),
                'total' => $vendors->count(),
            ];
        }

        return view('PSM.vendor', compact('vendors', 'stats'));
    }
}
