<?php

namespace App\Algorithms\Employee;

use App\Http\Requests\Employee\EmployeeRequest;
use App\Models\Employee\Employee;
use App\Services\Constant\Activity\ActivityAction;
use App\Services\Constant\Employee\EmployeeUserRole;
use App\Services\Number\Generator\Employee\EmployeeNumber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Exception;
use Illuminate\Support\Facades\Auth;

class EmployeeAlgo
{
    public function __construct(public ?Employee $employee = null)
    {
    }

    public function create(EmployeeRequest $request)
    {
        try {
            DB::transaction(function () use ($request) {
                
                $createdBy = [];
                if($user = Auth::user()){
                    $createdBy = [
                        'createdBy' => $user->employeeId,
                        'createdByName' => $user->employee->name
                    ];
                }

                $this->employee = Employee::create([
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
                ] + $createdBy);

                if ($request->has('siblings')) {
                    $siblings = $this->saveSiblings($request);
                    if (!$siblings) {
                       errEmployeeSiblingsSave();
                    }
                }
                $employeeUser = $this->saveEmployeeUser($request);
                if (!$employeeUser) {
                    errEmployeeUserSave();
                }

                $this->employee->setActivityPropertyAttributes(ActivityAction::CREATE)
                ->saveActivity("Enter new Employee :  {$this->employee->name}  [{$this->employee->id}]");
            });
            return success($this->employee);
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    public function update(Request $request){
        try {
            DB::transaction(function() use($request){

                $this->employee->setOldActivityPropertyAttributes(ActivityAction::UPDATE);

                $this->employee->update([
                    'name' => $request->name,
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

                if($request->has('siblings')) {
                    $siblings = $this->saveSiblings($request);
                    if (!$siblings) {
                       errEmployeeSiblingsSave();
                    }
                }

                $employeeUser = $this->saveEmployeeUser($request);
                if (!$employeeUser) {
                    errEmployeeUserSave();
                }

                $this->employee->setActivityPropertyAttributes(ActivityAction::UPDATE)
                ->saveActivity("Enter new Employee :  {$this->employee->name}  [{$this->employee->id}]");
            });
            return success($this->employee);
        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    public function delete(){
        try { 
            DB::transaction(function (){
                $this->employee->setOldActivityPropertyAttributes(ActivityAction::DELETE);

                $this->employee->delete();

                $this->employee->setActivityPropertyAttributes(ActivityAction::DELETE)
                ->saveActivity("Delete employee : {$this->employee->name} [{$this->employee->id}]");
            });
            return success();
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTIONS */

    private function saveEmployeeUser(Request $request){
        $form = $request->only(['email','password']);

        return $this->employee->saveUser($form);
    }

    private function saveSiblings(Request $request){
        return $this->employee->saveSiblings($request->siblings);
    }
}
