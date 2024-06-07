<?php

namespace App\Http\Controllers\Web\Employee;

use App\Algorithms\Employee\EmployeeAlgo;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\EmployeeRequest;
use App\Http\Requests\Employee\EmployeeResignRequest;
use App\Http\Requests\Employee\ResetPasswordRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee\Employee;
use App\Parser\Employee\EmployeeParser;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //
    public function get(Request $request){
        $employee = Employee::getOrPaginate($request);

        return success($employee);
    }

    public function getById($id){
        $employee = Employee::findOrFail($id);

        if (!$employee) {
            errEmployeeNotFound();
        }

        return success(EmployeeParser::first($employee));
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

    public function resign($id,EmployeeResignRequest $request){
        $employee = Employee::findOrFail($id);
        if(!$employee){
            errEmployeeNotFound();
        }

        $algo = new EmployeeAlgo($employee);
        return $algo->resign($request);
    }

    public function promoteAdmin($id){
        $employee = Employee::findOrFail($id);
        if (!$employee) {
            errEmployeeNotFound();
        }

        $algo = new EmployeeAlgo($employee);
        return $algo->changeRoleToAdmin();
    }

    public function resetPassword(ResetPasswordRequest $request, $id = null){
        $employee = Employee::find($id) ?? null;

        $algo = new EmployeeAlgo($employee);
        return $algo->resetPassword($request);
    }

    public function updateStatusResign($id){
        $employee = Employee::findOrFail($id);
        if (!$employee) {
            errEmployeeNotFound();
        }

        $algo = new EmployeeAlgo($employee);
        return $algo->updateStatusResign();
    }
}
