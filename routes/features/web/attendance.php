<?php

use App\Http\Controllers\Web\Attendance\ShiftController;
use Illuminate\Support\Facades\Route;

Route::prefix('attendances')->group(function() {
    //Shift
    Route::prefix('shifts')->middleware('role:admin')->group(function() {
        Route::post('', [ShiftController::class, 'create']);
        Route::get('', [ShiftController::class, 'get']);
        Route::put('/{id}', [ShiftController::class, 'update']);
        Route::delete('/{id}', [ShiftController::class, 'delete']);
    });
});