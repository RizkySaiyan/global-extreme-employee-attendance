<?php

namespace App\Algorithms\Employee;

use App\Http\Requests\Employee\EmployeeRequest;
use App\Models\Employee\Employee;
use App\Services\Constant\Employee\EmployeeUserRole;
use App\Services\Number\Generator\Employee\EmployeeNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
class EmployeeAlgo
{

    public function create(EmployeeRequest $request)
    {
        try {
            $employee = DB::transaction(function () use ($request) {

                $employee = Employee::create([
                    'name' => $request->name,
                    'number' => EmployeeNumber::generate(),
                    'companyOfficeId' => $request->companyOfficeId,
                    'departmentId' => $request->departmentId,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'photo' => $request->photo,
                    'fatherName' => $request->fatherName,
                    'fatherPhone' => $request->fatherPhone,
                    'fatherEmail' => $request->fatherEmail,
                    'motherName' => $request->motherName,
                    'motherPhone' => $request->motherPhone,
                    'motherEmail' => $request->motherEmail,
                ]);

                if ($request->has('siblings')) {
                    $siblings = $this->createSiblings($employee, $request);
                    if (!$siblings) {
                       errEmployeeSiblingsSave();
                    }
                }
                $user = $this->createEmployeeUser($employee, $request);
                if (!$user) {
                    errEmployeeUserSave();
                }

                return $employee;
            });
            return success($employee);
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTIONS */

    public function createEmployeeUser(Employee $employee,Request $request){
        return $employee->user()->create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => EmployeeUserRole::USER_ID
        ]);
    }

    public function createSiblings(Employee $employee, Request $request){
        $siblings = [];
        foreach ($request->siblings as $value) {
            $siblings[] = [
                'name' => $value['name'],
                'phone' => $value['phone'],
                'email' => $value['email']
            ];
        }
        return $employee->siblings()->createMany($siblings);
    }
}
