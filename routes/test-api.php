<?php

use App\Http\Controllers\ContractSigningController;
use Illuminate\Support\Facades\Route;

// Simple test route to check if controller works
Route::get('/test-contract-status/{id}', function($id) {
    try {
        $controller = new ContractSigningController();
        return $controller->getSigningStatus($id);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});
