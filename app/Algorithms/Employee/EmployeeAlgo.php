<?php

namespace App\Algorithms\Employee;

use App\Http\Requests\Employee\EmployeeRequest;
use App\Jobs\DeleteEmployeeAttendance;
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

                $this->employee = $this->saveEmployee($request);

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

                $this->saveEmployee($request);

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

                //activate when employee has attendance
               // DeleteEmployeeAttendance::dispatch($this->employee);

                $this->employee->delete();
                
                $this->employee->setActivityPropertyAttributes(ActivityAction::DELETE)
                ->saveActivity("Delete employee : {$this->employee->name} [{$this->employee->id}]");
            });
            return success();
        } catch (Exception $exception) {
            exception($exception);
        }
    }

    public function resign(Request $request){
        try {
            DB::transaction(function() use($request){
                
                if ($this->employee->resign){
                    errEmployeeResignExist();
                }
                
                $createdBy = [];
                if ($user = Auth::user()) {
                    $createdBy = [
                        'createdBy' => $user->employeeId,
                        'createdByName' => $user->employee->name
                    ];
                }

                $this->employee->resign()->create([
                    'date' => $request->date,
                    'reason' => $request->reason,
                    'file' => $request->file,  
                ] + $createdBy);                           
            });

            return success($this->employee->resign);

        } catch (\Exception $exception) {
            exception($exception);
        }
    }

    /** FUNCTIONS */
    private function saveEmployee(Request $request){
        $form = $request->except(['siblings','password']);
        
        if($this->employee){
            $this->employee->update($form);
            $this->employee->fresh($this->employee);
            return $this->employee;
        }

        $createdBy = [];
        if ($user = Auth::user()) {
            $createdBy = [
                'createdBy' => $user->employeeId,
                'createdByName' => $user->employee->name
            ];
        }

        return Employee::create($form + [
            'number' => EmployeeNumber::generate()
        ] + $createdBy);
    }

    private function saveEmployeeUser(Request $request){
        $form = $request->only(['email','password']);
        $form['role'] = EmployeeUserRole::USER_ID;
        $form['password'] = Hash::make($form['password']);
        
        return $this->employee->saveUser($form);
    }

    private function saveSiblings(Request $request){
        return $this->employee->saveSiblings($request->siblings);
    }

    public function savePhoto(){
        
    }
}
