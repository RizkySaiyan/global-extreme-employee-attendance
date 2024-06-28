<?php

use App\Http\Controllers\Web\Attendance\CorrectionController;
use App\Http\Controllers\Web\Attendance\LeaveController;
use App\Http\Controllers\Web\Attendance\PublicHolidayController;
use App\Http\Controllers\Web\Attendance\ScheduleController;
use App\Http\Controllers\Web\Attendance\ShiftController;
use App\Http\Controllers\Web\Attendance\TimesheetController;
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
        Route::middleware('role:admin,user')->group(function () {
            Route::get('', [LeaveController::class, 'get']);
            Route::post('', [LeaveController::class, 'create']);
            Route::patch('/{id}', [LeaveController::class, 'approveLeaves']);
            Route::delete('/{id}', [LeaveController::class, 'delete']);
            Route::get('/personal', [LeaveController::class, 'personalLeaves']);
            Route::get('/personal/balances', [LeaveController::class, 'checkBalance']);
        });
    });

    //Schedule
    Route::prefix('schedules')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::post('', [ScheduleController::class, 'create']);
            Route::delete('/{id}', [ScheduleController::class, 'delete']);
        });
        Route::middleware('role:admin,user')->group(function () {
            Route::get('', [ScheduleController::class, 'get']);
        });
    });

    //Timesheets
    Route::prefix('timesheets')->group(function () {
        Route::middleware('role:admin')->group(function () {
            Route::post('/generate-excel', [TimesheetController::class, 'generateExcel']);
            Route::post('/generate-pdf', [TimesheetController::class, 'generatePdf']);
        });
        Route::middleware('role:admin,user')->group(function () {
            Route::get('', [TimesheetController::class, 'get']);
            Route::post('', [TimesheetController::class, 'attend']);
        });
    });

    //Correction
    Route::prefix('corrections')->group(function () {
        Route::middleware('role:admin,user')->group(function () {
            Route::post('', [CorrectionController::class, 'create']);
            Route::get('', [CorrectionController::class, 'get']);
        });
        Route::middleware('role:admin')->group(function () {
            Route::post('/{id}/approves', [CorrectionController::class, 'approves']);
            Route::post('/{id}/disapproves', [CorrectionController::class, 'disapproves']);
        });
    });
});
