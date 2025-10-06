<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProcurementPlanningController;
use App\Http\Controllers\RfqController;
use App\Http\Controllers\VendorEvaluationController;

/*
|--------------------------------------------------------------------------
| Procurement Planning Routes
|--------------------------------------------------------------------------
|
| Here are the routes for procurement planning functionality including
| procurement plans, RFQs, vendor evaluations, and related features.
|
*/

// Procurement Planning Routes
Route::prefix('procurement')->name('procurement.')->group(function () {
    
    // Procurement Plans
    Route::prefix('plans')->name('plans.')->group(function () {
        Route::get('/', [ProcurementPlanningController::class, 'index'])->name('index');
        Route::post('/', [ProcurementPlanningController::class, 'store'])->name('store');
        Route::get('/stats', [ProcurementPlanningController::class, 'getStats'])->name('stats');
        Route::get('/{procurementPlan}', [ProcurementPlanningController::class, 'show'])->name('show');
        Route::put('/{procurementPlan}', [ProcurementPlanningController::class, 'update'])->name('update');
        Route::delete('/{procurementPlan}', [ProcurementPlanningController::class, 'destroy'])->name('destroy');
        Route::post('/{procurementPlan}/duplicate', [ProcurementPlanningController::class, 'duplicate'])->name('duplicate');
    });

    // RFQ Management
    Route::prefix('rfqs')->name('rfqs.')->group(function () {
        Route::get('/', [RfqController::class, 'index'])->name('index');
        Route::post('/', [RfqController::class, 'store'])->name('store');
        Route::get('/stats', [RfqController::class, 'getStats'])->name('stats');
        Route::get('/{rfq}', [RfqController::class, 'show'])->name('show');
        Route::put('/{rfq}', [RfqController::class, 'update'])->name('update');
        Route::patch('/{rfq}/publish', [RfqController::class, 'publish'])->name('publish');
        Route::patch('/{rfq}/close', [RfqController::class, 'close'])->name('close');
        Route::post('/{rfq}/award', [RfqController::class, 'award'])->name('award');
    });

    // Vendor Evaluations
    Route::prefix('evaluations')->name('evaluations.')->group(function () {
        Route::get('/', [VendorEvaluationController::class, 'index'])->name('index');
        Route::post('/', [VendorEvaluationController::class, 'store'])->name('store');
        Route::get('/stats', [VendorEvaluationController::class, 'getStats'])->name('stats');
        Route::get('/{vendorEvaluation}', [VendorEvaluationController::class, 'show'])->name('show');
        Route::put('/{vendorEvaluation}', [VendorEvaluationController::class, 'update'])->name('update');
        Route::patch('/{vendorEvaluation}/evaluate', [VendorEvaluationController::class, 'evaluate'])->name('evaluate');
        Route::get('/rfq/{rfq}', [VendorEvaluationController::class, 'getByRfq'])->name('by_rfq');
    });
});

// PLT (Project Logistics Tracker) Routes - Updated for Procurement
Route::prefix('plt')->name('plt.')->group(function () {
    Route::get('/procurement-planning', [ProcurementPlanningController::class, 'index'])->name('procurement_planning');
    Route::get('/toursetup', [ProcurementPlanningController::class, 'index'])->name('toursetup'); // Backward compatibility
});
