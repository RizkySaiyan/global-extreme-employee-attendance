<?php

namespace App\Http\Controllers\Web\Employee;

use App\Algorithms\Employee\EmployeeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //
    public function get(Request $request){
        $employee = Employee::getOrPaginate($request);

        return success($employee);
    }

    public function create(EmployeeRequest $request){
        $algo =  new EmployeeAlgo();

        return $algo->create($request);
    }

    public function update($id, UpdateEmployeeRequest $request){
        $employee = Employee::findOrFail($id);
        if (!$employee) {
            errEmployeeNotFound();
        }

        $algo = new EmployeeAlgo($employee);
        return $algo->update($request);
    }

    public function delete($id){
        $employee = Employee::findOrFail($id);
        if(!$employee){
            errEmployeeNotFound();
        }

        $algo = new EmployeeAlgo($employee);
        return $algo->delete();
    }
}
