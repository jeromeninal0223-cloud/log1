<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PickingDispatchController;

/*
|--------------------------------------------------------------------------
| Picking & Dispatch Routes
|--------------------------------------------------------------------------
|
| Routes for the picking and dispatch management system.
|
*/

Route::middleware(['auth'])->prefix('picking-dispatch')->name('picking-dispatch.')->group(function () {
    
    // Main picking and dispatch page
    Route::get('/', [PickingDispatchController::class, 'index'])->name('index');
    
    // Picking operations
    Route::post('/update-picked/{item}', [PickingDispatchController::class, 'updatePicked'])->name('update-picked');
    Route::post('/save-progress', [PickingDispatchController::class, 'saveProgress'])->name('save-progress');
    Route::post('/complete-session', [PickingDispatchController::class, 'completeSession'])->name('complete-session');
    Route::post('/remove-from-dispatch', [PickingDispatchController::class, 'removeFromDispatch'])->name('remove-from-dispatch');
    
    // Route management
    Route::post('/save-route', [PickingDispatchController::class, 'saveRoute'])->name('save-route');
    Route::get('/routes', [PickingDispatchController::class, 'getRoutes'])->name('get-routes');
    
    // Dispatch scheduling
    Route::post('/create-schedule', [PickingDispatchController::class, 'createSchedule'])->name('create-schedule');
    Route::get('/schedules', [PickingDispatchController::class, 'getSchedules'])->name('get-schedules');
    Route::post('/bulk-dispatch', [PickingDispatchController::class, 'bulkDispatch'])->name('bulk-dispatch');
    
    // Dispatch tracking
    Route::post('/schedules/{schedule}/tracking', [PickingDispatchController::class, 'updateTracking'])->name('update-tracking');
    
});
