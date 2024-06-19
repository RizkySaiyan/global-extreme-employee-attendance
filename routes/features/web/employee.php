<?php

use App\Http\Controllers\Web\Employee\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')->group(function () {
    Route::middleware('role:admin')->group(function () {
        Route::get('', [EmployeeController::class, 'get']);
        Route::post('', [EmployeeController::class, 'create']);
        Route::get('/{id}', [EmployeeController::class, 'getById']);
        Route::post('/{id}', [EmployeeController::class, 'update']);
        Route::delete('/{id}', [EmployeeController::class, 'delete']);
        Route::post('/{id}/assign-to-admin', [EmployeeController::class, 'promoteAdmin']);
        Route::post('/{id}/change-password', [EmployeeController::class, 'resetPassword']);
        Route::post('/{id}/update-status-resign', [EmployeeController::class, 'updateStatusResign']);
    });
    Route::middleware('role:admin,user')->group(function () {
        Route::post('/{id}/resign', [EmployeeController::class, 'resign']);
        Route::get('/personal/info', [EmployeeController::class, 'personalInfo']);
        Route::post('/personal/reset-password', [EmployeeController::class, 'resetPassword']);
    });
});
