<?php

use App\Http\Controllers\Web\Employee\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::prefix('employees')->group(function(){
    Route::get('',[EmployeeController::class, 'get']);
    Route::post('',[EmployeeController::class, 'create']);
    Route::put('/{id}',[EmployeeController::class, 'update']);
    Route::delete('/{id}',[EmployeeController::class, 'delete']);
});
