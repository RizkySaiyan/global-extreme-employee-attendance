<?php

namespace App\Algorithms\Employee;

use App\Http\Requests\Employee\EmployeeRequest;
use App\Models\Employee\Employee;
use App\Services\Constant\Employee\EmployeeUserRole;
use Illuminate\Support\Facades\DB;

class EmployeeAlgo
{

    public function create(EmployeeRequest $request)
    {
        try {
            $employee = DB::transaction(function () use ($request) {

                $employee = Employee::create([
                    'name' => $request->name,
                    'companyOfficeId' => $request->companyOfficeId,
                    'departmentId' => $request->departmentId,
                    'phone' => $request?->phone,
                    'address' => $request->address,
                    'photo' => $request->photo,
                    'email' => $request->email,
                    'password' => $request->password,
                    'fatherName' => $request->fatherName,
                    'fatherPhone' => $request->fatherPhone,
                    'fatherEmail' => $request->fatherEmail,
                    'motherName' => $request->motherName,
                    'motherPhone' => $request->motherPhone,
                    'motherEmail' => $request->motherEmail,
                ]);

                $siblings = [];
                foreach ($request->siblings as $value) {
                    $siblings[] = [
                        'name' => $value['name'],
                        'phone' => $value['phone'],
                        'email' => $value['email']
                    ];
                }
                $employee->siblings()->createMany($siblings);
                $employee->user()->create([
                    'email' => $request->email,
                    'password' => $request->password,
                    'role' => EmployeeUserRole::USER_ID
                ]);

                return $employee;
            });
            return success($employee);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }
}
