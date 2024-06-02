<?php

namespace App\Algorithms\Employee;

use App\Http\Requests\Employee\EmployeeRequest;
use App\Models\Employee\Employee;
use App\Services\Constant\Employee\EmployeeUserRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeAlgo
{

    public function __construct(?Employee $employee = null)
    {
        
    }

    public function create(EmployeeRequest $request)
    {
        try {
            $employee = DB::transaction(function () use ($request) {

                $photoPath = null;
                if ($request->has('photo')) {
                    $photoPath = $request->file('photo')->store('employee','public');
                }

                $employee = Employee::create([
                    'name' => $request->name,
                    'companyOfficeId' => $request->companyOfficeId,
                    'departmentId' => $request->departmentId,
                    'phone' => $request?->phone,
                    'address' => $request->address,
                    'photo' => $photoPath,
                    'fatherName' => $request->fatherName,
                    'fatherPhone' => $request->fatherPhone,
                    'fatherEmail' => $request->fatherEmail,
                    'motherName' => $request->motherName,
                    'motherPhone' => $request->motherPhone,
                    'motherEmail' => $request->motherEmail,
                ]);


                if ($request->has('siblings')) {
                    $this->saveSiblings($employee, $request);
                }

                $this->saveEmployeeUser($employee, $request);

                return $employee;
            });
            return success($employee);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTIONS */

    public function saveEmployeeUser(Employee $employee,Request $request){
        return $employee->user()->create([
            'email' => $request->email,
            'password' => $request->password,
            'role' => EmployeeUserRole::USER_ID
        ]);
    }

    public function saveSiblings(Employee $employee, Request $request){
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

    public function savePhoto(Request $request){

    }
}
