<?php

use App\Http\Controllers\Web\Attendance\LeaveController;
use App\Http\Controllers\Web\Attendance\PublicHolidayController;
use App\Http\Controllers\Web\Attendance\ShiftController;
use Illuminate\Support\Facades\Route;

Route::prefix('attendances')->group(function () {
    //Shift
    Route::prefix('shifts')->middleware('role:admin')->group(function () {
        Route::post('', [ShiftController::class, 'create']);
        Route::get('', [ShiftController::class, 'get']);
        Route::put('/{id}', [ShiftController::class, 'update']);
        Route::delete('/{id}', [ShiftController::class, 'delete']);
    });

    //Public Holiday
    Route::prefix('public-holidays')->middleware('role:admin')->group(function () {
        Route::post('', [PublicHolidayController::class, 'create']);
        Route::get('', [PublicHolidayController::class, 'get']);
        Route::put('/{id}', [PublicHolidayController::class, 'update']);
        Route::delete('/{id}', [PublicHolidayController::class, 'delete']);
        Route::post('/{id}/assign-schedules', [PublicHolidayController::class, 'assignPublicHoliday']);
    });

    //Leave
    Route::prefix('leaves')->group(function () {
        Route::get('', [LeaveController::class, 'get']);
        Route::post('', [LeaveController::class, 'create']);
    });
});
