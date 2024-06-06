<?php

use App\Http\Controllers\Web\Employee\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')->group(function() {
        Route::middleware('role:admin')->group(function(){
            Route::get('', [EmployeeController::class, 'get'])->middleware('role:admin');
            Route::post('', [EmployeeController::class, 'create']);
            Route::get('/{id}', [EmployeeController::class, 'getById']);
            Route::post('/{id}', [EmployeeController::class, 'update']);
            Route::delete('/{id}', [EmployeeController::class, 'delete']);
            Route::post('/{id}/resign', [EmployeeController::class, 'resign']);
            Route::post('/{id}/assign-to-admin', [EmployeeController::class, 'promoteAdmin']);
            Route::post('/{id}/change-password', [EmployeeController::class, 'resetPassword']);
        });
        Route::put('/reset-password', [EmployeeController::class, 'resetPassword'])->middleware('role:user');
});