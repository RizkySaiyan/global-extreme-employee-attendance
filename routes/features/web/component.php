<?php

use App\Http\Controllers\Web\Component\CompanyOfficeController;
use App\Http\Controllers\Web\Component\DepartmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('components')->middleware(['role:user'])->group(function(){

    //company office
    Route::prefix('company-offices')->group(function(){
        Route::get('',[CompanyOfficeController::class, 'get']);
        Route::post('',[CompanyOfficeController::class, 'create']);
        Route::put('/{id}',[CompanyOfficeController::class, 'update']);
        Route::delete('/{id}',[CompanyOfficeController::class, 'delete']);
        Route::get('/departments',[CompanyOfficeController::class,'getDepartmentMappings']);
        Route::get('/{id}/departments',[CompanyOfficeController::class,'getDepartmentMapping']);
        Route::post('/{id}/departments',[CompanyOfficeController::class,'saveDepartment']);
    });

    //department
    Route::prefix('departments')->group(function(){
        Route::get('',[DepartmentController::class, 'get']);
        Route::post('',[DepartmentController::class, 'create']);
        Route::put('/{id}',[DepartmentController::class, 'update']);
        Route::delete('/{id}',[DepartmentController::class, 'delete']);
    });
});