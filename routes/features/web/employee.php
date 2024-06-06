<?php

use App\Http\Controllers\Web\Employee\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')->middleware('role:admin')->group(function(){
    Route::get('',[EmployeeController::class, 'get']);
    Route::post('',[EmployeeController::class, 'create']);
    Route::post('/{id}',[EmployeeController::class, 'update']);
    Route::delete('/{id}',[EmployeeController::class, 'delete']);
    Route::post('/{id}/resign', [EmployeeController::class, 'resign']);
    Route::post('/{id}/assign-to-admin', [EmployeeController::class, 'promoteAdmin']);
});
